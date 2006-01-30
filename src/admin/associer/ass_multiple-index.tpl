<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
        "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title>{fenetre->titre}</title>
</head>
<frameset rows="60,*,24" border="0" frameborder="0">
<frame name="nord" src="ass_multiple-nord.php?tp={outil->titre}" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
<frame name="centre" src="ass_multiple.php?ID_FORM={formation->id}&STATUT_PERS={personne->statut}" marginwidth="5" marginheight="0" scrolling="no" noresize="noresize">
<frame name="sud" src="ass_multiple-sud.php" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
</html>
