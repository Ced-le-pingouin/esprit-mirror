<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://formation.css">
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="dossier_formations-liste.js"></script>
</head>
<body class="formation_liste">
<div id="liste">
[BLOCK_DOSSIER_FORMATIONS+]<div class="dossier">{dossier}</div>[BLOCK_DOSSIER_FORMATIONS-]
</div>
<form action="dossier_formations-liste.php" method="get">
<input type="hidden" name="idDossierForms" value="{dossier_formations.id}">
</form>
</body>
</html>
[SET_SANS_DOSSIER+]<p>Aucun dossier trouv&eacute;.</p><p>Commencez par créer un nouveau dossier. Dès que c'est fait, sélectionnez les formations dans la liste située à votre droite et n'oubliez pas d'enregistrer vos sélections.</p>[SET_SANS_DOSSIER-]
[SET_DOSSIER+]<a href="dossier_formations.php?idDossierForms={dossier_formation.id}" target="Principale">{dossier_formation.icone}</a><br><span class="titre">{dossier_formation.nom}</span>[SET_DOSSIER-]
[SET_ICONE_AVEC_FORMATIONS+]<img src="commun://icones/48x48/dossier_forms.gif" width="48" height="48" border="0" alt="">[SET_ICONE_AVEC_FORMATIONS-]
[SET_ICONE_AUCUNE_FORMATION+]<img src="commun://icones/48x48/dossier_forms-vide.gif" width="48" height="48" border="0" alt="">[SET_ICONE_AUCUNE_FORMATION-]
[SET_ICONE_PREMIER_DOSSIER+]<img src="commun://icones/48x48/dossier_forms-fav.gif" width="48" height="48" border="0" alt="">[SET_ICONE_PREMIER_DOSSIER-]
