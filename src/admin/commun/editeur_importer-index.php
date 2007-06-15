<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
   "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
<head>
<title>Importer</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="javascript">
<!--
function retour() {
	self.frames["menu"].location = "editeur_importer-menu.php?menu=recommencer";
}
function recommencer() {
	self.frames["principale"].location = "editeur_importer.php";
	self.frames["menu"].location = "editeur_importer-menu.php?menu=importer";
}
function importer() {
	self.frames["menu"].location = "editeur_importer-menu.php?menu=annuler";
	
	with (self.frames["principale"]) {
		if (document.getElementById)
			document.getElementById("id_barre_de_progression").style.visibility = "visible";
		document.forms[0].submit();
	}
}
//-->
</script>
</head>
<frameset rows="*,23">
<frame name="principale" src="editeur_importer.php" frameborder="0" marginwidth="20" marginheight="20" scrolling="no" noresize="noresize">
<frame name="menu" src="editeur_importer-menu.php?menu=importer" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
</html>
