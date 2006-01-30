<?php

require_once ("globals.inc.php");
require_once (dir_database ("evenement.tbl.php"));

$oProjet = new CProjet();

$ev = new CEvenement($oProjet->oBdd);

$a = $ev->retListeConnectes();

?>

<html>

<head>

<?php inserer_feuille_style(); ?>

<style type="text/css">
<!--

body, div
{
	font-size: 7pt;
	background-image: none;
	background-color: #FFFFE7;
	text-align: center;
}

//-->
</style>

<script language="javascript" type="text/javascript">
<!--

var IdTimeOut=null;

function rechargerPage()
{
	document.location = document.location;
}

IdTimeOut = window.setTimeout("rechargerPage()",30000);

//-->
</script>

</head>

<body>

<div style="text-align: centre;">
<?php

for ($i=0; $i<count ($a); $i++)
	echo $a[$i]."<br>\n";

?>
</div>

<br>

<div style="text-align: right;"><a href="javascript: self.location.reload();">Rafraichir</a></div>

<a name="fin">

</body>

</html>
