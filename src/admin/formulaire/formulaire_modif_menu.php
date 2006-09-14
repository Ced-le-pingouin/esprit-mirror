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

//Ceci est ajouté uniquement pour pouvoir effectuer un contrôle de l'utilisateur
require_once("globals.inc.php");
$oProjet = new CProjet();
$oTpl = new Template("formulaire_modif_menu.tpl");

if ($oProjet->verifPermission('PERM_MOD_FORMULAIRES')) // Verification de la permission d'utiliser le concepteur de formulaire
{
	// Récupération des variables
	if (isset($_GET['idformulaire']))
	{
		$v_iIdObjForm = $_GET['idobj'];
		$v_iIdFormulaire = $_GET['idformulaire'];
	}
	else
	{
		$v_iIdObjForm = 0;
		$v_iIdFormulaire = 0;
	}
	
	if ($v_iIdFormulaire > 0)
		$oTpl->remplacer("{AJOUTER}","<a href=\"javascript: ajoutobj($v_iIdFormulaire);\">Ajouter</a>");
	else
		$oTpl->remplacer("{AJOUTER}","<span class=\"element_desactive\">Ajouter</span>");
	
	if ($v_iIdObjForm > 0) //on envoie $v_iIdFormulaire uniquement pour pouvoir recharger la liste après la suppression
		$oTpl->remplacer("{SUPPRIMER}","<a href=\"javascript: supobj($v_iIdObjForm,$v_iIdFormulaire);\">Supprimer</a>");
	else
		$oTpl->remplacer("{SUPPRIMER}","<span class=\"element_desactive\">Supprimer</span>");
	
	if ($v_iIdObjForm > 0) //on envoie $v_iIdFormulaire pour pouvoir recharger la liste après le déplacement
		$oTpl->remplacer("{DEPLACER}","<a href=\"javascript: modifposobj($v_iIdObjForm,$v_iIdFormulaire);\">Déplacer</a>");
	else
		$oTpl->remplacer("{DEPLACER}","<span class=\"element_desactive\">Déplacer</span>");
		
	if ($v_iIdObjForm > 0) //on envoie $v_iIdFormulaire pour pouvoir recharger la liste après le déplacement
		$oTpl->remplacer("{COPIER}","<a href=\"javascript: copieobj($v_iIdObjForm,$v_iIdFormulaire);\">Copier</a>");
	else
		$oTpl->remplacer("{COPIER}","<span class=\"element_desactive\">Copier</span>");
	
	if ($v_iIdFormulaire > 0) //on envoie $v_iIdFormulaire pour pouvoir recharger la liste après le déplacement
		$oTpl->remplacer("{DEF_AXES}","<a href=\"javascript: modifaxeform($v_iIdFormulaire);\">Définir les axes de cette activité</a>");
	else
		$oTpl->remplacer("{DEF_AXES}","<span class=\"element_desactive\">Définir les axes de cette activité</span>");
	$oTpl->afficher();
}
?>
