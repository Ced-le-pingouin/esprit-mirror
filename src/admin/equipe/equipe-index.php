<?php

$sTitreFenetre = "Gestion des équipes";

$sParamsUrl = NULL;

$asParams = array("ACTION","ID_EQUIPE","NIVEAU","ID_NIVEAU");

foreach ($asParams as $sParam)
	if (isset($HTTP_POST_VARS[$sParam]))
		$sParamsUrl .= (isset($sParamsUrl) ? "&" : NULL)."$sParam=".$HTTP_POST_VARS[$sParam];

if (isset($sParamsUrl))
	$sParamsUrl = "?{$sParamsUrl}";

?>
<html>
<head>
<title><?=$sTitreFenetre?></title>
<script type="text/javascript" language="javascript">
<!--
function oPrincipal() { return top.frames["principal"]; }
function oMenu() { return top.frames["menu"]; }
//-->
</script>
</head>
</html>
<frameset rows="*,24" border="0" frameborder="0" framespacing="0">
<frame name="principal" src="equipe.php<?=$sParamsUrl?>" scrolling="no" noresize="1">
<frame name="menu" src="equipe_menu.php<?=$sParamsUrl?>" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="1">
</frameset>

