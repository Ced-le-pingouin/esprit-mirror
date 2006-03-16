<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link tyle="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://tableau_bord.css">
<link type="text/css" rel="stylesheet" href="theme://tableau_bord-filtre.css">
<script type="text/javascript" language="javascript" src="tableau_bord-filtre.js"></script>
</head>
<body>
<form action="tableau_bord.php" method="get" target="Principale">
<div id="idFiltres">
&nbsp;[TXT_UNITE]&nbsp;:&nbsp;
<select name="idNiveau" id="select_liste">
[BLOCK_MODULE+]
<optgroup label="&nbsp;{module.nom}&nbsp;:">
[BLOCK_RUBRIQUE+]<option value="{rubrique.option.value}" class="rubrique"{rubrique.option.selected}>&#8226; {rubrique.nom}</option>[BLOCK_RUBRIQUE-]
</optgroup>
[BLOCK_MODULE-]
</select>
<select name="idType" id="select_type">
<option value="0" class="titre">Tous les types d'actions</option>
[BLOCK_SOUS_ACTIVITE_TYPE+]<option value="{sous_activite_type.value}"{sous_activite_type.selected} class="element">&#8226;&nbsp;{sous_activite_type.label}</option>[BLOCK_SOUS_ACTIVITE_TYPE-]
</select>
<select name="idModal" id="select_modalite">
<option value="0" class="titre">Toutes les modalit&eacute;s</option>
<option value="1" class="element"{modalite.individuel.selected}>&#8226;&nbsp;Individuel</option>
<option value="2" class="element"{modalite.par_equipe.selected}>&#8226;&nbsp;Par &eacute;quipe</option>
</select>
</div>
<input type="hidden" name="typeNiveau" value="{typeNiveau.value}">
</form>
</body>
</html>

