<?php
require_once("globals.inc.php");
$sTexteFormatte = (empty($_POST["edition"]) ? NULL : convertBaliseMetaVersHtml($_POST["edition"]));
?>
<html>
<head>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style("globals.css"); ?>
</head>
<body>
<?php echo $sTexteFormatte?>
</body>
</html>
