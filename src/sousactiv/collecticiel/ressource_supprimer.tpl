<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<script type="text/javascript" language="javascript">
<!--

function init()
{
	if (document.forms.length > 0)
		top.document.getElementsByName("menu").item(0).src = "ressource_supprimer-menu.php?menu=1";
	else if (location.search.indexOf("recharger") != -1)
		top.opener.location = top.opener.location;
}

function confirmer() { document.forms[0].submit(); }

function annuler()
{
	if (top.opener && top.opener.nettoyer)
		top.opener.nettoyer();
	
	top.close();
}

//-->
</script>
</head>
<body onload="init()">
[BLOCK_EFFACER_RESSOURCE+]
[SET_PAS_RESSOURCE_SELECTIONNEE+]<h3 style="text-align: center;">Vous n'avez pas s&eacute;lectionn&eacute; de document &agrave; supprimer.</h3>[SET_PAS_RESSOURCE_SELECTIONNEE-]
[SET_CONFIRMER_EFFACEMENT+]
<h3 style="text-align: center;">&Ecirc;tes-vous certain de vouloir supprimer ces documents&nbsp;?</h3>
<form action="ressource_supprimer-index.php" method="get" target="_top"><input type="hidden" name="idResSA" value="{idResSA.ids}"></form>
[SET_CONFIRMER_EFFACEMENT-]
[SET_CONFIRMATION_EFFACEMENT+]<h3 style="text-align: center;">Les documents ont bien &eacute;t&eacute; supprim&eacute;s du serveur</h3>[SET_CONFIRMATION_EFFACEMENT-]
[BLOCK_EFFACER_RESSOURCE-]
</body>
</html>

