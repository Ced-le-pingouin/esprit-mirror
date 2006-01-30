<?php
require_once("globals.inc.php");
?>
<html>
<head>
<?=inserer_feuille_style()?>
<script type="text/javascript" language="javascript">
function changer(v_sName)
{
	parent.document.forms[0].elements[v_sName].value = (parent.document.forms[0].elements[v_sName].value == "1" ? "0" : "1");
}
</script>
</head>
<body style="background-image: none;">
<form>
<table border="0" cellspacing="1" cellpadding="0">
<tr>
<td><input type="checkbox" name="COPIER_FORUMS" value="1" onclick="changer(this.name)" checked></td>
<td>&nbsp;&nbsp;Forums</td>
</tr>
<tr>
<td><input type="checkbox" name="COPIER_SUJETS_FORUMS" value="1" onclick="changer(this.name)" checked></td>
<td>&nbsp;&nbsp;Sujets des forums&nbsp;<sup><small>1</small></sub></td>
</tr>
<tr>
<td><input type="checkbox" name="COPIER_CHATS" value="1" onclick="changer(this.name)" checked></td>
<td>&nbsp;&nbsp;Chats&nbsp;<sup><small>2</small></sub></td>
</tr>
</table>
</form>
</body>
</html>
