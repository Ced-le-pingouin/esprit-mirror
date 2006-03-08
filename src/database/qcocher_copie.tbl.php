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
	** Entr�e		: 
	**	 		&$v_oBdd : r�f�rence de l'objet Bdd appartenant a l'objet Projet
	**			$v_iId : identifiant d'un objet question de type "case � cocher"
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
	**					  directement une requ�te dans la BD avec 
	**                	  l'id pass� via la constructeur
	** Entr�e		:
	**			$v_oEnregExistant=NULL : enregistrement repr�sentant une question 
	**			de type "case � cocher"
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
	** Description	: cr�e un enregistrement dans la table QCocher en initialisant l'ID
	** Entr�e		:
	** Sortie		: Id renvoy� par la BD
	*/

 function ajouter ($v_iIdObjForm) //Cette fonction ajoute une question de type checkbox,
				 // avec tous ses champs vide, en fin de table
 {
   $sRequeteSql = "INSERT INTO QCocher SET IdObjForm='{$v_iIdObjForm}'";
   $this->oBdd->executerRequete($sRequeteSql);
   return ($this->iId = $this->oBdd->retDernierId());
 }


 	/* Fonctions de d�finition */

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
	** Description	: va rechercher dans la table r�ponse les r�ponses correspondant
	**		a la question de type checkbox en cours de traitement 
	**		+ mise en page de ces r�ponses (affichage horizontal ou vertical)
	** Entr�e		:
	**		$NbRepMaxQCTemp : nombre de maximum de r�ponses que l'on peut cocher
	**		$MessMaxQCTemp : message d'erreur lorsque l'utilisateur coche trop de cases
	** Sortie		: Code Html contenant les r�ponses et leur mise en page
	*/

function RetourReponseQC($NbRepMaxQCTemp,$MessMaxQCTemp)
{

//S�lection de toutes les r�ponses concernant l'objet QRadio en cours de traitement
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
		//utilise un tableau pour stocker les differents r�sultats possibles
	
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
		  		//utilise un tableau pour stocker les differents r�sultats possibles
		  
		  $CodeHtml.="<INPUT TYPE=\"checkbox\" NAME=\"$IdObjFormTemp\" "
				  ."VALUE=\"$IdReponseTemp\" onclick=\"verifNbQocher('$this->oEnregBdd->NbRepMaxQC','$this->oEnregBdd->MessMaxQC')\">$TexteTemp\n";
		  }
	  }
	
$this->oBdd->libererResult($hResultRRQC);
return "$CodeHtml";
}


function cHtmlQCocher()
	{
	//Mise en forme du texte (ex: remplacement de [b][/b] par le code html ad�quat)
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
	
	//Gen�ration du code html repr�sentant l'objet
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
	** Description		: va rechercher dans la table r�ponse les r�ponses correspondant
	**				  a la question de type case � cocher en cours de traitement 
	**				  + mise en page de ces r�ponses avec possibilit� de modification
	** Entr�e			:
	** Sortie			: Code Html contenant les r�ponses + mise en page 
	**				  + modification possible
	*/

function RetourReponseQCModif($v_iIdObjForm,$v_iIdFormulaire)
{
//S�lection de toutes les r�ponses concernant l'objet QRadio en cours de traitement
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
	** Description	: renvoie le code html qui permet de modifier les caract�ristiques 
	**		 d'une question de type "case � cocher", v�rifie les donn�es transmises 
	**	  	 par l'utilisateur afin de permettre un enregistrement ult�rieur dans la BD
	** Entr�e			:
	**				$v_iIdObjForm
	**				$v_iIdFormulaire
	** Sortie			:
	*/

function cHtmlQCocherModif($v_iIdObjForm,$v_iIdFormulaire)
{
	global $HTTP_POST_VARS, $HTTP_GET_VARS;

	$sMessageErreur2 = "";
	$iFlagErreur=0;
	
	if (isset($HTTP_POST_VARS['envoyer']) || $HTTP_POST_VARS['typeaction']=='ajouter' || $HTTP_POST_VARS['typeaction']=='supprimer')
		{
			//R�cup�ration des variables transmises par le formulaire
			$this->oEnregBdd->EnonQC = stripslashes($HTTP_POST_VARS['Enonce']);
			$this->oEnregBdd->AlignEnonQC = $HTTP_POST_VARS['AlignEnon'];
			$this->oEnregBdd->AlignRepQC = $HTTP_POST_VARS['AlignRep'];
			$this->oEnregBdd->TxtAvQC = stripslashes($HTTP_POST_VARS['TxtAv']);
			$this->oEnregBdd->TxtApQC = stripslashes($HTTP_POST_VARS['TxtAp']);
			$this->oEnregBdd->DispQC = $HTTP_POST_VARS['Disp'];
			$this->oEnregBdd->NbRepMaxQC = $HTTP_POST_VARS['NbRepMax'];		
			$this->oEnregBdd->MessMaxQC = $HTTP_POST_VARS['MessMax'];
			
			//Test des donn�es re�ues et marquage des erreurs(ast�risque) dans le formulaire
			if (!(int)$HTTP_POST_VARS['NbRepMax']) 
				{ $sMessageErreur2="<font color =\"red\">*</font>"; $iFlagErreur=1;}
			
			if ($iFlagErreur == 0) //si pas d'erreur, enregistrement physique dans la BD
				{		
				   //Enregistrement des r�ponses et de leurs poids pour les diff�rents axes
				   if (isset($HTTP_POST_VARS["rep"])) 	
					  {
					  foreach ($HTTP_POST_VARS["rep"] as $v_iIdReponse => $v_sTexteTemp) 
						{
							$oReponse = new CReponse($this->oBdd);
							$oReponse->defId($v_iIdReponse);
							
							$oReponse->defTexteReponse(stripslashes($v_sTexteTemp));
							$oReponse->enregistrer(FALSE);

							if (isset($HTTP_POST_VARS["repAxe"])) 	
									//V�rification pour ne pas effectuer le traitement si 
									//aucun axe n'est d�fini pour ce formulaire
								{
								   $tab = $HTTP_POST_VARS["repAxe"];
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
					  		
				//Lorsque la question est bien enregistr�e dans la BD 
				//(Pour cela on a cliqu� sur le bouton 'Appliquer les changements')
				//on rafra�chit la liste en cochant l'objet en cours de traitement
							
				echo "<script>\n";
				echo "rechargerliste($v_iIdObjForm,$v_iIdFormulaire)\n";
				echo "</script>\n";
				} 
		}
		
		
	//Si on a cliqu� sur le lien 'Ajouter' cela affecte, via javascript, 
	//au champ cach� ['typeaction']la valeur 'ajouter' et 
	//au champ cach� ['parametre'] la valeur '0'.
	//Attention lorsque l'on clique sur le lien 'Ajouter' cela implique �galement 
	//un enregistrement d'office dans la BD des modifications d�j� effectu�es sur 
	//l'objet en cours. (avec les v�rifications d'usage avant enregistrement dans la BD)
	if ($HTTP_POST_VARS['typeaction']=='ajouter')
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
			  La r�ponse qui sera cr��e ici contiendra :
					le numero de l'objet auquel elle appartient
					l'ordre dans lequel elle sera affich�e (toujours en derni�re place)
					son num�ro d'identifiant sera attribu� automatiquement par MySql
			  le texte de la r�ponse sera attribu� par apr�s.
			  */
			  $oReponse->enregistrer();
			  $this->oBdd->libererResult($hResultInt2);
		  }
		  
	//Si on a cliqu� sur le lien 'Supprimer' cela affecte, via javascript, 
	//au champ cach� ['typeaction']la valeur 'supprimer' et 
	//au champ cach� ['parametre'] l'id de la r�ponse a supprimer.
	//Attention lorsque l'on clique sur le lien 'supprimer' cela implique �galement 
	//un enregistrement d'office dans la BD des modifications d�j� effectu�es sur 
	//l'objet en cours.(avec les v�rifications d'usage avant enregistrement dans la BD)
	if ($HTTP_POST_VARS['typeaction']=='supprimer')
		  {
			  //echo "<br>je suis pass� par supprimer";
			  $v_iIdReponse = $HTTP_POST_VARS['parametre'];
			  $oReponse = new CReponse($this->oBdd,$v_iIdReponse);
			  $oReponse->effacer();
		  }
		  
		  
	//La fonction alignement renvoie 2 variables de type string contenant "CHECKED" 
	//et les 6 autres contiennent une cha�ne vide
	// aeX = alignement enonc�, arX = alignement r�ponse
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
		   ."<TD>$sMessageErreur1 Enonc� :</TD>\n"
		   ."<TD><textarea name=\"Enonce\" rows=\"5\" cols=\"70\">{$this->oEnregBdd->EnonQC}</textarea></TD>\n"
		   ."</TR>\n"
		   ."<TR>\n"
		   ."<TD>Alignement �nonc� :</TD>\n"
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
		   ."<TD>Texte avant la r�ponse :</TD>\n"
		   ."<TD><input type=\"text\" size=\"70\" maxlength=\"254\" name=\"TxtAv\" Value=\"{$this->oEnregBdd->TxtAvQC}\"></TR>\n"
		   ."</TR><TR>\n"
		   ."<TD>Texte apr�s la r�ponse :</TD>\n"
		   ."<TD><input type=\"text\" size=\"70\" maxlength=\"254\" name=\"TxtAp\" Value=\"{$this->oEnregBdd->TxtApQC}\"></TR>\n"
		   ."</TR><TR>\n"
		   ."<TD>Disposition :</TD>\n"
		   ."<TD><INPUT TYPE=\"radio\" NAME=\"Disp\" VALUE=\"Hor\" $d1>Horizontale\n"
		   ."<INPUT TYPE=\"radio\" NAME=\"Disp\" VALUE=\"Ver\" $d2>Verticale\n"
		   ."</TD>\n"
		   ."</TR><TR>\n"
		   ."<TD>R�ponse(s) :\n"
		   ."<a href=\"javascript: soumettre('ajouter',0);\">Ajouter</a>\n"
		   ."</TD>\n"
		   .$this->RetourReponseQCModif($v_iIdObjForm,$v_iIdFormulaire) 
		   ."<TR>\n"
		   ."<TD>$sMessageErreur2 Nombre de r�ponses max :</TD>\n"
		   ."<TD><input type=\"text\" size=\"2\" maxlength=\"2\" name=\"NbRepMax\" Value=\"{$this->oEnregBdd->NbRepMaxQC}\" onblur=\"verifNumeric(this)\"></TR>\n"
		   ."</TR><TR>\n"
		   ."<TD>Message \"Maximum d�pass�\"</TD>\n"
		   ."<TD><input type=\"text\" size=\"70\" maxlength=\"254\" name=\"MessMax\" Value=\"{$this->oEnregBdd->MessMaxQC}\"></TR>\n"
		   ."</TR>\n"
		   ."<TR>\n"
		   ."<TD>Alignement R�ponse :</TD>\n"
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
		   //Le champ cach� ci-dessous "simule" le fait d'appuyer sur le bouton submit 
		   //(qui s'appelait envoyer) et ainsi permettre l'enregistrement dans la BD
		   ."<input type=\"hidden\" name=\"envoyer\" value=\"1\">\n"
		   ."</form>\n";
	
	return $sCodeHtml;
}


	/*
	** Fonction 		: enregistrer
	** Description		: enregistre les donn�es de l'objet courant dans la BD
	** Entr�e			:
	** Sortie			:
	*/

function enregistrer ()
{
	if ($this->oEnregBdd->IdObjForm !=NULL)
	   {	
		// Les variables contenant du "texte" doivent �tre format�es, cela permet 
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
	** Entr�e			:
	**				$v_iIdNvObjForm : identifiant du parent
	** Sortie			:
	**				$iIdObjForm : identifiant de la copie, renvoy� par la BD
	*/

function copier ($v_iIdNvObjForm)
{
	if ($v_iIdNvObjForm < 1)
		return;
		
	// Les variables contenant du "texte" doivent �tre format�es, cela permet 
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
	** Entr�e			:
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
