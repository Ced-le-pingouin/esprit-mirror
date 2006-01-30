<?php

/*
** Fichier ................: mpseparateur.tbl.php
** Description ............: 
** Date de cr�ation .......: 
** Derni�re modification ..: 22-06-2004
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
								  //si 0 cr�e un objet presque vide sinon 
								  //rempli l'objet avec les donn�es de la table MPSEPARATEUR
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
				." FROM MPSeparateur"
				." WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
	    }

		$this->iId = $this->oEnregBdd->IdObjForm;
 }

 function ajouter ($v_iIdObjForm) //Cette fonction ajoute une ligne de type s�parateur,
				 // avec tous ses champs vide, en fin de table
 {
   $sRequeteSql = "INSERT INTO MPSeparateur SET IdObjForm='{$v_iIdObjForm}'";
   $this->oBdd->executerRequete($sRequeteSql);
   return ($this->iId = $this->oBdd->retDernierId());
 }


//Fonctions de d�finition

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
	if ($this->oEnregBdd->TypeLargMPS=="P")					//ajoute % ou px a la largeur pour ainsi cr�er une chaine de car
	{
	   $sLargeur=$this->oEnregBdd->LargeurMPS."%";
	}
	else												//se test est peut etre � deplacer car il a l'air a l'origine d'un certain ralentissement
	{
	   $sLargeur=$this->oEnregBdd->LargeurMPS."px";
	}
	
	
	//Gen�ration du code html repr�sentant l'objet
	$sCodeHtml="<hr width=$sLargeur size=\"2\" align={$this->oEnregBdd->AlignMPS}>";
	//<hr style="color: rgb(0,255,0); background-color: rgb(0,255,0); border: none; width: 250px; height: 5px;" align="right">
	return $sCodeHtml;	
}


	function cHtmlMPSeparateurModif($v_iIdObjForm,$v_iIdFormulaire)
	{
		global $HTTP_POST_VARS, $HTTP_GET_VARS;
		
		$oObjetFormulaire = new CObjetFormulaire($this->oBdd, $v_iIdObjForm);
		
		//initialisation des messages d'erreurs � 'vide' et de la variable servant a detecter
		//si une erreur dans le remplissage du formulaire a eu lieu (ce qui engendre le non enregistrement
		//de celui-ci dans la base de donn�es + affiche d'une ast�risque � l'endroit de l'erreur)
		$sMessageErreur1="";
		$iFlagErreur=0;
		
		if (isset($HTTP_POST_VARS['envoyer'])) 
		{
			//R�cup�ration des variables transmises par le formulaire
			$this->oEnregBdd->LargeurMPS = $HTTP_POST_VARS['Largeur'];
			$this->oEnregBdd->TypeLargMPS = $HTTP_POST_VARS['TypeLarg'];
			$this->oEnregBdd->AlignMPS = $HTTP_POST_VARS['Align'];
			
			//Test des donn�es re�ues et marquage des erreurs � l'aide d'une ast�risque dans le formulaire
			if (!(int)$_POST['Largeur']) { $sMessageErreur1="<font color =\"red\">*</font>"; $iFlagErreur=1;}
			
			if ($iFlagErreur == 0)
			{
				$oObjetFormulaire->verrouillerTablesQuestion();
				// enregistrement de la position de l'objet
				$oObjetFormulaire->DeplacerObjet($HTTP_POST_VARS["selOrdreObjet"], FALSE);
				// enregistrement des donn�es sp�cifiques � ce type d'�l�ment/objet
				$this->enregistrer();
				$oObjetFormulaire->deverrouillerTablesQuestion();
				
				echo "<script>\n";
				echo "rechargerliste($v_iIdObjForm,$v_iIdFormulaire)\n";
				echo "</script>\n";
			} //si pas d'erreur, enregistrement physique
		}
	
	
	
	//La fonction alignement renvoie 1 variables de type string contenant "CHECKED" 
	//et les 7 autres contiennent une cha�ne vide
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
	
	$sCodeHtml =
			"<form action=\"{$_SERVER['PHP_SELF']}$sParam\" name=\"formmodif\" method=\"POST\" enctype=\"text/html\">"
			.$oObjetFormulaire->cHtmlNumeroOrdre()
		   ."<fieldset><legend>&nbsp;Mise en page de type \"s�parateur\"&nbsp;</legend>"
		   
		   ."<TABLE BORDER=\"0\" WIDTH=\"99%\"><TR><TD ALIGN=\"right\">"
				."<INPUT TYPE=\"radio\" NAME=\"Align\" VALUE=\"left\" $ae1 ID=\"idAlignG\"><label for=\"idAlignG\">Gauche</label>\n"
				."<INPUT TYPE=\"radio\" NAME=\"Align\" VALUE=\"right\" $ae2 ID=\"idAlignD\"><label for=\"idAlignD\">Droite</label>\n"
				."<INPUT TYPE=\"radio\" NAME=\"Align\" VALUE=\"center\" $ae3 ID=\"idAlignC\"><label for=\"idAlignC\">Centrer</label>\n"
				."<INPUT TYPE=\"radio\" NAME=\"Align\" VALUE=\"justify\" $ae4 ID=\"idAlignJ\"><label for=\"idAlignJ\">Justifier</label>\n"
			."</TD></TR></TABLE>"
		   ."</TABLE>"
		   
		   ."<TABLE width=\"99%\">"
		   ."<TR>"
		   ."<TD width=\"1\" align=\"right\" valign=\"top\">$sMessageErreur1 Largeur&nbsp;:&nbsp;&nbsp;</TD>"
		   ."<TD><input type=\"text\" size=\"4\" maxlength=\"4\" name=\"Largeur\" Value=\"{$this->oEnregBdd->LargeurMPS}\" onblur=\"verifNumeric(this)\">"
		   	."&nbsp <INPUT TYPE=\"radio\" NAME=\"TypeLarg\" VALUE=\"P\" $sAR1>pourcents"
		   ."<INPUT TYPE=\"radio\" NAME=\"TypeLarg\" VALUE=\"N\" $sAR2>pixels"
		   ."</TD>"
		   ."</TR>"
		   ."</TABLE>"
		   ."</fieldset>"
		   //Le champ cach� ci-dessous "simule" le fait d'appuyer sur le bouton submit (qui s'appelait envoyer) et ainsi permettre l'enregistrement dans la BD
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
