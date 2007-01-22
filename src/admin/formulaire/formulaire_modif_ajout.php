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

require_once("globals.inc.php");
$oProjet = new CProjet();
//************************************************
//*       Récupération des variables             *
//************************************************

if (isset($_GET['idformulaire']))
{
	$v_iIdFormulaire = $_GET['idformulaire'];
	//formulaire ci-dessous
	$v_iIdTypeObj = $_GET['idtypeobj'];
}
else
{
	$v_iIdFormulaire = 0;
	$v_iIdTypeObj = 0;
}
	if(isset($_GET['bMesForms']))
		$bMesForms = $_GET['bMesForms'];
	else
		$bMesForms = 0;
$oTpl = new Template("formulaire_modif_ajout.tpl");
$oBlockModifAjout = new TPL_Block("BLOCK_MODIF_AJOUT",$oTpl);
if (isset($_GET['ajouter']) && $oProjet->verifPermission('PERM_MOD_FORMULAIRES'))
{
	if ($v_iIdTypeObj > 0)
	{
		$oObjetFormulaire = new CObjetFormulaire($oProjet->oBdd,$oObjetFormulaire); //$oObjetFormulaire a vérifier ????
		$oObjetFormulaire->defIdTypeObj($v_iIdTypeObj);
		$oObjetFormulaire->defIdForm($v_iIdFormulaire);
		
		$iOrdreMaxObjForm = $oObjetFormulaire->OrdreMaxObjForm($oObjetFormulaire->retIdForm());
		$iOrdreMaxObjForm = $iOrdreMaxObjForm + 1; //pour placer l'objet a la fin du formulaire
		
		$oObjetFormulaire->defOrdreObjForm($iOrdreMaxObjForm);
		$oObjetFormulaire->enregistrer();
		$iIdObjetFormActuel = $oObjetFormulaire->retId();
		
		switch($oObjetFormulaire->retIdTypeObj())
		{
			case 1:	$oQTexteLong = new CQTexteLong($oProjet->oBdd);
					$oQTexteLong->ajouter($iIdObjetFormActuel);
					unset($oQTexteLong);
					break;
			
			case 2:	$oQTexteCourt = new CQTexteCourt($oProjet->oBdd);
					$oQTexteCourt->ajouter($iIdObjetFormActuel);
					unset($oQTexteCourt);
					break;
			
			case 3:	$oQNombre = new CQNombre($oProjet->oBdd);
					$oQNombre->ajouter($iIdObjetFormActuel);
					unset($oQNombre);
					break;
			
			case 4:	$oQListeDeroul = new CQListeDeroul($oProjet->oBdd);
					$oQListeDeroul->ajouter($iIdObjetFormActuel);
					unset($oQListeDeroul);
					break;
			
			case 5:	$oQRadio = new CQRadio($oProjet->oBdd);
					$oQRadio->ajouter($iIdObjetFormActuel);
					unset($oQRadio);
					break;
			
			case 6:	$oQCocher = new CQCocher($oProjet->oBdd);
					$oQCocher->ajouter($iIdObjetFormActuel);
					unset($oQCocher);
					break;
			
			case 7:	$oMPTexte = new CMPTexte($oProjet->oBdd);
					$oMPTexte->ajouter($iIdObjetFormActuel);
					unset($oMPTexte);
					break;
			
			case 8:	$oMPSeparateur = new CMPSeparateur($oProjet->oBdd);
					$oMPSeparateur->ajouter($iIdObjetFormActuel);
					unset($oMPSeparateur);
					break;
		}
	}
	$oBlockModifAjout->effacer();
	$oTpl->remplacer("{onload}","onload=\"popupajout($v_iIdFormulaire,$iIdObjetFormActuel,$bMesForms); window.close ();\"");
}
else
{
	$oTpl->remplacer("{onload}","");
	$oTypeObjForm = new CTypeObjetForm($oProjet->oBdd);
	$aoTypeObjForm = $oTypeObjForm->retListeTypeObjet();
	if(!empty($aoTypeObjForm))
	{
		$oBlockModifAjout->beginLoop();
		foreach($aoTypeObjForm AS $oTypeObjForm)
		{
			$oBlockModifAjout->nextLoop();
			$oBlockModifAjout->remplacer("{desc_type_obj}",$oTypeObjForm->retDescTypeObj());
			$oBlockModifAjout->remplacer("{id_type_obj}",$oTypeObjForm->retId());
		}
		$oBlockModifAjout->afficher();
	}
	else
	{
		$oBlockModifAjout->effacer();
	}
	$oTpl->remplacer("{id_formulaire}",$v_iIdFormulaire);
	$oTpl->remplacer("{bMesForms}",$bMesForms);
}
$oTpl->afficher();	  
$oProjet->terminer();
?>
