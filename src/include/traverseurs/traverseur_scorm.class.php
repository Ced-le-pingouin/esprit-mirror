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

require_once(dirname(__FILE__).'/traverseur.class.php');
require_once(dirname(__FILE__).'/../../lib/dom/dom.class.php');

/**
 * Classe d'exportation des (éléments de) formations vers un paquet SCORM 2004 
 */
class CTraverseurScorm extends CTraverseur
{
	var $sEncodage = 'UTF-8'; ///< Encodage utilisé pour le fichier imsmanifest.xml (pas modifiable pour l'instant)
	
	var $oDocXml;			  ///< Objet interne qui représente le document xml du manifest
	
	/** Objets internes qui contiendront les noeuds xml nécessaires pendant la création du manifest */
	//@{
	var $oElementManifest;
	var $oElementOrgs;
	var $oElementOrg;
	
	var $oElementFormation;
	var $oElementModule;
	var $oElementRubrique;
	var $oElementActiv;
	var $oElementSousActiv;
	//@}
	
	var $poElementParent; ///< Référence à l'objet "noeud xml" parent des noeuds en cours de construction (utile pour les rattacher au document une fois créés)  
	
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
	}
	
	/**
	 * Rattache les noeuds xml les plus extérieurs à la racine du fichier manifest.
	 * Cette fonction est appelée en interne uniquement
	 */
	function finTraitement()
	{
		// fin organization => ajouté dans organizations		
		$this->oElementOrgs->appendChild($this->oElementOrg);
		
		// fin organizations => ajouté dans manifest
		$this->oElementManifest->appendChild($this->oElementOrgs);
		
		// fin manifest => ajouté dans le doc/root => fin document XML
		$this->oDocXml->appendChild($this->oElementManifest);
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
		//$this->oElementSousActiv->setAttribute('identifierref', 'RES-1');
		
		$title = $this->oDocXml->createElement('title', $this->oSousActiv->retNom());
		$this->oElementSousActiv->appendChild($title);
		
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
	 * Retourne le contenu (texte) du fichier imsmanifest.xml
	 * 
	 * @return	le contenu du fichier imsmanifest.xml créé pendant la traversée des (éléments de) formations
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
		$this->oDocXml->save('package_scorm/imsmanifest.xml');
	}
}

?>