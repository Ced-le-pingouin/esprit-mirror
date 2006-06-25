<?php
include('claviervirtuel.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN" "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Multilinguisme</title>
<link rel="stylesheet" type="text/css" href="clavier.css">
<script type="text/javascript" src="saisie.js"></script>
</head>

<body>

<h3><a href="#" onClick="showblock('cyrillique')">Saisie en cyrillique</a></h3>
<div id="cyrillique">
<?php
$textid = textarea("russian", 60, 5);
keyboard("russian", $textid);
?>
</div>

<h3><a href="#" onClick="showblock('arabe')">Saisie en arabe</a></h3>
<div id="arabe">
<?php
$textid = textarea("arabic", 60, 5);
keyboard("arabic", $textid);
?>
</div>

<h3><a href="#" onClick="showblock('pinyin')">Saisie en pinyin</a></h3>
<div id="pinyin">
Vous pouvez faire suivre &nbsp; <tt>a e i o u ü</tt> &nbsp; d'un chiffre de 1 à 4 pour indiquer leur ton.<br />
<?php
$textid = textarea("pinyin", 60, 4);
?>
</div>

<h3><a href="ml-help-fr.html">Aide à la configuration</a></h3>
</body>

</html>
