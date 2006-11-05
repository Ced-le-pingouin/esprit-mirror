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
 * Contient la classe qui traverse les formations et les exporte en SCORM
 */

require_once(dirname(__FILE__).'/traverseur.class.php');
require_once(dirname(__FILE__).'/../../lib/dom/dom.class.php');

class CTraverseurScorm extends CTraverseur
{
	var $sEncodage = 'UTF-8'; // pour le moment pas modifiable
	
	var $oDocXml;
	
	var $oElementManifest;
	var $oElementOrgs;
	var $oElementOrg;
	
	var $oElementFormation;
	var $oElementModule;
	var $oElementRubrique;
	var $oElementActiv;
	var $oElementSousActiv;
	
	var $poAncienElementParent;
	
	function debutTraitement()
	{
		$this->oDocXml = new CDOMDocument('1.0', $this->sEncodage);
		$this->oDocXml->formatOutput = TRUE;
		
		// manifest
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
		
		// organizations
		$this->oElementOrgs = $this->oDocXml->createElement('organizations');
		$this->oElementOrgs->setAttribute('default', 'ORG-1');
		
		// organization 
		$this->oElementOrg = $this->oDocXml->createElement('organization');
		$this->oElementOrg->setAttribute('identifier', 'ORG-1');
		
		$title = $this->oDocXml->createElement('title', 'Esprit');
		$this->oElementOrg->appendChild($title);
		
		// l'élément suivant devra être rattaché à celui, qui devient le "parent"
		$this->defElementParent($this->oElementOrg);
	}
	
	function finTraitement()
	{
		// fin organization => ajouté dans organizations		
		$this->oElementOrgs->appendChild($this->oElementOrg);
		
		// fin organizations => ajouté dans manifest
		$this->oElementManifest->appendChild($this->oElementOrgs);
		
		// fin manifest => ajouté dans le doc/root => fin document XML
		$this->oDocXml->appendChild($this->oElementManifest);
	}
	
	function debutFormation()
	{
		// formation (item)
		$this->oElementFormation = $this->oDocXml->createElement('item');
		$this->oElementFormation->setAttribute('identifier', 'FORM-'.$this->oFormation->retId());
		
		$title = $this->oDocXml->createElement('title', $this->oFormation->retNom());
		$this->oElementFormation->appendChild($title);
		
		$this->defElementParent($this->oElementFormation);
	}
	
	function finFormation()
	{
		// fin formation (item) => ajouté dans organization
		//$this->oElementOrg->appendChild($this->oElementFormation);
		$this->oElementFormation->oElementParent->appendChild($this->oElementFormation);
	}
	
	function debutModule()
	{
		// module (item)
		$this->oElementModule = $this->oDocXml->createElement('item');
		$this->oElementModule->setAttribute('identifier', 'MOD-'.$this->oModule->retId());
		
		$title = $this->oDocXml->createElement('title', $this->oModule->retNom());
		$this->oElementModule->appendChild($title);
		
		$this->defElementParent($this->oElementModule);
	}
	
	function finModule()
	{
		// fin module (item) => ajouté dans formation (= item parent)
		$this->oElementModule->oElementParent->appendChild($this->oElementModule);
	}
	
	function debutRubrique()
	{
		// rubrique (item)
		$this->oElementRubrique = $this->oDocXml->createElement('item');
		$this->oElementRubrique->setAttribute('identifier', 'RUB-'.$this->oRubrique->retId());
		
		$title = $this->oDocXml->createElement('title', $this->oRubrique->retNom());
		$this->oElementRubrique->appendChild($title);
		
		$this->defElementParent($this->oElementRubrique);
	}
	
	function finRubrique()
	{
		// fin rubrique (item) => ajouté dans module (= item parent)
		$this->oElementRubrique->oElementParent->appendChild($this->oElementRubrique);
	}
	
	function debutActiv()
	{
		// activ(ité) (item) (dans la DB, dans Esprit le terme est devenu "Groupe d'actions")
		$this->oElementActiv = $this->oDocXml->createElement('item');
		$this->oElementActiv->setAttribute('identifier', 'ACTIV-'.$this->oActiv->retId());
		
		$title = $this->oDocXml->createElement('title', $this->oActiv->retNom());
		$this->oElementActiv->appendChild($title);
		
		$this->defElementParent($this->oElementActiv);
	}
	
	function finActiv()
	{
		// fin activ (item) => ajouté dans rubrique (= item parent)
		$this->oElementActiv->oElementParent->appendChild($this->oElementActiv);
	}
	
	function debutSousActiv()
	{
		// sous-activ(ité) (item) (dans la DB, dans Esprit le terme est devenu "Action")
		$this->oElementSousActiv = $this->oDocXml->createElement('item');
		$this->oElementSousActiv->setAttribute('identifier', 'SOUSACTIV-'.$this->oSousActiv->retId());
		//$this->oElementSousActiv->setAttribute('identifierref', 'RES-1');
		
		$title = $this->oDocXml->createElement('title', $this->oSousActiv->retNom());
		$this->oElementSousActiv->appendChild($title);
		
		$this->defElementParent($this->oElementSousActiv);
	}
	
	function finSousActiv()
	{
		// fin sous-activ (item) => ajouté dans activ (= item parent)
		$this->oElementSousActiv->oElementParent->appendChild($this->oElementSousActiv);
	}
	
	
	function retContenuManifest()
	{
		echo $this->oDocXml->saveXML(); 
	}

	function enregistrerPaquetScorm()
	{	
		// sauver le fichier XML
		$this->oDocXml->save('package_scorm/imsmanifest.xml');
	}
	
	
	function defElementParent(&$v_poNouvelElementParent)
	{
		if (isset($this->poAncienElementParent))
			$v_poNouvelElementParent->oElementParent =& $this->poAncienElementParent;
		
		$this->poAncienElementParent =& $v_poNouvelElementParent;
	}
}

?>