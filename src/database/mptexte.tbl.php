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
** Fichier ................: mptexte.tbl.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 22-06-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/

class CMPTexte 
{
 var $oBdd;
 var $iId;
 var $oEnregBdd;
 var $aoFormulaire;

 function CMPTexte(&$v_oBdd,$v_iId=0) 
 {
   			$this->oBdd = &$v_oBdd;  
								  //si 0 crée un objet presque vide sinon 
								  //rempli l'objet avec les données de la table MPTEXTE
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
				." FROM MPTexte"
				." WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
	    }

		$this->iId = $this->oEnregBdd->IdObjForm;
 }

 function ajouter ($v_iIdObjForm) //Cette fonction ajoute une mise en page de type texte,
				 // avec tous ses champs vide, en fin de table
 {
   $sRequeteSql = "INSERT INTO MPTexte SET IdObjForm='{$v_iIdObjForm}'";
   $this->oBdd->executerRequete($sRequeteSql);
   return ($this->iId = $this->oBdd->retDernierId());
 }


//Fonctions de définition

 function defIdObjForm ($v_iIdObjForm)
{
  $this->oEnregBdd->IdObjForm = $v_iIdObjForm;
}

function defTexteMPT ($v_sTexteMPT)
{
  $this->oEnregBdd->TexteMPT = $v_sTexteMPT;
}

function defAlignMPT ($v_sAlignMPT)
{
  $this->oEnregBdd->AlignMPT = $v_sAlignMPT;
}

//Fonctions de retour

function retId () { return $this->oEnregBdd->IdObjForm; }
function retTexteMPT () { return $this->oEnregBdd->TexteMPT; }
function retAlignMPT () { return $this->oEnregBdd->AligntMPT; }


function cHtmlMPTexte()
{
	//Mise en page du texte
	$this->oEnregBdd->TexteMPT = convertBaliseMetaVersHtml($this->oEnregBdd->TexteMPT);
	
	//Genération du code html représentant l'objet
	$sCodeHtml="<div align={$this->oEnregBdd->AlignMPT}>{$this->oEnregBdd->TexteMPT}</div>";
	return $sCodeHtml;	
}




function cHtmlMPTexteModif($v_iIdObjForm,$v_iIdFormulaire)
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
			   $this->oEnregBdd->AlignMPT = $HTTP_POST_VARS['Align'];
			   $this->oEnregBdd->TexteMPT = stripslashes($HTTP_POST_VARS['Texte']);
			   
			   //Test des données reçues et marquage des erreurs à l'aide d'une astérisque dans le formulaire
			   if ($this->oEnregBdd->TexteMPT == "") { $sMessageErreur1="<font color =\"red\">*</font>"; $iFlagErreur=1;}
			   
			   if ($iFlagErreur == 0) 
					{		$this->enregistrer();
					  		echo "<script>\n";
							echo "rechargerliste($v_iIdObjForm,$v_iIdFormulaire)\n";
						  	echo "</script>\n";
					} //si pas d'erreur, enregistrement physique
		}
	
	
	//La fonction alignement renvoie 1 variable de type string contenant "CHECKED" 
	//et les 7 autres contiennent une chaîne vide
	list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = 
		Alignement($this->oEnregBdd->AlignMPT," ");
	
	
	$sParam="?idobj=".$v_iIdObjForm."&idformulaire=".$v_iIdFormulaire;
	
	$sCodeHtml ="<form action=\"{$_SERVER['PHP_SELF']}$sParam\" name=\"formmodif\" method=\"POST\" enctype=\"text/html\">"
		   ."<fieldset><legend><b>Mise en page de type \"texte\"</b></legend>"
		   ."<TABLE>"
		   ."<TR>"
		   ."<TD>$sMessageErreur1 Texte :</TD>"
		   ."<TD><textarea name=\"Texte\" rows=\"5\" cols=\"70\">{$this->oEnregBdd->TexteMPT}</textarea></TD>"
		   ."</TR>\n"
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
		// Les variables contenant du "texte" doivent être formatées, cela permet 
		//de les stocker dans la BD sans erreur 
		$sTexteMPT = validerTexte($this->oEnregBdd->TexteMPT);
		
		$sRequeteSql = "REPLACE MPTexte SET"									  
			." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
			.", TexteMPT='{$sTexteMPT}'"
			.", AlignMPT='{$this->oEnregBdd->AlignMPT}'";
		
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	   }
	else
	   {
	   echo "Identifiant NULL enregistrement impossible";
	   }
	}


function copier ($v_iIdNvObjForm)
	{
		if ($v_iIdNvObjForm < 1)
			return;
		
		// Les variables contenant du "texte" doivent être formatées, cela permet 
		//de les stocker dans la BD sans erreur 
		$sTexteMPT = validerTexte($this->oEnregBdd->TexteMPT);
		
		$sRequeteSql = "INSERT INTO MPTexte SET"									  
			." IdObjForm='{$v_iIdNvObjForm}'"
			.", TexteMPT='{$sTexteMPT}'"
			.", AlignMPT='{$this->oEnregBdd->AlignMPT}'"; 
			
		$this->oBdd->executerRequete($sRequeteSql);
		
		$iIdObjForm = $this->oBdd->retDernierId();
		
		return $iIdObjForm;
	}


function effacer ()
	{
		$sRequeteSql = "DELETE FROM MPTexte"
				." WHERE IdObjForm ='{$this->oEnregBdd->IdObjForm}'";
		//echo "<br>effacer MPTexte()".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
}

?>
