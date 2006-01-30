<?php

/*
** Fichier ................: qcocher.tbl.php
** Description ............: 
** Date de création .......: 
** Dernière modification ..: 22-06-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/

class CQCocher
{
	var $oBdd;
	var $iId;
	var $oEnregBdd;
	var $aoFormulaire;
	
	function CQCocher(&$v_oBdd, $v_iId = 0) 
	{
		$this->oBdd = &$v_oBdd;
			//si 0 crée un objet presque vide sinon 
			//rempli l'objet avec les données de la table QRadio
			//de l'elément ayant l'Id passé en argument 
			//(ou avec l'objet passé en argument mais sans passer par le constructeur)
		if (($this->iId = $v_iId) > 0)
			$this->init();
	}
	
	//INIT est une fonction que l'on peut utiliser sans passer par le constructeur. 
	//On lui passe alors un objet obtenu par exemple en faisant une requête sur une autre page.
	//Ceci permet alors d'utiliser toutes les fonctions disponibles sur cet objet
	function init ($v_oEnregExistant = NULL)
	{
		if (isset($v_oEnregExistant))
		{
			$this->oEnregBdd = $v_oEnregExistant;
		}
		else
		{
			$sRequeteSql = "SELECT * FROM QCocher WHERE IdObjForm='{$this->iId}'";
			$hResult = $this->oBdd->executerRequete($sRequeteSql);
			$this->oEnregBdd = $this->oBdd->retEnregSuiv($hResult);
			$this->oBdd->libererResult($hResult);
		}
		
		$this->iId = $this->oEnregBdd->IdObjForm;
	}
	
	function ajouter ($v_iIdObjForm) //Cette fonction ajoute une question de type checkbox,
				 // avec tous ses champs vide, en fin de table
	{
		$sRequeteSql = "INSERT INTO QCocher SET IdObjForm='{$v_iIdObjForm}'";
		$this->oBdd->executerRequete($sRequeteSql);
		return ($this->iId = $this->oBdd->retDernierId());
	}
	
	/*
	** Fonction 		: RetourReponseQC
	** Description	: renvoie le code html contenant les checkbox avec les réponses,
	**					  si $v_iIdFC est présent la/les réponse(s) fournie(s) par l'étudiant sera/seront pré-sélectionnée	
	** Entrée			:
	**				$NbRepMaxQCTemp : nombre de réponses que l'étudiant peut cocher au maximum
	**				$MessMaxQCTemp : message personnalisé si l'étudiant coche trop de cases
	**				$v_iIdFC : Id d'un formulaire complété -> récupération de la réponse dans la table correspondante
	** Sortie			:
	**				code html
	*/
	function RetourReponseQC($NbRepMaxQCTemp, $MessMaxQCTemp, $v_iIdFC = NULL)
	{
		$TabRepEtu = array();
		if ($v_iIdFC != NULL)
		{
			//Sélection de la réponse donnée par l'étudiant
			$sRequeteSql = "SELECT * FROM ReponseEntier"
							." WHERE IdFC = '{$v_iIdFC}' AND IdObjForm = '{$this->oEnregBdd->IdObjForm}'";
			$hResultRep = $this->oBdd->executerRequete($sRequeteSql);
			
			$i=0;
			$TabRepEtu=array();
			
			while ($oEnregRep = $this->oBdd->retEnregSuiv($hResultRep))
			{
				$TabRepEtu[$i] = $oEnregRep->IdReponse;
				//echo "<br>La réponse ".$TabRepEtu[$i];
				$i++;
			}
		}

		//Sélection de toutes les réponses concernant l'objet QRadio en cours de traitement
		$sRequeteSql = "SELECT * FROM Reponse WHERE IdObjForm = '{$this->iId}'"
					." ORDER BY OrdreReponse";
		$hResultRRQC = $this->oBdd->executerRequete($sRequeteSql);
		
		
		if ($this->oEnregBdd->DispQC == 'Ver')  //Présentation sous forme de tableau
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
				$IdObjFormTemp = $IdObjFormTemp."[]"; //utilise un tableau pour stocker les differents résultats possibles
				
				if(in_array($IdReponseTemp, $TabRepEtu))
					{$sPreSelection = "CHECKED";}
				else
					{$sPreSelection = "";}
				
				$CodeHtml.= "<TR><TD><INPUT TYPE=\"checkbox\" NAME=\"$IdObjFormTemp\" "
					."VALUE=\"$IdReponseTemp\" onclick=\"verifNbQcocher($NbRepMaxQCTemp,'$MessMaxQCTemp')\" $sPreSelection></TD><TD>$TexteTemp</TD></TR>\n";
			}
			$CodeHtml.="</TABLE>";
		}
		else //Présentation en ligne
		{
			$CodeHtml="";
			
			while ($oEnreg = $this->oBdd->retEnregSuiv($hResultRRQC))
			{
				$oReponse = new CReponse($this->oBdd);
				//$oReponse = new CReponse($oCBdd->oBdd);
				$oReponse->init($oEnreg);
				
				//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
				$TexteTemp = $oReponse->retTexteReponse();
				$TexteTemp = convertBaliseMetaVersHtml($TexteTemp);
				$IdReponseTemp = $oReponse->retId();
				$IdObjFormTemp = $oReponse->retIdObjForm();
				$IdObjFormTemp = $IdObjFormTemp."[]"; //utilise un tableau pour stocker les differents résultats possibles
				
				if (in_array($IdReponseTemp, $TabRepEtu))
					{$sPreSelection = "CHECKED";}
				else
					{$sPreSelection = "";}
				
				$CodeHtml .= "<INPUT TYPE=\"checkbox\" NAME=\"$IdObjFormTemp\" "
					."VALUE=\"$IdReponseTemp\" onclick=\"verifNbQocher($NbRepMaxQCTemp,'$MessMaxQCTemp')\" $sPreSelection>$TexteTemp\n";
			}
		}
		
		$this->oBdd->libererResult($hResultRRQC);
		return "$CodeHtml";
	}
	
	/*
	** Fonction 		: cHtmlQCocher
	** Description	: renvoie le code html qui permet d'afficher une question de type case à cocher,
	**				     si $v_iIdFC est passé en paramètre il est envoyé à la fonction RetourReponseQC qui permettra
	**					  de pré-sélectionner la/les réponse(s) encodée(s) par l'étudiant
	** Entrée			:
	**				$v_iIdFC : Id d'un formulaire complété
	** Sortie			:
	**				code html
	*/
	function cHtmlQCocher($v_iIdFC = NULL)
	{
		//Mise en forme du texte (ex: remplacement de [b][/b] par le code html adéquat)
		$this->oEnregBdd->EnonQC = convertBaliseMetaVersHtml($this->oEnregBdd->EnonQC);
		
		//Si alignement vertical alors suppression des textes avant et après sinon mise en forme
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
		$sCodeHtml = "\n<!--QCocher : {$this->oEnregBdd->IdObjForm} -->\n"
			."<div align={$this->oEnregBdd->AlignEnonQC}>{$this->oEnregBdd->EnonQC}</div>\n"
			."<div class=\"InterER\" align={$this->oEnregBdd->AlignRepQC}>\n"
			."{$this->oEnregBdd->TxtAvQC} \n"
			//Appel de la fonction qui renvoie les réponses sous forme de cases à cocher,
			//avec la réponse sélectionnée par l'étudiant si IdFC est présent
			.$this->RetourReponseQC($this->oEnregBdd->NbRepMaxQC,$this->oEnregBdd->MessMaxQC,$v_iIdFC)
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
	** Sortie			: Code Html contenant les réponses + mise en page + modification possible
	*/
	
	function RetourReponseQCModif($v_iIdObjForm,$v_iIdFormulaire)
	{
		//Sélection de toutes les réponses concernant l'objet QRadio en cours de traitement
		$sRequeteSql = "SELECT * FROM Reponse WHERE IdObjForm = '{$this->iId}' ORDER BY OrdreReponse";
		
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
				$CodeHtml .= "<TD><input type=\"text\" size=\"70\" maxlength=\"255\" "
					."name=\"rep[$IdReponseTemp]\" Value=\"$TexteTemp\">\n"
					."<a href=\"javascript: soumettre('supprimer',$IdReponseTemp);\">Supprimer</a><br></TD></TR>\n"
					.RetourPoidsReponse($v_iIdFormulaire,$v_iIdObjForm,$IdReponseTemp); 
					//cette fc se trouve dans le fichier fonctions_form.inc.php
			}
			else
			{
				$CodeHtml .= "<TR><TD></TD><TD><input type=\"text\" size=\"70\" maxlength=\"255\" "
					."name=\"rep[$IdReponseTemp]\" Value=\"$TexteTemp\">\n"
					."<a href=\"javascript: soumettre('supprimer',$IdReponseTemp);\">Supprimer</a><br></TD></TR>\n"
					.RetourPoidsReponse($v_iIdFormulaire,$v_iIdObjForm,$IdReponseTemp); 
					//cette fc se trouve dans le fichier fonctions_form.inc.php
			}
		}
		$this->oBdd->libererResult($hResultRRQCM);
		return "$CodeHtml";
	}

	function cHtmlQCocherModif($v_iIdObjForm,$v_iIdFormulaire)
	{
		global $HTTP_POST_VARS, $HTTP_GET_VARS;
		
		//initialisation des messages d'erreurs à 'vide' et de la variable servant a détecter
		//si une erreur dans le remplissage du formulaire a eu lieu (ce qui engendre le non enregistrement
		//de celui-ci dans la base de données + affiche d'une astérisque à l'endroit de l'erreur)
		
		$sMessageErreur1 = $sMessageErreur2 = $sMessageErreur3 = "";
		$iFlagErreur=0;
		
		if (isset($HTTP_POST_VARS['envoyer']) || $HTTP_POST_VARS['typeaction']=='ajouter' || $HTTP_POST_VARS['typeaction']=='supprimer')
		{
			//Récupération des variables transmises par le formulaire
			$this->oEnregBdd->EnonQC = stripslashes($HTTP_POST_VARS['Enonce']);
			$this->oEnregBdd->AlignEnonQC = $HTTP_POST_VARS['AlignEnon'];
			$this->oEnregBdd->AlignRepQC = $HTTP_POST_VARS['AlignRep'];
			$this->oEnregBdd->TxtAvQC = stripslashes($HTTP_POST_VARS['TxtAv']);
			$this->oEnregBdd->TxtApQC = stripslashes($HTTP_POST_VARS['TxtAp']);
			$this->oEnregBdd->DispQC = $HTTP_POST_VARS['Disp'];
			$this->oEnregBdd->NbRepMaxQC = $HTTP_POST_VARS['NbRepMax'];		
			$this->oEnregBdd->MessMaxQC = $HTTP_POST_VARS['MessMax'];
			
			//Test des données reçues et marquage des erreurs à l'aide d'une astérisque dans le formulaire
			if (!(int)$HTTP_POST_VARS['NbRepMax'])
				{ $sMessageErreur2 = "<font color =\"red\">*</font>"; $iFlagErreur=1; }
				
			if ($iFlagErreur == 0) //si pas d'erreur, enregistrement physique dans la BD
			{
				/* Remplacé par la methode ci-dessous
				// Enregistrement des réponses de l'objet QCocher
				// Sélection de toutes les réponses concernant l'objet QRadio en cours de traitement
				$sRequeteSql =
					"  SELECT * FROM Reponse WHERE IdObjForm = '{$this->iId}'"
					." ORDER BY OrdreReponse";
				
				$hResultint = $this->oBdd->executerRequete($sRequeteSql);
				
				while ($oEnreg = $this->oBdd->retEnregSuiv($hResultint))
				{
					$oReponse = new CReponse($this->oBdd);
					$oReponse->init($oEnreg);
					
					// Variables temporaires pour simplifier l'ecriture ci-dessous
					$TexteTemp = $oReponse->retTexteReponse();
					$IdReponseTemp = $oReponse->retId();
					$TexteTemp = $HTTP_POST_VARS["$IdReponseTemp"];
					$oReponse->defTexteReponse(stripslashes($TexteTemp));
					$oReponse->enregistrer();
				}
				*/
							
				//Enregistrement des réponses et de leurs poids pour les differents axes
				if (isset($HTTP_POST_VARS["rep"])) 	//on doit verifier car lorsque l'on appuie la premiere fois apres avoir cree l'objet 
													//sur ajouter, $HTTP_POST_VARS["rep"] n'existe pas 
				{
					foreach ($HTTP_POST_VARS["rep"] as $v_iIdReponse => $v_sTexteTemp) 
					{
						$oReponse = new CReponse($this->oBdd);
						$oReponse->defId($v_iIdReponse);
						
						$oReponse->defTexteReponse(stripslashes($v_sTexteTemp));
						$oReponse->enregistrer(FALSE);
								
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
								}
							}
						}
					}
				}
							
				//Enregistrement de l'objet QCocher actuel dans la BD
				$this->enregistrer();
				
				//Lorsque la question est bien enregistrée dans la BD 
				//(Pour cela on a cliqué sur le bouton 'Appliquer les changements')
				//on rafraîchit la liste en cochant l'objet que l'on est en train de traiter
				
				echo "<script>\n";
				echo "rechargerliste($v_iIdObjForm,$v_iIdFormulaire)\n";
				echo "</script>\n";
			}
		}
		
		//Si on a cliqué sur le lien 'Ajouter' cela affecte, via javascript, au champ caché ['typeaction']
		//la valeur 'ajouter' et au champ caché parametre la valeur '0'.
		//Attention lorsque l'on clique sur le lien 'Ajouter' cela implique également 
		//un enregistrement d'office dans la BD des modifications déjà effectuées sur l'objet en cours. 
		//(avec les vérifications d'usage avant enregistrement dans la BD)
		if ($HTTP_POST_VARS['typeaction']=='ajouter')
		{
			//echo "je suis passé par ajouter";
			
			$hResultInt2 = $this->oBdd->executerRequete
			(
				"  SELECT MAX(OrdreReponse) AS OrdreMax FROM Reponse"
				." WHERE IdObjForm = '{$this->oEnregBdd->IdObjForm}'"
			);
			
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
			//echo "<br>je suis passé par supprimer";
			$v_iIdReponse = $HTTP_POST_VARS['parametre'];
			$oReponse = new CReponse($this->oBdd,$v_iIdReponse);
			$oReponse->effacer();
		}
		
		//La fonction alignement renvoie 2 variables de type string contenant "CHECKED" 
		//et les 6 autres contiennent une chaîne vide
		// aeX = alignement enoncé, arX = alignement réponse
		list($ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4) = Alignement($this->oEnregBdd->AlignEnonQC,$this->oEnregBdd->AlignRepQC);
		
		if ($this->oEnregBdd->DispQC == "Hor")
			{ $d1 = "CHECKED"; }
		else if ($this->oEnregBdd->DispQC == "Ver")
			{ $d2 = "CHECKED"; }
		else
			{ $d2 = "CHECKED"; }
		
		$sParam="?idobj=".$v_iIdObjForm."&idformulaire=".$v_iIdFormulaire;
		
		$sCodeHtml = 
			"\n<form name=\"formmodif\" action=\"{$_SERVER['PHP_SELF']}$sParam\"  method=\"POST\" enctype=\"text/html\">\n"
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
			//."</TR>\n"
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
			$sEnonQC = validerTexte($this->oEnregBdd->EnonQC);
			$sTxtAvQC = validerTexte($this->oEnregBdd->TxtAvQC);
			$sTxtApQC = validerTexte($this->oEnregBdd->TxtApQC);
			
			$sRequeteSql =
				"  REPLACE QCocher SET"
				." IdObjForm='{$this->oEnregBdd->IdObjForm}'"
				." , EnonQC='{$sEnonQC}'"
				." , AlignEnonQC='{$this->oEnregBdd->AlignEnonQC}'"
				." , AlignRepQC='{$this->oEnregBdd->AlignRepQC}'"
				." , TxtAvQC='{$sTxtAvQC}'"
				." , TxtApQC='{$sTxtApQC}'"
				." , DispQC='{$this->oEnregBdd->DispQC}'"
				." , NbRepMaxQC='{$this->oEnregBdd->NbRepMaxQC}'"
				." , MessMaxQC='{$this->oEnregBdd->MessMaxQC}'"
				;
			
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
		$sEnonQC = validerTexte($this->oEnregBdd->EnonQC);
		$sTxtAvQC = validerTexte($this->oEnregBdd->TxtAvQC);
		$sTxtApQC = validerTexte($this->oEnregBdd->TxtApQC);
		
		$sRequeteSql =
			"  INSERT INTO QCocher SET"
			." IdObjForm='{$v_iIdNvObjForm}'"
			." , EnonQC='{$sEnonQC}'"
			." , AlignEnonQC='{$this->oEnregBdd->AlignEnonQC}'"
			." , AlignRepQC='{$this->oEnregBdd->AlignRepQC}'"
			." , TxtAvQC='{$sTxtAvQC}'"
			." , TxtApQC='{$sTxtApQC}'"
			." , DispQC='{$this->oEnregBdd->DispQC}'"
			." , NbRepMaxQC='{$this->oEnregBdd->NbRepMaxQC}'"
			." , MessMaxQC='{$this->oEnregBdd->MessMaxQC}'";
			
		$this->oBdd->executerRequete($sRequeteSql);
		
		$iIdObjForm = $this->oBdd->retDernierId();
		
		return $iIdObjForm;
	}
	
	function enregistrerRep ($v_iIdFC,$v_iIdObjForm,$v_sReponsePersQC)
	{
		if ($v_iIdObjForm !=NULL)
		{
			$sRequeteSql =
				" INSERT INTO ReponseEntier SET"
				." IdFC='{$v_iIdFC}'"
				." , IdObjForm='{$v_iIdObjForm}'"
				." , IdReponse='{$v_sReponsePersQC}'";
			
			//echo "<br>enregistrer ReponsePersQC : ".$sRequeteSql;
			$this->oBdd->executerRequete($sRequeteSql);
			
			return TRUE;
		}
		else
		{
			echo "Identifiant NULL: enregistrement impossible";
		}
	}
	
	function effacer ()
	{
		$sRequeteSql = "DELETE FROM QCocher WHERE IdObjForm ='{$this->oEnregBdd->IdObjForm}'";
		//echo "<br>effacer QCocher()".$sRequeteSql;
		$this->oBdd->executerRequete($sRequeteSql);
		
		return TRUE;
	}
	
	//Fonctions de définition
	function defIdObjForm ($v_iIdObjForm) { $this->oEnregBdd->IdObjForm = $v_iIdObjForm; }
	function defEnonQC ($v_sEnonQC) { $this->oEnregBdd->EnonQC = $v_sEnonQC; }
	function defAlignEnonQC ($v_sAlignEnonQC) { $this->oEnregBdd->AlignEnonQC = $v_sAlignEnonQC; }
	function defAlignRepQC ($v_sAlignRepQC) { $this->oEnregBdd->AlignRepQC = $v_sAlignRepQC; }
	function defTxtAvQC ($v_sTxtAvQC) { $this->oEnregBdd->TxtAvQC = $v_sTxtAvQC; }
	function defTxtApQC ($v_sTxtApQC) { $this->oEnregBdd->TxtApQC = $v_sTxtApQC; }
	function defDispQC ($v_sDispQC) { $this->oEnregBdd->DispQR = $v_sDispQR; }
	function defNbRepMaxQC ($v_iNbRepMaxQC) { $this->oEnregBdd->NbRepMaxQC = $v_iNbRepMaxQC; }
	function defMessMaxQC ($v_sMessMaxQC) { $this->oEnregBdd->MessMaxQC = $v_sMessMaxQC; }
	
	//Fonctions de retour
	function retId () { return $this->oEnregBdd->IdReponse; }
	function retEnonQC () { return $this->oEnregBdd->EnonQC; }
	function retAlignEnonQC () { return $this->oEnregBdd->AlignEnonQC; }
	function retAlignRepQC () { return $this->oEnregBdd->AlignRepQC; }
	function retTxTAvQC () { return $this->oEnregBdd->TxtAvQC; }
	function retTxtApQC () { return $this->oEnregBdd->TxtApQC; }
	function retDispQC () { return $this->oEnregBdd->DispQC; }
	function retNbRepMaxQC () {return $this->oEnregBdd->NbRepMaxQC; }
	function retMessMaxQC () { return $this->oEnregBdd->MessMaxQC; }
}

?>
