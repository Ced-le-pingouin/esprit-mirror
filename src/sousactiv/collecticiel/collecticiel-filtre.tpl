<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<script type="text/javascript" language="javascript" src="javascript://dom.window.js"></script>
<script type="text/javascript" language="javascript" src="collecticiel-filtre.js"></script>
</head>
<body onload="init()" class="barre_filtres">
<form action="collecticiel.php" method="get" target="collecticiel">
<div id="barre_filtres">
<select name="sltPersEquipe" onchange="changer_personne(this.options)" size="1">
[SET_MODALITE_PAR_EQUIPES+]Toutes les &eacute;quipes[SET_MODALITE_PAR_EQUIPES-]
[SET_MODALITE_INDIVIDUEL+]Tous les &eacute;tudiants[SET_MODALITE_INDIVIDUEL-]
<option value="0">{sltPersEquipe.options.tous}</option>
[BLOCK_EQUIPE_PERSONNE+]<option value="{sltPersEquipe.options.id}"{sltPersEquipe.options.selected}>{sltPersEquipe.options.nom}</option>[BLOCK_EQUIPE_PERSONNE-]
</select>
<select name="sltStatutDoc" onchange="this.form.submit()">
<option value="0" selected="selected">Tous les documents</option>
<option value="{sltStatutDoc.options.evalue}">Evalu&eacute;</option>
<option value="{sltStatutDoc.options.accepte}">Accept&eacute;</option>
<option value="{sltStatutDoc.options.approfondir}">Approfondir</option>
<option value="{sltStatutDoc.options.soumis_pour_evaluation}">Soumis pour &eacute;valuation</option>
<option value="{sltStatutDoc.options.en_cours}">En cours</option>
<option value="{sltStatutDoc.options.transfere}">Transf&eacute;r&eacute;</option>
</select>
<select name="sltDateDoc" onchange="this.form.submit()">
<option value="0" selected="selected">Tous les jours</option>
<option value="{sltDateDoc.options.aujourdhui}">Aujourd'hui</option>
<option value="{sltDateDoc.options.hier}">Depuis hier</option>
<option value="{sltDateDoc.options.2jours}">Depuis 2 jours</option>
<option value="{sltDateDoc.options.3jours}">Depuis 3 jours</option>
<option value="{sltDateDoc.options.4jours}">Depuis 4 jours</option>
<option value="{sltDateDoc.options.5jours}">Depuis 5 jours</option>
<option value="{sltDateDoc.options.6jours}">Depuis 6 jours</option>
<option value="{sltDateDoc.options.1semaine}">Depuis 1 semaine</option>
<option value="{sltDateDoc.options.1mois}">Depuis 1 mois</option>
</select><br>
<input type="checkbox" name="cbBlocsVides" onchange="this.form.submit()" onclick="blur()" value="1">Afficher les blocs vides</div>
<input type="hidden" name="tri" value="{tri.value}">
<input type="hidden" name="typeTri" value="{typeTri.value}">
<input type="hidden" name="pageYOffset" value="0">
</form>
</body>
</html>

