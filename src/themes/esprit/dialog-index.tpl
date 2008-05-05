<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" 
	"http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>{titre_page_html}</title>
[BLOCK_HEAD+][BLOCK_HEAD-]
<script type="text/javascript" language="javascript">
<!--
function oTitre() { return top.frames["Haut"]; }
function oMenu() { return top.frames["Bas"]; }
function changerTitrePrincipal(v_sTitrePrincipal)
{
	var oFrameTitre = oTitre();
	if (oFrameTitre && oFrameTitre.changerTitrePrincipal)
		oFrameTitre.changerTitrePrincipal(v_sTitrePrincipal);
	else
		setTimeout("changerTitrePrincipal('" + v_sTitrePrincipal + "')",1000);
}
function changerSousTitre(v_sSousTitre)
{
	var oFrameTitre = oTitre();
	if (oFrameTitre && oFrameTitre.changerSousTitre)
		oFrameTitre.changerSousTitre(v_sSousTitre);
	else
		setTimeout("changerSousTitre('" + v_sSousTitre + "')",2000);
}
function oFrmTitre() { return top.frames["Haut"]; }
function oFrmMenu() { return top.frames["Bas"]; }
//-->
</script>
</head>
<frameset rows="56,*,24" border="0" frameborder="0" framespacing="0">
<frame name="Haut" src="{frame_src_haut}" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
{frame_principal}
<frame name="Bas" src="{frame_src_bas}" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
</html>

