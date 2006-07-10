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

class CQCocher
{
 var $oBdd;
 var $iId;
 var $oEnregBdd;
 var $aoFormulaire;


 	/*
	** Fonction 	: CQCocher
	** Description	: constructeur
	** Entrée		: 
	**	 		&$v_oBdd : référence de l'objet Bdd appartenant a l'objet Projet
	**			$v_iId : identifiant d'un objet question de type "case à cocher"
	** Sortie		: 
	*/

 function CQCocher(&$v_oBdd,$v_iId=0) 
 {
   			$this->oBdd = &$v_oBdd;  
			
		if (($this->iId = $v_iId) > 0)
			$this->init();
 }

 	/*
	** Fonction 	: init
	** Description	: permet d'initialiser l'objet QCocher soit en lui passant
	**					  un enregistrement provenant de la BD, soit en effectuant 
	**					  directement une requête dans la BD avec 
	**                	  l'id passé via la constructeur
	** Entrée		:
	**			$v_oEnregExistant=NULL : enregistrement représentant une question 
	**			de type "case à cocher"
	** Sortie		: 
	*/

 function init ($v_oEnregExistant=NULL)  
 {
	    if (isset($v_oEnregExistant))
	    {
				 $this->oEnregBdd = $v_oEnregExistant;
	    }
	    else
	    {
			$sRequeteSql = "SELECT *"
				." FROM QCocher"
				." WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
	    }

		$this->iId = $this->oEnregBdd->IdObjForm;
 }


 	/*
	** Fonction 	: ajouter
	** Description	: crée un enregistrement dans la table QCocher en initialisant l'ID
	** Entrée		:
	** Sortie		: Id renvoyé par la BD
	*/

 function ajouter ($v_iIdObjForm) //Cette fonction ajoute une question de type checkbox,
				 // avec tous ses champs vide, en fin de table
 {
   $sRequeteSql = "INSERT INTO QCocher SET IdObjForm='{$v_iIdObjForm}'";
   $this->oBdd->executerRequete($sRequeteSql);
   return ($this->iId = $this->oBdd->retDernierId());
 }


 	/* Fonctions de définition */

 function defIdObjForm ($v_iIdObjForm)
{
  $this->oEnregBdd->IdObjForm = $v_iIdObjForm;
}

 function defEnonQC ($v_sEnonQC)
{
  $this->oEnregBdd->EnonQC = $v_sEnonQC;
}

function defAlignEnonQC ($v_sAlignEnonQC)
{
  $this->oEnregBdd->AlignEnonQC = $v_sAlignEnonQC;
}

function defAlignRepQC ($v_sAlignRepQC)
{
  $this->oEnregBdd->AlignRepQC = $v_sAlignRepQC;
}

function defTxtAvQC ($v_sTxtAvQC)
{
  $this->oEnregBdd->TxtAvQC = $v_sTxtAvQC;
}

function defTxtApQC ($v_sTxtApQC)
{
  $this->oEnregBdd->TxtApQC = $v_sTxtApQC;
}

function defDispQC ($v_sDispQC)
{
  $this->oEnregBdd->DispQR = $v_sDispQR;
}

function defNbRepMaxQC ($v_iNbRepMaxQC)
{
  $this->oEnregBdd->NbRepMaxQC = $v_iNbRepMaxQC;
}

function defMessMaxQC ($v_sMessMaxQC)
{
  $this->oEnregBdd->MessMaxQC = $v_sMessMaxQC;
}


	/* Fonctions de retour */

function retId () { return $this->oEnregBdd->IdReponse; }
function retEnonQC () { return $this->oEnregBdd->EnonQC; }
function retAlignEnonQC () { return $this->oEnregBdd->AlignEnonQC; }
function retAlignRepQC () { return $this->oEnregBdd->AlignRepQC; }
function retTxTAvQC () { return $this->oEnregBdd->TxtAvQC; }
function retTxtApQC () { return $this->oEnregBdd->TxtApQC; }
function retDispQC () { return $this->oEnregBdd->DispQC; }
function retNbRepMaxQC () {return $this->oEnregBdd->NbRepMaxQC; }
function retMessMaxQC () { return $this->oEnregBdd->MessMaxQC; }


	/*
	** Fonction 	: RetourReponseQC
	** Description	: va rechercher dans la table réponse les réponses correspondant
	**		a la question de type checkbox en cours de traitement 
	**		+ mise en page de ces réponses (affichage horizontal ou vertical)
	** Entrée		:
	**		$NbRepMaxQCTemp : nombre de maximum de réponses que l'on peut cocher
	**		$MessMaxQCTemp : message d'erreur lorsque l'utilisateur coche trop de cases
	** Sortie		: Code Html contenant les réponses et leur mise en page
	*/

function RetourReponseQC($NbRepMaxQCTemp,$MessMaxQCTemp)
{

//Sélection de toutes les réponses concernant l'objet QRadio en cours de traitement
$sRequeteSql = "SELECT * FROM Reponse WHERE IdObjForm = '{$this->iId}'"
			." ORDER BY OrdreReponse";
$hResultRRQC = $this->oBdd->executerRequete($sRequeteSql);


if ($this->oEnregBdd->DispQC == 'Ver')
{
$CodeHtml="<TABLE cellspacing=\"0\" cellpadding=\"0\">";

	while ($oEnreg = $this->oBdd->retEnregSuiv($hResultRRQC))
	{
	$oReponse = new CReponse($this->oBdd);
	$oReponse->init($oEnreg);
	
	//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
	$TexteTemp = $oReponse->retTexteReponse();
	$TexteTemp = convertBaliseMetaVersHtml($TexteTemp);
	$IdReponseTemp = $oReponse->retId();
	$IdObjFormTemp = $oReponse->retIdObjForm();
	$IdObjFormTemp = $IdObjFormTemp."[]"; 
		//utilise un tableau pour stocker les differents résultats possibles
	
	$CodeHtml.="<TR><TD><INPUT TYPE=\"checkbox\" NAME=\"$IdObjFormTemp\" "
	."VALUE=\"$IdReponseTemp\" onclick=\"verifNbQcocher($NbRepMaxQCTemp,'$MessMaxQCTemp')\"></TD><TD>$TexteTemp</TD></TR>\n";
	}
$CodeHtml.="</TABLE>";
}
else
	  {
	  $CodeHtml="";
	  
		  while ($oEnreg = $this->oBdd->retEnregSuiv($hResultRRQC))
		  {
		  $oReponse = new CReponse($this->oBdd);
		  $oReponse->init($oEnreg);
		  
		  //Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
		  $TexteTemp = $oReponse->retTexteReponse();
		  $TexteTemp = convertBaliseMetaVersHtml($TexteTemp);
		  $IdReponseTemp = $oReponse->retId();
		  $IdObjFormTemp = $oReponse->retIdObjForm();
		  $IdObjFormTemp = $IdObjFormTemp."[]"; 
		  		//utilise un tableau pour stocker les differents résultats possibles
		  
		  $CodeHtml.="<INPUT TYPE=\"checkbox\" NAME=\"$IdObjFormTemp\" "
				  ."VALUE=\"$IdReponseTemp\" onclick=\"verifNbQocher('$this->oEnregBdd->NbRepMaxQC','$this->oEnregBdd->MessMaxQC')\">$TexteTemp\n";
		  }
	  }
	
$this->oBdd->libererResult($hResultRRQC);
return "$CodeHtml";
}


function cHtmlQCocher()
	{
	//Mise en forme du texte (ex: remplacement de [b][/b] par le code html adéquat)
	$this->oEnregBdd->EnonQC = convertBaliseMetaVersHtml($this->oEnregBdd->EnonQC);
	
	
	//Si alignement vertical alors suppression des textes av et ap sinon mise en forme
	if ($this->oEnregBdd->DispQC == 'Ver')
		{
			$this->oEnregBdd->TxtAvQC = "";
			$this->oEnregBdd->TxtApQC = "";
		}
	else
		{
			$this->oEnregBdd->TxtAvQC = convertBaliseMetaVersHtml($this->oEnregBdd->TxtAvQC);
			$this->oEnregBdd->TxtApQC = convertBaliseMetaVersHtml($this->oEnregBdd->TxtApQC);
		}
	
	//Genération du code html représentant l'objet
	$sCodeHtml="\n<!--QCocher : {$this->oEnregBdd->IdObjForm} -->\n"
		."<div align={$this->oEnregBdd->AlignEnonQC}>{$this->oEnregBdd->EnonQC}</div>\n"
		."<div class=\"InterER\" align={$this->oEnregBdd->AlignRepQC}>\n"
		."{$this->oEnregBdd->TxtAvQC} \n"
		.$this->RetourReponseQC($this->oEnregBdd->NbRepMaxQC,$this->oEnregBdd->MessMaxQC)
		." {$this->oEnregBdd->TxtApQC}\n"
		."</div>\n";
	
	return $sCodeHtml;
	}


	/*
	** Fonction 		: RetourReponseQCModif
	** Description		: va rechercher dans la table réponse les réponses correspondant
	**				  a la question de type case à cocher en cours de traitement 
	**				  + mise en page de ces réponses avec possibilité de modification
	** Entrée			:
	** Sortie			: Code Html contenant les réponses + mise en page 
	**				  + modification possible
	*/

function RetourReponseQCModif($v_iIdObjForm,$v_iIdFormulaire)
{
//Sélection de toutes les réponses concernant l'objet QRadio en cours de traitement
$sRequeteSql = "SELECT * FROM Reponse WHERE IdObjForm = '{$this->iId}'"
			." ORDER BY OrdreReponse";

$hResultRRQCM = $this->oBdd->executerRequete($sRequeteSql);

$CodeHtml="";

	while ($oEnreg = $this->oBdd->retEnregSuiv($hResultRRQCM))
	{
	$oReponse = new CReponse($this->oBdd);
	$oReponse->init($oEnreg);
	
	//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
	$TexteTemp = $oReponse->retTexteReponse();
	$IdReponseTemp = $oReponse->retId();
	$IdObjFormTemp = $oReponse->retIdObjForm();
	
		  if ($CodeHtml =="")
		  {
			  
			  $CodeHtml.="<TD><input type=\"text\" size=\"70\" maxlength=\"255\" "
				  ."name=\"rep[$IdReponseTemp]\" Value=\"$TexteTemp\">\n"
				  ."<a href=\"javascript: soumettre('supprimer',$IdReponseTemp);\">"
				  ."Supprimer</a><br></TD></TR>\n"
				  .RetourPoidsReponse($v_iIdFormulaire,$v_iIdObjForm,$IdReponseTemp); 
				  		//cette fc se trouve dans le fichier fonctions_form.inc.php
		  }
		  else
		  {
			  $CodeHtml.="<TR><TD></TD><TD><input type=\"text\" size=\"70\"" 
			  	  ."maxlength=\"255\" "
				  ."name=\"rep[$IdReponseTemp]\" Value=\"$TexteTemp\">\n"
				  ."<a href=\"javascript: soumettre('supprimer',$IdReponseTemp);\">"
				  ."Supprimer</a><br></TD></TR>\n"
				  .RetourPoidsReponse($v_iIdFormulaire,$v_iIdObjForm,$IdReponseTemp); 
				  		//cette fc se trouve dans le fichier fonctions_form.inc.php
		  }
	
	}
$this->oBdd->libererResult($hResultRRQCM);
return "$CodeHtml";
}


	/*
	** Fonction 	: cHtmlQCocherModif
	** Description	: renvoie le code html qui permet de modifier les caractéristiques 
	**		 d'une question de type "case à cocher", vérifie les données transmises 
	**	  	 par l'utilisateur afin de permettre un enregistrement ultérieur dans la BD
	** Entrée			:
	**				$v_iIdObjForm
	**				$v_iIdFormulaire
	** Sortie			:
	*/

function cHtmlQCocherModif($v_iIdObjForm,$v_iIdFormulaire)
{
	$sMessageErreur2 = "";
	$iFlagErreur=0;
	
	if (isset($_POST['envoyer']) || $_POST['typeaction']=='ajouter' || $_POST['typeaction']=='supprimer')
		{
			//Récupération des variables transmises par le formulaire
			$this->oEnregBdd->EnonQC = stripslashes($_POST['Enonce']);
			$this->oEnregBdd->AlignEnonQC = $_POST['AlignEnon'];
			$this->oEnregBdd->AlignRepQC = $_POST['AlignRep'];
			$this->oEnregBdd->TxtAvQC = stripslashes($_POST['TxtAv']);
			$this->oEnregBdd->TxtApQC = stripslashes($_POST['TxtAp']);
			$this->oEnregBdd->DispQC = $_POST['Disp'];
			$this->oEnregBdd->NbRepMaxQC = $_POST['NbRepMax'];		
			$this->oEnregBdd->MessMaxQC = $_POST['MessMax'];
			
			//Test des données reçues et marquage des erreurs(astérisque) dans le formulaire
			if (!(int)$_POST['NbRepMax']) 
				{ $sMessageErreur2="<font color =\"red\">*</font>"; $iFlagErreur=1;}
			
			if ($iFlagErreur == 0) //si pas d'erreur, enregistrement physique dans la BD
				{		
				   //Enregistrement des réponses et de leurs poids pour les différents axes
				   if (isset($_POST["rep"])) 	
					  {
					  foreach ($_POST["rep"] as $v_iIdReponse => $v_sTexteTemp) 
						{
							$oReponse = new CReponse($this->oBdd);
							$oReponse->defId($v_iIdReponse);
							
							$oReponse->defTexteReponse(stripslashes($v_sTexteTemp));
							$oReponse->enregistrer(FALSE);

							if (isset($_POST["repAxe"])) 	
									//Vérification pour ne pas effectuer le traitement si 
									//aucun axe n'est défini pour ce formulaire
								{
								   $tab = $_POST["repAxe"];
								   foreach ($tab[$v_iIdReponse] as $v_iIdAxe => $v_iPoids)
								   {
									   if (($v_iPoids != "") && (is_numeric($v_iPoids)))
									   		{
											//echo "<br>v_iPoids : ".$v_iPoids;
											$oReponse_Axe = new CReponse_Axe($this->oBdd);
											$oReponse_Axe->defIdReponse($v_iIdReponse);
											$oReponse_Axe->defIdAxe($v_iIdAxe);
											$oReponse_Axe->defPoids($v_iPoids);
											$oReponse_Axe->enregistrer();
											}
									}
								}
						}
					  }
							
				//Enregistrement de l'objet QCocher actuel dans la BD
				$this->enregistrer();
					  		
				//Lorsque la question est bien enregistrée dans la BD 
				//(Pour cela on a cliqué sur le bouton 'Appliquer les changements')
				//on rafraîchit la liste en cochant l'objet en cours de traitement
							
				echo "<script>\n";
				echo "rechargerliste($v_iIdObjForm,$v_iIdFormulaire)\n";
				echo "</script>\n";
				} 
		}
		
		
	//Si on a cliqué sur le lien 'Ajouter' cela affecte, via javascript, 
	//au champ caché ['typeaction']la valeur 'ajouter' et 
	//au champ caché ['parametre'] la valeur '0'.
	//Attention lorsque l'on clique sur le lien 'Ajouter' cela implique également 
	//un enregistrement d'office dans la BD des modifications déjà effectuées sur 
	//l'objet en cours. (avec les vérifications d'usage avant enregistrement dans la BD)
	if ($_POST['typeaction']=='ajouter')
		  {
			  $sRequeteSql = "SELECT MAX(OrdreReponse) AS OrdreMax "
			  ."FROM Reponse "
			  ."WHERE IdObjForm = '".$this->oEnregBdd->IdObjForm."'");
			  $hResultInt2 = $this->oBdd->executerRequete(
			  $oEnreg = $this->oBdd->retEnregSuiv($hResultInt2);
			  $iOrdreMax = $oEnreg->OrdreMax;
			  $iOrdreMax = $iOrdreMax + 1;
			  
			  $oReponse = new CReponse($this->oBdd);
			  $oReponse->defIdObjForm($v_iIdObjForm);
			  $oReponse->defOrdreReponse($iOrdreMax);
			  
			  /*
			  La réponse qui sera créée ici contiendra :
					le numero de l'objet auquel elle appartient
					l'ordre dans lequel elle sera affichée (toujours en dernière place)
					son numéro d'identifiant sera attribué automatiquement par MySql
			  le texte de la réponse sera attribué par après.
			  */
			  $oReponse->enregistrer();
			  $this->oBdd->libererResult($hResultInt2);
		  }
		  
	//Si on a cliqué sur le lien 'Supprimer' cela affecte, via javascript, 
	//au champ caché ['typeaction']la valeur 'supprimer' et 
	//au champ caché ['parametre'] l'id de la réponse a supprimer.
	//Attention lorsque l'on clique sur le lien 'supprimer' cela implique également 
	//un enregistrement d'office dans la BD des modifications déjà effectuées sur 
	//l'objet en cours.(avec les vérifications d'usage avant enregistrement dans la BD)
	if ($_POST['typeaction']=='supprimer')
		  {
			  //echo "<br>je suis passé par supprimer";
			  $v_iIdReponse = $_POST['parametre'];
			  $oReponse = new CReponse($this->oBdd,$v_iIdReponse);
			  $oReponse->effacer();
		  }
		  
		  
	//La fonction alignement renvoie 2 variables de type string contenant "CHECKED" 
	//et les 6 autres contiennent une chaîne vide
	// aeX = alignement enoncé, arX = alignement réponse
	list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = 
		Alignement($this->oEnregBdd->AlignEnonQC,$this->oEnregBdd->AlignRepQC);
	
	
	if ($this->oEnregBdd->DispQC == "Hor") { $d1 = "CHECKED"; }
		else if ($this->oEnregBdd->DispQC == "Ver") { $d2 = "CHECKED"; }
		else { $d2 = "CHECKED"; }
	  
	$sParam="?idobj=".$v_iIdObjForm."&idformulaire=".$v_iIdFormulaire;
	
	$sCodeHtml ="\n<form name=\"formmodif\" action=\"{$_SERVER['PHP_SELF']}$sParam\"  method=\"POST\" enctype=\"text/html\">\n"
		   ."<fieldset><legend><b>ENONCE</b></legend>\n"
		   ."<TABLE>\n"
		   ."<TR>\n"
		   ."<TD>$sMessageErreur1 Enoncé :</TD>\n"
		   ."<TD><textarea name=\"Enonce\" rows=\"5\" cols=\"70\">{$this->oEnregBdd->EnonQC}</textarea></TD>\n"
		   ."</TR>\n"
		   ."<TR>\n"
		   ."<TD>Alignement énoncé :</TD>\n"
		   ."<TD><INPUT TYPE=\"radio\" NAME=\"AlignEnon\" VALUE=\"left\" $ae1>Gauche\n"
		   ."<INPUT TYPE=\"radio\" NAME=\"AlignEnon\" VALUE=\"right\" $ae2>Droite\n"
		   ."<INPUT TYPE=\"radio\" NAME=\"AlignEnon\" VALUE=\"center\" $ae3>Centrer\n"
		   ."<INPUT TYPE=\"radio\" NAME=\"AlignEnon\" VALUE=\"justify\" $ae4>Justifier\n"
		   ."</TD>\n"
		   ."</TR>\n"
		   ."</TABLE>\n"
		   ."</fieldset>\n"
		   
		   ."<fieldset><legend><b>REPONSE</b></legend>\n"
		   ."<TABLE>\n"
		   ."<TR>\n"
		   ."<TD>Texte avant la réponse :</TD>\n"
		   ."<TD><input type=\"text\" size=\"70\" maxlength=\"254\" name=\"TxtAv\" Value=\"{$this->oEnregBdd->TxtAvQC}\"></TR>\n"
		   ."</TR><TR>\n"
		   ."<TD>Texte après la réponse :</TD>\n"
		   ."<TD><input type=\"text\" size=\"70\" maxlength=\"254\" name=\"TxtAp\" Value=\"{$this->oEnregBdd->TxtApQC}\"></TR>\n"
		   ."</TR><TR>\n"
		   ."<TD>Disposition :</TD>\n"
		   ."<TD><INPUT TYPE=\"radio\" NAME=\"Disp\" VALUE=\"Hor\" $d1>Horizontale\n"
		   ."<INPUT TYPE=\"radio\" NAME=\"Disp\" VALUE=\"Ver\" $d2>Verticale\n"
		   ."</TD>\n"
		   ."</TR><TR>\n"
		   ."<TD>Réponse(s) :\n"
		   ."<a href=\"javascript: soumettre('ajouter',0);\">Ajouter</a>\n"
		   ."</TD>\n"
		   .$this->RetourReponseQCModif($v_iIdObjForm,$v_iIdFormulaire) 
		   ."<TR>\n"
		   ."<TD>$sMessageErreur2 Nombre de réponses max :</TD>\n"
		   ."<TD><input type=\"text\" size=\"2\" maxlength=\"2\" name=\"NbRepMax\" Value=\"{$this->oEnregBdd->NbRepMaxQC}\" onblur=\"verifNumeric(this)\"></TR>\n"
		   ."</TR><TR>\n"
		   ."<TD>Message \"Maximum dépassé\"</TD>\n"
		   ."<TD><input type=\"text\" size=\"70\" maxlength=\"254\" name=\"MessMax\" Value=\"{$this->oEnregBdd->MessMaxQC}\"></TR>\n"
		   ."</TR>\n"
		   ."<TR>\n"
		   ."<TD>Alignement Réponse :</TD>\n"
		   ."<TD><INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"left\" $ar1>Gauche\n"
		   ."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"right\" $ar2>Droite\n"
		   ."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"center\" $ar3>Centrer\n"
		   ."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"justify\" $ar4>Justifier\n"
		   ."</TD>\n"
		   ."</TR>\n"
		   ."</TABLE>\n"
		   ."</fieldset>\n"
			."<INPUT TYPE=\"hidden\" NAME=\"typeaction\" VALUE=\"\">\n"
			."<INPUT TYPE=\"hidden\" NAME=\"parametre\" VALUE=\"\">\n"
		   //Le champ caché ci-dessous "simule" le fait d'appuyer sur le bouton submit 
		   //(qui s'appelait envoyer) et ainsi permettre l'enregistrement dans la BD
		   ."<input type=\"hidden\" name=\"envoyer\" value=\"1\">\n"
		   ."</form>\n";
	
	return $sCodeHtml;
}


	/*
	** Fonction 		: enregistrer
	** Description		: enregistre les données de l'objet courant dans la BD
	** Entrée			:
	** Sortie			:
	*/

function enregistrer ()
{
	if ($this->oEnregBdd->IdObjForm !=NULL)
	   {	
		// Les variables contenant du "texte" doivent être formatées, cela permet 
		//de les stocker dans la BD sans erreur 
		$sEnonQC = validerTexte($this->oEnregBdd->EnonQC);
		$sTxtAvQC = validerTexte($this->oEnregBdd->TxtAvQC);
		$sTxtApQC = validerTexte($this->oEnregBdd->TxtApQC);
		
		$sRequeteSql = "REPLACE QCocher SET"									  
			." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
			.", EnonQC='{$sEnonQC}'"
			.", AlignEnonQC='{$this->oEnregBdd->AlignEnonQC}'"
			.", AlignRepQC='{$this->oEnregBdd->AlignRepQC}'"
			.", TxtAvQC='{$sTxtAvQC}'"
			.", TxtApQC='{$sTxtApQC}'"
			.", DispQC='{$this->oEnregBdd->DispQC}'"
			.", NbRepMaxQC='{$this->oEnregBdd->NbRepMaxQC}'"
			.", MessMaxQC='{$this->oEnregBdd->MessMaxQC}'"; 		
			
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	   }
	else
	   {
	   Echo "Identifiant NULL enregistrement impossible";
	   }
}

	
	/*
	** Fonction 		: copier
	** Description		: permet de faire une copie de l'objet courant au sein
	**			
	** Entrée			:
	**				$v_iIdNvObjForm : identifiant du parent
	** Sortie			:
	**				$iIdObjForm : identifiant de la copie, renvoyé par la BD
	*/

function copier ($v_iIdNvObjForm)
{
	if ($v_iIdNvObjForm < 1)
		return;
		
	// Les variables contenant du "texte" doivent être formatées, cela permet 
	//de les stocker dans la BD sans erreur 
	$sEnonQC = validerTexte($this->oEnregBdd->EnonQC);
	$sTxtAvQC = validerTexte($this->oEnregBdd->TxtAvQC);
	$sTxtApQC = validerTexte($this->oEnregBdd->TxtApQC);
	
	$sRequeteSql = "INSERT INTO QCocher SET"									  
		." IdObjForm='{$v_iIdNvObjForm}'"
		.", EnonQC='{$sEnonQC}'"
		.", AlignEnonQC='{$this->oEnregBdd->AlignEnonQC}'"
		.", AlignRepQC='{$this->oEnregBdd->AlignRepQC}'"
		.", TxtAvQC='{$sTxtAvQC}'"
		.", TxtApQC='{$sTxtApQC}'"
		.", DispQC='{$this->oEnregBdd->DispQC}'"
		.", NbRepMaxQC='{$this->oEnregBdd->NbRepMaxQC}'"
		.", MessMaxQC='{$this->oEnregBdd->MessMaxQC}'";
		
	$this->oBdd->executerRequete($sRequeteSql);
	
	$iIdObjForm = $this->oBdd->retDernierId();
	
	return $iIdObjForm;
}


	/*
	** Fonction 		: effacer
	** Description		: efface de la BD l'enregistrement concernant l'objet courant
	** Entrée			:
	** Sortie			:
	*/

function effacer ()
{
	$sRequeteSql = "DELETE FROM QCocher"
			." WHERE IdObjForm ='{$this->oEnregBdd->IdObjForm}'";
	//echo "<br>effacer QCocher()".$sRequeteSql;
	$this->oBdd->executerRequete($sRequeteSql);
	
	return TRUE;
}

}

?>
