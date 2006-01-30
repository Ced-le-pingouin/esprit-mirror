<?php

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// R�cup�rer les variables de l'url
// ---------------------
$url_iIdSousActiv = (empty($HTTP_GET_VARS["idSA"]) ? 0 : $HTTP_GET_VARS["idSA"]);
$url_aiIdResSA    = (empty($HTTP_GET_VARS["idResSA"]) ? NULL : explode("x",$HTTP_GET_VARS["idResSA"]));
$url_iErreur      = (empty($HTTP_GET_VARS["err"]) ? NULL : $HTTP_GET_VARS["err"]);

// ---------------------
// Initialiser
// ---------------------
$oSousActiv = new CSousActiv($oProjet->oBdd,$url_iIdSousActiv);

// ---------------------
// Construire la liste des documents non transf�r�
// ---------------------
if (isset($url_aiIdResSA))
{
	$sListeTransfert = "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"100%\">";
	
	$sListeTransfert .= "<tr>"
		."<td class=\"cellule_sous_titre\">&nbsp;Fichier source&nbsp;</td>"
		."<td class=\"cellule_sous_titre\">&nbsp;Collecticiel de&nbsp;destination&nbsp;</td>"
		."<td class=\"cellule_sous_titre\">&nbsp;D�pos� par&nbsp;</td>"
		."<td class=\"cellule_sous_titre\">&nbsp;Transf&eacute;r&eacute;&nbsp;</td>"
		."</tr>";
	
	$sNomClassCSS = NULL;
	
	foreach ($url_aiIdResSA as $iIdResSA)
	{
		$oRessourceSousActiv = new CRessourceSousActiv($oProjet->oBdd,$iIdResSA);
		
		$oRessourceSousActiv->initExpediteur();
		
		$sNomClassCSS = ($sNomClassCSS == "cellule_clair" ? "cellule_fonce" : "cellule_clair");
		
		$sListeTransfert .= "<tr>"
			."<td class=\"{$sNomClassCSS}\">&nbsp;<b>".$oRessourceSousActiv->retNom()."</b></td>"
			."<td class=\"{$sNomClassCSS}\" align=\"center\">".$oSousActiv->retNom()."</td>"
			."<td class=\"{$sNomClassCSS}\" align=\"center\">".$oRessourceSousActiv->oExpediteur->retNomComplet()."</td>"
			."<td class=\"{$sNomClassCSS}\" width=\"1%\" align=\"center\">&nbsp;<span class=\"Texte_Negatif\">non</span>&nbsp;</td>"
			."</tr>";
	}
	
	$sListeTransfert .= "</table>";
}

// ---------------------
// Construire le corp de la page avec un message d'erreur
// ---------------------
switch ($url_iErreur)
{
	case PAS_DOCUMENTS_SELECTIONNER:
	//   --------------------------
		$sTexteHTML = "<div align=\"center\">"
			."<h3>"
			."Vous devez, au moins, s&eacute;lectionner un document &agrave; transf&eacute;rer (Rubrique 1)"
			."</h3>"
			."</div>";
		
		break;
		
	case TRANSFERT_ECHOUE:
	//   ----------------
		$sTexteHTML = "<h3>"
			."Transfert &eacute;chou&eacute;&nbsp;:"
			."</h3>"
			."<br><br>"
			.$sListeTransfert
			."<br><br><div align=\"center\"><h3>V�rifier que les �tudiants associ�s � ces fichiers<br>sont bien inscrits dans le collecticiel cible.</h3></div>";
		
		break;
		
	case TRANSFERT_REUSSI_SAUF:
	//   ---------------------
		$sTexteHTML = "<h3>"
			."Transfert r&eacute;ussi sauf les fichiers suivants&nbsp;:"
			."</h3>"
			."<br><br>"
			.$sListeTransfert
			."<br><br><div align=\"center\"><h3>V�rifier que les �tudiants associ�s � ces fichiers<br>sont bien inscrits dans le collecticiel cible.</h3></div>";
		
		break;
		
	default:
		$sTexteHTML = "<div align=\"center\">"
			."<h3>"
			."Transfert r&eacute;ussi&nbsp;!"
			."</h3>"
			."</div>";
}

if ($url_iErreur == TRANSFERT_REUSSI || $url_iErreur == TRANSFERT_REUSSI_SAUF)
	$sTexteHTML .= "<script type=\"text/javascript\" language=\"javascript\">\n"
		."<!--\n"
		."top.opener.location = top.opener.location;\n"
		."//-->\n"
		."</script>\n";
?>
<html>
<head>
<?php inserer_feuille_style(); ?>
</head>
<body>
<?php echo $sTexteHTML; ?>
</body>
</html>

