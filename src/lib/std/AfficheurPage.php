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
 * @file	AfficheurPage.php
 */
 
require_once(dirname(__FILE__).'/OO.php');

/**
 * Contrôleur sommaire pour le système de templates. Prend en charge les erreurs
 * à afficher, l'utilisation d'un template par défaut, la mise à disposition des
 * variables GET/POST/SESSION, et surtout les quelques méthodes de base et leur
 * ordre d'appel (récupération et validation des données, exécution des actions 
 * demandées, affichage des erreurs et/ou des autres parties de la page)
 */
class AfficheurPage
{
	var $aDonneesUrl;		///< Tableau contenant les variables passées en URL (GET)
	var $aDonneesForm;		///< Tableau contenant les variables passées par formulaire POST
	var $aDonneesPersist;	///< Tableau contenant les variables persistantes, ici les variables de SESSION
	
	var $fichierTpl;		///< Nom du fichier template à utiliser pour afficher la page
	var $tpl;				///< Objet Template utilisé pour afficher la page
	
	var $aErreursPossibles = array();	///< Si des blocs [erreur...+/-] sont présents dans la page HTML, ils sont détectés et enregistrés (voir #detecterErreursPossibles())
	var $aErreurs = array();			///< Tableau qui contient les erreurs non-fatales déclarées pendant l'affichage de la page (voir #declarerErreur())
	var $aErreursFatales = array();		///< Tableau qui contient les erreurs fatales déclarées pendant l'affichage de la page
	
	var $sAction;			///< Chaîne destinée à recevoir le nom de "l'action" voulue pour la page (généralement définie dans #recupererDonnees() d'une classe fille)
	
	/**
	 * Lance l'affichage de la page
	 * 
	 * @param	fichierTpl	le chemin du fichier template à utiliser pour 
	 * 			afficher la page. Par défaut, il s'agit de 
	 * 			<nom-de-la-classe>.tpl (la classe peut être une classe fille)
	 */
	function demarrer($fichierTpl = NULL)
	{
		// récupération des données GET/POST/SESSION
		$this->aDonneesUrl     =  $_GET;
		$this->aDonneesForm    =  $_POST;
		if (is_null(session_id()) || session_id() == '') session_start();
		$this->aDonneesPersist =& $_SESSION;
		
		// les différentes étapes importantes pour l'affichage de la page sont appelées
		$this->recupererDonnees();
		$this->validerDonnees();
		if ($this->retNbErreursFatales() == 0)
			$this->gererActions();
		$this->defTpl($fichierTpl);
		$this->detecterErreursPossibles();
		$this->afficher();
	}
	
	/**
	 * Récupère les données nécessaires à l'exécution de la page. Dans une 
	 * classe fille, on peut par exemple déclarer les différentes données 
	 * utilisées pour le traitement et l'affichage en tant que propriétés de la 
	 * classe, et les récupérer dans la méthode #recupererDonnees()
	 * 
	 * @note	méthode à redéfinir dans les classes filles
	 */
	function recupererDonnees()
	{
		OO::abstraite();
	}
	
	/**
	 * Valide les données récupérées auparavant par la méthode 
	 * #recupererDonnees(). C'est ici qu'on ajuste leur valeur ou qu'on 
	 * déclenche éventuellement une méthode #declarerErreur()
	 * 
	 * @note	méthode à redéfinir dans les classes filles
	 */
	function validerDonnees()
	{
		OO::abstraite();
	}
	
	/**
	 * Gère les actions demandées pour la page. Généralement un switch/case sur 
	 * \c $this->sAction. N'est pas appelée si une erreur fatale a été déclarée 
	 * pendant la récupération ou la validation des données. On peut encore y 
	 * déclarer des erreurs fatales, car elles déterminent également si la 
	 * partie principale de la page sera affichée
	 * 
	 * @note	méthode à redéfinir dans les classes filles
	 */
	function gererActions()
	{
		OO::abstraite();
	}
	
	/**
	 * Assigne un fichier template pour l'affichage de la page
	 * 
	 * @param	fichierTpl	le nom du fichier template à utiliser. S'il n'est 
	 * 						pas fourni, <nom-de-la-classe>.tpl est utilisé
	 */
	function defTpl($fichierTpl)
	{
		if (empty($fichierTpl))
		{
			// trouver le .html du même nom que le fichier qui a instancié l'objet
			//$asTraces = debug_backtrace();
			//$this->fichierTpl = preg_replace('/\.[^.]*$/', '.html', basename($asTraces[1]['file']));
			
			// trouver le .tpl qui porte le même nom que la classe (fille) à afficher
			$this->fichierTpl = get_class($this).'.tpl';
			
			// en PHP 4, le nom de la classe est tjs renvoyé en minuscules => si la classe, et donc son fichier,
			// comportaient des majuscules, on peut trouver leur nom réel si le fichier existe vraiment (seulement utile
			// pour les systèmes non-Windows, où le respect de la casse pour les fichiers est important)
			if (phpversion() < 5)
			{
				$fichiers = @glob('*.tpl');
				if (is_array($fichiers))
				{
					$index = array_search($this->fichierTpl, array_map('strtolower', $fichiers));
					if (!empty($index))
						$this->fichierTpl = $fichiers[$index];
				}
			}
		}
		else
		{
			$this->fichierTpl = $fichierTpl;
		}
			
		$this->tpl = new Template($this->fichierTpl);
	}
	
	/**
	 * Redirige l'utilisateur vers une autre adresse
	 * 
	 * @param	sUrl	l'adresse vers laquelle on redirige
	 */
	function rediriger($sUrl)
	{
		header("Location: $sUrl\n");
	}
	
	/**
	 * Affiche les éventuelles erreurs, et s'il n'y a pas d'erreurs fatales, 
	 * affiche les autres parties de la page
	 */
	function afficher()
	{
		$this->afficherErreurs();
		if ($this->retNbErreursFatales() == 0)
			$this->afficherParties();
		
		$this->tpl->afficher();
	}
	
	/**
	 * Affiche les messages d'erreur qui ont été déclarés pendant le traitement
	 * de la page. Ils peuvent soit être présents au préalable dans le HTML, 
	 * sous forme de blocs [erreur...] contenant le message voulu, soit sous la 
	 * forme d'un seul bloc nommé [erreurs], dans lequel seront insérés les
	 * messages déclarés
	 * 
	 * @see #declarerErreur()
	 */
	function afficherErreurs()
	{
		$tplErreurs = new TPL_Block('erreurs', $this->tpl);
		
		// afficher les blocs correspondant aux erreurs qui se sont produites
		foreach ($this->aErreurs as $sNomErreur=>$sTexte)
		{
			if (($cle = array_search($sNomErreur, $this->aErreursPossibles)) !== FALSE)
			{
				if (empty($sTexte))
					$this->tpl->activerBloc($sNomErreur);
				unset($this->aErreursPossibles[$cle]);
			}
			else
			{
				if (!empty($sTexte))
					$tplErreurs->ajouter("<p>$sTexte</p>");
			}
		}
		
		foreach ($this->aErreursFatales as $sNomErreur=>$sTexte)
		{
			if (($cle = array_search($sNomErreur, $this->aErreursPossibles)) !== FALSE)
			{
				if (empty($sTexte))
					$this->tpl->activerBloc($sNomErreur);
				unset($this->aErreursPossibles[$cle]);
			}
			else
			{
				if (!empty($sTexte))
					$tplErreurs->ajouter("<p>$sTexte</p>");
			}
		}
		
		// effacer les blocs d'erreurs se trouvant sur la page, pour celles qui ne se sont pas produites
		foreach ($this->aErreursPossibles as $sNomErreur)
			$this->tpl->desactiverBloc($sNomErreur);
		
		$tplErreurs->afficher();
		
		// si pas d'erreur(s) fatale(s), on affiche le bloc "pasErreur", sinon on l'efface => pour bien faire, les blocs
		// représentant les erreurs fatales devraient se trouver en dehors du bloc "pasErreur", sinon on ne verra pas 
		// leur texte
		$this->tpl->activerBloc('pasErreur', count($this->aErreursFatales) == 0);
	}
	
	/**
	 * Affiche les différentes parties de la page non relatives aux messages 
	 * d'erreur
	 * 
	 * @note	méthode à redéfinir dans les classes filles
	 */
	function afficherParties()
	{
		OO::abstraite();
	}
	
	/**
	 * Détecte les blocs nommés [erreur...] dans le HTML du template (réservés 
	 * pour les messages d'erreur), de façon à les enlever au moment l'affichage
	 * si l'erreur qu'ils représentent n'a pas été déclenchée (déclarée)
	 */
	function detecterErreursPossibles()
	{
		preg_match_all('/\[(erreur[^]]+)\+\].*\[\1\-\]/s', $this->tpl->data, $aListeErreurs);
		if (count($aListeErreurs[0]) > 0)
			$this->aErreursPossibles = $aListeErreurs[1];
	}
	
	/**
	 * Déclare qu'une erreur s'est produite. Permet d'afficher des messages 
	 * d'erreurs à la fin du traitement de la page, lors de l'affichage
	 * 
	 * @param	sNomErreur	le nom de l'erreur déclarée. Si on omet \p v_sTexte,
	 * 						ce paramètre désigne le nom du bloc dans le template
	 * 						qui contient le message pour cette erreur, et qui 
	 * 						sera donc affiché
	 * @param	bFatale		indique si l'erreur est fatale, càd si elle 
	 * 						empêchera l'exécution de l'action demandée pour la 
	 * 						page et l'affichage des parties principales de la 
	 * 						page
	 * @param	v_sTexte	le message de l'erreur déclarée. S'il n'est pas 
	 * 						spécifié, le nom de l'erreur est considéré comme le 
	 * 						nom du bloc de template qui contient le message à 
	 * 						afficher. S'il est spécifié, ce texte sera affiché 
	 * 						dans le bloc [erreurs] du template en tant que §
	 */
	function declarerErreur($sNomErreur, $bFatale = FALSE, $v_sTexte = '')
	{
		if ($bFatale)
			$this->aErreursFatales[$sNomErreur] = $v_sTexte;
		else
			$this->aErreurs[$sNomErreur] = $v_sTexte;
	}
	
	function declarerErreurAction($sNomErreur, $bFatale = FALSE, $v_sTexte = '')
	{
		$this->sAction = '';
		$this->declarerErreur($sNomErreur, $bFatale, $v_sTexte);
	}
	
	/**
	 * Vérifie qu'une erreur est déjà déclarée
	 * 
	 * @param	sNomErreur	le nom de l'erreur
	 * 
	 * @return	\c true si l'erreur est déclarée, \c false sinon
	 */
	function erreurDeclaree($sNomErreur)
	{
		return (isset($this->aErreurs[$sNomErreur]) || isset($this->aErreursFatales[$sNomErreur]));
	}
	
	/**
	 * Retourne le nombre total d'erreurs déclarées, fatales ET non fatales
	 */
	function retNbErreurs()
	{
		return count($this->aErreurs) + count($this->aErreursFatales);
	}
	
	/**
	 * Retourne le nombre d'erreurs non fatales déclarées
	 */
	function retNbErreursNonFatales()
	{
		return count($this->aErreurs);
	}
	
	/**
	 * Retourne le nombre d'erreurs fatales déclarées
	 */
	function retNbErreursFatales()
	{
		return count($this->aErreursFatales);
	}
}

// cette classe est abstraite car il faut redéfinir les méthodes de base
OO::defClasseAbstraite();
?>
