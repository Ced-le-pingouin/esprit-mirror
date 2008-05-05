<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<script type="text/javascript" language="javascript" src="javascript://dom.window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://dom.element.js"></script>
<script type="text/javascript" language="javascript">
<!--
var g_oDOMWin;
var g_oDOMTextArea;

function redimensionner()
{
	if (document.getElementById)
	{
		var iHauteur = g_oDOMWin.innerHeight()-5;
		if (iHauteur < 35) iHauteur = 35;
		g_oDOMTextArea.setHeight(iHauteur);
	}
}

function init()
{
	if (document.getElementById)
	{
		g_oDOMWin = new DOMWindow(self);
		g_oDOMTextArea = new DOMElement("id_message_courriel");
		redimensionner();
	}
}
//-->
</script>
</head>
<body onload="init()" onresize="redimensionner()">
{form}
<table border="0" cellspacing="1" cellpadding="0" width="100%">
<tr><td><textarea id="id_message_courriel" name="messageCourriel" rows="14" cols="90" style="width: 100%;"></textarea></td></tr>
</table>
{/form}
</body>
</html>
