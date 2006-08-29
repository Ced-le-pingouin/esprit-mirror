<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css">

<SCRIPT language="JavaScript">
<!--
function suppression(TypeAct)
{
	if (document.listeformulaire.idformulaire.selectedIndex == -1)
	{
		alert('Veuillez sélectionner un formulaire dans la liste');
	}
	else
	{
		if(confirm('Voulez-vous supprimer le formulaire sélectionné ?'))
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
		alert('Veuillez sélectionner un formulaire dans la liste');
	}
	else
	{
		if(confirm('Voulez-vous copier le formulaire sélectionné ?'))
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
-->
</SCRIPT>
</head>

<body>

<form name="listeformulaire" action="formulaire_liste.php" target="FORMFRAMELISTE" method ="get">

<table border="0" cellpadding="0" cellspacing="2" width="100%">
<tr>
<td colspan=2>
<select class="listeForm" name="idformulaire" onchange="javascript: this.form.submit();" SIZE="10" border="0" style="width: 100%;">

[BLOCK_FORM+]
<OPTION {couleur} VALUE="{id_formulaire}" TITLE="{infobulle_formulaire}" onMouseover="top.defTexteStatut(escape(this.title));" onMouseout="top.defTexteStatut('&nbsp;');">{nom_formulaire}
[BLOCK_FORM-]
</SELECT>
<INPUT TYPE="hidden" NAME="typeaction" VALUE="">
<INPUT TYPE="hidden" NAME="idobj" VALUE="0">
<INPUT TYPE="hidden" NAME="verifUtilisation" VALUE="1">

</td>
</tr>
<tr>
<td style="text-align : left">
<a 
	href="javascript: suppression('supprimer');">Supprimer
</a>
</td>
<td style="text-align : right">
<a 
	href="javascript: copie('copier');">Copier
</a>
</td>
</tr>
</table>
</FORM>

<br>
<div align="center">
<a
	href="javascript: void(0);" 
	onClick="parent.FORMFRAMELISTE.location.replace('ajouter_formulaire.php');">
    Créer un formulaire
</a>
</div>

</body>
</html>
