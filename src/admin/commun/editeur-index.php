<?php require_once("globals.inc.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
   "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
<head>
<title>Editeur</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="javascript" src="<?=dir_javascript('window.js')?>"></script>
<script type="text/javascript" language="javascript">
<!--
var sFormDest = "<?=$_GET['formulaire']?>";
var sElemDest = "<?=$_GET['element']?>";

var sAnnuler = null;

function oPrincipale() { return top.frames["principale"]; }
function oSousMenu() { return top.frames["sous_menu"]; }
function oMenu() { return top.frames["menu"]; }
function annuler()
{
	if (sAnnuler != null)
		oElemDestination.value = sAnnuler;
	top.close();
}
function remplacer(v_sTexte)
{
	oPrincipale().document.forms[0].elements["edition"].value = unescape(v_sTexte);
	oPrincipale().editeur();
}
function recuperer() {
	oPrincipale().document.forms[0].elements["edition"].value = top.opener.document.forms[sFormDest].elements[sElemDest].value;
}
function valider()
{
	if (top.opener && top.opener.editeur_callback)
	{
		top.opener.editeur_callback(sFormDest,sElemDest,oPrincipale().document.forms[0].elements["edition"].value);
		top.close();
	}
	else
		alert("La fonction 'editeur_callback' n'a pas été trouvée");
}
function exporter()
{
	with (oPrincipale().document.forms[0])
	{
		elements["f"].value = "<?=@$_GET['nfexport']?>"
		action = "editeur_exporter.php";
		target = "visualiseur";
		submit();
	}
}
function importer()
{
	var wEditeurImporter = PopupCenter("editeur_importer-index.php","wEditeurImporter",450,150,"");
	wEditeurImporter.focus();
}
//-->
</script>
</head>
<frameset rows="*,23,21" frameborder="0" border="0">
<frame name="principale" src="editeur.php" frameborder="0" marginwidth="10" marginheight="10" scrolling="no" noresize="noresize">
<frame name="sous_menu" src="editeur-sous_menu.php" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
<frame name="menu" src="editeur-menu.php" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
</html>
