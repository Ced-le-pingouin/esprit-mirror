<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" 
	"http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>{html.title}</title>
<script type="text/javascript" language="javascript">
<!--
function oMailInfos() { return top.frames["Infos"]; }
function oPrincipale() { return top.frames["Principale"]; }
//-->
</script>
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
function oFrmTitre() { return top.frames["Haut"]; }
function oFrmMenu() { return top.frames["Bas"]; }
//-->
</script>
</head>
[BLOCK_FRAMESET+][BLOCK_FRAMESET-]
</html>

