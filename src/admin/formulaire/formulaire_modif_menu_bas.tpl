<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<script type="text/javascript">
<!--
function appliquer()
{
	if (parent.frames['FORMFRAMEMODIF'].document.forms.length > 0) //Teste si le formulaire existe si oui il execute le submit
	{ parent.frames['FORMFRAMEMODIF'].document.forms['formmodif'].submit(); } 
}

function annuler() 
{
	if (parent.frames['FORMFRAMEMODIF'].document.forms.length > 0) //Teste si le formulaire existe si oui il execute le submit
	{ parent.frames['FORMFRAMEMODIF'].document.forms['formmodif'].reset(); }
}
//-->
</script>
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css" />
<title>Conception de formulaires en ligne</title>
</head>
<body class="menumodifbas">
<div style="border-top:1px solid black; height:20px; text-align: right; padding: 2px 7px;" >
	<a href="javascript: appliquer();">Appliquer les changements</a> | <a href="javascript: annuler();">Annuler</a>
</div>
</body>
</html>
