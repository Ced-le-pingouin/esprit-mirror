<html>
<head>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://admin/admin_general.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="changer_dossier.js"></script>
</head>
<body class="changer_dossier">
{form}
<div id="contenaire">
[BLOCK_DOSSIER_FORMATIONS+]
[ARRAY_ICONES+]
<img src="commun://icones/48x48/dossier_forms-vide.gif" width="48" height="48" alt="" border="0">
###<img src="commun://icones/48x48/dossier_forms.gif" width="48" height="48" alt="" border="0">
###<img src="commun://icones/48x48/dossier_forms-fav.gif" width="48" height="48" alt="" border="0">
[ARRAY_ICONES-]
<div class="dossier_formations" dossier="{dossier_formations.id}">
<div class="icone">{dossier_formations.icone}</div><div class="titre">{dossier_formations.titre}</div>
</div>
[BLOCK_DOSSIER_FORMATIONS-]
</div>
<input type="hidden" name="idDossierForms" value="{dossier_formations.id}">
<input type="hidden" name="event" value="">
{/form}
<br><br>
<div id="remarque">
<p>[TXT_REMARQUE]</p>
</div>
</body>
</html>

