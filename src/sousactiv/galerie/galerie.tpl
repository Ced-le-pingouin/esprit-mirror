<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<link type="text/css" rel="stylesheet" href="css://sousactive/galerie.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
<script type="text/javascript" language="javascript" src="galerie.js"></script>
</head>
<body>
<h1>Galerie</h1>
[BLOCK_BARRE_OUTILS+]
<div id="barre_outils">{barre_outils.galerie}</div>
[SET_OUTIL_COMPOSER_GALERIE+]<a id="composer_galerie" href="composer_galerie-index.php?idSA={sousactiv.id}" target="_blank" title="Cliquer ici pour composer votre galerie"><img src="commun://icones/24x24/galerie.gif" width="24" height="24" border="0"></a>[SET_OUTIL_COMPOSER_GALERIE-]
[BLOCK_BARRE_OUTILS-]
[BLOCK_CONSIGNE+]<div id="consigne">{consigne}</div>[BLOCK_CONSIGNE-]
[BLOCK_GALERIE+]
<div class="conteneur_documents">
<h3>{collecticiel.nom}</h3>
<ul>
[BLOCK_DOCUMENT+]
<li>
<h4 title="{document.nom}">{document.nom}</h4>
<div class="icone"><a href="lib://download.php?f={document.href}" title="[TXT_CLIQUER_ICI_POUR_TELECHARGER_DOCUMENT]"><img src="commun://icones/64x64/{document.icone}" width="64" height="64" border="0"></a></div>
<div class="infos">
<p class="auteur">{personne.sexe}&nbsp;<span class="auteur">{personne.nom_complet}</span></p>
<p class="envoi_courriel">{envoi_courriel}</p>
<p class="telecharger"><a href="lib://download.php?f={document.href}" title="[TXT_CLIQUER_ICI_POUR_TELECHARGER_DOCUMENT]"><img src="commun://icones/16x16/telecharger.gif" width="16" height="16" alt="" border="0">&nbsp;T&eacute;l&eacute;charger</a></p>
</div>
[BLOCK_DOCUMENT-]
</ul>
<p>&nbsp;</p>
</div>
[BLOCK_GALERIE-]
[BLOCK_AUCUN_DOCUMENT+]<pre id="aucun_document_trouve">&#8220;&nbsp;Aucun document n'a &eacute;t&eacute; trouv&eacute; dans cette galerie.&nbsp;&#8221;</pre>[BLOCK_AUCUN_DOCUMENT-]
<p style="clear: both;">&nbsp;</p>
<p>&nbsp;</p>
</body>
</html>

