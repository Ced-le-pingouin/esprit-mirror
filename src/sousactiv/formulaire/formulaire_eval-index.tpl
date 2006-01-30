<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
   "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
<head>
<title>{html->title}</title>
<script type="text/javascript" language="javascript">
<!--
var g_oFrames = new Array();
function init()
{
	g_oFrames["titre"] = top.frames["TITRE"];
	g_oFrames["tuteurs"] = top.frames["TUTEURS"];
	g_oFrames["principale"] = top.frames["PRINCIPALE"];
	g_oFrames["menu"] = top.frames["MENU"];
}
//-->
</script>
<head>
<frameset rows="56,*,23" border="0" frameborder="0" framespacing="0" onload="init()">
<frame name="TITRE" src="{frame['titre']->src}" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
<frameset cols="179,1,*">
<frame name="TUTEURS" src="{frame['tuteurs']->src}" frameborder="0" marginwidth="5" marginheight="5" scrolling="auto" noresize="noresize">
<frame name="SEPARATEUR" src="theme://frame_separation.htm" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
<frame name="PRINCIPALE" src="{frame['principale']->src}" frameborder="0" marginwidth="5" marginheight="5" scrolling="auto" noresize="noresize">
</frameset>
<frame name="MENU" src="{frame['menu']->src}" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" noresize="noresize">
</frameset>
</html>

