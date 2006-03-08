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

if (isset($HTTP_GET_VARS))
{
	$v_iIdObjForm = $HTTP_GET_VARS['idobj'];
	$v_iIdFormulaire = $HTTP_GET_VARS['idformulaire'];
	$v_iNouvPos = $HTTP_GET_VARS['ordreobj'];
}
else if (isset($HTTP_POST_VARS))
{
	$v_iIdObjForm = $HTTP_POST_VARS['idobj'];
	$v_iIdFormulaire = $HTTP_POST_VARS['idformulaire'];
	$v_iNouvPos = $HTTP_POST_VARS['ordreobj'];
}
else
{
	$v_iIdObjForm = 0;
	$v_iIdFormulaire = 0;
	$v_iNouvPos = 0;
}


if (isset($HTTP_GET_VARS['deplacer']))
{

	$oObjetFormulaire = new CObjetFormulaire($oProjet->oBdd,$v_iIdObjForm);
	$oObjetFormulaire->retId();
	$oObjetFormulaire->DeplacerObjet($v_iNouvPos);
	echo "<html>\n";
	echo "<head>\n";
	echo "<script language=\"javascript\" src=\"selectionobj.js\" type=\"text/javascript\"></script>";
	echo "<script language=\"javascript\">\n";
	echo "rechargerlistepopup($v_iIdObjForm,$v_iIdFormulaire)\n";
	echo "window.close();\n";
	echo "</script>\n";
	echo "</head>\n";
	echo "<body>\n";
	echo "</body>\n";
	echo "</html>\n";
}
else
{
	$oObjetFormulaire = $oObjetFormulaire = new CObjetFormulaire($oProjet->oBdd,$v_iIdObjForm);
	$iOrdreObjFormDepart = $oObjetFormulaire->retOrdreObjForm();
	$sRequeteSql = "SELECT * FROM ObjetFormulaire"
				." WHERE IdForm='$v_iIdFormulaire' order by OrdreObjForm";
	$hResult = $oProjet->oBdd->executerRequete($sRequeteSql);
	$oTpl = new Template("position_objet.tpl");
	
	$oBlock = new TPL_Block("BLOCK_POSITION",$oTpl);
	  
	if (TRUE)
	{
		$oBlock->beginLoop();
				 
		while ($oEnreg = $oProjet->oBdd->retEnregSuiv($hResult))
		{
			$oBlock->nextLoop();
			 
			$oObjetFormulaire = new CObjetFormulaire($oProjet->oBdd);
			$oObjetFormulaire->init($oEnreg);
			$iOrdreObjForm = $oObjetFormulaire->retOrdreObjForm();
			$oBlock->remplacer("{ordre_obj_form}",$oObjetFormulaire->retOrdreObjForm());
			if ($iOrdreObjForm == $iOrdreObjFormDepart)
			{
				$oBlock->remplacer("{obj_actuel}","SELECTED");
			}
			else
			{
				$oBlock->remplacer("{obj_actuel}","");
			}
		}
				 
		$oBlock->afficher();
	}
	else
	{
		$oBlock->effacer();
	}
	  
	$oTpl->remplacer("{id_formulaire}",$v_iIdFormulaire);
	$oTpl->remplacer("{id_obj}",$v_iIdObjForm);
	$oTpl->afficher();	  
	$oProjet->oBdd->libererResult($hResult);
}
$oProjet->terminer();
?>
