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
	$v_iIdFormulaire = $HTTP_GET_VARS['idformulaire'];
	$v_iIdAxeS = $HTTP_GET_VARS['axe_s'];
	$v_iIdAxeM = $HTTP_GET_VARS['axe_m'];
	$v_sDescAxeM = $HTTP_GET_VARS['axemodif'];
	$v_sDescAxeA = $HTTP_GET_VARS['axeajout'];
}
else if (isset($HTTP_POST_VARS))
{
	$v_iIdFormulaire = $HTTP_POST_VARS['idformulaire'];
	$v_iIdAxeS = $HTTP_POST_VARS['axe_s'];
	$v_iIdAxeM = $HTTP_POST_VARS['axe_m'];
	$v_sDescAxeM = $HTTP_POST_VARS['axemodif'];
	$v_sDescAxeA = $HTTP_POST_VARS['axeajout'];
}
else
{
	$v_iIdFormulaire = 0;
	$v_iIdAxeS = 0;
	$v_iIdAxeM = 0;
	$v_sDescAxeM = "";
	$v_sDescAxeA = "";
}


if (isset($HTTP_GET_VARS['supprimer']))
{
	  echo "<html>\n";
	  echo "<head>\n";
	  echo "<TITLE>Gestion des Axes/Tendances</TITLE>";
	  //CSS
	  echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".dir_theme("formulaire/formulaire.css")."\">";
	  //FIN CSS
	  echo "</head>\n";
	  echo "<body class=\"popup\">";
	  
	  $oAxe = new CAxe($oProjet->oBdd,$v_iIdAxeS);
	  $oAxe->effacer(TRUE); //TRUE permet une vérification des dépendances avant effacement de l'axe
	  
	  echo "<p align=center><a href=\"gestion_axes.php\">Retour page précédente</a></p>";
	  echo "</body>\n";
	  echo "</html>\n";
}
else if (isset($HTTP_GET_VARS['modifier']))
		{
			  echo "<html>\n";
			  echo "<head>\n";
			  echo "<TITLE>Gestion des Axes/Tendances</TITLE>";
			  //CSS
			  echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".dir_theme("formulaire/formulaire.css")."\">";
			  //FIN CSS
			  echo "</head>\n";
			  echo "<body class=\"popup\">";
			  
			  if (strlen($v_sDescAxeM) > 0)
			  {	  
			  $oAxe = new CAxe($oProjet->oBdd,$v_iIdAxeM);
			  $oAxe->defDescAxe($v_sDescAxeM);
			  $oAxe->enregistrer();
			  echo "<h4 align=\"center\"><br>Le nom de l'axe a été correctement modifié</h4>";
			  
			  $oAxe->verificationdependances();
			  }
			  else
			  {
					 echo "<h4 align=\"center\"><br>Le nom de l'axe n'est pas valide</h4>";
			  }
			  
		  	  echo "<p align=center><a href=\"gestion_axes.php\">Retour page précédente</a></p>";			  
			  echo "</body>\n";
			  echo "</html>\n";
		}
		
		else if (isset($HTTP_GET_VARS['ajouter']))
				{
					  echo "<html>\n";
					  echo "<head>\n";
					  echo "<TITLE>Gestion des Axes/Tendances</TITLE>";
					  echo "<script src=\"selectionobj.js\" type=\"text/javascript\">";
					  echo "</script>\n";
					  //CSS
					  echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"".dir_theme("formulaire/formulaire.css")."\">";
					  //FIN CSS
					  echo "</head>\n";
					  echo "<body class=\"popup\">";
					  
					   if (strlen($v_sDescAxeA) > 0)
						{
							 $oAxe = new CAxe($oProjet->oBdd);
							 $oAxe->defDescAxe($v_sDescAxeA);
							 $oAxe->enregistrer();
							
							 echo "<h4 align=\"center\"><br>L'axe a  été correctement ajouté</h4>";
						}
						else
						{
							  echo "<h4 align=\"center\"><br>Le nom de l'axe n'est pas valide</h4>";
						}
					  
					  echo "<p align=center><a href=\"gestion_axes.php\">Retour page précédente</a></p>";
					  echo "</body>\n";
					  echo "</html>\n";
				}
				
				else // premier chargement de la page
					{
						  $hResult = $oProjet->oBdd->executerRequete("SELECT * FROM Axe order by IdAxe");
						  
						  $oTpl = new Template("gestion_axes.tpl");
						  
						  $oBlock = new TPL_Block("BLOCK_AXES",$oTpl);
						  
								 if(TRUE)
								 {
									 $oBlock->beginLoop();
									 
									 while ($oEnreg = $oProjet->oBdd->retEnregSuiv($hResult))
									 {
										 $oBlock->nextLoop();
										 $oAxe = new CAxe($oProjet->oBdd); //Crée un objet objetformulaire "presque vide"
										 $oAxe->init($oEnreg); //Remplit l'objet créé ci-dessus avec l'enreg en cours
					
										 $oBlock->remplacer("{id_axe}",$oAxe->retId());
										 $oBlock->remplacer("{desc_axe}",$oAxe->retDescAxe());
										 
										
										 
									 }
									 
									 $oBlock->afficher();
								 }
								 else
								 {
									 $oBlock->effacer();
								 }
						  
						  $hResult = $oProjet->oBdd->executerRequete("SELECT * FROM Axe order by IdAxe");
						  $oBlock = new TPL_Block("BLOCK_AXES2",$oTpl);
						  
								 if(TRUE)
								 {
									 $oBlock->beginLoop();
									 
									 while ($oEnreg = $oProjet->oBdd->retEnregSuiv($hResult))
									 {
										 $oBlock->nextLoop();
										 $oAxe = new CAxe($oProjet->oBdd); //Crée un objet objetformulaire "presque vide"
										 $oAxe->init($oEnreg); //Remplit l'objet créé ci-dessus avec l'enreg en cours
					
										 $oBlock->remplacer("{id_axe2}",$oAxe->retId());
										 $oBlock->remplacer("{desc_axe2js}",addslashes($oAxe->retDescAxe()));
										 $oBlock->remplacer("{desc_axe2}",$oAxe->retDescAxe());
										
										 
									 }
									 
									 $oBlock->afficher();
								 }
								 else
								 {
									 $oBlock->effacer();
								 }
						  
						  
					
						  $oTpl->afficher();	  
						  $oProjet->oBdd->libererResult($hResult);
						  $oProjet->terminer();  //Ferme la connection avec la base de données
					 }

?>
