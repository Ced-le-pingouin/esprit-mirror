<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
//
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2,
// as published by the Free Software Foundation.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
//
// Copyright (C) 2001-2006  Unite de Technologie de l'Education,
//                          Universite de Mons-Hainaut, Belgium.

/**
 * @file	OO.php
 */

require_once(dirname(__FILE__).'/Erreur.php');

$_aaInterfaces        = array(); ///< sauvegarde les interfaces déclarées (nom d'une interface = clé, valeur = tableau des noms de classes qui l'implémentent)
$_asClassesAbstraites = array(); ///< sauvegarde l'info indiquant que certaines classes sont considérées comme abstraites

/**
 * Classe permettant de gérer de concepts "forcés" absents de PHP 4, comme la déclaration d'interfaces, l'obligation
 * pour une classe d'implémenter une interface, les classes et méthodes abstraites, et les méthodes strictement
 * statiques
 */
class OO
{
	/**
	 * Définit une classe PHP comme étant une interface
	 *
	 * @param	v_sInterface	le nom de la classe qui doit être considérée comme une interface. S'il \c null (par
	 * 							défaut), on tentera de déterminer le nom de l'interface (classe) en fonction du fichier
	 * 							d'où provient l'appel, qui devra alors être un fichier qui définit l'interface (classe)
	 * 							tout en portant le même nom (avec ou sans extension)
	 *
	 * @warning	Il est préférable, pour des raisons de cohérence, de placer le OO:defInterface() après la définition de
	 * 			la classe PHP qu'on veut déclarer comme étant une interface. Voir la remarque sur #implemente().
	 */
	function defInterface($v_sInterface = NULL)
	{
		if (is_null($v_sInterface))
			$v_sInterface = OO::_retClasseAppelante();

		if (!OO::interfaceExiste($v_sInterface))
			$GLOBALS['_aaInterfaces'][$v_sInterface] = array();
	}

	/**
	 * Vérifie l'existence d'une interface
	 *
	 * @param	v_sInterface			le nom de l'interface dont il faut vérifier l'existence
	 * @param	v_bRechercheMinuscules	si \true, la recherche se fait sur la version en minuscules des noms
	 * 									d'interfaces déclarées. Si \c false (défaut), la recherche se fait sur les noms
	 * 									des interfaces tels qu'ils ont été enregistrés. Ce paramètre est utile en PHP 4
	 * 									si l'on veut passer en paramètre \p v_sInterface un nom de classe récupéré d'une
	 * 									fonction native PHP 4, car malheureusement, celles-ci retournent les noms de
	 * 									classes et méthodes connues entièrement en minuscules, et les noms tels qu'ils
	 * 									ont été déclarés, avec la casse originale, sont perdus
	 *
	 * @return	\c true si l'interface existe, \c false sinon
	 */
	function interfaceExiste($v_sInterface, $v_bRechercheMinuscules = FALSE)
	{
		if (!$v_bRechercheMinuscules)
		{
			return (isset($GLOBALS['_aaInterfaces'][$v_sInterface]));
		}
		else
		{
			$a = array_change_key_case($GLOBALS['_aaInterfaces'], CASE_LOWER);
			return (isset($a[$v_sInterface]));
		}
	}

	/**
	 * Indique qu'une classe doit implémenter une interface particulière. Si la classe ne dispose pas de toutes les
	 * méthodes de l'interface, une erreur sera générée. Si tout est ok, l'interface et la classe qui implémente sont
	 * enregistrées pour qu'on puisse par la suite vérifier plus rapidement que cette classe l'implémente bien  (voir
	 * #instanceDe())
	 *
	 * @param	v_sInterface			l'interface que la classe doit implémenter. Etant donné qu'en PHP 4, les
	 * 									interfaces n'existent pas, il s'agit en réalité d'une classe qui devra avoir
	 * 									été déclarée comme telle dans son fichier de définition, grâce à la méthode
	 * 									#defInterface()
	 * @param	v_sClasseQuiImplemente	nom de la classe dont on veut vérifier qu'elle implémente l'interface. Si
	 * 									\c null (défaut), c'est le nom du fichier d'où provient l'appel à la méthode
	 * 									qui sera utilisé comme nom de classe à vérifier. Cela implique que le fichier
	 * 									porte le même nom que la classe qu'il contient (extension non prise en compte)
	 *
	 * @warning	Au départ, je pensais placer des OO::implemente() devant la définition de la classe concernée (càd qui
	 * 			est censée implémenter une interface), dans son fichier source, mais ça pose problème (du moins selon
	 * 			mes tests en PHP 4), car malheureusement en adoptant cette méthode, au moment où OO::implémente() est
	 * 			appelée, la classe qui suit cet appel, dans le fichier source, n'a pas encore été lue par PHP, et donc
	 * 			ses méthodes n'existent pas (en fait il semble que ses méthodes sont connues, *sauf* dans le cas où
	 * 			cette classe en étend une autre) => il vaut mieux placer le OO::implemente() *après* la définition de
	 *			la classe qui implémente
	 */
	function implemente($v_sInterface, $v_sClasseQuiImplemente = NULL)
	{
		if (is_null($v_sClasseQuiImplemente))
			$v_sClasseQuiImplemente = OO::_retClasseAppelante();

		if (!OO::interfaceExiste($v_sInterface))
			Erreur::provoquer("L'interface $v_sInterface n'existe pas");

		// en réalité, ce ne sont pas *toutes* les méthodes présentes dans la classe "interface" qui doivent être
		// obligatoirement implémentées, mais bien ces méthodes, *moins* le(s) constructeur(s) de cette interface ET
		// le(s) constructeur(s) des éventuelles classes/interfaces parentes de celle-ci

		// on récupère les méthodes de l'interface à implémenter
		$asMethodesInterface = get_class_methods($v_sInterface);

		// dans les méthodes à ne pas implémenter, il y a le nom du constructeur en style PHP 5...
		$asMethodesAEliminer = array('__construct');
		// ...et les noms des constructeurs, en style PHP 4, de l'interface et des éventuels parents de celle-ci
		for ($sClasseParente = OO::_nomClasse($v_sInterface); !empty($sClasseParente);
		     $sClasseParente = get_parent_class($sClasseParente))
		{
			$asMethodesAEliminer[] = $sClasseParente;
		}

		$asMethodesInterfaceNettoyees = array_diff($asMethodesInterface, $asMethodesAEliminer);

		// vérification que la classe qui implémente dispose bien de toutes les méthodes requises de l'interface
		$asMethodesManquantes = array_diff($asMethodesInterfaceNettoyees,
		                                   get_class_methods($v_sClasseQuiImplemente));
		if (count($asMethodesManquantes))
		{
			Erreur::provoquer("$v_sClasseQuiImplemente: Implémentation incomplète de l'interface $v_sInterface. "
		                      ."Méthodes manquantes: ".implode(', ', $asMethodesManquantes));
		}
		// si c'est ok, on enregistre le fait que cette classe implémente l'interface
		else
		{
			OO::_defClasseQuiImplemente($v_sClasseQuiImplemente, $v_sInterface);
		}
	}

	/**
	 * Vérifie qu'un objet est une instance d'une classe X, ou d'une classe dérivée de X, ou que sa classe ou une de
	 * ses ancêtres implémente l'interface X
	 *
	 * @param	v_oObjet		l'objet à vérifier
	 * @param	v_sClasse		la classe ou interface dont X doit être instancié ou dérivé
	 *
	 * @return	\c true si \p v_sObjet est bien une instance de \p v_sClasse, ou d'une classe dérivée de \p v_sClasse,
	 * 			ou que sa classe ou une classe ancêtre implémente \p v_sClasse. Sinon \c false
	 */
	function instanceDe($v_oObjet, $v_sClasse)
	{
		if (!is_object($v_oObjet))
			Erreur::provoquer("$v_oObjet n'est pas un objet");

		// si la classe existe et que l'objet en est une instance, ou l'instance d'une classe dérivée => ok
		if (class_exists($v_sClasse))
		{
			$bExiste = TRUE;
		 	if (is_a($v_oObjet, $v_sClasse))
				return TRUE;
		}
		
		// si l'interface existe et que l'objet l'implémente
		if (OO::interfaceExiste($v_sClasse))
		{
			$bExiste = TRUE;
			if (OO::_estClasseQuiImplemente(get_class($v_oObjet), $v_sClasse))
				return TRUE;
		
		}
		
		// si le second paramètre n'était ni une classe, ni une interface existantes => erreur
		if (!$bExiste)
			Erreur::provoquer("La classe ou interface $v_sClasse n'existe pas");
		
		// si l'objet n'est ni une instance, ni une implémentation du second paramètre
		return FALSE;
	}

	/**
	 * Définit une classe PHP comme abstraite. Il s'agit juste d'une information, et la seule conséquence est que
	 * lorsqu'une classe disposera d'au moins une méthode appelant #abstraite(), si cette classe n'est pas définie
	 * comme abstraite grâce à #defClasseAbstraite(), une erreur sera générée. Je n'ai pas trouvé de moyen simple de
	 * pouvoir vérifier cela avant l'appel effectif d'une méthode OO::abstraite() en PHP 4
	 *
	 * @param	v_sClasseAbstraite	le nom de la classe qui doit être considérée comme abstraite. S'il \c null (par
	 * 								défaut), on tentera de déterminer le nom de la classe en fonction du fichier d'où
	 * 								provient l'appel, qui devra alors être un fichier qui définit la classe tout en
	 * 								portant le même nom (avec ou sans extension)
	 *
	 * @warning	Il est préférable, pour des raisons de cohérence, de placer le OO:defClasseAbstraite() après la
	 * 			définition de la classe PHP qu'on veut déclarer comme abstraite. Voir la remarque sur #implemente()
	 * 			(valable même si elle concerne les interfaces)
	 */
	function defClasseAbstraite($v_sClasseAbstraite = NULL)
	{
		if (is_null($v_sClasseAbstraite))
			$v_sClasseAbstraite = OO::_retClasseAppelante();

		// l'indice contient le nom de la classe telle qu'elle est déclarée, et la valeur le nom de la classe en
		// minuscules, de façon à faciliter la recherche en PHP 4 (ou les noms de classes sont retournés entièrement en
		// minuscules par les fonction natives)
		if (!OO::classeAbstraiteExiste($v_sClasseAbstraite))
			$GLOBALS['_asClassesAbstraites'][$v_sClasseAbstraite] = strtolower($v_sClasseAbstraite);
	}

	/**
	 * Vérifie l'existence d'une classe abstraite
	 *
	 * @param	v_sClasseAbstraite		le nom de la classe abstraite dont il faut vérifier l'existence
	 * @param	v_bRechercheMinuscules	si \true, la recherche se fait sur la version en minuscules des noms
	 * 									de classes abstraites déclarées. Si \c false (défaut), la recherche se fait sur
	 * 									les noms des classes abstraites tels qu'ils ont été enregistrés (voir remarque
	 * 									#interfaceExiste())
	 *
	 * @return	\c true si la classe abstraite existe, \c false sinon
	 */
	function classeAbstraiteExiste($v_sClasseAbstraite, $v_bRechercheMinuscules = FALSE)
	{
		if (!$v_bRechercheMinuscules)
			return (isset($GLOBALS['_asClassesAbstraites'][$v_sClasseAbstraite]));
		else
			return (in_array($v_sClasseAbstraite, $GLOBALS['_asClassesAbstraites']));
	}

	/**
	 * Placée dans une méthode d'une classe abstraite (ou une interface), provoquera volontairement une erreur si on
	 * tente d'appeler la méthode. Cela permet de s'assurer que les méthodes dans une classe abstraite (interface) ne
	 * seront jamais appelées. Si cette méthode est appelée par un constructeur d'une classe abstraite (interface), le
	 * message est adapté pour indiquer qu'il est impossible d'instancier ce type de classe. Si cette méthode
	 *
	 * exemple: function methodeClasseAbstraite() { OO::abstraite(); }
	 */
	function abstraite()
	{
		$bPhp4 = (phpversion() < 5);
		list( , $sAppelant) = debug_backtrace();

		// impossible de déclarer un méthode abstraite si on n'est pas une classe abstraite, ou une interface
		if (!OO::classeAbstraiteExiste($sAppelant['class'], $bPhp4)
		    && !OO::interfaceExiste($sAppelant['class'], $bPhp4))
		{
			Erreur::provoquer("La classe ".$sAppelant['class']." contient des méthodes abstraites mais n'est ni une "
			                 ."interface, ni une classe abstraite");
		}

		// si on a affaire à un constructeur, on provoque l'erreur en indiquant qu'on ne peut instancier une classe
		// abstraite ou une interface
		if ($sAppelant['function'] == $sAppelant['class'] || $sAppelant == '__construct')
		{
			if (OO::interfaceExiste($sAppelant['class'], $bPhp4))
				Erreur::provoquer("Instanciation impossible: ".$sAppelant['class']." est une interface");
			else
				Erreur::provoquer("Instanciation impossible: ".$sAppelant['class']." est une classe abstraite");
		}
		// sinon, c'est une méthode abstraite classique, mais on ne peut pas l'appeler => erreur également
		else
		{
			if (OO::interfaceExiste($sAppelant['class'], $bPhp4))
				Erreur::provoquer("Appel impossible: méthode d'interface");
			else
				Erreur::provoquer("Appel impossible: méthode abstraite");
		}
	}

	/**
	 * Vérifie que la méthode qui l'appelle a elle-même été appelée statiquement (avec l'opérateur '::'), et provoque
	 * une erreur dans le cas contraire. Cela permet de forcer des méthodes à être statiques, étant donné que PHP 4 ne
	 * supporte pas le mot-clé qui forcerait ce type d'appel
	 */
	function statique()
	{
		list( , $asAppelant) = debug_backtrace();
		if ($asAppelant['type'] != '::')
			Erreur::provoquer("Appel impossible à partir d'une instance: méthode statique");
	}

	/**
	 * Sauvegarde le fait qu'une classe implémente une interface
	 *
	 * @param	v_sClasse		le nom de la classe qui implémente l'interface
	 * @param	v_sInterface	le nom de l'interface implémentée
	 *
	 * @note	 Méthode privée
	 */
	function _defClasseQuiImplemente($v_sClasse, $v_sInterface)
	{
		$GLOBALS['_aaInterfaces'][$v_sInterface][$v_sClasse] = strtolower($v_sClasse);
	}

	/**
	 * Vérifie qu'un classe, ou une de ses classes parentes, implémente une interface donnée
	 *
	 * @param	v_sClasse		le nom de la classe
	 * @param	v_sInterface	le nom de l'interface
	 *
	 * @return	\c true si la classe ou une de ses classes parentes implémente l'interface, \c false sinon
	 *
	 * @note	 Méthode privée
	 */
	function _estClasseQuiImplemente($v_sClasse, $v_sInterface)
	{
		if (phpversion() < 5)
		{
			for ($sClasseParente = $v_sClasse; !empty($sClasseParente); 
			     $sClasseParente = get_parent_class($sClasseParente))
			{
				if (in_array($sClasseParente, $GLOBALS['_aaInterfaces'][$v_sInterface]))
					return TRUE;
			}
		}
		else
		{
			for ($sClasseParente = $v_sClasse; !empty($sClasseParente); 
			     $sClasseParente = get_parent_class($sClasseParente))
			{
				if (isset($GLOBALS['_aaInterfaces'][$v_sInterface][$sClasseParente]))
					return TRUE;
			}
		}
		
		return FALSE;
	}

	/**
	 * Retourne le nom de la classe dans laquelle un appel à OO a été fait, en se basant sur le nom du fichier dans
	 * lequel a été effectué l'appel. Pour que cette méthode fonctionne :
	 *
	 * - elle ne doit pas être appelée directement en dehors de OO
	 * - le fichier duquel l'appel est effectué est censé définir une classe
	 * - il doit s'agir d'une seule classe, et le fichier doit porter le même nom que la classe (avec toutefois une
	 *   éventuelle extension)
	 *
	 * @return	le nom de la classe au sein de laquelle a été appelée la dernière méthode de OO
	 *
	 * @note	 Méthode privée
	 */
	function _retClasseAppelante()
	{
		// cette méthode étant appelée d'abord par une autre méthode de OO, il faut prendre la seconde entrée dans les
		// "traces" (indice 1), pas la première (indice 0)
		$asTraces = debug_backtrace();
		return preg_replace('/\..+$/', '', basename($asTraces[1]['file']));
	}

	/**
	 * Retourne le nom d'une classe, converti selon le type de valeurs de retour des fonctions de gestion de
	 * classes/méthodes/fonctions du PHP courant (PHP 4 retourne tous les noms en minuscules, alors que PHP 5 les
	 * retourne tels qu'ils ont été déclarés)
	 *
	 * @param	v_sClasse	le nom de classe (ou méthode) à convertir
	 *
	 * @return	le nom de la classe, converti selon la version de PHP courante
	 */
	function _nomClasse($v_sClasse)
	{
		if (phpversion() < 5)
			return strtolower($v_sClasse);
		else
			return $v_sClasse;
	}
}

?>