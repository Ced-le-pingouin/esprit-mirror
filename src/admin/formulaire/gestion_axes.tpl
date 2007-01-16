<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Gestion des Axes/Tendances</title>
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css" />
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire_axe.css" />
<script type="text/javascript">
function fermer()
{
	top.opener.top.location.replace("formulaire_axe.php?idformulaire={idformulaire}"); 
	parent.window.close();
}
</script>
</head>
<body>
<div id="principal">
<h1>Gestion des axes</h1>
[BLOCK_SUPPRESSION+]
<form action="gestion_axes.php" name="formgestion" method ="post">
	<fieldset><legend>Supprimer un axe</legend>
	[BLOCK_AXES+]
	<input type="radio" name="axe_s" value="{id_axe}" />{desc_axe}<br />
	<input type="hidden" name="idformulaire" value="{idformulaire}" />
	[BLOCK_AXES-]
	</fieldset>
</form>
[BLOCK_SUPPRESSION-]
[BLOCK_MODIF+]
<form action="gestion_axes.php" name="formgestion" method ="post">
	<fieldset><legend>Modifier le nom d'un axe</legend>
	[BLOCK_AXES2+]
	<input type="radio" name="axe_m" onclick="document.formgestion.axemodif.value = '{desc_axe2js}'; document.formgestion.axemodif.focus();" value="{id_axe2}" />
	{desc_axe2}<br />
	[BLOCK_AXES2-]
	<br />
	<input type="text" name="axemodif" size="60" maxlength="100" />
	<input type="hidden" name="idformulaire" value="{idformulaire}" />
	</fieldset>
</form>
[BLOCK_MODIF-]
[BLOCK_AJOUT+]
<form action="gestion_axes.php" name="formgestion" method ="post">
	<fieldset><legend>Ajouter un axe</legend>
	<div align="center">
	<input type="text" name="axeajout" size="60" maxlength="100" />
	</div>
	<input type="hidden" name="idformulaire" value="{idformulaire}" />
	</fieldset>
</form>
[BLOCK_AJOUT-]
[BLOCK_CHOIX+]
<fieldset>
	<ul>
	<li><a href="gestion_axes.php?idformulaire={idformulaire}&amp;action=ajout">Ajouter un axe</a></li>
	<li><a href="gestion_axes.php?idformulaire={idformulaire}&amp;action=modif">Modifier un axe</a></li>
	<li><a href="gestion_axes.php?idformulaire={idformulaire}&amp;action=supp">Supprimer un axe</a></li>
	</ul>
</fieldset>
[BLOCK_CHOIX-]
{Message}
</div>
<div id="piedpage">
	<a href="#" onclick="fermer();" id="fermer">Fermer</a>
	[BLOCK_LIEN+]
	<a href="#" onclick="document.forms['formgestion'].submit();" id="valider">{Titre_Lien}</a>
	[BLOCK_LIEN-]
</div>
</body>
</html>
