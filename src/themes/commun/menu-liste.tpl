<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<script type="text/javascript" language="javascript">
<!--
var g_sNomId = null;

function surbrillance(v_sNomId,v_bBrillant)
{
	if (!document.getElementById || !document.getElementById(v_sNomId)) return;
	if (g_sNomId != null) { document.getElementById(g_sNomId).style.color = "rgb(0,0,0)"; g_sNomId = null; }
	if (v_bBrillant) { document.getElementById(v_sNomId).style.color = "rgb(201,91,40)"; g_sNomId = v_sNomId; }
}

function init() {}
//-->
</script>
</head>
<body onload="init()">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
[BLOCK_MENU_LISTE+]
<tr><td>{menu_liste.icone}</td></tr>
<tr><td>{menu_liste.label}</td></tr>
[BLOCK_MENU_LISTE-]
</table>
</body>
</html>

