<?php

/*
** Fichier ................: qtextelong.tbl.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 22-06-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/

class CQTexteLong 
{
	var $oBdd;
	var $iId;
	var $oEnregBdd;
	var $aoFormulaire;

	function CQTexteLong(&$v_oBdd,$v_iId=0) 
	{
		$this->oBdd = &$v_oBdd;  
								  //si 0 crée un objet presque vide sinon 
								  //rempli l'objet avec les données de la table QTexteLong
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
				." FROM QTexteLong"
				." WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		$this->iId = $this->oEnregBdd->IdObjForm;
	}

	function ajouter ($v_iIdObjForm) //Cette fonction ajoute une question de type texte long, avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO QTexteLong SET IdObjForm='{$v_iIdObjForm}'";
		//echo $sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}


	//Fonctions de définition
	function defIdObjForm ($v_iIdObjForm)
	{
		$this->oEnregBdd->IdObjForm = $v_iIdObjForm;
	}


	function defEnonQTL ($v_sEnonQTL)
	{
		$this->oEnregBdd->EnonQTL = $v_sEnonQTL;
	}

	function defAlignEnonQTL ($v_sAlignEnonQTL)
	{
		$this->oEnregBdd->AlignEnonQTL = $v_sAlignEnonQTL;
	}
	
	function defAlignRepQTL ($v_sAlignRepQTL)
	{
		$this->oEnregBdd->AlignRepQTL = $v_sAlignRepQTL;
	}
	
	function defLargeurQTL ($v_iLargeurQTL)
	{
		$this->oEnregBdd->LargeurQTL = trim($v_iLargeurQTL);
	}
	
	function defHauteurQTL ($v_iHauteurQTL)
	{
		$this->oEnregBdd->HauteurQTL = trim($v_iHauteurQTL);
	}

	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdObjForm; }
	function retEnonQTL () { return $this->oEnregBdd->EnonQTL; }
	function retAlignEnonQTL () { return $this->oEnregBdd->AlignEnonQTL; }
	function retAlignRepQTL () { return $this->oEnregBdd->AlignRepQTL; }
	function retLargeurQTL () { return $this->oEnregBdd->LargeurQTL; }
	function retHauteurQTL () { return $this->oEnregBdd->HauteurQTL; }

    /*
	** Fonction 		: cHtmlQTexteLong
	** Description	: renvoie le code html qui permet d'afficher une question de type texte "long",
	**				     si $v_iIdFC est passé en paramètre la réponse correspondante sera également affichée
	** Entrée			:
	**				$v_iIdFC : Id d'un formulaire complété -> récupération de la réponse dans la table correspondante
	** Sortie			:
	**				code html
	*/

	function cHtmlQTexteLong($v_iIdFC = NULL)
	{
		$sValeur = "";
		
		if ($v_iIdFC != NULL)
		{
			$sRequeteSql = "SELECT * FROM ReponseTexte"
			." WHERE IdFC = '{$v_iIdFC}' AND IdObjForm = '{$this->oEnregBdd->IdObjForm}'";
			
			$hResultRep = $this->oBdd->executerRequete($sRequeteSql);
			$oEnregRep = $this->oBdd->retEnregSuiv($hResultRep);
			$sValeur = $oEnregRep->Valeur;
		}
		
		//Mise en forme du texte (ex: remplacement de [b][/b] par le code html adéquat)
		$this->oEnregBdd->EnonQTL = convertBaliseMetaVersHtml($this->oEnregBdd->EnonQTL);
		
		//Genération du code html représentant l'objet
		$sCodeHtml="\n<!--QTexteLong : {$this->oEnregBdd->IdObjForm} -->\n"
			."<div align={$this->oEnregBdd->AlignEnonQTL}>{$this->oEnregBdd->EnonQTL}</div>\n"
			."<div class=\"InterER\" align={$this->oEnregBdd->AlignRepQTL}>\n"
			."<TEXTAREA NAME=\"{$this->oEnregBdd->IdObjForm}\" ROWS=\"{$this->oEnregBdd->HauteurQTL}\" COLS=\"{$this->oEnregBdd->LargeurQTL}\">\n"
			."$sValeur"
			."</TEXTAREA>\n"
			."</div><br>\n";
			   
		return $sCodeHtml;
	}


	function cHtmlQTexteLongModif($v_iIdObjForm,$v_iIdFormulaire)
	{
		global $HTTP_POST_VARS, $HTTP_GET_VARS;
		
		$oObjetFormulaire = new CObjetFormulaire($this->oBdd, $v_iIdObjForm);
		
		//initialisation des messages d'erreurs à 'vide' et de la variable servant a détecter
		//si une erreur dans le remplissage du formulaire a eu lieu (ce qui engendre le non enregistrement
		//de celui-ci dans la base de données + affichage d'une astérisque à l'endroit de l'erreur)
		$sMessageErreur1 = $sMessageErreur2 = $sMessageErreur3 = "";
		$iFlagErreur=0;
		
		if (isset($HTTP_POST_VARS['envoyer'])) 
		{
			//Récupération des variables transmises par le formulaire
			$this->oEnregBdd->EnonQTL = stripslashes($HTTP_POST_VARS['Enonce']);
			$this->oEnregBdd->AlignEnonQTL = $HTTP_POST_VARS['AlignEnon'];
			$this->oEnregBdd->AlignRepQTL = $HTTP_POST_VARS['AlignRep'];
			$this->oEnregBdd->LargeurQTL = $HTTP_POST_VARS['Largeur'];
			$this->oEnregBdd->HauteurQTL = $HTTP_POST_VARS['Hauteur'];		
			
			//Test des données reçues et marquage des erreurs à l'aide d'une astérisque dans le formulaire
			if (strlen($HTTP_POST_VARS['Enonce']) < 1) { $sMessageErreur1="<font color =\"red\">*</font>"; $iFlagErreur=1;}
			if (!(int)$HTTP_POST_VARS['Largeur']) { $sMessageErreur2="<font color =\"red\">*</font>"; $iFlagErreur=1;}
			if (!(int)$HTTP_POST_VARS['Hauteur']){$sMessageErreur3="<font color =\"red\">*</font>"; $iFlagErreur=1;}
			
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
			Alignement($this->oEnregBdd->AlignEnonQTL,$this->oEnregBdd->AlignRepQTL);
			  
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
			   ."<TD><textarea name=\"Enonce\" rows=\"5\" cols=\"70\" style=\"width: 100%\">{$this->oEnregBdd->EnonQTL}</textarea></TD>\n"
			   ."</TR>\n"
			   ."</TABLE>\n"
			   ."</fieldset>\n"
			   
			   ."<fieldset><legend>&nbsp;Zone réponse&nbsp;</legend>\n"
				."<TABLE BORDER=\"0\" WIDTH=\"99%\"><TR><TD ALIGN=\"right\">"
					."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"left\" $ar1 ID=\"idAlignRepG\"><label for=\"idAlignRepG\">Gauche</label>\n"
					."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"right\" $ar2 ID=\"idAlignRepD\"><label for=\"idAlignRepD\">Droite</label>\n"
					."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"center\" $ar3 ID=\"idAlignRepC\"><label for=\"idAlignRepC\">Centrer</label>\n"
					."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"justify\" $ar4 ID=\"idAlignRepJ\"><label for=\"idAlignRepJ\">Justifier</label>\n"
				."</TD></TR></TABLE>"
			   ."<TABLE width=\"99%\">\n"
			   ."<TR>\n"
			   ."<TD width=\"1\" align=\"right\" valign=\"top\">$sMessageErreur2 Largeur&nbsp;de&nbsp;la&nbsp;boîte&nbsp;de&nbsp;texte&nbsp;:&nbsp;&nbsp;</TD>\n"
			   ."<TD><input type=\"text\" size=\"3\" maxlength=\"10\" name=\"Largeur\" Value=\"{$this->oEnregBdd->LargeurQTL}\" onblur=\"verifNumeric(this)\"></TD>\n"
			   ."</TR><TR>\n"
			   ."<TD width=\"1\" align=\"right\" valign=\"top\">$sMessageErreur3 Hauteur&nbsp;de&nbsp;la&nbsp;boîte&nbsp;de&nbsp;texte&nbsp;:&nbsp;&nbsp;</TD>\n"
			   ."<TD><input type=\"text\" size=\"3\" maxlength=\"10\" name=\"Hauteur\" Value=\"{$this->oEnregBdd->HauteurQTL}\" onblur=\"verifNumeric(this)\"></TD>\n"
			   ."</TR>"
			   ."</TABLE>\n"
			   ."</fieldset>\n"
			   
				//Le champ caché ci-dessous "simule" le fait d'appuyer sur le bouton submit (qui s'appelait envoyer) et ainsi permettre l'enregistrement dans la BD
			   ."<input type=\"hidden\" name=\"envoyer\" value=\"1\">\n"
			   ."</form>\n";
		
		return $sCodeHtml;
	}
	
	function enregistrer ()
	{
		if ($this->oEnregBdd->IdObjForm != NULL)
		{
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			//de les stocker dans la BD sans erreur 
			$sEnonQTL = validerTexte($this->oEnregBdd->EnonQTL);
			
			$sRequeteSql = "REPLACE QTexteLong SET"
				." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
				.", EnonQTL='{$sEnonQTL}'"
				.", AlignEnonQTL='{$this->oEnregBdd->AlignEnonQTL}'"
				.", AlignRepQTL='{$this->oEnregBdd->AlignRepQTL}'"
				.", LargeurQTL='{$this->oEnregBdd->LargeurQTL}'"
				.", HauteurQTL='{$this->oEnregBdd->HauteurQTL}'";
			
			//echo "<br>enregistrer qtexteLong : ".$sRequeteSql;
			$this->oBdd->executerRequete($sRequeteSql);
			
			return TRUE;
		}
		else
		{
			echo "Identifiant NULL: enregistrement impossible";
		}
	}
	
	
	function enregistrerRep ($v_iIdFC, $v_iIdObjForm, $v_sReponsePersQTL)
	{
		if ($v_iIdObjForm != NULL)
		{
			// Les variables contenant du "texte" doivent être formatées, cela permet 
			// de les stocker dans la BD sans erreur 
			$sReponsePersQTL = validerTexte($v_sReponsePersQTL);
			
			$sRequeteSql = "REPLACE ReponseTexte SET"
				." IdFC='{$v_iIdFC}'"
				.", IdObjForm='{$v_iIdObjForm}'"
				.", Valeur='{$sReponsePersQTL}'";
				
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
		$sEnonQTL = validerTexte($this->oEnregBdd->EnonQTL);
		
		$sRequeteSql = "INSERT INTO QTexteLong SET"
			." IdObjForm='{$v_iIdNvObjForm}'"
			.", EnonQTL='{$sEnonQTL}'"
			.", AlignEnonQTL='{$this->oEnregBdd->AlignEnonQTL}'"
			.", AlignRepQTL='{$this->oEnregBdd->AlignRepQTL}'"
			.", LargeurQTL='{$this->oEnregBdd->LargeurQTL}'"
			.", HauteurQTL='{$this->oEnregBdd->HauteurQTL}'"; 
			
		$this->oBdd->executerRequete($sRequeteSql);
		
		$iIdObjForm = $this->oBdd->retDernierId();

		return $iIdObjForm;
	}


	function effacer ()
	{
		$sRequeteSql = "DELETE FROM QTexteLong"
				." WHERE IdObjForm ='{$this->oEnregBdd->IdObjForm}'";
		//echo "<br>effacer QTexteLong()".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
}

?>
