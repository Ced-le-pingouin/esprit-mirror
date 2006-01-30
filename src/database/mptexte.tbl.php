<?php
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

	function ajouter ($v_iIdObjForm) 	//Cette fonction ajoute une mise en page de type texte,
										// avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO MPTexte SET IdObjForm='{$v_iIdObjForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}

	//Fonctions de définition
	function defIdObjForm ($v_iIdObjForm) { $this->oEnregBdd->IdObjForm = $v_iIdObjForm; }
	function defTexteMPT ($v_sTexteMPT) { $this->oEnregBdd->TexteMPT = $v_sTexteMPT; }
	function defAlignMPT ($v_sAlignMPT) { $this->oEnregBdd->AlignMPT = $v_sAlignMPT; }

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
		
		$oObjetFormulaire = new CObjetFormulaire($this->oBdd, $v_iIdObjForm);
		
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
	
		//La fonction alignement renvoie 1 variable de type string contenant "CHECKED" 
		//et les 7 autres contiennent une chaîne vide
		list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = 
		Alignement($this->oEnregBdd->AlignMPT," ");
		
		$sParam="?idobj=".$v_iIdObjForm."&idformulaire=".$v_iIdFormulaire;
		
		$sCodeHtml =
			"<form action=\"{$_SERVER['PHP_SELF']}$sParam\" name=\"formmodif\" method=\"POST\" enctype=\"text/html\">"
			.$oObjetFormulaire->cHtmlNumeroOrdre()
			."<fieldset><legend>&nbsp;Mise en page de type \"texte\"&nbsp;</legend>"

			."<TABLE BORDER=\"0\" WIDTH=\"99%\"><TR><TD ALIGN=\"right\">"
				."<INPUT TYPE=\"radio\" NAME=\"Align\" VALUE=\"left\" $ae1 ID=\"idAlignG\"><label for=\"idAlignG\">Gauche</label>\n"
				."<INPUT TYPE=\"radio\" NAME=\"Align\" VALUE=\"right\" $ae2 ID=\"idAlignD\"><label for=\"idAlignD\">Droite</label>\n"
				."<INPUT TYPE=\"radio\" NAME=\"Align\" VALUE=\"center\" $ae3 ID=\"idAlignC\"><label for=\"idAlignC\">Centrer</label>\n"
				."<INPUT TYPE=\"radio\" NAME=\"Align\" VALUE=\"justify\" $ae4 ID=\"idAlignJ\"><label for=\"idAlignJ\">Justifier</label>\n"
			."</TD></TR></TABLE>"
			."</TABLE>"
			
			."<TABLE width=\"99%\">"
			."<TR>"
			."<TD width=\"1\" align=\"right\" valign=\"top\">$sMessageErreur1 Texte&nbsp;:&nbsp;&nbsp;</TD>"
			."<TD><textarea name=\"Texte\" rows=\"5\" cols=\"70\" style=\"width: 100%\">{$this->oEnregBdd->TexteMPT}</textarea></TD>"
			."</TR>\n"
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
