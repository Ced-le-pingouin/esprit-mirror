<?php
require_once (dir_database("bdd.class.php"));  	//permet d'utiliser la bdd sans creer un objet
																//CProjet et ainsi cela permet de creer des objets
																//d'une autre classe à partir de celle-ci.
/*
** Fichier ................: qlistederoul.tbl.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 22-06-2004
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
		//si 0 crée un objet presque vide sinon 
		//rempli l'objet avec les données de la table Formulaire
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
				." FROM QListeDeroul"
				." WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
	    }

		$this->iId = $this->oEnregBdd->IdObjForm;
	}

	function ajouter ($v_iIdObjForm) //Cette fonction ajoute une question de type liste déroulante, avec tous ses champs
				 							// vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO QListeDeroul SET IdObjForm='{$v_iIdObjForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}


	//Fonctions de définition
	function defIdObjForm ($v_iIdObjForm) { $this->oEnregBdd->IdObjForm = $v_iIdObjForm; }
	function defEnonQLD ($v_sEnonQLD) { $this->oEnregBdd->EnonQLD = $v_sEnonQLD; }
	function defAlignEnonQLD ($v_sAlignEnonQLD) { $this->oEnregBdd->AlignEnonQLD = $v_sAlignEnonQLD; }
	function defAlignRepQLD ($v_sAlignRepQLD) { $this->oEnregBdd->AlignRepQLD = $v_sAlignRepQLD; }
	function defTxtAvQLD ($v_sTxtAvQLD) { $this->oEnregBdd->TxtAvQLD = $v_sTxtAvQLD; }
	function defTxtApQLD ($v_sTxtApQLD) { $this->oEnregBdd->TxtApQLD = $v_sTxtApQLD; }
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdObjForm; }
	function retEnonQLD () { return $this->oEnregBdd->EnonQLD; }
	function retAlignEnonQLD () { return $this->oEnregBdd->AlignEnonQLD; }
	function retAlignRepQLD () { return $this->oEnregBdd->AlignRepQLD; }
	function retTxTAvQLD () { return $this->oEnregBdd->TxtAvQLD; }
	function retTxtApQLD () { return $this->oEnregBdd->TxtApQLD; }


	/*
	** Fonction		: RetourReponseQLD
	** Description	: renvoie le code html contenant la liste déroulante avec les réponses,
	**				  si $v_iIdFC la réponse fournie par l'étudiant sera pré-sélectionnée	
	** Entrée		:
	**				$v_iIdFC : Id d'un formulaire complété -> récupération de la réponse dans la table correspondante
	** Sortie		:
	**				code html
	*/

	function RetourReponseQLD($v_iIdFC=NULL)
	{
		$iIdReponseEtu = "";
		if ($v_iIdFC != NULL)
		{
			//Sélection de la réponse donnée par l'étudiant
			$sRequeteSql = "SELECT * FROM ReponseEntier"
				." WHERE IdFC = '{$v_iIdFC}' AND IdObjForm = '{$this->oEnregBdd->IdObjForm}'";
			
			$hResultRep = $this->oBdd->executerRequete($sRequeteSql);
			$oEnregRep = $this->oBdd->retEnregSuiv($hResultRep);
			$iIdReponseEtu = $oEnregRep->IdReponse;
		}
		
		//Sélection de toutes les réponses concernant l'objet QListeDeroul en cours de traitement
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
				$sPreSelection = "SELECTED";
			else
				$sPreSelection = "";
			
			$CodeHtml.="<OPTION VALUE=\"$IdReponseTemp\" $sPreSelection>$TexteTemp\n";
		}
		$CodeHtml.="</SELECT>\n";

		$this->oBdd->libererResult($hResultRRQLD);
		return "$CodeHtml";
	}


	/*
	** Fonction 		: cHtmlQListeDeroul
	** Description		: renvoie le code html qui permet d'afficher une question de type liste déroulante,
	**				     si $v_iIdFC est passé en paramètre il est envoyé à la fonction RetourReponseQLD qui permettra
	**					  de pré-sélectionner la réponse entrée par l'étudiant
	** Entrée			:
	**					$v_iIdFC : Id d'un formulaire complété
	** Sortie			:
	**					code html
	*/

	function cHtmlQListeDeroul($v_iIdFC=NULL)
	{
		//Mise en forme du texte (ex: remplacement de [b][/b] par le code html adéquat)
		$this->oEnregBdd->EnonQLD = convertBaliseMetaVersHtml($this->oEnregBdd->EnonQLD);
		$this->oEnregBdd->TxtAvQLD = convertBaliseMetaVersHtml($this->oEnregBdd->TxtAvQLD);
		$this->oEnregBdd->TxtApQLD = convertBaliseMetaVersHtml($this->oEnregBdd->TxtApQLD);
		
		//Genération du code html représentant l'objet
		$sCodeHtml="\n<!--QListeDeroul : {$this->oEnregBdd->IdObjForm} -->\n"
			."<div align={$this->oEnregBdd->AlignEnonQLD}>{$this->oEnregBdd->EnonQLD}</div>\n"
			."<div class=\"InterER\" align={$this->oEnregBdd->AlignRepQLD}>\n"
			."{$this->oEnregBdd->TxtAvQLD} \n"
			.$this->RetourReponseQLD($v_iIdFC) 			//Appel de la fonction qui renvoie les réponses sous forme de liste déroulante, 
														//avec la réponse sélectionnée par l'étudiant si IdFC est présent
			." {$this->oEnregBdd->TxtApQLD}\n"
			."</div>\n"
			;
		
		return $sCodeHtml;
	}

	/*
	** Fonction 		: RetourReponseQLDModif
	** Description		: va rechercher dans la table réponse les réponses correspondant
	**					  a la question de type liste déroulante en cours de traitement 
	**					  + mise en page de ces réponses avec possibilité de modification
	** Entrée			:
	** Sortie			: Code Html contenant les réponses + mise en page + modification possible
	*/

	function RetourReponseQLDModif($v_iIdObjForm, $v_iIdFormulaire, $v_bUtiliserScores = TRUE, $v_bUtiliserAxes = TRUE)
	{
		$hResult = $this->oBdd->executerRequete
		(
			"  SELECT MAX(OrdreReponse) AS OrdreMax FROM Reponse"
			." WHERE IdObjForm = '{$this->oEnregBdd->IdObjForm}'"
		);
		$oEnreg = $this->oBdd->retEnregSuiv();
		$iOrdreMax = $oEnreg->OrdreMax;
		$this->oBdd->libererResult($hResult);
		
		// sélection de toutes les réponses concernant l'objet QListeDeroul en cours de traitement
		$sRequeteSql = "SELECT * FROM Reponse WHERE IdObjForm = '{$this->iId}' ORDER BY OrdreReponse";
		$hResultInt = $this->oBdd->executerRequete($sRequeteSql);
		
		$CodeHtml="";
		
		if ($v_bUtiliserScores)
			$sDisplayScores = "inline";
		else
			$sDisplayScores = "none";
		
		if ($v_bUtiliserAxes)
			$sDisplayAxes = "inline";
		else
			$sDisplayAxes = "none";
		
		if ($this->oBdd->retNbEnregsDsResult($hResultInt) > 0)
		{
			$iNumProposition = 1;
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResultInt))
			{
				$oReponse = new CReponse($this->oBdd);
				$oReponse->init($oEnreg);
				
				//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
				$TexteTemp = $oReponse->retTexteReponse();
				$sFeedbackTemp = $oReponse->retFeedback();
				$IdReponseTemp = $oReponse->retId();
				$IdObjFormTemp = $oReponse->retIdObjForm();
				
				$sCorrectionTemp = $oReponse->retCorrection();
				if ($sCorrectionTemp == 'v')
				{
					$sSelV = "CHECKED"; $sSelX = ""; $sSelN = "";
				}
				else if ($sCorrectionTemp == 'x')
				{
					$sSelV = ""; $sSelX = "CHECKED"; $sSelN = "";
				}
				else if ($sCorrectionTemp == '-')
				{
					$sSelV = ""; $sSelX = ""; $sSelN = "CHECKED";
				}
				
				$CodeHtml .= 
					"<TABLE BORDER=\"0\" WIDTH=\"99%\">"
					."<TR>"
						."<TD VALIGN=\"top\" WIDTH=\"1\">"
							."&nbsp;Proposition&nbsp;{$iNumProposition}&nbsp;:&nbsp;&nbsp;"
						."</TD>"
						."<TD>"
							."<TABLE BORDER=\"0\" WIDTH=\"100%\">"
							."<TR><TD>"
								."<input type=\"text\" size=\"".LARGEUR_CASE_PROP."\" maxlength=\"255\" name=\"rep[$IdReponseTemp]\" Value=\"$TexteTemp\">\n"
				;
				
				$sCodeOptionsOrdre = "";
				for ($iNumOrdre = 1; $iNumOrdre <= $iOrdreMax; $iNumOrdre++)
				{
					if ($iNumOrdre == $oReponse->retOrdre())
						$sSelOrdre = "SELECTED";
					else
						$sSelOrdre = "";
					
					$sCodeOptionsOrdre .= "<option value=\"$iNumOrdre\" $sSelOrdre>$iNumOrdre</option>";
				}
				
				$CodeHtml .=
					 "&nbsp;"
					."<select name=\"selOrdreProposition[$IdReponseTemp]\">"
					.$sCodeOptionsOrdre
					."</select>"
				;
				
				$CodeHtml .=
								 "<span style=\"display: $sDisplayScores;\">"
									."&nbsp;<IMG SRC=\"".dir_theme_commun('icones/v.gif')."\" ALIGN=\"top\"><input type=\"radio\" name=\"correctionRep[$IdReponseTemp]\" value=\"v\" $sSelV>&nbsp;&nbsp;"
									."&nbsp;<IMG SRC=\"".dir_theme_commun('icones/x.gif')."\" ALIGN=\"top\"><input type=\"radio\" name=\"correctionRep[$IdReponseTemp]\" value=\"x\" $sSelX>&nbsp;&nbsp;"
									."&nbsp;<IMG SRC=\"".dir_theme_commun('icones/-.gif')."\" ALIGN=\"top\"><input type=\"radio\" name=\"correctionRep[$IdReponseTemp]\" value=\"-\" $sSelN>&nbsp;&nbsp;"
								."</span>"
								."&nbsp;&nbsp;"
								."<span style=\"display: $sDisplayAxes;\">"
									."<a href=\"javascript: afficherAxes($IdReponseTemp, -1);\" class=\"lienModif\">Axes</a>"
								."</span>"
							."</td></tr>"
							."<tr><td align=\"left\">"
								.RetourPoidsReponse($v_iIdFormulaire, $v_iIdObjForm, $IdReponseTemp, $v_bUtiliserAxes)
							."</td></tr>"
							."<tr><td>"
								."<textarea cols=\"40\" rows=\"2\" style=\"width: 90%\" name=\"feedbackRep[$IdReponseTemp]\">$sFeedbackTemp</textarea>"
							."</td></tr>"
							."</TABLE>"
						."</TD>"
					."</TR>"
					."<TR>"
						."<td colspan=\"2\" align=\"right\">"
							."<div width=\"95%\" align=\"right\" style=\"padding-top: 10px; padding-right: 5px; padding-bottom: 3px; margin-bottom: 10px;"
							 ." border-bottom: 1px solid rgb(208,208,191);\">"
								."<a href=\"javascript: soumettre('ajouter',0);\" class=\"lienModif\">Ajouter</a> "
								."<a href=\"javascript: soumettre('supprimer',$IdReponseTemp);\" class=\"lienModif\">Supprimer</a>"
							."</div>"
						."</TD>"
					."</TR>"
					."</TABLE>"
				;
				$iNumProposition++;
			}
		}
		else
		{
			$CodeHtml .= "<a href=\"javascript: soumettre('ajouter',0);\" class=\"lienModif\">Ajouter une proposition</a>";
		}
		$this->oBdd->libererResult($hResultInt);
		
		//$CodeHtml .= "</TABLE>";
		return "$CodeHtml";
	}


	function cHtmlQListeDeroulModif($v_iIdObjForm,$v_iIdFormulaire)
	{
		global $HTTP_POST_VARS, $HTTP_GET_VARS;
		
		$oObjetFormulaire = new CObjetFormulaire($this->oBdd, $v_iIdObjForm);
		
		//initialisation des messages d'erreurs à 'vide' et de la variable servant a détecter
		//si une erreur dans le remplissage du formulaire a eu lieu (ce qui engendre le non enregistrement
		//de celui-ci dans la base de données + affiche d'une astérisque à l'endroit de l'erreur)
		$sMessageErreur1 = "";
		$iFlagErreur=0;
		
		if (isset($HTTP_POST_VARS['envoyer']) || $HTTP_POST_VARS['typeaction']=='ajouter' || $HTTP_POST_VARS['typeaction']=='supprimer')
		{
			//Récupération des variables transmises par le formulaire
			$this->oEnregBdd->EnonQLD = stripslashes($HTTP_POST_VARS['Enonce']);
			$this->oEnregBdd->AlignEnonQLD = $HTTP_POST_VARS['AlignEnon'];
			$this->oEnregBdd->AlignRepQLD = $HTTP_POST_VARS['AlignRep'];
			$this->oEnregBdd->TxtAvQLD = stripslashes($HTTP_POST_VARS['TxtAv']);
			$this->oEnregBdd->TxtApQLD = stripslashes($HTTP_POST_VARS['TxtAp']);
			
			//Test des données reçues et marquage des erreurs à l'aide d'une astérisque dans le formulaire
			//if (strlen($HTTP_POST_VARS['Enonce']) < 1) { $sMessageErreur1="<font color =\"red\">*</font>"; $iFlagErreur=1;}
			
			//if ($iFlagErreur == 0) //si pas d'erreur, enregistrement physique dans la BD
			//{
				$oObjetFormulaire->verrouillerTablesQuestion();
				
				//Enregistrement des réponses et de leurs poids pour les differents axes
				if (isset($HTTP_POST_VARS["rep"])) 	//on doit verifier car lorsque l'on appuie la premiere fois, apres avoir cree l'objet, 
													//sur ajouter $HTTP_POST_VARS["rep"] n'existe pas 
				{
					foreach ($HTTP_POST_VARS["rep"] as $v_iIdReponse => $v_sTexteTemp) 
					{
						$oReponse = new CReponse($this->oBdd);
						$oReponse->defId($v_iIdReponse);
						
						$oReponse->defTexteReponse(stripslashes($v_sTexteTemp));
						$oReponse->enregistrer(FALSE);  //On utilise FALSE car on n'initialise (on ne connait pas sa position)
						
						if (isset($HTTP_POST_VARS["repAxe"])) 	//Vérifier pour ne pas effectuer le traitement si aucun axe 
																// n'est défini pour ce formulaire
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
				
				// enregistrement de la position de l'objet
				$oObjetFormulaire->DeplacerObjet($HTTP_POST_VARS["selOrdreObjet"], FALSE);
				// enregistrement de l'objet QListeDeroul actuel dans la BD
				$this->enregistrer();
				$oObjetFormulaire->deverrouillerTablesQuestion();
					  		
				//Lorsque la question est bien enregistrée dans la BD 
				//(Pour cela on a cliqué sur le bouton 'Appliquer les changements')
				//on rafraîchit la liste en cochant l'objet que l'on est en train de traiter
				echo "<script>\n";
				echo "rechargerliste($v_iIdObjForm,$v_iIdFormulaire)\n";
				echo "</script>\n";
			//}  //Fin SI 'Enregistrement physique'
		}
		
		//Si on a cliqué sur le lien 'Ajouter' cela affecte, via javascript, au champ caché ['typeaction']
		//la valeur 'ajouter' et au champ caché parametre la valeur '0'.
		//Attention lorsque l'on clique sur le lien 'Ajouter' cela implique également 
		//un enregistrement d'office dans la BD des modifications déjà effectuées sur l'objet en cours. 
		//(avec les vérifications d'usage avant enregistrement dans la BD)
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
			La réponse qui sera créée ici contiendra :
						le numero de l'objet auquel elle appartient
						l'ordre dans lequel elle sera affichée (toujours en dernière place)
						son numéro d'identifiant sera attribué automatiquement par MySql
			le texte de la réponse sera attribué par après.
			*/
			$oReponse->enregistrer();
			$this->oBdd->libererResult($hResultInt2);
		}
		
		//Si on a cliqué sur le lien 'Supprimer' cela affecte, via javascript, au champ caché ['typeaction']
		//la valeur 'supprimer' et au champ caché ['parametre'] l'id de la réponse a supprimer.
		//Attention lorsque l'on clique sur le lien 'supprimer' cela implique également 
		//un enregistrement d'office dans la BD des modifications déjà effectuées sur l'objet en cours.
		//(avec les vérifications d'usage avant enregistrement dans la BD)
		if ($HTTP_POST_VARS['typeaction']=='supprimer')
		{
			$v_iIdReponse = $HTTP_POST_VARS['parametre'];
			$oReponse = new CReponse($this->oBdd,$v_iIdReponse);
			$oReponse->effacer();
		}
		
		//La fonction alignement renvoie 2 variables de type string contenant "CHECKED" 
		//et les 6 autres contiennent une chaîne vide
		// aeX = alignement enoncé, arX = alignement réponse
		list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = Alignement($this->oEnregBdd->AlignEnonQLD,$this->oEnregBdd->AlignRepQLD);
		
		$sParam="?idobj=".$v_iIdObjForm."&idformulaire=".$v_iIdFormulaire;
		
		$sCodeHtml =
			"\n<form name=\"formmodif\" action=\"{$_SERVER['PHP_SELF']}$sParam\" method=\"POST\" enctype=\"text/html\">\n"
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
			."<TD align=\"left\" valign=\"top\" width=\"1\">$sMessageErreur1 Enoncé&nbsp;:&nbsp;&nbsp;</TD>\n"
			."<TD><textarea name=\"Enonce\" rows=\"5\" cols=\"70\" style=\"width: 100%\">{$this->oEnregBdd->EnonQLD}</textarea></TD>\n"
			."</TR>\n"
			."</TABLE>\n"
			."</fieldset>\n"
			
			."<fieldset><legend>&nbsp;Zone Réponse&nbsp;</legend>\n"
			."<TABLE BORDER=\"0\" WIDTH=\"99%\"><TR><TD ALIGN=\"right\">"
				."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"left\" $ar1 ID=\"idAlignRepG\"><label for=\"idAlignRepG\">Gauche</label>\n"
				."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"right\" $ar2 ID=\"idAlignRepD\"><label for=\"idAlignRepD\">Droite</label>\n"
				."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"center\" $ar3 ID=\"idAlignRepC\"><label for=\"idAlignRepC\">Centrer</label>\n"
				."<INPUT TYPE=\"radio\" NAME=\"AlignRep\" VALUE=\"justify\" $ar4 ID=\"idAlignRepJ\"><label for=\"idAlignRepJ\">Justifier</label>\n"
			."</TD></TR></TABLE>"
			
			."<TABLE BORDER=\"0\" WIDTH=\"99%\">\n"
			."<TR>\n"
				."<TD WIDTH=\"45%\">"
					."<textarea cols=\"40\" rows=\"2\" name=\"TxtAv\" style=\"width: 100%;\">{$this->oEnregBdd->TxtAvQLD}</textarea>"
				."</TD>\n"
				."<TD STYLE=\"text-align: center; vertical-align: middle; font-weight: bold; "
				  ."background-color: rgb(255,255,255); color: rgb(153,73,89); border: 1px solid rgb(127,157,185);\">"
					."Zone<br>réponse"
				."</TD>"
				."<TD WIDTH=\"45%\">"
					."<textarea cols=\"40\" rows=\"2\" name=\"TxtAp\" style=\"width: 100%;\">{$this->oEnregBdd->TxtApQLD}</textarea>"
				."</TD>\n"
			."</TR>\n"
			."</TABLE>\n"
			."</fieldset>\n"
			
			."<fieldset><legend>&nbsp;Propositions&nbsp;</legend>\n"
			.$this->RetourReponseQLDModif($v_iIdObjForm,$v_iIdFormulaire)
			."</fieldset>\n"
			
			."<INPUT TYPE=\"hidden\" NAME=\"typeaction\" VALUE=\"\">\n"
			."<INPUT TYPE=\"hidden\" NAME=\"parametre\" VALUE=\"\">\n"
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
			echo "Identifiant NULL enregistrement impossible";
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
			echo "Identifiant NULL enregistrement impossible";
		}
	}

	function copier ($v_iIdNvObjForm)
	{
		if ($v_iIdNvObjForm < 1)
			return;
		
		// Les variables contenant du "texte" doivent être formatées, cela permet 
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
