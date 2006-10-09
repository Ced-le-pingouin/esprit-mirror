<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css" />
<script type="text/javascript">
function suppression(TypeAct)
{
	if (document.listeformulaire.idformulaire.selectedIndex == -1)
	{
		alert('Veuillez sélectionner une activité dans la liste');
	}
	else
	{
		if(confirm("Voulez-vous supprimer l'activité sélectionnée ?"))
		{
			document.forms['listeformulaire'].typeaction.value=TypeAct;
			document.forms['listeformulaire'].action='formulaire_menu.php';
			document.forms['listeformulaire'].target='_self';
			document.forms['listeformulaire'].submit();
			parent.FORMFRAMELISTE.location.replace('formulaire_liste.php');
			
			parent.FORMFRAMELISTE.location.replace('formulaire_liste.php');
			parent.FORMFRAMEMODIF.location.replace('formulaire_modif.php');
			parent.FORMFRAMEMODIFMENU.location.replace('formulaire_modif_menu.php');
		}
	}
}
function copie(TypeAct)
{
	if (document.listeformulaire.idformulaire.selectedIndex == -1)
	{
		alert('Veuillez sélectionner une activité dans la liste');
	}
	else
	{
		if(confirm("Voulez-vous copier l'activité sélectionnée ?"))
		{
			document.forms['listeformulaire'].typeaction.value=TypeAct;
			document.forms['listeformulaire'].action='formulaire_menu.php';
			document.forms['listeformulaire'].target='_self';
			document.forms['listeformulaire'].submit();
			parent.FORMFRAMEMODIF.location.replace('formulaire_modif.php');
			parent.FORMFRAMEMODIFMENU.location.replace('formulaire_modif_menu.php')
			parent.FORMFRAMELISTE.location.replace('formulaire_liste.php');
			//-> de preference pointer vers la copie
		}
	}
}
function ajouter(TypeAct)
{
	document.forms['listeformulaire'].typeaction.value=TypeAct;
	document.forms['listeformulaire'].action='formulaire_menu.php';
	document.forms['listeformulaire'].target='_self';
	document.forms['listeformulaire'].submit();
}
</script>
<script src="selectionobj.js" type="text/javascript"></script>
<title>Conception d'activités en ligne</title>
</head>
<body>
<form name="listeformulaire" action="formulaire_liste.php" target="FORMFRAMELISTE" method ="get">
<table border="0" cellpadding="0" cellspacing="2" width="100%">
<tr>
	<td colspan="2">
		<select class="listeForm" name="idformulaire" onchange="javascript: this.form.submit();" size="10" style="width: 100%;">
	[BLOCK_FORM+]
			<option {couleur} value="{id_formulaire}" title="{infobulle_formulaire}" onmouseover="top.defTexteStatut(escape(this.title));" onmouseout="top.defTexteStatut('&nbsp;');"{selected}>{nom_formulaire}</option>
	[BLOCK_FORM-]
		</select>
		<input type="hidden" name="typeaction" value="" />
		<input type="hidden" name="idobj" value="0" />
		<input type="hidden" name="verifUtilisation" value="1" />
	</td>
</tr>
<tr>
	<td style="text-align : left">
		<a href="javascript: suppression('supprimer');">Supprimer</a>
	</td>
	<td style="text-align : right">
		<a href="javascript: copie('copier');">Copier</a>
	</td>
</tr>
</table>
</form>
<br />
<div align="center">
	<a href="#" onclick="ajouter('ajouter');">Créer une activité</a>
</div>
{Message_Etat}
</body>
</html>
