<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" 
	"http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>{titre_page_html}</title>
[BLOCK_HEAD+][BLOCK_HEAD-]
<script type="text/javascript" language="javascript">
<!--

function changerTitrePrincipal(v_sTitrePrincipal)
{
	if (top.frames["Haut"] && top.frames["Haut"].changerTitrePrincipal)
		top.frames["Haut"].changerTitrePrincipal(v_sTitrePrincipal);
	else
		setTimeout("changerTitrePrincipal('" + v_sTitrePrincipal + "')",1000);
}

function changerSousTitre(v_sSousTitre)
{
	if (top.frames["Haut"] && top.frames["Haut"].changerSousTitre)
		top.frames["Haut"].changerSousTitre(v_sSousTitre);
	else
		setTimeout("changerSousTitre('" + v_sSousTitre + "')",1000);
}

//-->
</script>
</head>
</html>
<frameset rows="66,*,24" border="0" frameborder="0" framespacing="0">
<frame name="Haut" src="{frame_src_haut}" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
{frame_principal}
<frame name="Bas" src="{frame_src_bas}" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
