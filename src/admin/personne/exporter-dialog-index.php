<?php
$sTitrePrincipal = emb_htmlentities("Exporter");
$sParamsURL = "?tp=".rawurlencode($sTitrePrincipal);
$sParamsURLPrincipal = "?LISTE_IDPERS=".$_POST["LISTE_IDPERS"];
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title><?=$sTitrePrincipal?></title>
<script type="text/javascript" language="javascript">
<!--
function oPrincipale() { return top.frames["principale"]; }
function oMenu() { return top.frames["menu"]; }
//-->
</script>
</head>
<frameset rows="64,*,25" border="0" frameborder="0" framespacing="3">
<frame name="titre" src="exporter-dialog-titre.php<?=$sParamsURL?>" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize="noresize">
<frame name="principale" src="exporter-dialog.php<?=$sParamsURLPrincipal?>" marginwidth="10" marginheight="5" frameborder="0" scrolling="no" noresize="noresize">
<frame name="menu" src="exporter-dialog-menu.php?exporter=1" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize="noresize">
</frameset>
</html>
