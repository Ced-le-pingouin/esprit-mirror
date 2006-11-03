<?php
require_once("globals.inc.php");

// Permet de sauvegarder les variables du formulaire
// que la page login a envoyée
$oProjet = new CProjet();
$sNomPlateforme = mb_convert_encoding($oProjet->retNom(),"HTML-ENTITIES","UTF-8");
$oProjet->terminer();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title><?php echo $sNomPlateforme?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link rel="Shortcut Icon" type="image/x-icon" href="/esprit/favicon.ico">
<script type="text/javascript" language="javascript">
<!--
function changerStatutUtilisateur(v_sNouvStatutUtilisateur) {
	if (top.frames["INDEX"].frames["Titre"] &&
		top.frames["INDEX"].frames["Titre"].document.getElementById &&
		top.frames["INDEX"].frames["Titre"].document.getElementById("statut"))
		top.frames["INDEX"].frames["Titre"].document.getElementById("statut").innerHTML = unescape(v_sNouvStatutUtilisateur);
}
//-->
</script>
</head>
<frameset rows="2,*" border="0" frameborder="0">
<frame name="AWARENESS" src="awareness.php" frameborder="0" scrolling="no" noresize="noresize">
<frame name="INDEX" src="zone_menu-index.php" frameborder="0" scrolling="no" noresize="noresize">
</frameset>
</html>

