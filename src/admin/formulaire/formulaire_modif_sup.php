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
}
else if (isset($HTTP_POST_VARS))
{
	$v_iIdObjForm = $HTTP_POST_VARS['idobj'];
	$v_iIdFormulaire = $HTTP_POST_VARS['idformulaire'];
}
else
{
	$v_iIdObjForm = 0;
	$v_iIdFormulaire = 0;
}
	  
	 
	 //echo "<br>v_iIdTypeObj".$v_iIdTypeObj;
	 //echo "<br>v_iIdFormulaire : ".$v_iIdFormulaire;



echo "<html>\n";
echo "<head>\n";
echo "<script src=\"selectionobj.js\" type=\"text/javascript\">";
echo "</script>\n";

//CSS
echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".dir_theme("formulaire/formulaire.css")."\">";
//FIN CSS
echo "</head>\n";

echo "<body class=\"modif\">";

if ($v_iIdObjForm > 0)
{
			 		  
				$oObjetFormulaire = new CObjetFormulaire($oProjet->oBdd,$v_iIdObjForm);
				//echo $oObjetFormulaire->retIdObjForm();
					    
					  
				switch($oObjetFormulaire->retIdTypeObj())
							 {
							 case 1:
							 		$oQTexteLong = new CQTexteLong($oProjet->oBdd,$v_iIdObjForm);
									$oQTexteLong->effacer();
							 break;
							 case 2:
								 //echo "Objet de type 2<br>";
								 	$oQTexteCourt = new CQTexteCourt($oProjet->oBdd,$v_iIdObjForm);
									$oQTexteCourt->effacer();
									break;
							 case 3:
								 //echo "Objet de type 3<br>";
								 	$oQNombre = new CQNombre($oProjet->oBdd,$v_iIdObjForm);
									$oQNombre->effacer();
									break;
							 case 4:
								 //echo "Objet de type 4<br>";
									$oQListeDeroul = new CQListeDeroul($oProjet->oBdd,$v_iIdObjForm);
									//$oQListeDeroul->effacer();
									$oReponse = new CReponse($oProjet->oBdd);
									$oReponse->effacerRepPoidsObj($v_iIdObjForm);
									break;
							 case 5:
								 //echo "Objet de type 5<br>";
								 	$oQRadio = new CQRadio($oProjet->oBdd,$v_iIdObjForm);
									$oQRadio->effacer();
									$oReponse = new CReponse($oProjet->oBdd);
									$oReponse->effacerRepPoidsObj($v_iIdObjForm);						 
									break;
							 case 6:
								 //echo "Objet de type 6<br>";
									$oQCocher = new CQCocher($oProjet->oBdd,$v_iIdObjForm);
									$oQCocher->effacer();
									$oReponse = new CReponse($oProjet->oBdd);
									$oReponse->effacerRepPoidsObj($v_iIdObjForm);
							 
							 break;
							 case 7:
								 //echo "Objet de type 7<br>";
								 	$oMPTexte = new CMPTexte($oProjet->oBdd,$v_iIdObjForm);
									$oMPTexte->effacer();
									break;
							 case 8:
								 //echo "Objet de type 8<br>";
									$oMPSeparateur = new CMPSeparateur($oProjet->oBdd,$v_iIdObjForm);
									$oMPSeparateur->effacer();
									break;			   
							 default:
								 	echo "Numéro de l'objet a effacer incorrect<br>";
							 }
	  $oObjetFormulaire->effacer();
	  echo "<script>\n";
	  echo "rechargerliste(0,$v_iIdFormulaire)\n";
	  echo "</script>\n";
	
}
else
{
echo "Erreur impossible de supprimer l'objet";
}
echo "</body>\n";
echo "</html>\n";
?>
