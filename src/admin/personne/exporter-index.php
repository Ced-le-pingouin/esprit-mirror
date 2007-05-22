<?php
require_once("globals.inc.php");
$sTitrePrincipal = emb_htmlentities("Exporter une liste de la table des personnes");
$sParamsURL = "?tp=".rawurlencode($sTitrePrincipal);
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title><?php echo $sTitrePrincipal?></title>
<script type="text/javascript" language="javascript">
<!--
function oPersonnes() { return top.frames["personnes"]; }
//-->
</script>
</head>
<frameset rows="64,48,*,2,*,25" border="0" frameborder="0" framespacing="3">
<frame name="titre" src="exporter-titre.php<?php echo $sParamsURL?>" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize="noresize">
<frame name="filtres" src="exporter-filtres.php" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize="noresize">
<frame name="personnes" src="" marginwidth="1" marginheight="0" scrolling="yes">
<frame src="" style="background-color: rgb(231,239,239);" scrolling="no" noresize="noresize">
<frame name="liste" src="exporter-liste.php" marginwidth="1" marginheight="2" scrolling="yes">
<frame name="menu" src="exporter-menu.php" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize="noresize">
</frameset>
</html>
