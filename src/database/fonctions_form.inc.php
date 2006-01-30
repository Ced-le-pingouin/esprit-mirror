<?php
/*
** Fichier ................: fonctions_form.inc.php
** Description ............: Ensemble de fonctions communes aux formulaires
** Date de cr�ation .......: 
** Derni�re modification ..: 22-06-2004
** Auteurs ................: Ludovic FLAMME
** Emails .................: ute@umh.ac.be
**
*/


	/*
	** Fonction 		: Alignement
	** Description		: Sert � cocher les cases concernant l'alignement d'un enonce
	**                    et de sa r�ponse
	** Entr�e			: $sAlignEnon,$sAlignRep : contiennent la chaine de caract�res 
	**										{left,right,center ou justify)
	** Sortie			: $ae1,$ae2,$ae3,$ae4,$ar1,$ar2,$ar3,$ar4
	**					Une cha�ne de caract�re $aeX contient "CHECKED" et les autres ""	
	**					Une cha�ne de caract�re $arX contient "CHECKED" et les autres ""
	*/
	
function Alignement($sAlignEnon,$sAlignRep)
{
	$sAE1 = $sAE2 = $sAE3 = $sAE4 = "";
	$sAR1 = $sAR2 = $sAR3 = $sAR4 = "";	

	if ($sAlignEnon != "")
	{
		if ($sAlignEnon == "left") { $sAE1 = "CHECKED"; }
		else if ($sAlignEnon == "right") { $sAE2 = "CHECKED"; }
		else if ($sAlignEnon == "center") { $sAE3 = "CHECKED"; }
		else if ($sAlignEnon == "justify") { $sAE4 = "CHECKED"; }
	}
	else { $ae1 = "CHECKED"; }
	
	if ($sAlignRep != "")
	{
		if ($sAlignRep == "left") { $sAR1 = "CHECKED"; }
		else if ($sAlignRep == "right") { $sAR2 = "CHECKED"; }
		else if ($sAlignRep == "center") { $sAR3 = "CHECKED"; }
		else if ($sAlignRep == "justify") { $sAR4 = "CHECKED"; }
	}
	else
	{ $ar1 = "CHECKED"; }

	return array($sAE1,$sAE2,$sAE3,$sAE4,$sAR1,$sAR2,$sAR3,$sAR4);
}
	
function validerTexte($v_sTexte)
{
	return mysql_escape_string(stripslashes(trim($v_sTexte)));
}

	
	/*
	** Fonction 		: RetourPoidsReponse
	** Description		: renvoie pour chaque r�ponse appartenant a l'objet en cours de traitement
	**				  le poids pour chaque axe du formulaire m�me si celui-ci est NULL 
	** Entr�e			: $v_iIdFormulaire,$v_iIdObjForm,$v_iIdReponse
	** Sortie			: Code Html contenant le(s) poids + mise en page + modification possible
	*/

function RetourPoidsReponse($v_iIdFormulaire,$v_iIdObjForm,$v_iIdReponse)
{
	/*
	Utilisation de l'objet CBdd bcp plus l�ger pour faire les requ�tes qu'un objet Projet
	Attention ne pas oublier le : require_once (dir_database("bdd.class.php"));
	*/
	$oCBdd2 = new CBdd;
	/*
	Cette requ�te retourne pour chaque r�ponse, n lignes repr�sentant chaque axe du formulaire.
	Chaque ligne contient la valeur de l'axe[poids] si elle existe sinon contient la valeur NULL pour cet axe. 
	Exemple de partie de r�sultat :
	+-------+---------------+-----------+--------------+--------------+-----------+--------+
	| IdAxe | DescAxe       | IdReponse | TexteReponse | OrdreReponse | IdObjForm | Poids  |
	+-------+---------------+-----------+--------------+--------------+-----------+--------+
	|     1 | Determination |        55 |              |            3 |       123 | [NULL] |
	|     2 | Objectivite   |        55 |              |            3 |       123 | [NULL] |
	+-------+---------------+-----------+--------------+--------------+-----------+--------+
	*/

	$sRequeteSqlAxes =	 "Select"
							 ." a.*"
							 .", r.*"
							 .", ra.Poids"
						 ." FROM"
							 ." Formulaire_Axe as fa"
							 .", Axe as a"
							 .", Reponse as r"
							 ." LEFT JOIN Reponse_Axe as ra ON (r.IdReponse = ra.IdReponse AND a.IdAxe = ra.IdAxe)"
					  	 ." WHERE"
							 ." fa.IdForm = '{$v_iIdFormulaire}' AND fa.IdAxe = a.IdAxe"
							 ." AND r.IdObjForm = '{$v_iIdObjForm}'"
							 ." AND r.IdReponse = '{$v_iIdReponse}'"
						 ." ORDER BY"
							 ." r.OrdreReponse"
							 .", a.DescAxe";
	
	//echo "<br><br>$sRequeteSqlAxes";
	$hResultAxe = $oCBdd2->executerRequete($sRequeteSqlAxes);

	$CodeHtml="";

	while ($oEnreg = $oCBdd2->retEnregSuiv($hResultAxe))
	{
		//Variables temporaires pour simplifier l'ecriture du code Html ci-dessous
		$iPoids= $oEnreg->Poids;
		$iIdAxe = $oEnreg->IdAxe;
		$iIdReponse = $oEnreg->IdReponse;
		$sDescAxe = $oEnreg->DescAxe;
	
		$CodeHtml.="<TR><TD></TD><TD>\n"
				  ."<TABLE><TR><TD width=200> &#149 $sDescAxe</TD><TD><input type=\"text\" size=\"4\" maxlength=\"4\" "
				  ."name=\"repAxe[$iIdReponse][$iIdAxe]\" Value=\"$iPoids\" onblur=\"verifNumeric(this)\"></TD></TR></TABLE>\n"
				  ."</TD></TR>\n"; 
	}

	return "$CodeHtml";
}

function CopierUnFormulaire(&$v_oBdd,$v_iIdFormulaire,$iIdNvPers)
{
	$this->oBdd = &$v_oBdd;
	
	//Copie de formulaire
	$oFormulaire = new CFormulaire($this->oBdd,$v_iIdFormulaire);
	$v_iIdNvFormulaire = $oFormulaire->copier($v_iIdFormulaire,$iIdNvPers);  //On envoie l'Id du formulaire a copier et l'IdPers du futur propri�taire de la copie
	
	//Copie des axes du formulaires
	CopieFormulaire_Axe($this->oBdd,$v_iIdFormulaire,$v_iIdNvFormulaire);
	
	//Copie des objets du formulaire 1 par 1
	$hResult = $this->oBdd->executerRequete("SELECT * FROM ObjetFormulaire"
								  ." WHERE IdForm = $v_iIdFormulaire");
								  
	while ($oEnreg = $this->oBdd->retEnregSuiv($hResult))
	{
		CopierUnObjetFormulaire($this->oBdd, $oEnreg, $v_iIdNvFormulaire);
		
		/*
		$oObjetFormulaire = new CObjetFormulaire($this->oBdd);
		$oObjetFormulaire->init($oEnreg);
		$iIdObjActuel = $oObjetFormulaire->retId();
		//echo"<br>iIdObjActuel : ".$iIdObjActuel;
		$iIdNvObjForm = $oObjetFormulaire->copier($v_iIdNvFormulaire,$iIdObjActuel);
		//echo"<br>iIdNvObjForm : ".$iIdNvObjForm;
		  
		switch($oObjetFormulaire->retIdTypeObj())
		{
			case 1:
				//echo "Objet de type 1<br>";
				$oQTexteLong = new CQTexteLong($this->oBdd,$iIdObjActuel);
				$oQTexteLong->copier($iIdNvObjForm);
				break;

			case 2:
				//echo "Objet de type 2<br>";
				$oQTexteCourt = new CQTexteCourt($this->oBdd,$iIdObjActuel);
				$oQTexteCourt->copier($iIdNvObjForm);
				break;
				
			case 3:
				//echo "Objet de type 3<br>";
				$oQNombre = new CQNombre($this->oBdd,$iIdObjActuel);
				$oQNombre->copier($iIdNvObjForm);
				break;
				
			case 4:
				//echo "Objet de type 4<br>";
				$oQListeDeroul = new CQListeDeroul($this->oBdd,$iIdObjActuel);
				$oQListeDeroul->copier($iIdNvObjForm);
				CopieReponses($this->oBdd,$iIdObjActuel,$iIdNvObjForm);
				break;
				
			case 5:
				//echo "Objet de type 5<br>";
				$oQRadio = new CQRadio($this->oBdd,$iIdObjActuel);
				$oQRadio->copier($iIdNvObjForm);
				CopieReponses($this->oBdd,$iIdObjActuel,$iIdNvObjForm);
				break;
				
			case 6:
				//echo "Objet de type 6<br>";
				$oQCocher = new CQCocher($this->oBdd,$iIdObjActuel);
				$oQCocher->copier($iIdNvObjForm);
				CopieReponses($this->oBdd,$iIdObjActuel,$iIdNvObjForm);
				break;
				
			case 7:
				//echo "Objet de type 7<br>";
				$oMPTexte = new CMPTexte($this->oBdd,$iIdObjActuel);
				$oMPTexte->copier($iIdNvObjForm);
				break;
				
			case 8:
				//echo "Objet de type 8<br>";
				$oMPSeparateur = new CMPSeparateur($this->oBdd,$iIdObjActuel);
				$oMPSeparateur->copier($iIdNvObjForm);
				break;
				
			default:
				echo "Erreur IdObjForm invalide<br>";
		} //Fin switch
		*/
	} //Fin while
	$this->oBdd->libererResult($hResult);
	return TRUE;
}

function CopierUnObjetFormulaire(&$v_oBdd, &$v_oObjForm, $v_iIdFormulaireDest, $v_iOrdreObjet = NULL)
{
	if (!is_object($v_oObjForm) && is_numeric($v_oObjForm))
	{
		$oObjetFormulaire = new CObjetFormulaire($v_oBdd, $v_oObjForm);
	}
	else
	{
		$oObjetFormulaire = new CObjetFormulaire($v_oBdd);
		$oObjetFormulaire->init($v_oObjForm);
	}
	
	$iIdObjActuel = $oObjetFormulaire->retId();
	//echo"<br>iIdObjActuel : ".$iIdObjActuel;
	$iIdNvObjForm = $oObjetFormulaire->copier($v_iIdFormulaireDest, $iIdObjActuel, $v_iOrdreObjet);
	//echo"<br>iIdNvObjForm : ".$iIdNvObjForm;
	
	switch($oObjetFormulaire->retIdTypeObj())
	{
		case 1:
			//echo "Objet de type 1<br>";
			$oQTexteLong = new CQTexteLong($v_oBdd,$iIdObjActuel);
			$oQTexteLong->copier($iIdNvObjForm);
			break;
		
		case 2:
			//echo "Objet de type 2<br>";
			$oQTexteCourt = new CQTexteCourt($v_oBdd,$iIdObjActuel);
			$oQTexteCourt->copier($iIdNvObjForm);
			break;
		
		case 3:
			//echo "Objet de type 3<br>";
			$oQNombre = new CQNombre($v_oBdd,$iIdObjActuel);
			$oQNombre->copier($iIdNvObjForm);
			break;
		
		case 4:
			//echo "Objet de type 4<br>";
			$oQListeDeroul = new CQListeDeroul($v_oBdd,$iIdObjActuel);
			$oQListeDeroul->copier($iIdNvObjForm);
			CopieReponses($v_oBdd,$iIdObjActuel,$iIdNvObjForm);
			break;
		
		case 5:
			//echo "Objet de type 5<br>";
			$oQRadio = new CQRadio($v_oBdd,$iIdObjActuel);
			$oQRadio->copier($iIdNvObjForm);
			CopieReponses($v_oBdd,$iIdObjActuel,$iIdNvObjForm);
			break;
		
		case 6:
			//echo "Objet de type 6<br>";
			$oQCocher = new CQCocher($v_oBdd,$iIdObjActuel);
			$oQCocher->copier($iIdNvObjForm);
			CopieReponses($v_oBdd,$iIdObjActuel,$iIdNvObjForm);
			break;
		
		case 7:
			//echo "Objet de type 7<br>";
			$oMPTexte = new CMPTexte($v_oBdd,$iIdObjActuel);
			$oMPTexte->copier($iIdNvObjForm);
			break;
		
		case 8:
			//echo "Objet de type 8<br>";
			$oMPSeparateur = new CMPSeparateur($v_oBdd,$iIdObjActuel);
			$oMPSeparateur->copier($iIdNvObjForm);
			break;
		
		default:
			echo "Erreur IdObjForm invalide<br>";
	} //Fin switch
	
	return $iIdNvObjForm;
}

function CopieReponses(&$v_oBdd,$v_iIdObjForm,$v_iIdNvObjForm)
{
	$this->oBdd = &$v_oBdd;
	$hResult2 = $this->oBdd->executerRequete("SELECT * FROM Reponse"
										  ." WHERE IdObjForm = $v_iIdObjForm");
										  
	while ($oEnreg2 = $this->oBdd->retEnregSuiv($hResult2))
	{
		$oReponse = new CReponse($this->oBdd);
		$oReponse->init($oEnreg2);
		$iIdReponse = $oReponse->retId();
		$iIdNvReponse = $oReponse->copier($v_iIdNvObjForm);
		CopieReponse_Axe($this->oBdd,$iIdReponse,$iIdNvReponse);
	}
	$this->oBdd->libererResult($hResult2);
	return TRUE;
}

function CopieReponse_Axe(&$v_oBdd,$v_iIdReponse,$v_iIdNvReponse)
{
	$this->oBdd = &$v_oBdd;
	
	$sRequeteSql = "SELECT * FROM Reponse_Axe"
						." WHERE IdReponse = $v_iIdReponse";
	//echo $sRequeteSql;
	$hResult3 = $this->oBdd->executerRequete($sRequeteSql);
											  
	while ($oEnreg3 = $this->oBdd->retEnregSuiv($hResult3))
	{
		$oReponse_Axe = new CReponse_Axe($this->oBdd);
		$oReponse_Axe->init($oEnreg3);
		$oReponse_Axe->copier($v_iIdNvReponse);
	}
	$this->oBdd->libererResult($hResult3);
	return TRUE;
}


function CopieFormulaire_Axe(&$v_oBdd,$v_iIdForm,$v_iIdNvForm)
{
	$this->oBdd = &$v_oBdd;
	$hResult4 = $this->oBdd->executerRequete("SELECT * FROM Formulaire_Axe"
											  ." WHERE IdForm = $v_iIdForm");
										  
	while ($oEnreg4 = $this->oBdd->retEnregSuiv($hResult4))
	{
		$oFormulaire_Axe = new CFormulaire_Axe($this->oBdd);
		$oFormulaire_Axe->init($oEnreg4);
		$oFormulaire_Axe->copier($v_iIdNvForm);
	}
	$this->oBdd->libererResult($hResult4);
	return TRUE;
}
?>
