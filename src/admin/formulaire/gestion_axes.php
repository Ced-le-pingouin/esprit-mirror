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
$oTpl = new Template("gestion_axes.tpl");

$oBlockSupp = new TPL_Block("BLOCK_SUPPRESSION",$oTpl); // formulaire de suppression d'un axe
$oBlockModif = new TPL_Block("BLOCK_MODIF",$oTpl); // formulaire de modification d'un axe
$oBlockAjout = new TPL_Block("BLOCK_AJOUT",$oTpl); // formulaire d'ajout d'un axe
$oBlockChoix = new TPL_Block("BLOCK_CHOIX",$oTpl); // menu
$oBlockLien = new TPL_Block("BLOCK_LIEN",$oTpl); // lien de validation des formulaires

if(isset($_GET['idformulaire']))
{
	$v_iIdFormulaire = $_GET['idformulaire'];
}
else
{
	if(isset($_POST['idformulaire']))
		$v_iIdFormulaire = $_POST['idformulaire'];
}
			$v_iIdAxeS = $_POST['axe_s'];
			$v_iIdAxeM = $_POST['axe_m'];
			$v_sDescAxeM = $_POST['axemodif'];
			$v_sDescAxeA = $_POST['axeajout'];

if(isset($_POST['axeajout'])) //ajout d'un axe dans la DB
{
	$oBlockSupp->effacer();
	$oBlockModif->effacer();
	$oBlockAjout->effacer();
	$oBlockChoix->effacer();
	$oBlockLien->effacer();

	if (strlen($v_sDescAxeA) > 0)
	{
		 $oAxe = new CAxe($oProjet->oBdd);
		 $oAxe->defDescAxe($v_sDescAxeA);
		 $oAxe->enregistrer();
		 $sMsg = "L'axe a  été correctement ajouté";
	}
	else
	{
		 $sMsg = "Le nom de l'axe n'est pas valide";
	}
	$oTpl->remplacer("{Message}",$sMsg);
}
else
{
	if(isset($_POST['axemodif'])) //modification d'un axe dans la DB
	{
		$oBlockSupp->effacer();
		$oBlockModif->effacer();
		$oBlockAjout->effacer();
		$oBlockChoix->effacer();
		$oBlockLien->effacer();
		
		if (strlen($v_sDescAxeM) > 0)
		{	  
			$oAxe = new CAxe($oProjet->oBdd,$v_iIdAxeM);
			$oAxe->defDescAxe($v_sDescAxeM);
			$oAxe->enregistrer();
			$sMsg = "Le nom de l'axe a été correctement modifié<br />";
			
			$sMsg.= $oAxe->verificationdependances();
			$oTpl->remplacer("{Message}",$sMsg);
		}
		else
		{
			 $oTpl->remplacer("{Message}","Le nom de l'axe n'est pas valide");
		}
	}
	else
	{
		if(isset($_POST['axe_s'])) // suppression d'un axe dans la DB
		{
			$oBlockSupp->effacer();
			$oBlockModif->effacer();
			$oBlockAjout->effacer();
			$oBlockChoix->effacer();
			$oBlockLien->effacer();
			$oAxe = new CAxe($oProjet->oBdd,$v_iIdAxeS);
			$amSortie = $oAxe->effacer(TRUE); //TRUE permet une vérification des dépendances avant effacement de l'axe
			$oTpl->remplacer("{Message}",$amSortie[1]);
		}
		else // pages d'affichage du menu ou des formulaires(ajout, supp et modification)
		{
			$oTpl->remplacer("{Message}","");
			switch($_GET['action'])
			{
				case "ajout":	$oBlockSupp->effacer();
								$oBlockModif->effacer();
								$oBlockAjout->afficher();
								$oBlockChoix->effacer();
								$oBlockLien->afficher();
								$oTpl->remplacer("{Titre_Lien}","Ajouter");
								break;
		
				case "modif":	$oBlockSupp->effacer();
								$oBlockModif->afficher();
								$oBlockAjout->effacer();
								$oBlockChoix->effacer();
								$oAxe = new CAxe($oProjet->oBdd); //Crée un objet objetformulaire "presque vide"
								$aoListeAxes = $oAxe->retListeAxes();
								$oBlock = new TPL_Block("BLOCK_AXES2",$oTpl);
								if (count($aoListeAxes)>0)
								{
									$oBlock->beginLoop();
									foreach ($aoListeAxes as $oEnreg)
									{
										$oBlock->nextLoop();
										$oAxe = new CAxe($oProjet->oBdd); //Crée un objet objetformulaire "presque vide"
										$oAxe->init($oEnreg); //Remplit l'objet créé ci-dessus avec l'enreg en cours
										$oBlock->remplacer("{id_axe2}",$oAxe->retId());
										$oBlock->remplacer("{desc_axe2js}",addslashes($oAxe->retDescAxe()));
										$oBlock->remplacer("{desc_axe2}",$oAxe->retDescAxe());
									}
									$oBlock->afficher();
									$oBlockLien->afficher();
									$oTpl->remplacer("{Titre_Lien}","Modifier");
								}
								else
								{
									$oBlock->effacer();
									$oBlockLien->effacer();
								}
								break;
		
				case "supp":	$oBlockSupp->afficher();
								$oBlockModif->effacer();
								$oBlockAjout->effacer();
								$oBlockChoix->effacer();
								$oAxe = new CAxe($oProjet->oBdd); //Crée un objet objetformulaire "presque vide"
								$aoListeAxes = $oAxe->retListeAxes();
								$oBlock = new TPL_Block("BLOCK_AXES",$oTpl);
								if (count($aoListeAxes)>0)
								{
									$oBlock->beginLoop();
									foreach ($aoListeAxes as $oEnreg)
									{
										$oBlock->nextLoop();
										$oAxe = new CAxe($oProjet->oBdd); //Crée un objet objetformulaire "presque vide"
										$oAxe->init($oEnreg); //Remplit l'objet créé ci-dessus avec l'enreg en cours
										$oBlock->remplacer("{id_axe}",$oAxe->retId());
										$oBlock->remplacer("{desc_axe}",$oAxe->retDescAxe());
									}
									$oBlock->afficher();
									$oBlockLien->afficher();
									$oTpl->remplacer("{Titre_Lien}","Supprimer");
								}
								else
								{
									$oBlock->effacer();
									$oBlockLien->effacer();
								}
								break;
		
				default:		$oBlockSupp->effacer();
								$oBlockModif->effacer();
								$oBlockAjout->effacer();
								$oBlockChoix->afficher();
								$oBlockLien->effacer();
			}
		}
	}
}
$oTpl->remplacer("{idformulaire}",$v_iIdFormulaire);	
$oTpl->afficher();
$oProjet->terminer();  //Ferme la connection avec la base de données
?>
