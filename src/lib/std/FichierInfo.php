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
 * @file	FichierInfo.php
 */

require_once(dirname(__FILE__).'/Erreur.php');
require_once(dirname(__FILE__).'/IterateurRecursif.php');
require_once(dirname(__FILE__).'/IterateurDossier.php');

/** Constantes - Séparateurs de fichiers/dossiers */
//@{
define('FICHIER_SEPARATEUR_UNIX'   , '/') ;                ///< Le séparateur de fichiers/dossiers par défaut sous Unix         @enum FICHIER_SEPARATEUR_UNIX
define('FICHIER_SEPARATEUR_WINDOWS', '\\');                ///< Le séparateur de fichiers/dossiers par défaut sous Windows      @enum FICHIER_SEPARATEUR_WINDOWS
define('FICHIER_SEPARATEUR_DEFAUT' , DIRECTORY_SEPARATOR); ///< Le séparateur de fichiers/dossiers par défaut pour l'OS courant @enum FICHIER_SEPARATEUR_DEFAUT
//@}

/**
 * Classe d'information sur les fichiers/dossiers du système de fichier: interrogations des caractéristiques, accès,
 * etc.
 *
 * @note	Un objet de cette classe ne doit pas obligatoirement représenter un fichier existant, il s'agit seulement
 * 			d'un chemin, qui peut ensuite être vérifié (il y a une exception : FichierInfo#retCheminReel())
 *
 * @note	Cette classe ne gère PAS les lectures/écritures DANS un fichier
 *
 * @note	Cette classe utilise des fonctions str...(), qui ne sont par défaut pas compatibles multibyte, mais je pense
 * 			que pour les noms de fichiers/dossiers récupérés par l'OS, ça ne devrait pas poser de problèmes
 *
 * @todo	Il faudrait créer des méthodes pour:
 * 			  - transformer un chemin relatif en absolu (et vice versa) sans obligation que le chemin fourni existe 
 * 			    réellement quand c'est possible;
 * 			  - déterminer la partie "racine" d'un chemin;
 * 			  - etc
 */
class FichierInfo
{
	var $sChemin;                           ///< Le chemin représenté par l'objet
	var $sSeparateur = DIRECTORY_SEPARATOR; ///< Le séparateur de dossiers à utiliser pour ce chemin (les chemins retournés utiliseront celui-là)

	/**
	 * Constructeur.
	 *
	 * @param	v_sChemin	le chemin qui sera représenté par l'objet. Ce chemin peut être absolu ou relatif, et n'est
	 * 						pas obligé de représenter un fichier/dossier existant ou lisible dans un premier temps.
	 */
 	function FichierInfo($v_sChemin)
 	{
 		$this->defChemin($v_sChemin);
 	}

	/**
	 * Initialise un chemin dans l'objet
	 *
	 * @param	v_sChemin	le chemin qui sera représenté par l'objet courant. Le chemin, même s'il contient un type de 
	 * 						séparateur qui n'est pas celui par défaut de l'OS courant (par exemple un chemin contenant 
	 * 						des séparateurs '/' sous Windows), utilisera par défaut ce séparateur pour ses opérations 
	 * 						internes et dans les valeurs de retour des méthodes publiques de cette classe
	 */
	function defChemin($v_sChemin)
	{
		$this->sSeparateur = $this->detecterSeparateur($v_sChemin);
		$this->sChemin = $this->enleverSeparateursDeFin($v_sChemin);
	}

	/**
	 * Retourne le chemin qui est représenté par l'objet courant
	 *
	 * @return	le chemin représenté par l'objet courant
	 */
 	function retChemin()
 	{
 		return $this->sChemin;
 	}
	
	/**
	 * Retourne le chemin réel représenté par l'objet, càd sa forme absolue et canonique (séparateurs corrects pour
	 * l'OS et chemins '.' et '..' résolus)
	 *
	 * @return	le chemin absolu représenté par l'objet courant
	 *
	 * @warning	Le chemin doit représenter un dossier/fichier existant pour que cette méthode fonctionne
	 */
 	function retCheminReel()
 	{
 		return $this->convertirSeparateurs(realpath($this->sChemin));
 	}

	/**
	 * Retourne le nom du dossier parent extrait du chemin représenté par l'objet courant
	 *
	 * @param	v_bPasDePoint	si \c true (défaut), la méthode ne retournera pas le dossier sous forme de point ('.') 
	 * 							lorsque celui-ci représente un "dossier courant", elle retournera une chaîne vide
	 *
	 * @return	le nom du dossier parent dans le chemin représenté par l'objet courant
	 */
	function retDossier($v_bPasDePoint = TRUE)
 	{
 		$sDossier = $this->convertirSeparateurs(dirname($this->sChemin));
 		
 		if ($v_bPasDePoint && $sDossier === '.')
 			return '';
 		else
 			return $sDossier;
 	}

 	/**
 	 * Retourne le nom du fichier/dossier contenu dans le chemin représenté par l'objet courant, y compris son
 	 * éventuelle extension
 	 *
 	 * @return	le nom du fichier/dossier dans le chemin représenté par l'objet courant
 	 */
	function retNom()
 	{
 		return $this->convertirSeparateurs(basename($this->sChemin));
 	}
	
 	/**
 	 * Retourne l'extension du fichier/dossier contenue dans le chemin représenté par l'objet courant
 	 *
 	 * @return	le nom de l'extension du fichier/dossier représenté par l'objet courant, s'il y en a une (sinon chaîne
 	 * 			vide)
 	 */
 	function retExtension()
 	{
		$asInfosChemin = pathinfo($this->sChemin);
		return $asInfosChemin['extension'];
 	}

 	/**
 	 * Retourne le nom du fichier/dossier contenu dans le chemin représenté par l'objet courant, SANS son éventuelle
 	 * extension
 	 *
 	 * @return	le nom du fichier/dossier dans le chemin représenté par l'objet courant, sans extension
 	 */
 	function retNomSansExtension()
 	{
 		$iTailleExt = strlen($this->retExtension());
 		$sNom = $this->retNom();

		if ($iTailleExt > 0)
 			return substr($sNom, 0, strlen($sNom) - ($iTailleExt + 1));
 		else
 			return $sNom;
 	}
 	
 	/**
 	 * @return	la taille en octets du fichier représenté par l'objet courant
 	 */
 	function retTaille()
 	{
 		if (!$this->estFichier())
 			Erreur::provoquer($this->retChemin().": récupération de la taille impossible");

 		return @filesize($this->retChemin());
 	}


	/**
	 * Indique si le fichier/dossier représenté par le chemin courant existe bien
	 *
	 * @return	\c true si le fichier/dossier existe, \c false sinon
	 */
 	function existe()
 	{
 		return file_exists($this->sChemin);
 	}

	/**
	 * Indique si l'objet du système de fichier représenté par le chemin courant est bien de type "fichier"
	 *
	 * @return	\c true si le l'objet du système de fichiers représenté par l'objet courant est un fichier, \c false
	 * 			sinon
	 */
 	function estFichier()
 	{
 		return is_file($this->sChemin);
 	}

	/**
	 * Indique si l'objet du système de fichier représenté par le chemin courant est bien de type "dossier"
	 *
	 * @return	\c true si le l'objet du système de fichiers représenté par l'objet courant est un dossier, \c false
	 * 			sinon
	 */
 	function estDossier()
 	{
 		return is_dir($this->sChemin);
 	}

	/**
	 * Indique si le fichier/dossier représenté par le chemin courant est accessible en lecture
	 *
	 * @return	\c true si le le fichier/dossier représenté par l'objet est accessible en lecture, \c false
	 * 			sinon
	 */
 	function estLisible()
 	{
 		return is_readable($this->sChemin);
 	}

	/**
	 * Indique si le fichier/dossier représenté par le chemin courant est accessible en écriture
	 *
	 * @return	\c true si le le fichier/dossier représenté par l'objet est accessible en écriture, \c false
	 * 			sinon
	 */
 	function estModifiable()
 	{
 		return is_writable($this->sChemin);
 	}

	function estDescendantDe($v_sDossierSource)
	{
		$oDossierSource = new FichierInfo($v_sDossierSource);
		
		if ($this->retCheminReel() === FALSE || $oDossierSource->retCheminReel() === FALSE)
			return FALSE;
		
		$oThis = new FichierInfo($this->retCheminReel());
		$sCheminReduit = $oThis->reduireChemin($oDossierSource->retCheminReel());
				
		if (!empty($sCheminReduit) && $sCheminReduit != $oThis->retChemin())
			return TRUE;
		else
			return FALSE;
	}

	/**
	 * Définit un séparateur par défaut pour tous les objets de cette classe qui seront créés. Si cette méthode n'est 
	 * jamais appelée, le séparateur par défaut est celui de l'OS. Cette méthode retourne l'actuel séparateur par défaut 
	 * si elle est appelée sans paramètre
	 * 
	 * @param	v_sSeparateur	le séparateur par défaut à utiliser pour tous les objets de la classe. Si \c null 
	 * 							(défaut), alors la méthode ne définit pas le séparateur par défaut, mais le retourne
	 * 
	 * @return	si \p v_sSeparateur est \c null, le séparateur par défaut actuel
	 */
	function separateurParDefaut($v_sSeparateur = NULL)
	{
		static $s_sSeparateur = NULL;
		
		if (is_null($v_sSeparateur))
		{
			if (is_null($s_sSeparateur))
				return DIRECTORY_SEPARATOR;
			else
				return $s_sSeparateur;
		}
		else
		{
			$s_sSeparateur = $v_sSeparateur;			
		}
	}
	
	/**
	 * Définit un séparateur de chemin pour l'objet
	 * 
	 * @param	v_sSeparateur			le séparateur qu'on veut "reconnu" comme tel pour les chemins stockés dans 
	 * 									l'objet
	 * @param	v_bRemplacerExistant	si \c true (défaut), le chemin actuellement stocké est converti pour utiliser 
	 * 									le nouveau séparateur défini
	 */	
	function defSeparateur($v_sSeparateur, $v_bRemplacerExistant = TRUE)
	{
		if ($v_sSeparateur != FICHIER_SEPARATEUR_UNIX && $v_sSeparateur != FICHIER_SEPARATEUR_WINDOWS)
			Erreur::provoquer("Séparateur de fichiers/dossiers non reconnu : $v_sSeparateur");
		
		if ($v_bRemplacerExistant)
			$this->sChemin = $this->convertirSeparateurs($this->sChemin, $this->sSeparateur, $v_sSeparateur);
		
		$this->sSeparateur = $v_sSeparateur;
	}
	
	/**
	 * @return	le séparateur défini pour l'objet courant
	 */
	function retSeparateur()
	{
		return $this->sSeparateur;
	}

	/**
	 * Détecte et retourne le séparateur utilisé dans un chemin
	 * 
	 * @param	v_sChemin	le chemin dont il faut détecter les séparateur
	 * 
	 * @return	le séparateur utilisé dans le chemin, soit FICHIER_SEPARATEUR_UNIX ou FICHIER_SEPARATEUR_WINDOWS. Dans 
	 * 			le cas où le chemin passé est relatif et ne contient aucun séparateur reconnu, le séparateur par défaut 
	 * 			est retourné. Pour connaître le séparateur par défaut, voir #separateurParDefaut()
	 */
 	function detecterSeparateur($v_sChemin)
 	{
 		if (strpos($v_sChemin, FICHIER_SEPARATEUR_UNIX) !== FALSE)
 			return FICHIER_SEPARATEUR_UNIX;
 		else if (strpos($v_sChemin, FICHIER_SEPARATEUR_WINDOWS) !== FALSE)
 			return FICHIER_SEPARATEUR_WINDOWS;
 		else
 			return $this->separateurParDefaut();
 	}

	/**
	 * Convertit, dans un chemin, les séparateurs de fichier/dossiers par défaut de l'OS courant, en séparateurs exigés 
	 * par l'attribut \c sSeparateur de l'objet (détecté par #defChemin() ou modifié par #defSeparateur())
	 * 
	 * @param	v_sChemin			le chemin dans lequel il faut convertir les séparateurs
	 * @param	v_sSeparateurSrc	le séparateur à remplacer. Par défaut, il s'agit du séparateur standard de l'OS
	 * @param	v_sSeparateurDst	le séparateur qui doit remplacer celui spécifié par le paramètre 
	 * 								\c v_sSeparateurSrc. Par défaut, il s'agit de l'attribut \c sSeparateur défini pour 
	 * 								l'objet courant 
	 */
	function convertirSeparateurs($v_sChemin, $v_sSeparateurSrc = DIRECTORY_SEPARATOR, $v_sSeparateurDst = NULL)
	{
		// impossible d'utiliser une expression comme valeur par défaut pour le paramètre v_sSeparateurDst => il faut 
		// donner cette valeur par défaut en premier lieu
		if (is_null($v_sSeparateurDst))
			$v_sSeparateurDst = $this->sSeparateur;
		
		// erreur si l'un des deux séparateurs spécifiés n'est pas reconnu
		if ($v_sSeparateurSrc != FICHIER_SEPARATEUR_UNIX && $v_sSeparateurSrc != FICHIER_SEPARATEUR_WINDOWS)
			Erreur::provoquer("Séparateur source non reconnu : $v_sSeparateurSrc");
		if ($v_sSeparateurDst != FICHIER_SEPARATEUR_UNIX && $v_sSeparateurDst != FICHIER_SEPARATEUR_WINDOWS)
			Erreur::provoquer("Séparateur destination non reconnu : $v_sSeparateurDst");
		
		// si les séparateur source et destination désirés sont identique, pas la peine de tenter le remplacement
		if ($v_sSeparateurSrc == $v_sSeparateurDst)
			return $v_sChemin;
		else
			return str_replace($v_sSeparateurSrc, $v_sSeparateurDst, $v_sChemin);
	}
	
 	/**
 	 * Enlève les séparateurs de dossier qui se trouveraient au début d'un chemin
 	 *
 	 * @param	v_sChemin	le chemin à "nettoyer"
 	 *
 	 * @return	le chemin passé en paramètre, nettoyé d'éventuels séparateurs initiaux
 	 */
	function enleverSeparateursDeDebut($v_sChemin)
 	{
 		return ltrim($v_sChemin, $this->sSeparateur);
 	}
	
 	/**
 	 * Enlève les séparateurs de dossier qui se trouveraient à la fin d'un chemin
 	 *
 	 * @param	v_sChemin	le chemin à "nettoyer"
 	 *
 	 * @return	le chemin passé en paramètre, nettoyé d'éventuels séparateurs finaux
 	 */
 	function enleverSeparateursDeFin($v_sChemin)
 	{
 		return rtrim($v_sChemin, $this->sSeparateur);
 	}
 	
 	/**
	 * Crée et retourne un chemin construit sur base du chemin représenté par l'objet, en lui ajoutant de nouvelles
	 * composantes
	 *
	 * @param	v_sSupplement	la chaîne représentant les composantes à ajouter au chemin courant
	 * @param	v_bRemplacer	indique si le chemin formé, en plus d'être retourné, doit également remplacer le chemin  
	 * 							actuellement enregistré dans l'objet
	 *
	 * @return	un nouveau chemin, formé à partir de celui représenté dans l'objet + les composantes passées en
	 * 			paramètre
	 */
 	function formerChemin($v_sSupplement, $v_bRemplacer = FALSE)
 	{
 		if (!empty($v_sSupplement))
 		{
 			$sNouveauChemin = $this->enleverSeparateursDeFin($this->sChemin)
 			                . $this->sSeparateur
 			                . $this->enleverSeparateursDeDebut($v_sSupplement);
 			
 			$sNouveauChemin = $this->enleverSeparateursDeFin($sNouveauChemin);
 			
 			if ($v_bRemplacer)
 				$this->defChemin($sNouveauChemin);
 			
 			return $sNouveauChemin;
 		}
 		else
 		{
 			return $this->sChemin;
 		}
 	}
 	
 	/**
 	 * Enlève le début du chemin représenté par l'objet, si ce début correspond à une chaîne donnée
 	 * 
 	 * @param	v_sPartieAEnlever	la partie qu'on veut enlever du chemin, et qui doit se trouver au début de celui-ci
	 * @param	v_bRemplacer		indique si le chemin réduit, en plus d'être retourné, doit également remplacer le 
	 * 								chemin actuellement enregistré dans l'objet
	 *
	 * @return	le nouveau chemin, dont la partie initiale aura été supprimée si elle correspondait au paramètre 
	 * 			\p v_sPartieAEnlever 
 	 */
	function reduireChemin($v_sPartieAEnlever, $v_bRemplacer = FALSE)
 	{
 		if (strpos($this->sChemin, $v_sPartieAEnlever) === 0)
 		{
 			$sNouveauChemin = substr($this->sChemin, strlen($v_sPartieAEnlever));
 			$sNouveauChemin = $this->enleverSeparateursDeDebut($sNouveauChemin);
 			
 			if ($v_bRemplacer)
 				$this->defChemin($sNouveauChemin);
 			
 			return $sNouveauChemin;
 		}
 		else
 		{
 			return $this->sChemin;
 		}
 	}
 	
	/**
	 * Indique si le chemin actuellement représenté par l'objet contient explicitement un dossier, ou s'il s'agit d'un 
	 * simple nom de fichier
	 * 
	 * @return	\c true si le chemin contient une référence à un dossier (par ex "/monDossier/monFichier.ext", ou même 
	 * 			"./monFichier.ext" et "../monFichier.ext"), \c false sinon (par ex "monFichier.ext")
	 * 
	 * @note	Je me sers de cette méthode pour différencier les chemins "./monFichier.ext" et "monFichier.ext", qui en
	 * 			temps normal sont "égaux" et représentent tous les deux "./monFichier.ext", mais qui dans certaines 
	 * 			circonstances devraient pouvoir être différenciés pour modifier le comportement d'une méthode en 
	 * 			fonction du type de chemin utilisé (par exemple dans #renommer(), où le fichier sera toujours renommé 
	 * 			dans son dossier d'origine si aucun dossier n'est spécifié explicitement dans le chemin de destination, 
	 * 			mais où il sera *déplacé dans le dossier courant* si un "./" est spécifié devant le nom dans le chemin 
	 * 			de destination) 
	 */
	function cheminContientDossier()
	{
		return ($this->retChemin() != $this->retNom());
	}
 	
 	/**
 	 * Crée le dossier représenté dans l'objet courant
 	 * 
 	 * @param	v_sCreerEnfant	le nom du dossier à créer *dans* le dossier actuellement représenté par l'objet. 
 	 * 							Si \c null, c'est le chemin actuellement représenté par l'objet qui est considéré comme
 	 * 							le dossier à créer
 	 * @param	v_bCreerInterm	si \c true, crée également les dossiers parents (intermédiaires) si ceux-ci n'existent 
 	 * 							pas
 	 * @param	v_iMode			le mode utilisé pour créer le dossier (permissions sur les système Unix, ignoré sous 
 	 * 							Windows)
 	 * 
 	 * @return	\c true si l'opération s'est bien déroulée, \c false dans le cas contraire
 	 */
 	function creerDossier($v_sCreerEnfant = NULL, $v_bCreerInterm = FALSE, $v_iMode = 0777)
 	{
		if (!empty($v_sCreerEnfant))
			$sDossierACreer = $this->formerChemin($v_sCreerEnfant);
		else
			$sDossierACreer = $this->retChemin();

		if ($v_bCreerInterm)
		{
			$r = FALSE;
			$asDossiersInterm = explode($this->sSeparateur, $sDossierACreer);
			
			if (count($asDossiersInterm))
			{
				$oDossierInterm = new FichierInfo(array_shift($asDossiersInterm));
				$oDossierInterm->defSeparateur($this->sSeparateur);
				
				foreach ($asDossiersInterm as $sDossierInterm)
				{
					$oDossierInterm->formerChemin($sDossierInterm, TRUE);
					if (!$oDossierInterm->existe())
						$r = $oDossierInterm->creerDossier() || $r;
				}
				
				return $r;
			}
		}
		else
		{
			// sera différent selon la valeur du 1er paramètre
			$oDossierACreer = new FichierInfo($sDossierACreer);
			
	 		if (!is_dir($oDossierACreer->retDossier()))
	 		{
	 			Erreur::provoquer($oDossierACreer->retNom().": création dans ".$this->retDossier()." impossible."
	 			                 ." La cible n'est pas un dossier");
	 			return FALSE;
	 		}
	 		
	 		if (!is_writable($oDossierACreer->retDossier()))
	 		{
	 			Erreur::provoquer($oDossierACreer->retNom().": création dans ".$this->retDossier()." impossible."
	 			                 ." Accès en écriture interdit");
	 			return FALSE;
	 		}
	 		
	 		return @mkdir($sDossierACreer, $v_iMode);			
		}
 	}
 	
 	/**
 	 * Supprime le dossier représenté par l'objet courant
 	 * 
 	 * @param	v_bRecursif si \c true, indique qu'il faut également effacer les fichiers et dossiers présents dans le 
 	 * 						dossier de base. Si \c false, le dossier sera supprimé uniquement s'il est vide
 	 * 
 	 * @return	\c true si l'opération s'est bien déroulée, \c false dans le cas contraire
 	 */
 	function supprimerDossier($v_bRecursif = FALSE)
 	{
 		if (!$this->estDossier())
 		{
 			Erreur::provoquer($this->retChemin().": suppression impossible. N'est pas un dossier");
 			return FALSE;
 		}
 		
 		$r = FALSE;
		
 		// si on a demandé l'effacement de tout le contenu du dossier
 		if ($v_bRecursif)
 		{
 			$itr = new IterateurRecursif(new IterateurDossier($this->retChemin()), ITR_REC_ENFANTS_AVANT);
 			
 			for (; $itr->estValide(); $itr->suiv())
 			{
 				$f = $itr->courant();
 				$r = $f->supprimer(FALSE) || $r;
 			}
 		}
 		// si pas d'effacement du contenu, mais que le dossier n'est pas vide, on provoque un avertissement et on sort  
 		// (ici, dossier = itérateur => si au moins un élément valide, ça veut dire que ce dossier n'est pas vide)
 		else
 		{
 			$itr = new IterateurDossier($this->retChemin());
 			
 			if ($itr->estValide())
 			{
				Erreur::provoquer($this->retChemin().": suppression impossible. Le dossier n'est pas vide!");
				return FALSE;
 			}
 		}
		
		// qu'on ait demandé ou pas l'effacement du contenu du dossier, il faut effacer le dossier lui-même
		return (@rmdir($this->retChemin()) || $r);
 	}
 	
 	/**
 	 * Supprime le fichier représenté par l'objet courant
 	 * 
 	 * @return	\c true si l'opération s'est bien déroulée, \c false dans le cas contraire
 	 */
 	function supprimerFichier()
 	{
 		if (!$this->estFichier())
 		{
 			Erreur::provoquer($this->retChemin().": suppression impossible. N'est pas un fichier");
 			return FALSE;
 		}
 			
 		if (!$this->estModifiable())
 		{
 			Erreur::provoquer($this->retChemin().": suppression impossible. Accès en écriture interdit");
 			return FALSE;
 		}
 			
 		return @unlink($this->retChemin());
	}
 	
 	/**
 	 * Supprime le fichier ou dossier représenté par l'objet courant
 	 * 
 	 * @param	v_bRecursif si \c true, indique qu'il faut également effacer les fichiers et dossiers présents dans le 
 	 * 						dossier de base. Si \c false, le dossier sera supprimé uniquement s'il est vide. 
 	 * 						Ce paramètre n'est donc utile qu'au cas ou le chemin représenté par l'objet courant est un 
 	 * 						dossier
 	 * 
 	 * @return	\c true si l'opération s'est bien déroulée, \c false dans le cas contraire
 	 */
 	function supprimer($v_bRecursif = FALSE)
 	{
 		if (!$this->existe())
 		{
 			Erreur::provoquer($this->retChemin().": suppression impossible. N'existe pas");
 			return FALSE;
 		}
 		
 		if ($this->estDossier())
 			return $this->supprimerDossier($v_bRecursif);
 		else
 			return $this->supprimerFichier();
 	}
 	
 	/**
 	 * Copie le fichier représenté par l'objet courant dans un autre dossier
 	 * 
 	 * @param	v_sDossierDest		le chemin du dossier destination pour la copie du fichier
 	 * @param	v_bEcraserExistant	si \c true, les dossiers ou fichiers existants seront créés/copiés, même si la cible
 	 * 								existait déjà, sans provoquer d'erreur ou d'avertissement. Si \c false, tout fichier 
 	 * 								cible existant au cours de la copie provoquera une erreur
 	 * @param	v_fnCallback		la fonction à appeler lorsqu'un fichier est copié. Ce paramètre doit représenter un 
 	 * 								type 'callback' valide, tel que reconnu par PHP. La fonction appelée devra accepter 
 	 * 								3 paramètres : les deux premiers de type FichierInfo, représenteront respectivement 
 	 * 								le fichier source qui devait être copié, et le fichier destination. Le 3è sera la 
 	 * 								valeur de retour de l'opération de copie
 	 * 
 	 * @return	\c true si l'opération s'est bien déroulée, \c false dans le cas contraire
 	 */
 	function copierFichier($v_sDossierDest, $v_bEcraserExistant = FALSE, $v_fnCallback = NULL)
 	{
 		// fichier source est vraiment un fichier ?
 		if (!$this->estFichier())
 		{
 			Erreur::provoquer($this->retChemin().": copie impossible. N'est pas un fichier");
 			return FALSE;
 		}

 		// fichier source est lisible ?
 		if (!$this->estLisible())
 		{
 			Erreur::provoquer($this->retChemin().": copie impossible. Accès en lecture interdit");
 			return FALSE;
 		}
 		
 		$oDossierDest = new FichierInfo($v_sDossierDest);
		// dossier destination est vraiment un dossier ?
 		if (!$oDossierDest->estDossier())
 		{
 			Erreur::provoquer($this->retChemin().": copie dans ".$oDossierDest->retChemin()." impossible."
 			                 ." La cible n'est pas un dossier");
 			return FALSE;
 		}
 		
 		// dossier destination est "inscriptible" ?
 		if (!$oDossierDest->estModifiable())
 		{
 			Erreur::provoquer($this->retChemin().": copie dans ".$oDossierDest->retChemin()." impossible. Accès en "
 			                ."écriture interdit");
 			return FALSE;
 		}
 		
 		// copie du fichier => dossier destination
 		$oDossierDest->formerChemin($this->retNom(), TRUE);
 		if (!$v_bEcraserExistant && $oDossierDest->existe())
 		{
 			Erreur::provoquer($this->retChemin().": copie dans ".$oDossierDest->retChemin()." impossible. Existe déjà "
 			                 ."dans le dossier cible");
 			return FALSE;
 		}
 		$r = @copy($this->retChemin(), $oDossierDest->retChemin());
 		
// APPAREMMENT, LE FIX SUIVANT N'EST PLUS D'ACTUALITE ?
// 		// normalement, la fonction copy() retourne false si le fichier source a une taille nulle, même si ce dernier a 
// 		// bien été copié. Je voudrais retourner true si le fichier a vraiment été copié, false sinon
// 		if ($r === FALSE && $this->retTaille() === 0 && $oDossierDest->existe() && $oDossierDest->retTaille() === 0)
//			$r = TRUE;
// FIN FIX
		
		if (!is_null($v_fnCallback))
			call_user_func($v_fnCallback, $this, $oDossierDest, $r);
		
 		return $r;
 	}
 	
 	/**
 	 * Copie le dossier représenté par l'objet courant *dans* un autre dossier
 	 * 
 	 * @param	v_sDossierDest		le chemin du dossier destination, *dans* lequel le dossier source sera copié
 	 * @param	v_bEcraserExistant	si \c true, les dossiers ou fichiers existants seront créés/copiés, même si la cible
 	 * 								existait déjà, sans provoquer d'erreur ou d'avertissement. Si \c false, tout fichier 
 	 * 								cible existant au cours de la copie provoquera une erreur
 	 * @param	v_bCreerDsDest		si \c true (défaut), un dossier du même nom que le source sera d'abord créé dans le 
 	 * 								dossier de destination, avant d'y copier le contenu de source. Si \c false, le 
 	 * 								contenu du dossier source sera copié directement dans le dossier cible spécifié 
 	 * @param	v_bRecursif			si \c true, les sous-dossiers et leur contenu seront également copiés
 	 * @param	v_fnCallback		voir le paramètre du même nom de #copierFichier()
 	 * 
 	 * @return	\c true si l'opération s'est bien déroulée, \c false dans le cas contraire
 	 */
 	function copierDossier($v_sDossierDest, $v_bEcraserExistant = FALSE, $v_bCreerDsDest = TRUE, $v_bRecursif = FALSE,  
 	                       $v_fnCallback = NULL)
 	{
 		if (!$this->estDossier())
 		{
 			Erreur::provoquer($this->retChemin().": copie impossible. N'est pas un dossier");
 			return FALSE;
 		}
 		
 		$oDossierDest = new FichierInfo($v_sDossierDest);
		// dossier destination est vraiment un dossier ?
 		if (!$oDossierDest->estDossier())
 		{
 			Erreur::provoquer($this->retChemin().": copie dans ".$oDossierDest->retChemin()." impossible."
 			                 ." La cible n'est pas un dossier");
 			return FALSE;
 		}
 		
 		// dossier destination est "inscriptible" ?
 		if (!$oDossierDest->estModifiable())
 		{
 			Erreur::provoquer($this->retChemin().": copie dans ".$oDossierDest->retChemin()." impossible. Accès "
 			                 ."en écriture interdit");
 			return FALSE;
 		}
 		
 		// "copie" du dossier => dossier destination : pas une copie mais une création de dossier...
 		if ($v_bCreerDsDest)
 		{
	 		$oDossierDest->formerChemin($this->retNom(), TRUE);
	 		// si le dossier à créer existe déjà:
	 		//  - SI on doit "écraser" l'existant, on ne fait rien mais on ne provoque pas d'erreur
	 		//  - SI on ne doit pas "écraser" l'existant, on provoque une erreur
	 		if ($oDossierDest->existe())
	 		{
	 			if ($v_bEcraserExistant)
	 			{
					$r = TRUE;
	 			}
				else
				{
					Erreur::provoquer($this->retChemin().": copie dans ".$oDossierDest->retChemin()." impossible. "
					                ." Existe déjà dans le dossier cible");
					$r = FALSE;
				}
	 		}
	 		else
	 		{
	 			$r = $oDossierDest->creerDossier();
	 		}
 		}
 		// ...sauf si a demandé de copier *le contenu* du dossier source dans le dossier dest => pas de création 
 		// préalable
 		else
 		{
 			$r = TRUE;
 		}
		
		// si le dossier de destination est disponible, on peut y copier le contenu du dossier source
		if ($r)
		{
			$r = FALSE;
			$itr = new IterateurDossier($this->retChemin());
			for (; $itr->estValide(); $itr->suiv())
			{
				$oFichierSource = $itr->courant();
				
				$oCheminRelatif = new FichierInfo($oFichierSource->retChemin());
				$oCheminRelatif->reduireChemin($this->retChemin(), TRUE);
				
				if ($oFichierSource->estFichier() || $v_bRecursif)
					$r = $oFichierSource->copier($oDossierDest->formerChemin($oCheminRelatif->retDossier()), 
					                             $v_bEcraserExistant, TRUE, $v_bRecursif, $v_fnCallback)
				   		 || $r;
			}
		}
		
		if (!is_null($v_fnCallback))
			call_user_func($v_fnCallback, $this, $oDossierDest, $r);
		
		return $r;
 	}
 	
 	/**
 	 * Copie le fichier ou dossier représenté par l'objet courant dans un autre dossier
 	 * 
 	 * @param	v_sDossierDest		le chemin du dossier destination, *dans* lequel le fichier ou dossier source sera 
 	 * 								copié 
 	 * @param	v_bEcraserExistant	si \c true, les dossiers ou fichiers existants seront créés/copiés, même si la cible
 	 * 								existait déjà, sans provoquer d'erreur ou d'avertissement. Si \c false, tout fichier 
 	 * 								cible existant au cours de la copie provoquera une erreur
 	 * @param	v_bCreerDsDest		uniquement utilisé dans le cas où la source de la copie est un dossier.
 	 * 								Voir le paramètre du même nom dans #copierDossier()
 	 * @param	v_bRecursif			si \c true, les sous-dossiers du dossier source, et leur contenu, seront également 
 	 * 								copiés.
 	 *  							Ce paramètre n'est donc utile qu'au cas ou le chemin représenté par l'objet courant est
 	 * 								un dossier
 	 * @param	v_fnCallback		voir le paramètre du même nom de #copierFichier()
 	 * 
 	 * @return	\c true si l'opération s'est bien déroulée, \c false dans le cas contraire
 	 */
 	function copier($v_sDossierDest, $v_bEcraserExistant = FALSE, $v_bCreerDsDest = TRUE, $v_bRecursif = FALSE, 
 	                $v_fnCallback = NULL)
 	{
 		if ($this->estDossier())
 			return $this->copierDossier($v_sDossierDest, $v_bEcraserExistant, $v_bCreerDsDest, $v_bRecursif, 
 			                            $v_fnCallback);
 		else
 			return $this->copierFichier($v_sDossierDest, $v_bEcraserExistant, $v_fnCallback);
 	}
 	
 	/**
 	 * Renomme le fichier ou dossier représenté par l'objet courant
 	 * 
 	 * @param	v_sCheminDest		le chemin du nouveau fichier/dossier
 	 * @param	v_bEcraserExistant	si \c true, et que nouvel emplacement du fichier/dossier à renommer existe déjà, 
 	 * 								ce dernier sera écrasé. Sinon, l'opération provoque une erreur
	 * @param	v_bRemplacer		indique si le chemin du fichier ou dossier de départ enregistré dans l'objet doit 
	 * 								être immédiatement remplacé par le chemin du fichier/dossier renommé, dans le cas où
	 * 								l'opération s'est déroulée correctement
 	 * @param	v_fnCallback		la fonction à appeler lorsqu'un fichier est renommé. Ce paramètre doit représenter 
 	 * 								un type 'callback' valide, tel que reconnu par PHP. La fonction appelée devra 
 	 * 								accepter 3 paramètres : les deux premiers de type FichierInfo, représenteront 
 	 * 								respectivement le fichier/dossier source qui devait être renommé, et le nouveau
 	 * 								fichier/dossier résultant du renommage. Le 3è sera la valeur de retour de 
 	 * 								l'opération de renommage
 	 * 
 	 * @return	\c true si l'opération s'est bien déroulée, \c false dans le cas contraire
 	 */
 	function renommer($v_sCheminDest, $v_bEcraserExistant = FALSE, $v_bRemplacer = FALSE, $v_fnCallback = NULL)
 	{
 		// fichier ou dossier source est lisible ?
 		if (!$this->estLisible())
 		{
 			Erreur::provoquer($this->retChemin().": renommage/déplacement impossible. Accès en lecture interdit");
 			return FALSE;
 		}
 		
 		// fichier source modifiable ?
 		if (!$this->estModifiable())
 		{
 			Erreur::provoquer($this->retChemin().": renommage/déplacement impossible. Accès en écriture interdit");
 			return FALSE;
 		}
 		
 		$oDossierSrc = new FichierInfo($this->retDossier(FALSE));
 		$oFichierDest = new FichierInfo($v_sCheminDest);
 		// si aucun dossier ne fait partie du "nom" (chemin) de destination, c'est qu'on veut garder le fichier renommé
 		// dans son dossier d'origine => Il faut manipuler le dossier de destination dans ce cas, car ce n'est pas le 
 		// comportement par défaut de la fonction rename(), elle déplacerait (renommerait) le fichier/dossier vers le
 		// dossier courant)
 		if (!$oFichierDest->cheminContientDossier())
 			$oFichierDest->defChemin($oDossierSrc->formerChemin($oFichierDest->retNom()));
 		$oDossierDest = new FichierInfo($oFichierDest->retDossier(FALSE));

		// dossier de destination "inscriptible" ?
 		if (!$oDossierDest->estModifiable())
 		{
 			Erreur::provoquer($this->retChemin().": renommage/déplacement vers ".$oDossierDest->retChemin()
 			                 ." impossible. Accès en écriture interdit");
 			return FALSE;
 		}

 		if ($oFichierDest->existe())
 		{
 			if ($oDossierSrc->retCheminReel() == $oDossierDest->retCheminReel()
 			 && $this->retNom() == $oFichierDest->retNom())
 				return TRUE;
 			
 			if (!$v_bEcraserExistant)
 			{
 				Erreur::provoquer($this->retChemin().": renommage/déplacement vers ".$oDossierDest->retChemin()
                                 ." impossible. Existe déjà dans le dossier cible");
 				return FALSE;
 			}
 			else
 			{
 				$oFichierDest->supprimer(TRUE);
 			}
 		}

		// renommer le fichier/dossier
		$r = @rename($this->retChemin(), $oFichierDest->retChemin());
 		
		if (!is_null($v_fnCallback))
			call_user_func($v_fnCallback, $this, $oDossierDest, $r);
		
		if ($r && $v_bRemplacer)
			$this->defChemin($oFichierDest->retChemin());
		
 		return $r;
 	}
 	
 	/**
 	 * Déplace le fichier ou dossier représenté par l'objet courant
 	 * 
 	 * @param	v_sDossierDest		le dossier de destination du déplacement
 	 * @param	v_bEcraserExistant	si \c true, et que nouvel emplacement du fichier/dossier à renommer existe déjà, 
 	 * 								ce dernier sera écrasé. Sinon, l'opération provoque une erreur
	 * @param	v_bRemplacer		indique si le chemin du fichier ou dossier de départ enregistré dans l'objet doit 
	 * 								être immédiatement remplacé par le chemin du fichier/dossier déplacé, dans le cas où
	 * 								l'opération s'est déroulée correctement
 	 * @param	v_fnCallback		la fonction à appeler lorsqu'un fichier est déplacé. Ce paramètre doit représenter 
 	 * 								un type 'callback' valide, tel que reconnu par PHP. La fonction appelée devra 
 	 * 								accepter 3 paramètres : les deux premiers de type FichierInfo, représenteront 
 	 * 								respectivement le fichier/dossier source qui devait être déplacé, et le nouveau
 	 * 								fichier/dossier résultant du déplacement. Le 3è sera la valeur de retour de 
 	 * 								l'opération de déplacement
 	 * 
 	 * @return	\c true si l'opération s'est bien déroulée, \c false dans le cas contraire
 	 * 
 	 * @note	Cette méthode est un cas particulier de #renommer() et y fait appel avec des paramètres prédéfinis 
 	 */
 	function deplacer($v_sDossierDest, $v_bEcraserExistant = FALSE, $v_bRemplacer = FALSE, $v_fnCallback = NULL)
 	{
 		$oDossierDest = new FichierInfo($v_sDossierDest);

		// dossier destination est vraiment un dossier ?
 		if (!$oDossierDest->estDossier())
 		{
 			Erreur::provoquer($this->retChemin().": déplacement vers ".$oDossierDest->retChemin()." impossible."
 			                 ." La cible n'est pas un dossier");
 			return FALSE;
 		}

 		return $this->renommer($oDossierDest->formerChemin($this->retNom()), $v_bEcraserExistant, $v_bRemplacer, $v_fnCallback);
 	}
}

?>