<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<script type="text/javascript" language="javascript">
<!--
function init()
{
	[BLOCK_JAVASCRIPT_FUNCTION_INIT+]
	[SET_BOITE_COURRIELLE_PLATEFORME+]top.location = "racine://admin/mail/mail-index.php{courriel_params}";[SET_BOITE_COURRIELLE_PLATEFORME-]
	[SET_BOITE_COURRIELLE_OS+]document.forms[0].submit(); setTimeout("top.location = top.location;",500);[SET_BOITE_COURRIELLE_OS-]
	[BLOCK_JAVASCRIPT_FUNCTION_INIT-]
}
//-->
</script>
</head>
<body onload="init()">
[BLOCK_BOITE_ENVOI_OS+]
[BLOCK_BOITE_ENVOI_DIRECTE+]<form action="mailto:{liste_adresses_courrielles}?subject=" method="post" enctype="text/plain"></form>[BLOCK_BOITE_ENVOI_DIRECTE-]
[BLOCK_BOITE_ENVOI_INDIRECTE+]
<p>Il manque ici le fameux texte de JJ.</p>
[BLOCK_LISTE_DESTINATAIRES+]<div style="margin: 10px; padding: 5px; background-color: rgb(241,239,226); border: rgb(172,168,153) dashed 1px;"><a href="mailto:{liste_adresses_courrielles}">{liste_adresses_courrielles:htmlentities}</a></div>[BLOCK_LISTE_DESTINATAIRES-]
[BLOCK_BOITE_ENVOI_INDIRECTE-]
[BLOCK_BOITE_ENVOI_OS-]
</body>
</html>

