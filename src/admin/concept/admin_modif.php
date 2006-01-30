<?php

/*
** Fichier ................: admin_modif.php
** Description ............:
** Date de cr�ation .......: 01/02/2002
** Derni�re modification ..: 26/01/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit� de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// D�claration des fichiers � inclure
// ---------------------
include_once("admin_modif.inc.php");
include_once(dir_code_lib("upload.inc.php"));

// ---------------------
// Initialisations
// ---------------------
$type = $params = $rafraichir = $act = NULL;

$g_iIdUtilisateur = (is_object($oProjet->oUtilisateur) ? $oProjet->oUtilisateur->retId() : 0);

$g_bModifier = $g_bModifierStatut = FALSE;

if (!empty($HTTP_POST_VARS))
{
	// R�cup�rer les variables du formulaire
	$type       = $HTTP_POST_VARS["type"];
	$params     = $HTTP_POST_VARS["params"];
	$rafraichir = (isset($HTTP_POST_VARS["rafraichir"]) ? $HTTP_POST_VARS["rafraichir"] : NULL);
	$act        = (isset($HTTP_POST_VARS["act"]) ? $HTTP_POST_VARS["act"] : NULL);
}
else if (!empty($HTTP_GET_VARS))
{
	// R�cup�rer les variables de l'url
	$type   = $HTTP_GET_VARS["type"];
	$params = $HTTP_GET_VARS["params"];
}

// Variable des fonctions (voir admin_fonction.inc.php)
$g_iType = $type;
$g_sParams = $params;

$g_iFormation = $g_iModule = $g_iRubrique = $g_iUnite = $g_iActiv = $g_iSousActiv = 0;

if (!empty($params))
	list($g_iFormation,$g_iModule,$g_iRubrique,$g_iUnite,$g_iActiv,$g_iSousActiv) = explode(":",$params);

// ---------------------
// Gestion
// ---------------------
if (isset($act))
{
	include_once("admin_globals.inc.php");
	
	$url_bModifier       = (empty($HTTP_POST_VARS["modifier"]) ? FALSE : $HTTP_POST_VARS["modifier"]);
	$url_bModifierStatut = (empty($HTTP_POST_VARS["modifierStatut"]) ? FALSE : $HTTP_POST_VARS["modifierStatut"]);
	
	// {{{ Mettre � jour
	$sRequeteSql = "LOCK TABLES"
		." Formation WRITE"
		.", Module WRITE"
		.", Module_Rubrique WRITE"
		.", Intitule WRITE"
		.", Activ WRITE"
		.", SousActiv WRITE"
		.", SousActiv_SousActiv WRITE"
		//.", SousActiv_Glossaire WRITE"
		.", Forum WRITE"
		.", SujetForum WRITE"
		.", MessageForum WRITE"
		.", Chat WRITE";
	$oProjet->oBdd->executerRequete($sRequeteSql);
	
	switch ($g_iType)
	{
		case TYPE_FORMATION: include_once("gestion.form.php"); break;
		case TYPE_MODULE: include_once("gestion.mod.php"); break;
		case TYPE_RUBRIQUE:
		case TYPE_UNITE: include_once("gestion.rubr.php"); break;
		case TYPE_ACTIVITE: include_once("gestion.activ.php"); break;
		case TYPE_SOUS_ACTIVITE: include_once("gestion.sousactiv.php"); break;
	}
	
	$oProjet->oBdd->executerRequete("UNLOCK TABLES");
	// }}}
	
	// {{{ Recharger la liste
	$params = implode(":",array($g_iFormation,$g_iModule,$g_iRubrique,0,$g_iActiv,$g_iSousActiv));
	
	echo "<html>"
		."<head>"
		.inserer_feuille_style("concept.css")
		."<script type=\"text/javascript\" language=\"javascript\">"
		."<!--\n\n"
		."function rechargerPages()\n"
		."{\n"
		."\tvar sRechargerPageHTML = \"admin_liste.php?type={$type}&params={$params}\";\n"
		."\ttop.frames['ADMINFRAMELISTE'].location = sRechargerPageHTML;\n"
		."\ttop.frames['ADMINFRAMELISTE'].location.replace(sRechargerPageHTML);"
		."}\n"
		."\n//-->"
		."</script>"
		."</head>"
		."<body class=\"econcept_modif\" onload=\"rechargerPages()\">"
		."<p>&nbsp;</p>"
		."<p>&nbsp;</p>"
		."<div align=\"center\">"
		."<p>"
		."<img src=\"".dir_theme("barre-de-progression.gif")."\">"
		."<br>Mise &agrave; jour"
		."</p>"
		."</div>"
		."</body>"
		."</html>";
	// }}}
	
	exit();
}

// *************************************
// Initialisation
// *************************************

if ($g_iFormation > 0)
	$oProjet->defFormationCourante($g_iFormation);
else
	$type = $g_iFormation = $g_iModule = $g_iRubrique = $g_iUnite = $g_iActiv = $g_iSousActiv = 0;

// Titre principal
$g_sTitre = ($g_iFormation > 0) ? $oProjet->oFormationCourante->retNom() : "Accueil";

function formatSousTitre ($v_sType,$v_sNom)
{
	return "<b>{$v_sType}</b>"
		."&nbsp;&raquo;&nbsp;"
		.htmlentities($v_sNom);
}

if ($g_iSousActiv > 0)
{
	$oSousActiv = new CSousActiv($oProjet->oBdd,$g_iSousActiv);
	$g_sSous_Titre = formatSousTitre(INTITULE_SOUS_ACTIV,$oSousActiv->retNom());
	unset($oSousActiv);
}
else if ($g_iActiv > 0)
{
	$oActiv = new CActiv($oProjet->oBdd,$g_iActiv);
	$g_sSous_Titre = formatSousTitre(INTITULE_ACTIV,$oActiv->retNom());
	unset($oActiv);
}
else if ($g_iRubrique > 0)
{
	$oRub = new CModule_Rubrique($oProjet->oBdd,$g_iRubrique);
	$g_sSous_Titre = formatSousTitre(INTITULE_RUBRIQUE,$oRub->retNom());
	unset($oRub);
}
else if ($g_iModule > 0)
{
	$oMod = new CModule($oProjet->oBdd,$g_iModule);
	$g_sSous_Titre = formatSousTitre(INTITULE_MODULE,$oMod->retNom());
	unset($oMod);
}
else if ($g_iFormation > 0)
{
	$oForm = new CFormation($oProjet->oBdd,$g_iFormation);
	$g_sSous_Titre = formatSousTitre(INTITULE_FORMATION,$oForm->retNom());
	unset($oForm);
}
else
	$g_sSous_Titre = "(&nbsp;Accueil&nbsp;)";

// *************************************
// D�claration des fonctions locales
// *************************************

function afficherTitre ($v_sTitre,$v_sNom=NULL)
{
	global $g_iType, $g_sParams;
	global $g_bModifier;
	
	echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"2\" width=\"100%\">\n"
		/*."<tr>\n"
		."<td class=\"cellule_fonce\" colspan=\"3\" align=\"center\"><b>$v_sTitre</b>"
		.(($v_sNom != NULL) ? "<br><font size=\"2\">[&nbsp;$v_sNom&nbsp;]</font>" : NULL)
		."</td>\n"
		."</tr>\n"*/;
	
	echo "<tr>\n"
		."<td class=\"admin_modif_menu\">\n"
		."<div class=\"admin_modif_nom_element\">{$v_sNom}</div>"
		//."<img src=\"images/nouveau.gif\">&nbsp;<a href=\"javascript: ".(($g_iType == TYPE_FORMATION) ? "ajouterFormation();" : "Ajouter();")."\">Ajouter</a>\n"
		//." | <img src=\"images/couper.gif\">&nbsp;<a href=\"javascript: Couper();\">Couper</a>\n"
		//." | <img src=\"images/copier.gif\">&nbsp;<a href=\"javascript: Copier();\">Copier</a>\n"
		//." | <img src=\"images/coller.gif\">&nbsp;<a href=\"javascript: Coller();\">Coller</a>\n"
		//." | <img src=\"images/effacer.gif\">&nbsp;<a href=\"javascript: Effacer(".($g_iType-1).");\">Supprimer</a>\n"
		."</td>\n"
		."<td class=\"admin_modif_outils\"  width=\"1%\" nowrap=\"1\">"
		.(($g_iType >= TYPE_ACTIVITE && $g_bModifier) ? "<a href=\"javascript: DeposerFichiers();\">D�poser sur le serveur</a>" : "...")
		.(($g_iType >= TYPE_ACTIVITE) ? "&nbsp;&#8226;&nbsp;<a href=\"javascript: RecupererFichiers();\">R�cup�rer du serveur</a>\n" : "")
		."</td>\n"
		."</tr>\n";
	
	echo "</table>\n"
		."<br>\n";
}

function boutonDeposer ()
{
	global $g_bModifier;
	if ($g_bModifier)
		echo "[&nbsp;<a href=\"javascript: DeposerFichiers();\">D&eacute;poser</a>&nbsp;]";
}

function boutonInfoBulle () { echo retBoutonInfoBulle(); }
function retBoutonInfoBulle ()
{
	global $g_bModifier, $type, $g_sParams;
	$aiIdType = explode(":",$g_sParams);
	return ($g_bModifier
		? "[&nbsp;<a href=\"javascript: info_bulle('{$type}','".$aiIdType[$type-1]."');\">Info bulle</a>&nbsp;]"
		: NULL);
}

function selectionnerModalAff ($v_aoModes,$v_iModeActuel=NULL,$v_sNomSelectHTML=NULL,$v_sNomDivHTML="div_modalite_affichage")
{
	global $g_bModifier;
	echo "<tr>\n"
		."<td><div class=\"intitule\">Modalit&eacute; d'affichage&nbsp;:&nbsp;</div></td>\n"
		."<td>\n"
		."<select name=\"{$v_sNomSelectHTML}\""
		." onchange=\"MontrerCacher('{$v_sNomDivHTML}',this.options[this.selectedIndex].value)\""
		.($g_bModifier ? NULL : " disabled")
		.">\n";
	for ($idx=0; $idx<count($v_aoModes); $idx++)
		echo "<option value=\"".$v_aoModes[$idx][0]."\""
			.($v_iModeActuel == $v_aoModes[$idx][0] ? " selected" : NULL)
			.">".$v_aoModes[$idx][1]."</option>\n";
	echo "</select>\n"
		."</td>\n"
		."</tr>\n";
}

function selectionnerNumeroOrdre ($v_sNomSelect,$v_iNbrNumOrdre,$v_iNumOrdreCourant,$v_iNumDepart=1)
{
	global $g_bModifier;
	
	echo "<!-- Num�ro d'ordre -->\n\n"
		."<tr>\n"
		."<td nowrap=\"nowrap\" width=\"1%\"><div  class=\"intitule\">Num&eacute;ro d'ordre&nbsp;:</div></td>\n"
		."<td>\n";
	
	echo "<select name=\"{$v_sNomSelect}\""
		.($g_bModifier ? NULL : " disabled")
		.">\n";
	
	for ($i=$v_iNumDepart; $i<=$v_iNbrNumOrdre; $i++)
		echo "<option"
			." value=\"{$i}\""
			.($i == $v_iNumOrdreCourant ? " selected" : NULL)
			.">"
			."&nbsp;&nbsp;{$i}&nbsp;&nbsp;"
			."</option>\n";
	
	echo "</select>\n";
	
	echo "</td>\n"
		."</tr>\n";
}

function selectionnerStatut ($v_sNom,$v_aoStatut,$v_iStatutActuel=0)
{
	global $g_bModifierStatut;
	
	$sStatut = "<!-- Statut -->\n\n"
		."<tr>\n"
		."<td><div class=\"intitule\">Statut&nbsp;:</div></td>\n"
		."<td>";
	
	$sStatut .= "\n<select name=\"{$v_sNom}\""
		.($g_bModifierStatut ? NULL : " disabled")
		.">\n";
	
	for ($i=0; $i<count ($v_aoStatut); $i++)
		$sStatut .= "\t<option"
			." value=\"{$v_aoStatut[$i][0]}\""
			.($v_aoStatut[$i][0] == $v_iStatutActuel ? " selected" : NULL)
			.">"
			.htmlentities($v_aoStatut[$i][1])
			."</option>"
			."\n";
	
	$sStatut .= "</select>\n";
	
	$sStatut .= "</td>\n"
		."</tr>\n";
	
	echo $sStatut;
}

function selectionnerType ($v_sNom,$v_aoTypes,$v_iTypeActuel=0,$v_sParametres=NULL)
{
	global $g_bModifier;
	
	$sType = "\n<!-- S�lectionnez un type -->\n\n"
		."<tr>\n"
		."<td><div class=\"intitule\">Type&nbsp;:</div></td>\n"
		."<td>\n"
		."<select name=\"{$v_sNom}\""
		.(isset($v_sParametres) ? " $v_sParametres" : NULL)
		.($g_bModifier ? NULL : " disabled")
		." style=\"width: 200px;\""
		.">\n";
	
	for ($i=0; $i<count($v_aoTypes); $i++)
		$sType .= "\t<option"
			." value=\"{$v_aoTypes[$i][0]}\""
			.(($i+1) == $v_iTypeActuel ? " selected" : NULL)
			.">"
			.$v_aoTypes[$i][1]
			."</option>"
			."\n";
	
	$sType .= "</select>\n"
		."</td>\n"
		."</tr>\n";
	
	echo $sType;
}

function selectionner_modalite ($aoModalites,$sNomBalise,$iChoixModalite)
{
	global $g_bModifier;
	
	$sConteneur = "<select name=\"$sNomBalise\""
		.($g_bModifier ? NULL : " disabled")
		.">\n";
	
	for ($i=0; $i<count($aoModalites); $i++)
		$sConteneur .= "<option"
			." value=\"".$aoModalites[$i][0]."\""
			.($iChoixModalite == $aoModalites[$i][0] ? " selected" : NULL)
			.">".$aoModalites[$i][1]."</option>"
			."\n";
	
	$sConteneur .= "</select>\n";
	
	return $sConteneur;
}

function entrerNom ($v_sNom,$v_mValeur,$v_bAfficherBoutonInfoBulle=FALSE)
{
	global $g_bModifier;
	
	echo "\n<!-- Nom -->\n\n"
		."<tr>\n"
		."<td><div class=\"intitule\">Nom&nbsp;:</div></td>\n"
		."<td>"
		."<input type=\"text\""
		." name=\"{$v_sNom}\""
		." size=\"53\""
		." value=\"{$v_mValeur}\""
		.($g_bModifier ? NULL : " disabled")
		.">" // <input>
		.($v_bAfficherBoutonInfoBulle ? "&nbsp;".retBoutonInfoBulle() : NULL)
		."</td>\n"
		."</tr>\n\n";
}

function entrerDescription ($v_sNom,$v_mValeur,$v_sTitre=NULL,$v_sNomFichierExport=NULL,$v_bAfficher=TRUE)
{
	global $g_bModifier;
	
	if (empty($v_sTitre))
		$v_sTitre = "Description";
	
	$sConteneur = "\n<!-- Description -->\n\n"
		."<tr>\n"
		."<td class=\"intitule\">{$v_sTitre}&nbsp;:&nbsp;</td>\n"
		."<td>"
		."<span style=\"text-align: right;\">"
		."<textarea name=\"{$v_sNom}\""
		." cols=\"55\""
		." rows=\"5\""
		.($g_bModifier ? NULL : " disabled")
		.">".$v_mValeur."</textarea>"
		.($g_bModifier ? "&nbsp;&nbsp;[&nbsp;<a href=\"javascript: editeur('form_admin_modif','{$v_sNom}','$v_sNomFichierExport'); void(0);\" onfocus=\"blur()\">Editeur</a>&nbsp;]" : NULL)
		."</span>"
		."</td>\n"
		."</tr>\n\n";
	
	if ($v_bAfficher) echo $sConteneur; else return $sConteneur;
}

$sCheminJavascript = dir_javascript();

?>
<html>
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?=inserer_feuille_style("onglet.css; concept.css")?>
<script type="text/javascript" language="javascript" src="<?="{$sCheminJavascript}globals.js.php"?>"></script>
<script type="text/javascript" language="javascript" src="<?="{$sCheminJavascript}window.js"?>"></script>
<script type="text/javascript" language="javascript" src="<?="{$sCheminJavascript}outils_admin.js"?>"></script>
<script type="text/javascript" language="javascript" src="<?="{$sCheminJavascript}sous_activ.js"?>"></script>
<script type="text/javascript" language="javascript" src="admin_modif.js"></script>
<script type="text/javascript" language="javascript">
<!--

var g_iIdInterval=null;

function DeposerFichiers()
{
	var oWinDeposerFichiers;
<?php
$sDeposerFichier = dir_admin("commun","deposer_fichiers.php",FALSE)
	."?repDest=".rawurlencode(dir_cours($g_iActiv,$g_iFormation,NULL,FALSE));

if ($g_iSousActiv > 0)
	$sDeposerFichier .= "&tpf=".rawurlencode("D�poser des fichiers relatifs � l'�l�ment actif");
else if ($g_iActiv > 0)
	$sDeposerFichier .= "&tpf=".rawurlencode("D�poser des fichiers relatifs � ce bloc");
?>
	var sUrl     = "<?=$sDeposerFichier?>";
	var iLargeur = 470;
	var iHauteur = 190;
	var iGauche  = (screen.width-iLargeur)/2;
	var iHaut    = (screen.height-iHauteur)/2;
	var sFeatures = "left=" + iGauche
		+ ",top=" + iHaut
		+ ",width=" + iLargeur
		+ ",height=" + iHauteur
		+ ",location=0,menubar=0,scrollbars=0,resizable=0,status=0,toolbar=0";
	
	oWinDeposerFichiers = window.open(sUrl,"winDeposerFichiers",sFeatures);
	oWinDeposerFichiers.focus();
}

function editeur_callback(v_sForm,v_sElem,v_sTexte)
{
	document.forms[v_sForm].elements[v_sElem].value = v_sTexte;
	document.forms[v_sForm].submit();
}

function deposer_fichiers_callback()
{
	document.forms[0].submit();
}

function RecupererFichiers()
{
	var wForm;
	
	var iLargeurFenetre = 400;
	var iHauteurFenetre = 500;
	var iPositionGauche = (screen.width-iLargeurFenetre)/2;
	var iPositionHaut   = (screen.height-iHauteurFenetre)/2;
	
	var sUrl = "recuperer_fichiers.php<?php echo "?FORM={$g_iFormation}&ACTIV={$g_iActiv}"; ?>";
	var sFeatures = "top=" + iPositionHaut  + ",left=" + iPositionGauche + ",width=" + iLargeurFenetre + ",height=" + iHauteurFenetre + ",location=0,menubar=0,scrollbars=0,resizable=0,status=0,toolbar=0";
	
	wForm = window.open(sUrl,"WIN_RECUPERER_FICHIERS",sFeatures);
	wForm.focus();
}

function type_different()
{
	if (!top.frames["ADMINFRAMEMODIF"] &&
		!top.frames["ADMINFRAMEMODIF"].document.forms[0] &&
		!top.frames["ADMINFRAMEMODIF"].document.forms[0].elements["TYPE"])
		return true;
	
	var oSelectType = top.frames["ADMINFRAMEMODIF"].document.forms[0].elements["TYPE"];
	
	if (typeof(oSelectType) == "undefined")
		return true;
	
	var iAncType = -1;
	for (i=0; i<oSelectType.options.length; i++)
		if (oSelectType.options[i].defaultSelected) { iAncType = oSelectType.options[i].value; break; }
	
	var iNouvType = oSelectType.options[oSelectType.selectedIndex].value;
	if (iAncType > 0 && iAncType != iNouvType)
	{
		var wTypeDifferent = PopupCenter("changer_type-index.php","wTypeDifferent",300,150,"");
		wTypeDifferent.focus();
		return false;
	}
	return true;
}

function info_bulle(v_iType,v_iIdType)
{
	var sUrl = "info_bulle-index.php"
		+ "?type=" + v_iType
		+ "&idType=" + v_iIdType;
	var wInfoBulle = PopupCenter(sUrl,"wInfoBulle",400,180,"");
	wInfoBulle.focus();
}
function envoyer() { top.frames["AdminModifMenu"].envoyer(); }
function menu()
{
	var obj = document.getElementById("id_menu_sous_activ").style;
	var iPosMenu;
	
	if (typeof(window.innerHeight) != "undefined")
		iPosMenu = window.pageYOffset + window.innerHeight;
	else if (typeof(document.body.clientHeight) != "undefined")
		iPosMenu = document.body.scrollTop + document.body.clientHeight;
	
	obj.top = iPosMenu - 17;
}

function init()
{
	var sParams = "";
	
	if (top.frames["Titre"] && top.frames["Titre"].changerSousTitre)
		top.frames["Titre"].changerSousTitre("<?=rawurlencode($g_sSous_Titre)?>");
	
	if (document.forms.length > 0 && document.forms[0].elements["act"])
		sParams = "<?="?type={$g_iType}&params={$g_sParams}"?>";
	
	top.frames["AdminModifMenu"].location = "admin_modif-menu.php" 
		+ sParams;
}

//-->
</script>
</head>
<body class="econcept_modif" onload="init()">
<?php
if (!isset($rafraichir))
{
	include_once("formulaire.init.php");
	
	switch ($type)
	{
		case TYPE_FORMATION: include_once("formulaire.form.php"); break;
		case TYPE_MODULE: include_once("formulaire.mod.php"); break;
		case TYPE_RUBRIQUE:
		case TYPE_UNITE: include_once("formulaire.rubr.php"); break;
		case TYPE_ACTIVITE: include_once("formulaire.activ.php"); break;
		case TYPE_SOUS_ACTIVITE: include_once("formulaire.sousactiv.php"); break;
		default: include_once(dir_theme("econcept/econcept-info.htm",FALSE,TRUE));
	}
}

if ($g_bModifier || $g_bModifierStatut)
	echo "<input type=\"hidden\" name=\"act\" value=\"modifier\">\n"
		."<input type=\"hidden\" name=\"modifier\" value=\"{$g_bModifier}\">\n"
		."<input type=\"hidden\" name=\"modifierStatut\" value=\"{$g_bModifierStatut}\">\n";

echo "<input type=\"hidden\" name=\"type\" value=\"$type\">\n"
	."<input type=\"hidden\" name=\"params\" value=\"$params\">\n";

echo "</form>\n";
?>
<p>&nbsp;</p>
</body>
</html>
<?php $oProjet->terminer(); ?>
