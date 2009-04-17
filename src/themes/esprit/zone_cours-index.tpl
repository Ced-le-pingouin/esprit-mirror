<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>{titre_page_html} - Zone de cours</title>
<link rel="Shortcut Icon" type="image/x-icon" href="/esprit/favicon.ico">
<script type="text/javascript" language="javascript">
<!--
function changerTitres(v_sNomFormation,v_sHistorique)
{
	top.g_iPosYPagePrincipale = 0;
	if (self.frames["Haut"] && self.frames["Haut"].changerTitres)
		self.frames["Haut"].changerTitres(v_sNomFormation,v_sHistorique);
	else
		setTimeout("changerHistorique('" + v_sHistorique + "')",5000);
}

function premierePage(v_sPremierePage)
{
	if (self.frames["Principal"])
		self.frames["Principal"].location = unescape(v_sPremierePage);
	else
		setTimeout("premierePage('" + v_sPremierePage + "')",5000);
}
//-->
</script>
</head>
<frameset rows="116,*,27" border="0" frameborder="0" framespacing="0">
<frame name="Haut" src="{src_frame_haut}" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
<frameset cols="224,1,*" border="0" frameborder="0" framespacing="0">
<frame name="Gauche" src="{src_frame_gauche}" marginwidth="5" marginheight="15" frameborder="0" scrolling="auto" noresize="noresize">
<frame name="Sep1" src="theme://frame_separation.htm" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize="noresize">
<frame name="Principal" src="{src_frame_principal}" frameborder="0" scrolling="yes" noresize="noresize">
</frameset>
<frame name="Bas" src="{src_frame_bas}" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize="noresize">
</frameset>
</html>

