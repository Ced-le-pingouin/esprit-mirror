<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<script type="text/javascript" language="javascript">
<!--
function init()
{
	top.oMenu().location = "info_bulle-menu.php{menu}";
}
//-->
</script>
</head>
<body onload="init()">
<form action="info_bulle.php" method="get">
[BLOCK_INFO_BULLE+][BLOCK_INFO_BULLE-]
<input type="hidden" name="type" value="{type->id}">
<input type="hidden" name="idType" value="{idType->id}">
</form>
</body>
</html>
[SET_INFO_BULLE+]<input type="text" name="info_bulle" style="width: 100%;" value="{info_bulle->texte}">[SET_INFO_BULLE-]

