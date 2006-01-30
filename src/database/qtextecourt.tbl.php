<?php

/*
** Fichier ................: qtextecourt.tbl.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 22-06-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/

class CQTexteCourt 
{
	var $oBdd;
	var $iId;
	var $oEnregBdd;
	var $aoFormulaire;

	function CQTexteCourt(&$v_oBdd,$v_iId=0) 
	{
		$this->oBdd = &$v_oBdd;  
			//si 0 crée un objet presque vide sinon 
			//rempli l'objet avec les données de la table QTexteCourt
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
				." FROM QTexteCourt"
				." WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		$this->iId = $this->oEnregBdd->IdObjForm;
	}

	function ajouter ($v_iIdObjForm) //Cette fonction ajoute une question de type texte court,
								 // avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO QTexteCourt SET IdObjForm='{$v_iIdObjForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}

	//Fonctions de définition
	function defIdObjForm ($v_iIdObjForm) { $this->oEnregBdd->IdObjForm = $v_iIdObjForm; }
	function defEnonQTC ($v_sEnonQTC) { $this->oEnregBdd->EnonQTC = $v_sEnonQTC; }
	function defAlignEnonQTC ($v_sAlignEnonQTC) { $this->oEnregBdd->AlignEnonQTC = $v_sAlignEnonQTC; }
	function defAlignRepQTC ($v_sAlignRepQTC) { $this->oEnregBdd->AlignRepQTC = $v_sAlignRepQTC; }
	function defTxtAvQTC ($v_sTxtAvQTC) { $this->oEnregBdd->TxtAvQTC = $v_sTxtAvQTC; }
	function defTxtApQTC ($v_sTxtApQTC) { $this->oEnregBdd->TxtApQTC = $v_sTxtApQTC; }
	function defLargeurQTC ($v_iLargeurQTC) { $this->oEnregBdd->LargeurQTC = trim($v_iLargeurQTC); }
	function defMaxCarQTC ($v_iMaxCarQTC) { $this->oEnregBdd->MaxCarQTC = trim($v_iMaxCarQTC); }

	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdObjForm; }
	function retEnonQTC () { return $this->oEnregBdd->EnonQTC; }
	function retAlignEnonQTC () { return $this->oEnregBdd->AlignEnonQTC; }
	function retAlignRepQTC () { return $this->oEnregBdd->AlignRepQTC; }
	function retTxtAvQTC () { return $this->oEnregBdd->TxtAvQTC; }
	function retTxtApQTC () { return $this->oEnregBdd->TxtApQTC; }
	function retLargeurQTC () { return $this->oEnregBdd->LargeurQTC; }
	function retMaxCarQTC () { return $this->oEnregBdd->MaxCarQTC; }

	/*
	** Fonction 		: cHtmlQTexteCourt
	** Description		: renvoie le code html qui permet d'afficher une question de type texte "court",
	**				  	  si $v_iIdFC est passé en paramètre la réponse correspondante sera également affichée
	** Entrée			:
	**				$v_iIdFC : Id d'un formulaire complété -> récupération de la réponse dans la table correspondante
	** Sortie			:
	**				code html
	*/

	function cHtmlQTexteCourt($v_iIdFC=NULL)
	{
		$sValeur = "";
		
		if ($v_iIdFC != NULL)
		{
			$sRequeteSql = "SELECT * FROM ReponseCar"
				." WHERE IdFC = '{$v_iIdFC}' AND IdObjForm = '{$this->oEnregBdd->IdObjForm}'";
			
			$hResultRep = $this->oBdd->executerRequete($sRequeteSql);
			$oEnregRep = $this->oBdd->retEnregSuiv($hResultRep);
			$sValeur = $oEnregRep->Valeur;
		}

		//Mise en forme du texte (ex: remplacement de [b][/b] par le code html adéquat)
		$this->oEnregBdd->EnonQTC = convertBaliseMetaVersHtml($this->oEnregBdd->EnonQTC);
		$this->oEnregBdd->TxtAvQTC = convertBaliseMetaVersHtml($this->oEnregBdd->TxtAvQTC);
		$this->oEnregBdd->TxtApQTC = convertBaliseMetaVersHtml($this->oEnregBdd->TxtApQTC);
	
		//Genération du code html représentant l'objet
		$sCodeHtml="\n<!--QTexteCourt : {$this->oEnregBdd->IdObjForm} -->\n"
			."<div align={$this->oEnregBdd->AlignEnonQTC}>{$this->oEnregBdd->EnonQTC}</div>\n"
			."<div class=\"InterER\" align={$this->oEnregBdd->AlignRepQTC}>\n"
			."{$this->oEnregBdd->TxtAvQTC} \n"
			."<input type=\"text\" name=\"{$this->oEnregBdd->IdObjForm}\" SIZE=\"{$this->oEnregBdd->LargeurQTC}\" MAXLENGTH=\"{$this->oEnregBdd->MaxCarQTC}\" VALUE=\"$sValeur\">\n"
			." {$this->oEnregBdd->TxtApQTC}\n"
			."</div><br>\n";
		
		return $sCodeHtml;
	}


	function cHtmlQTexteCourtModif($v_iIdObjForm,$v_iIdFormulaire)
	{
		global $HTTP_POST_VARS, $HTTP_GET_VARS;
		
		$oObjetFormulaire = new CObjetFormulaire($this->oBdd, $v_iIdObjForm);
		
		//initialisation des messages d'erreurs à 'vide' et de la variable servant a détecter
		//si une erreur dans le remplissage du formulaire a eu lieu (ce qui engendre le non enregistrement
		//de celui-ci dans la base de données + affiche d'une astérisque à l'endroit de l'erreur)
		$sMessageErreur1 = $sMessageErreur2 = $sMessageErreur3 = "";
		$iFlagErreur=0;
		
		if (isset($HTTP_POST_VARS['envoyer'])) 
		{
			//Récupération des variables transmises par le formulaire
			$this->oEnregBdd->EnonQTC = stripslashes($HTTP_POST_VARS['Enonce']);
			$this->oEnregBdd->AlignEnonQTC = $HTTP_POST_VARS['AlignEnon'];
			$this->oEnregBdd->AlignRepQTC = $HTTP_POST_VARS['AlignRep'];
			$this->oEnregBdd->TxtAvQTC = stripslashes($HTTP_POST_VARS['TxtAv']);
			$this->oEnregBdd->TxtApQTC = stripslashes($HTTP_POST_VARS['TxtAp']);
			$this->oEnregBdd->LargeurQTC = $HTTP_POST_VARS['Largeur'];
			$this->oEnregBdd->MaxCarQTC = $HTTP_POST_VARS['MaxCar'];
			
			//Test des données reçues et marquage des erreurs à l'aide d'une astérisque dans le formulaire
			//if ($this->oEnregBdd->EnonQTC == "") { $sMessageErreur1="<font color =\"red\">*</font>"; $iFlagErreur=1;}
			if (!(int)$HTTP_POST_VARS['Largeur']) { $sMessageErreur2="<font color =\"red\">*</font>"; $iFlagErreur=1;}
			if ((int)$HTTP_POST_VARS['MaxCar'] || strlen($HTTP_POST_VARS['MaxCar']) < 1) {;}
			else { $sMessageErreur3="<font color =\"red\">*</font>"; $iFlagErreur=1;}
								
			if ($iFlagErreur == 0) 
			{
				$oObjetFormulaire->verrouillerTablesQuestion();
				// enregistrement de la position de l'objet
				$oObjetFormulaire->DeplacerObjet($HTTP_POST_VARS["selOrdreObjet"], FALSE);
				// enregistrement des données spécifiques à ce type d'élément/objet
				$this->enregistrer();
				$oObjetFormulaire->deverrouillerTablesQuestion();
				
				echo "<script>\n";
				echo "rechargerliste($v_iIdObjForm,$v_iIdFormulaire)\n";
				echo "</script>\n";
			} //si pas d'erreur, enregistrement physique
		}
	
		//La fonction alignement renvoie 2 variables de type string contenant "CHECKED" 
		//et les 6 autres contiennent une chaîne vide
		// aeX = alignement enoncé, arX = alignement réponse
		list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = 
		Alignement($this->oEnregBdd->AlignEnonQTC,$this->oEnregBdd->AlignRepQTC);
		  
		$sParam="?idobj=".$v_iIdObjForm."&idformulaire=".$v_iIdFormulaire;
	
		$sCodeHtml =
			"<form action=\"{$_SERVER['PHP_SELF']}$sParam\" name=\"formmodif\" method=\"POST\" enctype=\"text/html\">\n"
			.$oObjetFormulaire->cHtmlNumeroOrdre()
			."<fieldset><legend>&nbsp;Enoncé&nbsp;</legend>\n"
			."<TABLE BORDER=\"0\" WIDTH=\"99%\"><TR><TD ALIGN=\"right\">"
				."<INPUT TYPE=\"radio\" NAME=\"AlignEnon\" VALUE=\"left\" $ae1 ID=\"idAlignEnonG\"><label for=\"idAlignEnonG\">Gauche</label>\n"
				."<INPUT TYPE=\"radio\" NAME=\"AlignEnon\" VALUE=\"right\" $ae2 ID=\"idAlignEnonD\"><label for=\"idAlignEnonD\">Droite</label>\n"
				."<INPUT TYPE=\"radio\" NAME=\"AlignEnon\" VALUE=\"center\" $ae3 ID=\"idAlignEnonC\"><label for=\"idAlignEnonC\">Centrer</label>\n"
				."<INPUT TYPE=\"radio\" NAME=\"AlignEnon\" VALUE=\"justify\" $ae4 ID=\"idAlignEnonJ\"><label for=\"idAlignEnonJ\">Justifier</label>\n"
			."</TD></TR></TABLE>"
			."<TABLE width=\"99%\">\n"
			."<TR>\n"
			."<TD width=\"1\" align=\"right\" valign=\"top\">$sMessageErreur1 Enoncé&nbsp;:&nbsp;&nbsp;</TD>\n"
			."<TD><textarea name=\"Enonce\" rows=\"5\" cols=\"70\" style=\"width: 100%\">{$this->oEnregBdd->EnonQTC}</textarea></TD>\n"
			."</TR>\n"
			."</TABLE>\n"
			."</fieldset>\n"
			
			."<fieldset><legend><b>&nbsp;Zone réponse&nbsp;</b></legend>\n"
			."<TABLE BORDER=\"0\" WIDTH=\"99%\"><TR><TD ALIGN=\"right\">"
				."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"left\" $ar1 ID=\"idAlignRepG\"><label for=\"idAlignRepG\">Gauche</label>\n"
				."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"right\" $ar2 ID=\"idAlignRepD\"><label for=\"idAlignRepD\">Droite</label>\n"
				."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"center\" $ar3 ID=\"idAlignRepC\"><label for=\"idAlignRepC\">Centrer</label>\n"
				."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"justify\" $ar4 ID=\"idAlignRepJ\"><label for=\"idAlignRepJ\">Justifier</label>\n"
			."</TD></TR></TABLE>"

			."<TABLE BORDER=\"0\" WIDTH=\"99%\">\n"
			."<TR>\n"
				."<TD WIDTH=\"45%\">"
					."<textarea cols=\"40\" rows=\"2\" name=\"TxtAv\" style=\"width: 100%;\">{$this->oEnregBdd->TxtAvQTC}</textarea>"
				."</TD>\n"
				."<TD STYLE=\"text-align: center; vertical-align: middle; font-weight: bold; "
				  ."background-color: rgb(255,255,255); color: rgb(153,73,89); border: 1px solid rgb(127,157,185);\">"
					."Zone<br>réponse"
				."</TD>"
				."<TD WIDTH=\"45%\">"
					."<textarea cols=\"40\" rows=\"2\" name=\"TxtAp\" style=\"width: 100%;\">{$this->oEnregBdd->TxtApQTC}</textarea>"
				."</TD>\n"
			."</TR>\n"
			."</TABLE>\n"
			."<TABLE width=\"99%\">\n"
			."<TR>"
				."<TD width=\"1\" align=\"right\" valign=\"top\">$sMessageErreur2 Taille&nbsp;de&nbsp;la&nbsp;boîte&nbsp;de&nbsp;texte&nbsp;:&nbsp;&nbsp;</TD>\n"
				."<TD><input type=\"text\" size=\"3\" maxlength=\"3\" name=\"Largeur\" Value=\"{$this->oEnregBdd->LargeurQTC}\" onblur=\"verifNumeric(this)\"></TD>\n"
			."</TR>\n"
			."<TR>\n"
				."<TD width=\"1\" align=\"right\" valign=\"top\">$sMessageErreur3 Nombre&nbsp;de&nbsp;caractères&nbsp;maximum&nbsp;:&nbsp;&nbsp;</TD>\n"
				."<TD><input type=\"text\" size=\"3\" maxlength=\"3\" name=\"MaxCar\" Value=\"{$this->oEnregBdd->MaxCarQTC}\" onblur=\"verifNumeric(this)\"></TD>\n"
			."</TR>\n"
			."</TABLE>\n"
			."</fieldset>\n"

			//Le champ caché ci-dessous "simule" le fait d'appuyer sur le bouton submit (qui s'appelait envoyer) et ainsi permettre l'enregistrement dans la BD
			."<input type=\"hidden\" name=\"envoyer\" value=\"1\">\n"
			."</form>\n";
		
		return $sCodeHtml;
	}
	
	
	function enregistrer ()
	{
		if ($this->oEnregBdd->IdObjForm !=NULL)
		{
		
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			//de les stocker dans la BD sans erreur 
			$sEnonQTC = validerTexte($this->oEnregBdd->EnonQTC);
			$sTxtAvQTC = validerTexte($this->oEnregBdd->TxtAvQTC);
			$sTxtApQTC = validerTexte($this->oEnregBdd->TxtApQTC);
			
			//Valeur par défaut de MaxCar c'est la valeur de LargeurQTC
			if (strlen($this->oEnregBdd->MaxCarQTC) < 1) 
				{$this->oEnregBdd->MaxCarQTC = $this->oEnregBdd->LargeurQTC;}
			
			$sRequeteSql = "REPLACE QTexteCourt SET"									  
				." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
				.", EnonQTC='{$sEnonQTC}'"
				.", AlignEnonQTC='{$this->oEnregBdd->AlignEnonQTC}'"
				.", AlignRepQTC='{$this->oEnregBdd->AlignRepQTC}'"
				.", TxtAvQTC='{$sTxtAvQTC}'"
				.", TxtApQTC='{$sTxtApQTC}'"
				.", LargeurQTC='{$this->oEnregBdd->LargeurQTC}'"
				.", MaxCarQTC='{$this->oEnregBdd->MaxCarQTC}'"
				;
			
			$this->oBdd->executerRequete($sRequeteSql);
			
			return TRUE;
		}
		else
		{
			echo "Identifiant NULL enregistrement impossible";
		}
	}

	function enregistrerRep ($v_iIdFC,$v_iIdObjForm,$v_sReponsePersQTC)
	{
		if ($v_iIdObjForm !=NULL)
		{
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			//de les stocker dans la BD sans erreur 
			$sReponsePersQTC = validerTexte($v_sReponsePersQTC);
			
			$sRequeteSql = "REPLACE ReponseCar SET"									  
				." IdFC='{$v_iIdFC}'"
				.", IdObjForm='{$v_iIdObjForm}'"
				.", Valeur='{$sReponsePersQTC}'";
			
			//echo "<br>enregistrer ReponsePersQTL : ".$sRequeteSql;
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
		$sEnonQTC = validerTexte($this->oEnregBdd->EnonQTC);
		$sTxtAvQTC = validerTexte($this->oEnregBdd->TxtAvQTC);
		$sTxtApQTC = validerTexte($this->oEnregBdd->TxtApQTC);
		
		$sRequeteSql = "INSERT INTO QTexteCourt SET"									  
			." IdObjForm='{$v_iIdNvObjForm}'"
			.", EnonQTC='{$sEnonQTC}'"
			.", AlignEnonQTC='{$this->oEnregBdd->AlignEnonQTC}'"
			.", AlignRepQTC='{$this->oEnregBdd->AlignRepQTC}'"
			.", TxtAvQTC='{$sTxtAvQTC}'"
			.", TxtApQTC='{$sTxtApQTC}'"
			.", LargeurQTC='{$this->oEnregBdd->LargeurQTC}'"
			.", MaxCarQTC='{$this->oEnregBdd->MaxCarQTC}'"; 
			
		$this->oBdd->executerRequete($sRequeteSql);
		
		$iIdObjForm = $this->oBdd->retDernierId();
		
		

		return $iIdObjForm;
	}


	function effacer ()
	{
		$sRequeteSql = "DELETE FROM QTexteCourt"
				." WHERE IdObjForm ='{$this->oEnregBdd->IdObjForm}'";
		//echo "<br>effacer QTexteCourt()".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
}
?>
