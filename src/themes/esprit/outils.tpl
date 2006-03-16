<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
[BLOCK_JAVASCRIPT+][BLOCK_JAVASCRIPT-]
<style type="text/css">
<!--
body { background-image: none; }
a.titre_outil { font-size: 8pt; font-weight: bold; }
span.description { font-size: 8pt; color: rgb(128,128,128); }
td.outil_fond_clair { background-color: rgb(255,255,255); }
td.outil_fond_fonce { background-color: rgb(250,250,251); }
-->
</style>
</head>
<body>
<table border="0" cellspacing="0" cellpadding="3">
[BLOCK_OUTIL+]
<tr>
<td class="{outil.style}"><a href="javascript: {outil.lien}; top.close(); void(0);"><img src="commun://icones/64x64/{outil.icone}" width="64" height="64" border="0" title="Cliquer ici pour lancer cet outil"></a></td>
<td class="{outil.style}" style="border: rgb(180,180,180) none 1px; border-bottom-style: dashed;" width="99%"><a class="titre_outil" href="javascript: {outil.lien}; top.close(); void(0);" title="Cliquer ici pour lancer cet outil">{outil.nom}</a></b><br><span class="description">{outil.description}</span></td>
</tr>
[BLOCK_OUTIL-]
</table>
</body>
</html>

