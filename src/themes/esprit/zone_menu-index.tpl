<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>{titre_page_html} - Menu</title>
<script type="text/javascript" language="javascript">
<!--

function changerTitres(v_sTitre,v_sSousTitre)
{
	if (self.frames["Titre"] && self.frames["Titre"].changerTitres)
		self.frames["Titre"].changerTitres(v_sTitre,v_sSousTitre);
	else
		setTimeout("changerTitres('" + v_sTitre + "','" + v_sSousTitre + "')",1000);
}

//-->
</script>
</head>
<frameset rows="116,*,27" border="0" frameborder="0" framespacing="0">
<frame name="Titre" src="{src_frame_titre}" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
<frameset cols="224,1,*" border="0" frameborder="0" framespacing="0">
<frame name="Formation" src="{src_frame_gauche}" frameborder="0" marginwidth="5" marginheight="5" scrolling="auto" noresize="noresize">
<frame name="Sep1" src="theme://frame_separation.htm" marginwidth="0" marginheight="0" frameborder="0" scrolling="no" noresize="noresize">
<frame name="Principal" src="{src_frame_principale}" frameborder="0" marginwidth="5" marginheight="5" scrolling="auto" noresize="noresize">
</frameset>
<frame name="Menu" src="{src_frame_menu}" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
</html>

