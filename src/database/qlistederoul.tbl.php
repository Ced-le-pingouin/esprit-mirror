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

require_once (dir_database("bdd.class.php"));  	//permet d'utiliser la bdd sans creer un objet
																//CProjet et ainsi cela permet de creer des objets
																//d'une autre classe � partir de celle-ci.
/*
** Fichier ................: qlistederoul.tbl.php
** Description ............: 
** Date de cr�ation .......: 
** Derni�re modification ..: 22-06-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/

class CQListeDeroul
{
 var $oBdd;
 var $iId;
 var $oEnregBdd;
 var $aoFormulaire;

 function CQListeDeroul(&$v_oBdd,$v_iId=0) 
 {
   			$this->oBdd = &$v_oBdd;  
								  //si 0 cr�e un objet presque vide sinon 
								  //rempli l'objet avec les donn�es de la table Formulaire
								  //de l'el�ment ayant l'Id pass� en argument 
								  //(ou avec l'objet pass� en argument mais sans passer par le constructeur)
		if (($this->iId = $v_iId) > 0)
			$this->init();
 }
	
	//INIT est une fonction que l'on peut utiliser sans passer par le constructeur. 
	//On lui passe alors un objet obtenu par exemple en faisant une requ�te sur une autre page.
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
				." FROM QListeDeroul"
				." WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
	    }

		$this->iId = $this->oEnregBdd->IdObjForm;
 }

 function ajouter ($v_iIdObjForm) //Cette fonction ajoute une question de type liste d�roulante, avec tous ses champs
				 							// vide, en fin de table
 {
   $sRequeteSql = "INSERT INTO QListeDeroul SET IdObjForm='{$v_iIdObjForm}'";
   $this->oBdd->executerRequete($sRequeteSql);
   return ($this->iId = $this->oBdd->retDernierId());
 }


//Fonctions de d�finition

 function defIdObjForm ($v_iIdObjForm)
{
  $this->oEnregBdd->IdObjForm = $v_iIdObjForm;
}


 function defEnonQLD ($v_sEnonQLD)
{
  $this->oEnregBdd->EnonQLD = $v_sEnonQLD;
}

function defAlignEnonQTC ($v_sAlignEnonQLD)
{
  $this->oEnregBdd->AlignEnonQLD = $v_sAlignEnonQLD;
}

function defAlignRepQLD ($v_sAlignRepQLD)
{
  $this->oEnregBdd->AlignRepQLD = $v_sAlignRepQLD;
}

function defTxtAvQLD ($v_sTxtAvQLD)
{
  $this->oEnregBdd->TxtAvQLD = $v_sTxtAvQLD;
}

function defTxtApQLD ($v_sTxtApQLD)
{
  $this->oEnregBdd->TxtApQLD = $v_sTxtApQLD;
}


//Fonctions de retour

function retId () { return $this->oEnregBdd->IdObjForm; }
function retEnonQLD () { return $this->oEnregBdd->EnonQLD; }
function retAlignEnonQLD () { return $this->oEnregBdd->AlignEnonQLD; }
function retAlignRepQLD () { return $this->oEnregBdd->AlignRepQLD; }
function retTxTAvQLD () { return $this->oEnregBdd->TxtAvQLD; }
function retTxtApQLD () { return $this->oEnregBdd->TxtApQLD; }


	  /*
	  ** Fonction 		: RetourReponseQLD
	  ** Description	: renvoie le code html contenant la liste d�roulante avec les r�ponses,
	  **					  si $v_iIdFC la r�ponse fournie par l'�tudiant sera pr�-s�lectionn�e	
	  ** Entr�e			:
	  **				$v_iIdFC : Id d'un formulaire compl�t� -> r�cup�ration de la r�ponse dans la table correspondante
	  ** Sortie			:
	  **				code html
	  */

function RetourReponseQLD($v_iIdFC=NULL)
{
$iIdReponseEtu = "";
if ($v_iIdFC != NULL)
	{
	//S�lection de la r�ponse donn�e par l'�tudiant
	$sRequeteSql = "SELECT * FROM ReponseEntier"
	." WHERE IdFC = '{$v_iIdFC}' AND IdObjForm = '{$this->oEnregBdd->IdObjForm}'";
	
	$hResultRep = $this->oBdd->executerRequete($sRequeteSql);
	$oEnregRep = $this->oBdd->retEnregSuiv($hResultRep);
	$iIdReponseEtu = $oEnregRep->IdReponse;
	}

//S�lection de toutes les r�ponses concernant l'objet QListeDeroul en cours de traitement
$sRequeteSql = "SELECT * FROM Reponse WHERE IdObjForm = '{$this->iId}'"
			." ORDER BY OrdreReponse";
$hResultRRQLD = $this->oBdd->executerRequete($sRequeteSql);

$CodeHtml="<SELECT NAME=\"{$this->iId}\">\n";

	while ($oEnreg = $this->oBdd->retEnregSuiv($hResultRRQLD))
	{
	$oReponse = new CReponse($this->oBdd->oBdd);
	$oReponse->init($oEnreg);
	
	//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
	$TexteTemp = $oReponse->retTexteReponse();
	$TexteTemp = convertBaliseMetaVersHtml($TexteTemp);
	$IdReponseTemp = $oReponse->retId();
	$IdObjFormTemp = $oReponse->retIdObjForm();
	
	if ($iIdReponseEtu == $IdReponseTemp) 
		{$sPreSelection = "SELECTED";}
	else {$sPreSelection = "";}
	
	$CodeHtml.="<OPTION VALUE=\"$IdReponseTemp\" $sPreSelection>$TexteTemp\n";
		
	}
$CodeHtml.="</SELECT>\n";

$this->oBdd->libererResult($hResultRRQLD);
return "$CodeHtml";
}


	  /*
	  ** Fonction 		: cHtmlQListeDeroul
	  ** Description	: renvoie le code html qui permet d'afficher une question de type liste d�roulante,
	  **				     si $v_iIdFC est pass� en param�tre il est envoy� � la fonction RetourReponseQLD qui permettra
	  **					  de pr�-s�lectionner la r�ponse entr�e par l'�tudiant
	  ** Entr�e			:
	  **				$v_iIdFC : Id d'un formulaire compl�t�
	  ** Sortie			:
	  **				code html
	  */

function cHtmlQListeDeroul($v_iIdFC=NULL)
	{
	//Mise en forme du texte (ex: remplacement de [b][/b] par le code html ad�quat)
	$this->oEnregBdd->EnonQLD = convertBaliseMetaVersHtml($this->oEnregBdd->EnonQLD);
	$this->oEnregBdd->TxtAvQLD = convertBaliseMetaVersHtml($this->oEnregBdd->TxtAvQLD);
	$this->oEnregBdd->TxtApQLD = convertBaliseMetaVersHtml($this->oEnregBdd->TxtApQLD);
	
	//Gen�ration du code html repr�sentant l'objet
	$sCodeHtml="\n<!--QListeDeroul : {$this->oEnregBdd->IdObjForm} -->\n"
		."<div align={$this->oEnregBdd->AlignEnonQLD}>{$this->oEnregBdd->EnonQLD}</div>\n"
		."<div class=\"InterER\" align={$this->oEnregBdd->AlignRepQLD}>\n"
		."{$this->oEnregBdd->TxtAvQLD} \n"
		.$this->RetourReponseQLD($v_iIdFC) 			//Appel de la fonction qui renvoie les r�ponses sous forme de liste d�roulante, 
																//avec la r�ponse s�lectionn�e par l'�tudiant si IdFC est pr�sent
		." {$this->oEnregBdd->TxtApQLD}\n"
		."</div>\n";
	
	return $sCodeHtml;
	}


	  	  /*
		  ** Fonction 		: RetourReponseQCModif
		  ** Description		: va rechercher dans la table r�ponse les r�ponses correspondant
		  **				  a la question de type liste d�roulante en cours de traitement 
		  **				  + mise en page de ces r�ponses avec possibilit� de modification
		  ** Entr�e			:
		  ** Sortie			: Code Html contenant les r�ponses + mise en page + modification possible
		  */

function RetourReponseQCModif($v_iIdObjForm,$v_iIdFormulaire)
{
/*
Utilisation de l'objet CBdd bcp plus l�ger pour faire les requ�tes qu'un objet Projet
Attention ne pas oublier le : require_once (dir_database("bdd.class.php"));
*/
$oCBdd = new CBdd;

//S�lection de toutes les r�ponses concernant l'objet QRadio en cours de traitement
$sRequeteSql = "SELECT * FROM Reponse WHERE IdObjForm = '{$this->iId}'"
			." ORDER BY OrdreReponse";

$hResultRep = $oCBdd->executerRequete($sRequeteSql);

$CodeHtml="";

	while ($oEnreg = $oCBdd->retEnregSuiv($hResultRep))
	{
	$oReponse = new CReponse($oCBdd->oBdd);
	$oReponse->init($oEnreg);
	
	//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
	$TexteTemp = $oReponse->retTexteReponse();
	$IdReponseTemp = $oReponse->retId();
	$IdObjFormTemp = $oReponse->retIdObjForm();
	
		  if ($CodeHtml =="")
		  {
				  
				  $CodeHtml.="<TD><input type=\"text\" size=\"70\" maxlength=\"255\" "
							  ."name=\"rep[$IdReponseTemp]\" Value=\"$TexteTemp\">\n"
							  ."<a href=\"javascript: soumettre('supprimer',$IdReponseTemp);\">Supprimer</a><br></TD></TR>\n"
							  .RetourPoidsReponse($v_iIdFormulaire,$v_iIdObjForm,$IdReponseTemp); //cette fc se trouve dans fonctions_form.inc.php
		  }
		  else
		  {
				  $CodeHtml.="<TR><TD></TD><TD><input type=\"text\" size=\"70\" maxlength=\"255\" "
							  ."name=\"rep[$IdReponseTemp]\" Value=\"$TexteTemp\">\n"
							  ."<a href=\"javascript: soumettre('supprimer',$IdReponseTemp);\">Supprimer</a><br></TD></TR>\n"
							  .RetourPoidsReponse($v_iIdFormulaire,$v_iIdObjForm,$IdReponseTemp); //cette fc se trouve dans fonctions_form.inc.php
		  }
	
	}
return "$CodeHtml";
}


function cHtmlQListeDeroulModif($v_iIdObjForm,$v_iIdFormulaire)
	{
	global $HTTP_POST_VARS, $HTTP_GET_VARS;
	
	//initialisation des messages d'erreurs � 'vide' et de la variable servant a d�tecter
	//si une erreur dans le remplissage du formulaire a eu lieu (ce qui engendre le non enregistrement
	//de celui-ci dans la base de donn�es + affiche d'une ast�risque � l'endroit de l'erreur)
	
	$sMessageErreur1 = "";
	$iFlagErreur=0;
	
	if (isset($HTTP_POST_VARS['envoyer']) || $HTTP_POST_VARS['typeaction']=='ajouter' || $HTTP_POST_VARS['typeaction']=='supprimer')
		{
			   //R�cup�ration des variables transmises par le formulaire
			   $this->oEnregBdd->EnonQLD = stripslashes($HTTP_POST_VARS['Enonce']);
			   $this->oEnregBdd->AlignEnonQLD = $HTTP_POST_VARS['AlignEnon'];
				$this->oEnregBdd->AlignRepQLD = $HTTP_POST_VARS['AlignRep'];
				$this->oEnregBdd->TxtAvQLD = stripslashes($HTTP_POST_VARS['TxtAv']);
				$this->oEnregBdd->TxtApQLD = stripslashes($HTTP_POST_VARS['TxtAp']);
				
			   //Test des donn�es re�ues et marquage des erreurs � l'aide d'une ast�risque dans le formulaire
			   //if (strlen($HTTP_POST_VARS['Enonce']) < 1) { $sMessageErreur1="<font color =\"red\">*</font>"; $iFlagErreur=1;}
				
			   //if ($iFlagErreur == 0) //si pas d'erreur, enregistrement physique dans la BD
					//{		
						   
							//Enregistrement des r�ponses et de leurs poids pour les differents axes
							if (isset($HTTP_POST_VARS["rep"])) 	//on doit verifier car lorsque l'on appuie la premiere fois, apres avoir cree l'objet, 
																			//sur ajouter $HTTP_POST_VARS["rep"] n'existe pas 
							{
								foreach ($HTTP_POST_VARS["rep"] as $v_iIdReponse => $v_sTexteTemp) 
								{
									$oReponse = new CReponse($this->oBdd);
									$oReponse->defId($v_iIdReponse);
									
									$oReponse->defTexteReponse(stripslashes($v_sTexteTemp));
									$oReponse->enregistrer(FALSE);  //On utilise FALSE car on n'initialise (on ne connait pas sa position)
								
									  if (isset($HTTP_POST_VARS["repAxe"])) 	//V�rifier pour ne pas effectuer le traitement si aucun axe 
									  														// n'est d�fini pour ce formulaire
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
											  }  //Fin SI
										  }  //Fin FOREACH
									  }  //Fin SI
								}  //Fin FOREACH
							}  //Fin SI
							
						   //Enregistrement de l'objet QRadio actuel dans la BD
						   $this->enregistrer();
					  		
						   //Lorsque la question est bien enregistr�e dans la BD 
						   //(Pour cela on a cliqu� sur le bouton 'Appliquer les changements')
						   //on rafra�chit la liste en cochant l'objet que l'on est en train de traiter
							
							echo "<script>\n";
							echo "rechargerliste($v_iIdObjForm,$v_iIdFormulaire)\n";
						  	echo "</script>\n";
					//}  //Fin SI 'Enregistrement physique'
		}
		
		
	//Si on a cliqu� sur le lien 'Ajouter' cela affecte, via javascript, au champ cach� ['typeaction']
	//la valeur 'ajouter' et au champ cach� parametre la valeur '0'.
	//Attention lorsque l'on clique sur le lien 'Ajouter' cela implique �galement 
	//un enregistrement d'office dans la BD des modifications d�j� effectu�es sur l'objet en cours. 
	//(avec les v�rifications d'usage avant enregistrement dans la BD)
	if ($HTTP_POST_VARS['typeaction']=='ajouter')
		  {
				 $hResultInt2 = $this->oBdd->executerRequete("SELECT MAX(OrdreReponse) AS OrdreMax FROM Reponse"
					." WHERE IdObjForm = '{$this->oEnregBdd->IdObjForm}'");
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

	//Si on a cliqu� sur le lien 'Supprimer' cela affecte, via javascript, au champ cach� ['typeaction']
	//la valeur 'supprimer' et au champ cach� ['parametre'] l'id de la r�ponse a supprimer.
	//Attention lorsque l'on clique sur le lien 'supprimer' cela implique �galement 
	//un enregistrement d'office dans la BD des modifications d�j� effectu�es sur l'objet en cours.
	//(avec les v�rifications d'usage avant enregistrement dans la BD)
	if ($HTTP_POST_VARS['typeaction']=='supprimer')
		  {
				 $v_iIdReponse = $HTTP_POST_VARS['parametre'];
				 $oReponse = new CReponse($this->oBdd,$v_iIdReponse);
				 $oReponse->effacer();
		  }
		  
		  
	//La fonction alignement renvoie 2 variables de type string contenant "CHECKED" 
	//et les 6 autres contiennent une cha�ne vide
	// aeX = alignement enonc�, arX = alignement r�ponse
	list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = 
		Alignement($this->oEnregBdd->AlignEnonQLD,$this->oEnregBdd->AlignRepQLD);
	
	
	$sParam="?idobj=".$v_iIdObjForm."&idformulaire=".$v_iIdFormulaire;
	
	$sCodeHtml ="\n<form name=\"formmodif\" action=\"{$_SERVER['PHP_SELF']}$sParam\" method=\"POST\" enctype=\"text/html\">\n"
		   ."<fieldset><legend><b>ENONCE</b></legend>\n"
		   ."<TABLE>\n"
		   ."<TR>\n"
		   ."<TD>$sMessageErreur1 Enonc� :</TD>\n"
		   ."<TD><textarea name=\"Enonce\" rows=\"5\" cols=\"70\">{$this->oEnregBdd->EnonQLD}</textarea></TD>\n"
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
		   ."<TD><input type=\"text\" size=\"70\" maxlength=\"254\" name=\"TxtAv\" Value=\"{$this->oEnregBdd->TxtAvQLD}\"></TR>\n"
		   ."</TR><TR>\n"
		   ."<TD>Texte apr�s la r�ponse :</TD>\n"
		   ."<TD><input type=\"text\" size=\"70\" maxlength=\"254\" name=\"TxtAp\" Value=\"{$this->oEnregBdd->TxtApQLD}\"></TR>\n"
		   ."</TR>"
		   ."<TD>R�ponse(s) :\n"
			."<a href=\"javascript: soumettre('ajouter',0);\">Ajouter</a>\n"
			."</TD>\n"
		   .$this->RetourReponseQCModif($v_iIdObjForm,$v_iIdFormulaire)
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
		   //Le champ cach� ci-dessous "simule" le fait d'appuyer sur le bouton submit (qui s'appelait envoyer) et ainsi permettre l'enregistrement dans la BD
		   ."<input type=\"hidden\" name=\"envoyer\" value=\"1\">\n"
		   ."</form>\n";
	
	return $sCodeHtml;
	}


function enregistrer ()
{
	  if ($this->oEnregBdd->IdObjForm !=NULL)
	  {	
		// Les variables contenant du "texte" doivent �tre format�es, cela permet 
		//de les stocker dans la BD sans erreur 
		$sEnonQLD = validerTexte($this->oEnregBdd->EnonQLD);
		$sTxtAvQLD = validerTexte($this->oEnregBdd->TxtAvQLD);
		$sTxtApQLD = validerTexte($this->oEnregBdd->TxtApQLD);
		
		$sRequeteSql = "REPLACE QListeDeroul SET"									  
			." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
			.", EnonQLD='{$sEnonQLD}'"
			.", AlignEnonQLD='{$this->oEnregBdd->AlignEnonQLD}'"
			.", AlignRepQLD='{$this->oEnregBdd->AlignRepQLD}'"
			.", TxtAvQLD='{$sTxtAvQLD}'"
			.", TxtApQLD='{$sTxtApQLD}'";
			
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	  }
	  else
	  {
	   Echo "Identifiant NULL enregistrement impossible";
	  }
}


function enregistrerRep ($v_iIdFC,$v_iIdObjForm,$v_sReponsePersQLD)
	{
	if ($v_iIdObjForm !=NULL)
	   {
		
		$sRequeteSql = "REPLACE ReponseEntier SET"									  
			." IdFC='{$v_iIdFC}'"
			.", IdObjForm='{$v_iIdObjForm}'"
			.", IdReponse='{$v_sReponsePersQLD}'";
			
		//echo "<br>enregistrer ReponsePersQLD : ".$sRequeteSql;
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
		
		// Les variables contenant du "texte" doivent �tre format�es, cela permet 
		//de les stocker dans la BD sans erreur 
		$sEnonQLD = validerTexte($this->oEnregBdd->EnonQLD);
		$sTxtAvQLD = validerTexte($this->oEnregBdd->TxtAvQLD);
		$sTxtApQLD = validerTexte($this->oEnregBdd->TxtApQLD);
		
		$sRequeteSql = "INSERT INTO QListeDeroul SET"									  
			." IdObjForm='{$v_iIdNvObjForm}'"
			.", EnonQLD='{$sEnonQLD}'"
			.", AlignEnonQLD='{$this->oEnregBdd->AlignEnonQLD}'"
			.", AlignRepQLD='{$this->oEnregBdd->AlignRepQLD}'"
			.", TxtAvQLD='{$sTxtAvQLD}'"
			.", TxtApQLD='{$sTxtApQLD}'";
			
		$this->oBdd->executerRequete($sRequeteSql);
		
		$iIdObjForm = $this->oBdd->retDernierId();
		
		return $iIdObjForm;
	}


function effacer ()
{
	  $sRequeteSql = "DELETE FROM QListeDeroul"
			." WHERE IdObjForm ='{$this->oEnregBdd->IdObjForm}'";
		  //echo "<br>effacer QListeDeroul()".$sRequeteSql;
	  $this->oBdd->executerRequete($sRequeteSql);

	  return TRUE;
}

}

?>
