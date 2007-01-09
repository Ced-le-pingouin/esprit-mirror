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
 * @file	traverseur_scorm.class.php
 * 
 * Contient la classe qui traverse les (éléments de) formations et les exporte en SCORM 2004
 */

require_once(dirname(__FILE__).'/../../globals.inc.php');
require_once(dirname(__FILE__).'/traverseur.class.php');
require_once(dirname(__FILE__).'/../../lib/dom/dom.class.php');
require_once(dirname(__FILE__).'/../../lib/std/FichierInfo.php');

/**
 * Classe d'exportation des (éléments de) formations vers un paquet SCORM 2004 
 */
class CTraverseurScorm extends CTraverseur
{
	var $sEncodage = 'UTF-8'; ///< Encodage utilisé pour le fichier imsmanifest.xml (pas modifiable pour l'instant)
	
	var $oDocXml;             ///< Objet interne qui représente le document xml du manifest
	var $oDossierPaquet;      ///< Objet de type FichierInfo qui représente le chemin de la racine du paquet créé
	var $oDossierRessources;  ///< Objet de type FichierInfo qui représente le chemin du dossier où seront placées les ressources du paquet SCORM
	
	/** Objets internes qui contiendront les noeuds xml nécessaires pendant la création du manifest */
	//@{
	var $oElementManifest;
	var $oElementOrgs;
	var $oElementOrg;
	var $oElementRessources;
	var $oElementRessource;
	
	var $oElementFormation;
	var $oElementModule;
	var $oElementRubrique;
	var $oElementActiv;
	var $oElementSousActiv;
	//@}
	
	var $poElementParent;   ///< Référence à l'objet "noeud xml" parent des noeuds en cours de construction (utile pour les rattacher au document une fois créés)
	
	var $asRes   = array(); ///< Tableau contenant les objet xml qui représentent des ressources de rubriques ou de sous-activités
	
	/**
	 * Sauvegarde une référence à l'item (noeud xml du manifest) actuellement traité 
	 */
	function defElementCourant(&$v_poElementCourant)
	{
		$this->poElementCourant =& $v_poElementCourant;
	}
	
	/**
	 * Retourne la référence à l'item (noeud xml du manifest) actuellement traité
	 * 
	 * @return	l'objet (noeud xml) actuellement traité dans la traversée
	 */
	function &retElementCourant()
	{
		return $this->poElementCourant;
	}
	
	/**
	 * Sauvegarde la référence à un élément parent (noeud xml) dans un élément enfant, de façon à pouvoir 
	 * rattacher par la suite l'enfant à l'élément correct 
	 */
	function defElementParent(&$v_poElementEnfant, &$v_poElementParent)
	{
		$v_poElementEnfant->oElementParent =& $v_poElementParent;
	}
	
	/**
	 * Crée les noeuds xml présents au début du manifest (jusqu'à organization, sans les items).
	 * Cette fonction est appelée en interne uniquement
	 */
	function debutTraitement()
	{
		// construction du chemin où se trouvera la paquet SCORM et ses fichiers avant compression en PIF (zip)
		$this->oDossierPaquet = new FichierInfo(dir_tmp(NULL, TRUE));
		$sDossierPaquet = 'esprit-scorm-'.md5(uniqid(rand(), TRUE));
		$this->oDossierPaquet->formerChemin($sDossierPaquet, TRUE);
		
		// création du dossier racine et du sous-dossier destiné à contenir d'éventuels fichiers (ressources)
		$this->oDossierPaquet->creerDossier();
		$this->oDossierRessources = new FichierInfo($this->oDossierPaquet->formerChemin('fichiers'));
		$this->oDossierRessources->creerDossier();
		
		// ressources à zéro
		$this->asRes = array();
		
		// création de l'objet "document xml" pour le manifest, avec son encodage, dans un format lisible avec indentation
		$this->oDocXml = new CDOMDocument('1.0', $this->sEncodage);
		$this->oDocXml->formatOutput = TRUE;
		
		// manifest (élément racine du fichier xml)
		$this->oElementManifest = $this->oDocXml->createElement('manifest');
		$this->oElementManifest->setAttribute('identifier', 'Esprit-SCORM-2004');
		$this->oElementManifest->setAttribute('version', '1.3');
		$this->oElementManifest->setAttribute('xmlns', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$this->oElementManifest->setAttribute('xmlns:adlcp', 'http://www.adlnet.org/xsd/adlcp_v1p3');
		
		//$this->oElementManifest->setAttribute('xmlns:lom', 'http://ltsc.ieee.org/xsd/LOM');
		//$this->oElementManifest->setAttribute('xmlns:adlseq', 'http://www.adlnet.org/xsd/adlseq_v1p3');
		//$this->oElementManifest->setAttribute('xmlns:adlnav_1p3', 'http://www.adlnet.org/xsd/adlnav_v1p3');
		//$this->oElementManifest->setAttribute('xmlns:imsss', 'http://www.imsglobal.org/xsd/imsss');
		
		// metadata
		$metadata = $this->oDocXml->createElement('metadata');
		$schema = $this->oDocXml->createElement('schema', 'ADL SCORM');
		$metadata->appendChild($schema);
		
		$schemaversion = $this->oDocXml->createElement('schemaversion', 'CAM 1.3');
		$metadata->appendChild($schemaversion);
				
		// infos LOM
		//$lom = $this->oDocXml->createElement('lom:lom');
		//$metadata->appendChild($lom);
		// fin  infos LOM
		
		$this->oElementManifest->appendChild($metadata);
		// fin metadata
		
		// organizations (pour le moment il n'y en aura qu'une seule dans le manifest)
		$this->oElementOrgs = $this->oDocXml->createElement('organizations');
		$this->oElementOrgs->setAttribute('default', 'ORG-1');
		
		// organization (élément parent au-dessous duquel seront créés les éléments de la structure d'une formation) 
		$this->oElementOrg = $this->oDocXml->createElement('organization');
		$this->oElementOrg->setAttribute('identifier', 'ORG-1');
		
		$title = $this->oDocXml->createElement('title', 'Esprit');
		$this->oElementOrg->appendChild($title);
		
		// l'élément suivant devra être rattaché à celui-ci, qui devient le "parent"
		$this->defElementCourant($this->oElementOrg);
		
		// cet élément est créé dès le départ, mais sera rattaché au document principal uniquement à la fin
		$this->oElementRessources = $this->oDocXml->createElement('resources');
	}
	
	/**
	 * Rattache les noeuds xml les plus extérieurs (ceux qui ne sont pas des "item") à la racine du fichier manifest.
	 * Cette fonction est appelée en interne uniquement
	 */
	function finTraitement()
	{
		// fin organization => ajouté dans organizations		
		$this->oElementOrgs->appendChild($this->oElementOrg);
		
		// fin organizations => ajouté dans manifest
		$this->oElementManifest->appendChild($this->oElementOrgs);
		
		// fin resources => ajouté dans manifest
		$this->oElementManifest->appendChild($this->oElementRessources);
		
		// fin manifest => ajouté dans le doc/root => fin document XML
		$this->oDocXml->appendChild($this->oElementManifest);
		
		// suppression du dossier temporaire créé pour le paquet
		//$this->oDossierPaquet->supprimerDossier(TRUE);
	}
	
	/**
	 * Crée le début de l'élément xml "item" correspondant à une formation.
	 * Cette fonction est appelée en interne uniquement
	 */
	function debutFormation()
	{
		// formation (item)
		$this->oElementFormation = $this->oDocXml->createElement('item');
		$this->oElementFormation->setAttribute('identifier', 'FORM-'.$this->oFormation->retId());
		
		$title = $this->oDocXml->createElement('title', $this->oFormation->retNom());
		$this->oElementFormation->appendChild($title);
		
		$this->defElementParent($this->oElementFormation, $this->retElementCourant());
		$this->defElementCourant($this->oElementFormation);
	}
	
	/**
	 * Rattache le noeud xml "item" correspondant à une formation, et tout son contenu, à l'élément xml parent 
	 * Cette fonction est appelée en interne uniquement
	 */
	function finFormation()
	{
		// fin formation (item) => ajouté dans item parent (organization)
		$this->oElementFormation->oElementParent->appendChild($this->oElementFormation);
		$this->defElementCourant($this->oElementFormation->oElementParent);
	}
	
	/**
	 * Crée le début de l'élément xml "item" correspondant à un module.
	 * Cette fonction est appelée en interne uniquement
	 */
	function debutModule()
	{
		// module (item)
		$this->oElementModule = $this->oDocXml->createElement('item');
		$this->oElementModule->setAttribute('identifier', 'MOD-'.$this->oModule->retId());
		
		$title = $this->oDocXml->createElement('title', $this->oModule->retNom());
		$this->oElementModule->appendChild($title);
		
		$this->defElementParent($this->oElementModule, $this->retElementCourant());
		$this->defElementCourant($this->oElementModule);
	}
	
	/**
	 * Rattache le noeud xml "item" correspondant à un module, et tout son contenu, à l'élément xml parent 
	 * Cette fonction est appelée en interne uniquement
	 */
	function finModule()
	{
		// fin module (item) => ajouté dans item parent (normalement formation, sauf si exporté seul)
		$this->oElementModule->oElementParent->appendChild($this->oElementModule);
		$this->defElementCourant($this->oElementModule->oElementParent);
	}
	
	/**
	 * Crée le début de l'élément xml "item" correspondant à une rubrique.
	 * Cette fonction est appelée en interne uniquement
	 */
	function debutRubrique()
	{
		// rubrique (item)
		$this->oElementRubrique = $this->oDocXml->createElement('item');
		$this->oElementRubrique->setAttribute('identifier', 'RUB-'.$this->oRubrique->retId());
		
		$title = $this->oDocXml->createElement('title', $this->oRubrique->retNom());
		$this->oElementRubrique->appendChild($title);
		
		// des fichiers peuvent être associés aux rubriques => transformés en ressources SCORM
		$this->_exporterRessources(TYPE_RUBRIQUE, $this->oRubrique, $this->oElementRubrique);
		
		$this->defElementParent($this->oElementRubrique, $this->retElementCourant());
		$this->defElementCourant($this->oElementRubrique);
	}
	
	/**
	 * Rattache le noeud xml "item" correspondant à une rubrique, et tout son contenu, à l'élément xml parent 
	 * Cette fonction est appelée en interne uniquement
	 */
	function finRubrique()
	{
		// fin rubrique (item) => ajouté dans item parent (normalement module, sauf si exporté seul)
		$this->oElementRubrique->oElementParent->appendChild($this->oElementRubrique);
		$this->defElementCourant($this->oElementRubrique->oElementParent);
	}
	
	/**
	 * Crée le début de l'élément xml "item" correspondant à une activité.
	 * Cette fonction est appelée en interne uniquement
	 */
	function debutActiv()
	{
		// activ(ité) (item) (dans la DB, dans Esprit le terme est devenu "Groupe d'actions")
		$this->oElementActiv = $this->oDocXml->createElement('item');
		$this->oElementActiv->setAttribute('identifier', 'ACTIV-'.$this->oActiv->retId());
		
		$title = $this->oDocXml->createElement('title', $this->oActiv->retNom());
		$this->oElementActiv->appendChild($title);
		
		$this->defElementParent($this->oElementActiv, $this->retElementCourant());
		$this->defElementCourant($this->oElementActiv);
	}
	
	/**
	 * Rattache le noeud xml "item" correspondant à une activité, et tout son contenu, à l'élément xml parent 
	 * Cette fonction est appelée en interne uniquement
	 */
	function finActiv()
	{
		// fin activ (item) => ajouté dans item parent (normalement rubrique, sauf si exporté seul)
		$this->oElementActiv->oElementParent->appendChild($this->oElementActiv);
		$this->defElementCourant($this->oElementActiv->oElementParent);
	}
	
	/**
	 * Crée le début de l'élément xml "item" correspondant à une sous-activité.
	 * Cette fonction est appelée en interne uniquement
	 */
	function debutSousActiv()
	{
		// sous-activ(ité) (item) (dans la DB, dans Esprit le terme est devenu "Action")
		$this->oElementSousActiv = $this->oDocXml->createElement('item');
		$this->oElementSousActiv->setAttribute('identifier', 'SOUSACTIV-'.$this->oSousActiv->retId());
		
		$title = $this->oDocXml->createElement('title', $this->oSousActiv->retNom());
		$this->oElementSousActiv->appendChild($title);
		
		// des fichiers peuvent être associés aux sous-activités => transformés en ressources SCORM
		$this->_exporterRessources(TYPE_SOUS_ACTIVITE, $this->oSousActiv, $this->oElementRubrique);
		
		$this->defElementParent($this->oElementSousActiv, $this->retElementCourant());
		$this->defElementCourant($this->oElementSousActiv);
	}
	
	/**
	 * Rattache le noeud xml "item" correspondant à une sous-activité, et tout son contenu, à l'élément xml parent 
	 * Cette fonction est appelée en interne uniquement
	 */
	function finSousActiv()
	{
		// fin sous-activ (item) => ajouté dans item parent (normalement activ, sauf si exporté seul)
		$this->oElementSousActiv->oElementParent->appendChild($this->oElementSousActiv);
		$this->defElementCourant($this->oElementSousActiv->oElementParent);
	}
	
	/**
	 * Retourne le contenu généré pour la création du fichier imsmanifest.xml
	 * 
	 * @return	la chaîne de texte générée pendant la traversée des (éléments de) formations, et qui constituera le 
	 * 			contenu du fichier imsmanifest.xml créé pour le paquet SCORM
	 */
	function retContenuManifest()
	{
		return $this->oDocXml->saveXML(); 
	}
	
	/**
	 * Crée le paquet SCORM correspondant aux éléments de formations traversés
	 */
	function enregistrerPaquetScorm()
	{	
		// sauver le fichier XML
		$this->oDocXml->save($this->oDossierPaquet->formerChemin('imsmanifest.xml'));
	}
	
	/**
	 * Indique si une ressource ou un ensemble de ressources a déjà été exporté(e) (décrit en xml)
	 * 
	 * @param	v_sNomRes	l'identificateur de la ressource à vérifier
	 * 
	 * @return	\c true si cette (ces) ressource(s) a (ont) déjà été exportée(s), \c false sinon
	 */
	function _ressourceDejaExportee($v_sNomRes)
	{
		return array_key_exists($v_sNomRes, $this->asRes);
	}
	
	/**
	 * Déclare une ressource ou en ensemble de ressource comme exporté(e)
	 * 
	 * @param	v_sNomRes				l'identificateur de la ressource exportée
	 * @param	v_oElementXmlRessource	l'objet xml qui représente cette ressource en SCORM
	 */
	function _declarerRessourceExportee($v_sNomRes, &$v_oElementXmlRessource)
	{
		$this->asRes[$v_sNomRes] =& $v_oElementXmlRessource;
	}
	
	/**
	 * Intègre un fichier à un ensemble de ressources exportées (balise file en SCORM). Cette fonction est appelée en 
	 * callback par un objet FichierInfo chargé de copier les fichiers d'un élément de formation
	 * 
	 * @param	l'objet FichierInfo qui représente le fichier source qui est exporté (il se trouve dans les rubriques 
	 * 			ou sous-activités)
	 * @param	l'objet FichierInfo qui représente le fichier copié, dans son emplacement final au sein du paquet SCORM 
	 * 			créé dans un dossier temporaire
	 * @param	si \c true, la copie du fichier s'est bien déroulée (ce paramètre est requis pour les fonctions 
	 * 			callback de FichierInfo dans le cadre des copies)
	 */
	function _cbExporterFichierRessource($v_oFichierSrc, $v_oFichierDest, $v_bCopieReussie)
	{
		if ($v_bCopieReussie && $v_oFichierDest->estFichier())
		{
			$f = $this->oDocXml->createElement('file');
			$f->setAttribute('href', $v_oFichierDest->reduireChemin($this->oDossierPaquet->retChemin()
			                                                        .$v_oFichierDest->retSeparateur()));
			$this->oElementRessource->appendChild($f);
		}
	}
	
	/**
	 * Prend en charge l'exportation de toutes les ressources d'un niveau de formation. Cela implique la copie des 
	 * fichiers dans le paquet SCORM en création, l'écriture des balises correspondantes, la transformation de 
	 * ressources au format Esprit vers un format lisible et utilisable dans un paquet SCORM, etc
	 * 
	 * @param	v_iNiveau		le niveau pour lequel s'effectue l'exportation de ressources (rubrique, (sous-)activité,
	 * 							etc)
	 * @param	v_oElementPhp	l'objet qui représente l'élément de formation en terme de classes Esprit, càd CFormation, 
	 * 							CModule, etc
	 * @param	v_oElementXml	l'objet qui représente l'élément de formation sous sa forme *exportée*, càd dire en DOM 
	 * 							XML
	 */
	function _exporterRessources($v_iNiveau, &$v_oElementPhp, &$v_oElementXml)
	{
		// les ressources de toutes les rubriques d'une formation se trouvent dans un dossier commun 
		if ($v_iNiveau == TYPE_RUBRIQUE)
			$sNomResGlobales = 'RES-RUB-FORM-'.$this->oFormation->retId();
		// idem pour les ressources de toutes les sous-activités d'une même activité
		else if ($v_iNiveau == TYPE_SOUS_ACTIVITE)
			$sNomResGlobales = 'RES-ACTIV-'.$this->oActiv->retId();
		else
			Erreur::provoquer("Exportation de ressources non supportée à ce niveau ($v_iNiveau)");
		
		// si on n'a pas déjà créé le bloc ressource "global" pour les rubriques d'une même formation, ou pour les 
		// sous-activités d'une même activité, on le crée. Ca ne sera donc fait qu'une seule fois par formation ou 
		// activité. Les rubriques et sous-activités (respectivement) dépendantes auront uniquement un href pour 
		// indiquer l'index, et une balise dependency pour pointer vers l'ensemble des ressources de son "parent"
		if (!$this->_ressourceDejaExportee($sNomResGlobales))
		{
			$this->oElementRessource = $this->oDocXml->createElement('resource');
			$this->oElementRessource->setAttribute('identifier', $sNomResGlobales);
			$this->oElementRessource->setAttribute('type', 'webcontent');
			$this->oElementRessource->setAttribute('adlcp:scormType', 'asset');
			
			$oDossierSrc = new FichierInfo($v_oElementPhp->retDossier());
			$oDossierSrc->copier($this->oDossierRessources->retChemin(), TRUE, array($this, '_cbExporterFichierRessource'));
			
			$this->oElementRessources->appendChild($this->oElementRessource);
			
			$this->_declarerRessourceExportee($sNomResGlobales, $this->oElementRessource);
		}
		/*
		switch($v_oElementPhp->retType())
		{
			case LIEN_PAGE_HTML:
				break;
			
			case LIEN_DOCUMENT_TELECHARGER:
				$sUrl = $v_oElementPhp->retDonnee(DONNEES_URL);
				$sNomRes = $this->_retNomRessource($sUrl);
				
				$v_oElementXml->setAttribute('identifierref', $sNomRes);
				
				$r = $this->oDocXml->createElement('resource');
				$r->setAttribute('identifier', $sNomRes);
				$r->setAttribute('type', 'webcontent');
				$r->setAttribute('adlcp:scormType', 'asset');
				$r->setAttribute('href', $sUrl);
					
					$f = $this->oDocXml->createElement('file');
					$f->setAttribute('href', $sUrl);
					$r->appendChild($f);
					
					$this->oElementRessources->appendChild($r);
				
				break;
				
			case LIEN_SITE_INTERNET:
				$sUrl = $v_oElementPhp->retDonnee(DONNEES_URL);
				$bResExiste = $this->_ressourceDejaExportee($sUrl);
				$sNomRes = $this->_retNomRessource($sUrl);
				
				$v_oElementXml->setAttribute('identifierref', $sNomRes);

				if (!$bResExiste)
				{				
					$r = $this->oDocXml->createElement('resource');
					$r->setAttribute('identifier', $sNomRes);
					$r->setAttribute('type', 'webcontent');
					$r->setAttribute('adlcp:scormType', 'asset');
					$r->setAttribute('href', $sUrl);
					$this->oElementRessources->appendChild($r);
				}
				break;
			
			case LIEN_TEXTE_FORMATTE:
//				$sUrl = $v_oElementPhp->retDonnee(DONNEES_URL);
//				$bResExiste = $this->_ressourceDejaExportee($sUrl);
//				$sNomRes = $this->_retNomRessource($sUrl);
//				
//				$v_oElementXml->setAttribute('identifierref', $sNomRes);
//
//				if (!$bResExiste)
//				{				
//					$r = $this->oDocXml->createElement('resource');
//					$r->setAttribute('identifier', $sNomRes);
//					$r->setAttribute('type', 'webcontent');
//					$r->setAttribute('adlcp:scormType', 'asset');
//					$r->setAttribute('href', $sUrl);
//					
//					$f = $this->oDocXml->createElement('file');
//					$f->setAttribute('href', $sUrl);
//					$r->appendChild($f);
//					
//					$this->oElementRessources->appendChild($r);
//				}
//				break;
			
			// types connus mais qui n'ont pour le moment pas d'exportation de ressources supplémentaires
			case LIEN_CHAT:
			case LIEN_FORUM:
			case LIEN_UNITE:
			case LIEN_GALERIE:
			case LIEN_COLLECTICIEL:
			case LIEN_TABLEAU_DE_BORD:
				break;
			
			// si type inconnu, aucune ressource exportée, forcément
			default:
				break;
		}
		*/
	}
}

?>