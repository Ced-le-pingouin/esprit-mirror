<?php
require_once("globals.inc.php");
$sTexteFormatte = (empty($HTTP_POST_VARS["edition"]) ? NULL : convertBaliseMetaVersHtml($HTTP_POST_VARS["edition"]));
?>
<html>
<head>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style("description.css"); ?>
</head>
<body>
<?=$sTexteFormatte?>
</body>
</html>
