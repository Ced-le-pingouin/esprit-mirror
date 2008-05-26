<?php
require_once('../../globals.inc.php');
include('claviervirtuel.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN" "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Multilinguisme</title>
<link rel="stylesheet" type="text/css" href="../../themes/css/clavier.css">
<?php
if (!empty($_REQUEST['size']) && !empty($_REQUEST['id'])) {
	echo '<style type="text/css">';
	echo '#'.$_REQUEST['id'].' textarea { font-size: '.$_REQUEST['size'].'px; } ';
	echo "</style>\n";
}
?>
<script type="text/javascript" src="saisie.js"></script>
</head>

<body<?php
if (!empty($_REQUEST['type'])) {
	switch ($_REQUEST['type']) {
		case 'russian': echo " onload=\"showblock('cyrillique')\""; break;
		case 'arabic': echo " onload=\"showblock('arabe')\""; break;
	}
}
?>>

<h3><a href="#" onClick="showblock('cyrillique')">Saisie en cyrillique</a></h3>
<div id="cyrillique">
<?php
if (!empty($_REQUEST['type']) && $_REQUEST['type']==='russian') {
	$width = (empty($_REQUEST['size'])?50:intval(50*20/$_REQUEST['size']));
	$height = (empty($_REQUEST['size'])?5:1+round(4*20/$_REQUEST['size']));
} else {
	$width = 50;
	$height = 5;
}
$textid = textarea("russian", $width, $height);
keyboard("russian", $textid);
?>
</div>

<h3><a href="#" onClick="showblock('arabe')">Saisie en arabe</a></h3>
<div id="arabe">
<?php
if (!empty($_REQUEST['type']) && $_REQUEST['type']==='arabic') {
	$width = (empty($_REQUEST['size'])?40:intval(40*30/$_REQUEST['size']));
	$height = (empty($_REQUEST['size'])?5:1+round(4*30/$_REQUEST['size']));
} else {
	$width = 40;
	$height = 4;
}
$textid = textarea("arabic", $width, $height);
keyboard("arabic", $textid);
?>
</div>

<h3><a href="#" onClick="showblock('pinyin')">Saisie en pinyin</a></h3>
<div id="pinyin">
Vous pouvez faire suivre &nbsp; <tt>a e i o u ù</tt> &nbsp; d'un chiffre de 1 à 4 pour indiquer leur ton.<br />
<?php
$textid = textarea("pinyin", 50, 4, false);
?>
</div>

<h3><a href="ml-help-fr.html">Aide à la configuration</a></h3>
</body>

</html>
