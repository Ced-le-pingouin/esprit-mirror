<html>
<head>
<title>{glossaire->titre}</title>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
</head> 
<body>
<h1 style="text-align: center;"><a name="haut"></a>{glossaire->titre}</h1>
[BLOCK_GLOSSAIRE_ELEMENTS+][BLOCK_GLOSSAIRE_ELEMENTS-]
</body>
</html>
[SET_GLOSSAIRE_ELEMENT+]
<h5>{glossaire->element->titre}</h5>
<p>{glossaire->element->texte}</p>
<div style="text-align: center;"><a href="#top" onfocus="blur()">Retour</a></div>
<hr>
[SET_GLOSSAIRE_ELEMENT-]
[SET_GLOSSAIRE_SANS_ELEMENTS+]<p style="text-align: center; font-weight: bold;">Pas d'élément trouvé</p>[SET_GLOSSAIRE_SANS_ELEMENTS-]