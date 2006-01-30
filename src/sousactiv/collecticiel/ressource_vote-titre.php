<?php
require_once("globals.inc.php");
$sHead = <<<BLOCK_HTML_HEAD
<script type="text/javascript" language="javascript">
<!--
function recharger()
{
	if (top.voter &&
		top.opener && top.opener.recharger)
		top.opener.recharger();
}
window.onunload = recharger;
//-->
</script>
BLOCK_HTML_HEAD;
$sTitrePrincipal = NULL;
$sSousTitre = NULL;
include_once(dir_template("dialogue","dialog-titre.tpl.php"));
?>

