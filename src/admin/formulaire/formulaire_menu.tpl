<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="css://sousactive/formulaire.css" />
<script type="text/javascript">
<!--
function suppression(TypeAct)
{
	if (document.listeformulaire.idformulaire.selectedIndex == -1)
	{
		alert('Veuillez sélectionner une activité en ligne dans la liste');
	}
	else
	{
		if(confirm("Voulez-vous supprimer l'activité en ligne sélectionnée ?"))
		{
			document.forms['listeformulaire'].typeaction.value=TypeAct;
			document.forms['listeformulaire'].submit();
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
		if(confirm("Voulez-vous copier l'activité en ligne sélectionnée ?"))
		{
			document.forms['listeformulaire'].typeaction.value=TypeAct;
			document.forms['listeformulaire'].submit();
		}
	}
}
function ajouter(TypeAct)
{
	document.forms['listeformulaire'].typeaction.value=TypeAct;
	document.forms['listeformulaire'].submit();
}
//-->
</script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script src="selectionobj.js" type="text/javascript"></script>
<title>Conception d'activités en ligne</title>
</head>
<body class="formulaire_menu">
<form name="listeformulaire" action="formulaire_menu.php" method="get">
<input type="checkbox" name="bMesForms" id="bMesForms" value="1" onclick="this.form.submit();" {bMesFormsCoche} /><label for="bMesForms">uniquement mes activités</label>
<input type="hidden" name="typeaction" value="selection" />
<input type="hidden" name="idobj" value="{idObjForm}" />
<div class="bloc">
	<h3>Activité en ligne</h3>
	<p class="nom">
		<select class="listeForm" name="idformulaire" onchange="this.form.submit();" size="1" style="width: 100%;">
			<option value="0" style="background-color: rgb(240,240,240);">-Veuillez choisir une activité-</option>
		[BLOCK_SEL_FORM+]
			<option {couleur} value="{id_formulaire}" title="{infobulle_formulaire}" onmouseover="top.defTexteStatut(escape(this.title));" onmouseout="top.defTexteStatut('&nbsp;');"{selected}>{nom_formulaire}</option>
		[BLOCK_SEL_FORM-]
		</select>
	</p>
	<p class="liens">
		<a href="#" onclick="ajouter('ajouter');">Ajouter</a>
		[BLOC_LIEN_FORM+]
		| <a href="javascript: suppression('supprimer');">Supprimer</a>
		| <a href="javascript: copie('copier');">Copier</a>
		<span class="liens" style="border-top: 1px solid rgb(111,105,87); display: block; margin: 4px 0 0 0; padding: 4px 0 0 0;">
			<a href="javascript: modifaxeform({id_formulaire_sel});">Définir les axes</a>
		</span>
		[BLOC_LIEN_FORM-]
	</p>
</div>
[BLOC_ELEM_COURANT+]
<div class="bloc">
<h3>Elément</h3>
<p class="nom">{nom_elem_courant}</p>
<p class="liens">
	<a href="javascript: ajoutobj({id_formulaire_sel},{bMesForms});">Ajouter</a>
	[BLOC_ELEM_COURANT_LIENS+]
	| <a href="javascript: supobj({id_formulaire_sel},{id_obj},{bMesForms});">Supprimer</a>
	| <a href="javascript: copieobj({id_formulaire_sel},{id_obj},{bMesForms});">Copier</a>
	[BLOC_ELEM_COURANT_LIENS-]
</p>
</div>
[BLOC_ELEM_COURANT-]
</form>
{Message_Etat}
</body>
</html>
