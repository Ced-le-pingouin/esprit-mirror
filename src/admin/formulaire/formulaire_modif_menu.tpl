<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript">
<!--
function ajoutobj(idformulaire)
{
	PopupCenter('formulaire_modif_ajout.php?idformulaire='+idformulaire,'WinAjoutObjForm',450,150,'location=no,status=no,toolbar=no,scrollbars=no');
}
function supobj(idobj,idformulaire) 
{
	if (confirm('Voulez-vous supprimer l\'objet sélectionné ?'))
	{
		parent.FORMFRAMEMODIF.location.replace("formulaire_modif_sup.php?idobj="+idobj+"&idformulaire="+idformulaire);
	}
}
function modifposobj(idobj,idformulaire)
{
	PopupCenter('position_objet.php?idobj='+idobj+'&idformulaire='+idformulaire,'WinModifPosObjForm',300,150,'location=no,status=no,toolbar=no,scrollbars=no');
}
function copieobj(idobj,idformulaire) 
{
	if (confirm('Voulez-vous copier l\'objet sélectionné ?'))
	{
		parent.FORMFRAMEMODIF.location.replace("formulaire_modif_copie.php?idobj="+idobj+"&idformulaire="+idformulaire);
	}
}
function modifaxeform(idformulaire)
{
	PopupCenter('formulaire_axe.php?idformulaire='+idformulaire,'WinModifAxesForm',550,400,'location=no,status=no,toolbar=no,scrollbars=yes,resizable=no');
}
//-->
</script>
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css" />
<title>Conception d'activités en ligne</title>
</head>
<body class="menumodif">
<div id="menu_element">
	Elément : {AJOUTER} - {SUPPRIMER} - {DEPLACER} - {COPIER} <span id="def_axes">{DEF_AXES}</span>
</div>
</body>
</html>
