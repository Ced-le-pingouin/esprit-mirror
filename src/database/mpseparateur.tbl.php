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

/*
** Fichier ................: mpseparateur.tbl.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 22-06-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/

class CMPSeparateur 
{
 var $oBdd;
 var $iId;
 var $oEnregBdd;
 var $aoFormulaire;

 function CMPSeparateur(&$v_oBdd,$v_iId=0) 
 {
   			$this->oBdd = &$v_oBdd;  
								  //si 0 crée un objet presque vide sinon 
								  //rempli l'objet avec les données de la table MPSEPARATEUR
								  //de l'elément ayant l'Id passé en argument 
								  //(ou avec l'objet passé en argument mais sans passer par le constructeur)
		if (($this->iId = $v_iId) > 0)
			$this->init();
 }
	
	//INIT est une fonction que l'on peut utiliser sans passer par le constructeur. 
	//On lui passe alors un objet obtenu par exemple en faisant une requête sur une autre page.
	//Ceci permet alors d'utiliser toutes les fonctions disponibles sur cet objet
 function init ($v_oEnregExistant=NULL)  
 {
	    if (isset($v_oEnregExistant))
	    {
				 $this->oEnregBdd = $v_oEnregExistant;
	    }
	    else
	    {
			$sRequeteSql = "SELECT *"
				." FROM MPSeparateur"
				." WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
	    }

		$this->iId = $this->oEnregBdd->IdObjForm;
 }

 function ajouter ($v_iIdObjForm) //Cette fonction ajoute une ligne de type séparateur,
				 // avec tous ses champs vide, en fin de table
 {
   $sRequeteSql = "INSERT INTO MPSeparateur SET IdObjForm='{$v_iIdObjForm}'";
   $this->oBdd->executerRequete($sRequeteSql);
   return ($this->iId = $this->oBdd->retDernierId());
 }


//Fonctions de définition

 function defIdObjForm ($v_iIdObjForm)
{
  $this->oEnregBdd->IdObjForm = $v_iIdObjForm;
}

function defLargeurMPS ($v_iLargeurMPS)
{
  $this->oEnregBdd->LargeurMPS = $v_iLargeurMPS;
}

function defTypeLargMPS ($v_sTypeLargMPS)
{
  $this->oEnregBdd->TypeLargMPS = $v_sTypeLargMPS;
}

function defAlignMPS ($v_sAlignMPS)
{
  $this->oEnregBdd->AlignMPS = $v_sAlignMPS;
}

//Fonctions de retour

function retId () { return $this->oEnregBdd->IdObjForm; }
function retLargeurMPS () { return $this->oEnregBdd->LargeurMPS; }
function retTypeLargMPS () { return $this->oEnregBdd->TypeLargMPS; }
function retAlignMPS () { return $this->oEnregBdd->AlignMPS; }


function cHtmlMPSeparateur()
{
	if ($this->oEnregBdd->TypeLargMPS=="P")					//ajoute % ou px a la largeur pour ainsi créer une chaine de car
	{
	   $sLargeur=$this->oEnregBdd->LargeurMPS."%";
	}
	else												//se test est peut etre à deplacer car il a l'air a l'origine d'un certain ralentissement
	{
	   $sLargeur=$this->oEnregBdd->LargeurMPS."px";
	}
	
	
	//Genération du code html représentant l'objet
	$sCodeHtml="<hr width=$sLargeur size=\"2\" align={$this->oEnregBdd->AlignMPS}>";
	//<hr style="color: rgb(0,255,0); background-color: rgb(0,255,0); border: none; width: 250px; height: 5px;" align="right">
	return $sCodeHtml;	
}


function cHtmlMPSeparateurModif($v_iIdObjForm,$v_iIdFormulaire)
	{
	global $HTTP_POST_VARS, $HTTP_GET_VARS;
	
	//initialisation des messages d'erreurs à 'vide' et de la variable servant a detecter
	//si une erreur dans le remplissage du formulaire a eu lieu (ce qui engendre le non enregistrement
	//de celui-ci dans la base de données + affiche d'une astérisque à l'endroit de l'erreur)
	$sMessageErreur1="";
	$iFlagErreur=0;
	
	if (isset($HTTP_POST_VARS['envoyer'])) 
		{
			   //Récupération des variables transmises par le formulaire
			   $this->oEnregBdd->LargeurMPS = $HTTP_POST_VARS['Largeur'];
			   $this->oEnregBdd->TypeLargMPS = $HTTP_POST_VARS['TypeLarg'];
			   $this->oEnregBdd->AlignMPS = $HTTP_POST_VARS['Align'];
				
			   //Test des données reçues et marquage des erreurs à l'aide d'une astérisque dans le formulaire
			   if (!(int)$_POST['Largeur']) { $sMessageErreur1="<font color =\"red\">*</font>"; $iFlagErreur=1;}
			   
			   if ($iFlagErreur == 0)
					{		$this->enregistrer();
					  		echo "<script>\n";
							echo "rechargerliste($v_iIdObjForm,$v_iIdFormulaire)\n";
						  	echo "</script>\n";
					} //si pas d'erreur, enregistrement physique
		}
	
	
	
	//La fonction alignement renvoie 1 variables de type string contenant "CHECKED" 
	//et les 7 autres contiennent une chaîne vide
	list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = 
		Alignement($this->oEnregBdd->AlignMPS," ");
	
	
	$sAR1= $sAR2= "";
		  if ($this->oEnregBdd->TypeLargMPS=="P")
		  {
		  $sAR1="CHECKED";
		  }
		  else
		  {
		  $sAR2="CHECKED";
		  }
	
	
	$sParam="?idobj=".$v_iIdObjForm."&idformulaire=".$v_iIdFormulaire;
	
	$sCodeHtml ="<form action=\"{$_SERVER['PHP_SELF']}$sParam\" name=\"formmodif\" method=\"POST\" enctype=\"text/html\">"
		   ."<fieldset><legend><b>Mise en page de type \"séparateur\"</b></legend>"
		   ."<TABLE>"
		   ."<TR>"
		   ."<TD>$sMessageErreur1 Largeur :</TD>"
		   ."<TD><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"Largeur\" Value=\"{$this->oEnregBdd->LargeurMPS}\" onblur=\"verifNumeric(this)\">"
		   ."&nbsp <INPUT TYPE=\"radio\" NAME=\"TypeLarg\" VALUE=\"P\" $sAR1>pourcents"
		   ."<INPUT TYPE=\"radio\" NAME=\"TypeLarg\" VALUE=\"N\" $sAR2>pixels"
		   ."</TD>"
			."</TR>"
		   ."<TR>"
		   ."<TD>Alignement :</TD>"
		   ."<TD><INPUT TYPE=\"radio\" NAME=\"Align\" VALUE=\"left\" $ae1>Gauche"
		   ."<INPUT TYPE=\"radio\" NAME=\"Align\" VALUE=\"right\" $ae2>Droite"
		   ."<INPUT TYPE=\"radio\" NAME=\"Align\" VALUE=\"center\" $ae3>Centrer"
		   ."<INPUT TYPE=\"radio\" NAME=\"Align\" VALUE=\"justify\" $ae4>Justifier"
		   ."</TD>"
		   ."</TR>"
		   ."</TABLE>"
		   ."</fieldset>"
		   //Le champ caché ci-dessous "simule" le fait d'appuyer sur le bouton submit (qui s'appelait envoyer) et ainsi permettre l'enregistrement dans la BD
		   ."<input type=\"hidden\" name=\"envoyer\" value=\"1\">\n"
		   ."</form>";
	
	return $sCodeHtml;
	}	
	
function enregistrer ()  
	{
	if ($this->oEnregBdd->IdObjForm !=NULL)
	   {	
		
		$sRequeteSql = "REPLACE MPSeparateur SET"									  
			." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
			.", LargeurMPS='{$this->oEnregBdd->LargeurMPS}'"
			.", TypeLargMPS='{$this->oEnregBdd->TypeLargMPS}'"
			.", AlignMPS='{$this->oEnregBdd->AlignMPS}'";
			
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	   }
	else
	   {
	   Echo "Identifiant NULL enregistrement impossible";
	   }
	
	
	}


	function copier ($v_iIdNvObjForm)
	{
		if ($v_iIdNvObjForm < 1)
			return;
		
		$sRequeteSql = "INSERT INTO MPSeparateur SET"									  
			." IdObjForm='{$v_iIdNvObjForm}'"
			.", LargeurMPS='{$this->oEnregBdd->LargeurMPS}'"
			.", TypeLargMPS='{$this->oEnregBdd->TypeLargMPS}'"
			.", AlignMPS='{$this->oEnregBdd->AlignMPS}'";
			
		$this->oBdd->executerRequete($sRequeteSql);
		
		$iIdObjForm = $this->oBdd->retDernierId();
		
		return $iIdObjForm;
	}


function effacer ()
	{
		$sRequeteSql = "DELETE FROM MPSeparateur"
				." WHERE IdObjForm ='{$this->oEnregBdd->IdObjForm}'";
		//echo "<br>effacer MPSeparateur()".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
}

?>
