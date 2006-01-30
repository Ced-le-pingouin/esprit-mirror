<?php

/*
** Fichier ................: econcept-index.php
** Description ............:
** Date de création .......: 01/09/2001
** Dernière modification ..: 31/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();
$oProjet->initSousActivCourante();

// ---------------------
// Initialiser
// ---------------------
$sTitrePrincipal = "eCONCEPT";

$iIdForm      = (isset($oProjet->oFormationCourante) && is_object($oProjet->oFormationCourante) ? $oProjet->oFormationCourante->retId() : 0);
$iIdMod       = (isset($oProjet->oModuleCourant) && is_object($oProjet->oModuleCourant) ? $oProjet->oModuleCourant->retId() : 0);
$iIdRubrique  = (isset($oProjet->oRubriqueCourante) && is_object($oProjet->oRubriqueCourante) ? $oProjet->oRubriqueCourante->retId() : 0);
$iIdActiv     = (isset($oProjet->oActivCourante) && is_object($oProjet->oActivCourante) ? $oProjet->oActivCourante->retId() : 0);
$iIdSousActiv = (isset($oProjet->oSousActivCourante) && is_object($oProjet->oSousActivCourante) ? $oProjet->oSousActivCourante->retId() : 0);

if ($iIdRubrique > 0)
	$iType = TYPE_UNITE;
else if ($iIdMod > 0)
	$iType = TYPE_MODULE;
else if ($iIdForm > 0)
	$iType = TYPE_FORMATION;
else
	$iType = 0;

$sParams   = "?type={$iType}&params={$iIdForm}:{$iIdMod}:{$iIdRubrique}:0:0:0";
$sParamsJS = "?idForm={$iIdForm}&idMod={$iIdMod}&idUnite={$iIdRubrique}&idActiv={$iIdActiv}&idSousActiv={$iIdSousActiv}";

$sAdmin_liste = "admin_liste.php{$sParams}";

$oProjet->terminer();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
   "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<title><?php echo $sTitrePrincipal; ?></title>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('globals.js.php')?>"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('window.js')?>"></script>
<script type="text/javascript" language="javascript" src="<?=dir_javascript('outils_admin.js')?>"></script>
<script type="text/javascript" language="javascript">
<!--
function init() {}

function rafraichir()
{
	oListe().location.replace(oListe().location);
	oListe().location.reload();
}

function choix_formation_callback(v_iIdForm)
{
	top.location = "<?=$HTTP_SERVER_VARS['PHP_SELF']?>"
		+ "?idForm=" + v_iIdForm
		+ "&idMod=0"
		+ "&idUnite=0"
		+ "&idActiv=0"
		+ "&idSousActiv=0";
}

function mettre_a_jour_Fen_Parent()
{
	if (top.opener &&
		top.opener.parent &&
		top.opener.parent.location)
	{
			obj = top.opener.parent.location;
			top.opener.parent.location = obj.protocol + "//"
				+ obj.host
				+ obj.pathname
				+ "<?php echo $sParamsJS; ?>";
	}
}

function status(v_sTitle) {	top.frames["menu"].document.getElementById("id_status").innerHTML = unescape(v_sTitle); }
function oListe() { return top.frames["ADMINFRAMELISTE"]; }
function oModifMenu() { return top.frames["AdminModifMenu"]; }
function capturerErreursJS() { return true; }

window.onerror = capturerErreursJS;

//-->
</script>
</head>
<frameset rows="66,*,24" border="0" frameborder="0" framespacing="0" onload="init()" onunload="">
<frame src="econcept-titre.php" name="Titre" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="true">
<frameset cols="209,1,*" border="0" frameborder="0" framespacing="0">
<frame name="ADMINFRAMEMENU" marginwidth="2" marginheight="2" frameborder="0" noresize="true">
<frame src="<?=dir_theme('frame_separation.htm')?>" frameborder="0" scrolling="no" noresize="noresize">
<frameset rows="*,45%,20" border="0" frameborder="0" framespacing="0">
<frame name="ADMINFRAMELISTE" src="<?php echo $sAdmin_liste; ?>" frameborder="0">
<frame name="ADMINFRAMEMODIF" src="" marginwidth="0" frameborder="0" border="0"  scrolling="yes">
<frame name="AdminModifMenu" src="admin_modif-menu.php" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize>
</frameset>
</frameset>
<frame name="menu" src="econcept-menu.php?tp=<?=rawurlencode($sTitrePrincipal)?>" frameborder="0" marginwidth="0" marginheight="0" noresize scrolling="no">
</frameset>
</html>

