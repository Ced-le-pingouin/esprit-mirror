<?php

require_once("globals.inc.php");

$oProjet = new CProjet();

$oProjet->terminer();

?>
<html>
<head><?php inserer_feuille_style(); ?></head>
<body class="cellule_fonce">
<?php
echo "<div align=\"center\">"
	."<b>".$oProjet->oUtilisateur->retNomComplet()."</b>"
	."<br>"
	."(".$oProjet->retTexteStatutUtilisateur().")"
	."</div>\n";
?>
</body>
</html>
