<?php

/*
** Fichier ................: intitule-index.php
** Description ............:
** Date de création .......:
** Dernière modification ..: 30/06/2004
** Auteurs ................: Filippo PORCO
** Emails .................: ute@umh.ac.be
**
*/

// Récupérer les variables de l'url
$url_iType = (empty($HTTP_GET_VARS) ? NULL : $HTTP_GET_VARS["TYPE_INTITULE"]);

if (!isset($url_iType))
	exit();
?>
<html>
<head>
<title>Liste des intitul&eacute;s</title>
<script type="text/javascript" language="javascript">
<!--
function oPrincipal() { return frames["Principale"]; }
function oModif() { return frames["Modif"]; }
function reinitIntitules()
{
	var amIntitules = top.oPrincipal().retIntitules();
	if (top.opener && top.opener.reinitIntitules)
		top.opener.reinitIntitules(amIntitules,null);
}
//-->
</script>
</head>
<frameset rows="*,50,23" onunload="reinitIntitules()">
<frame name="Principale" src="intitule.php?TYPE_INTITULE=<?=$url_iType?>" marginwidth="0" marginheight="0" frameborder="0" scrolling="yes" noresize>
<frame name="Modif" src="intitule-modif.php?TYPE_INTITULE=<?=$url_iType?>" marginwidth="5" marginheight="5" frameborder="0" scrolling="no" noresize>
<frame name="Menu" src="intitule-menu.php?TYPE_INTITULE=<?=$url_iType?>" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize>
</frameset>
</html>
