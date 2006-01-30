<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css">
<script type="text/javascript" src="javascript://window.js"></script>
<script type="text/javascript" src="formulaire.js"></script>
<script type="text/javascript" src="objetformulaire.js"></script>
<style type="text/css">
p { margin: 0px; }

.bloc
{
	margin: 0px 3px 12px 3px;
	background-color: rgb(111,105,87);
	padding: 2px;
	text-align: center;
}

.bloc h3
{
	margin: 0px;
	padding: 2px 0px;
	
	font-size: 12px;
	font-weight: bold;
	
	color: rgb(250,250,250);
}

.bloc .nom
{
	margin: 1px 0px;
	background-color: rgb(231,225,212);
	padding: 4px 0px;
}

.bloc .liens
{
	background-color: rgb(202,195,177);
	padding: 4px 0px;
}

.bloc .liens a { font-size: 11px; }
</style>
</head>

<body onLoad="top.defTitre(escape('{titre_haut}'));">

<form name="listeformulaire" action="formulaire_liste.php" target="FORMFRAMELISTE" method ="get">
<input type="checkbox" name="cbMesForms" id="cbMesForms" value="1" onChange="selectionFormulaire();" {cbMesFormsCoche}><label for="cbMesForms">uniquement mes formulaires</label>
<table border="0" cellpadding="0" cellspacing="2" width="100%">
	<tr><td>
		<select class="listeForm" name="idformulaire" onChange="selectionFormulaire();" SIZE="1" border="0" style="width: 99%;">
			<OPTION VALUE="0">[Veuillez choisir un formulaire]</OPTION>
			[BLOCK_FORM+]
			<OPTION {couleur} VALUE="{id_formulaire}" TITLE="{infobulle_formulaire}" onMouseover="top.defTexteStatut(escape(this.title));" onMouseout="top.defTexteStatut('&nbsp;');" {select_form}>{nom_formulaire}</OPTION>
			[BLOCK_FORM-]
		</SELECT>
		<INPUT TYPE="hidden" NAME="typeaction" VALUE="">
		<INPUT TYPE="hidden" NAME="idobj" VALUE="{id_obj}">
		<INPUT TYPE="hidden" NAME="verifUtilisation" VALUE="1">
	</td></tr>
</table>
</form>

[BLOC_FORM_COURANT+]
<div class="bloc">
<h3>Formulaire</h3>
<p class="nom">{nom_form_courant}</p>
<p class="liens">
	<a href="javascript: void(0);" onClick="parent.FORMFRAMELISTE.location.replace('ajouter_formulaire.php');">Nouveau</a>
	| <a href="javascript: suppression('supprimer');">Supprimer</a>
	| <a href="javascript: copie('copier');">Copier</a>
</p>
</div>
[BLOC_FORM_COURANT-]

[BLOC_ELEM_COURANT+]
<div class="bloc">
<h3>Elément</h3>
<p class="nom">{nom_elem_courant}</p>
<p class="liens">
	<a href="javascript: ajoutobj({id_formulaire_sel});">Nouveau</a>
	[BLOC_ELEM_COURANT_LIENS+]
	| <a href="javascript: supobj({id_obj}, {id_formulaire_sel});">Supprimer</a>
	| <a href="javascript: copieobj({id_obj}, {id_formulaire_sel});">Copier</a>
	[BLOC_ELEM_COURANT_LIENS-]
</p>
</div>
[BLOC_ELEM_COURANT-]

</body>
</html>
