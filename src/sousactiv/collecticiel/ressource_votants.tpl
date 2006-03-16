<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://dialogue.css">
<link type="text/css" rel="stylesheet" href="theme://ressource_votants.css">
<script type="text/javascript" language="javascript" src="ressource_votants.js"></script>
<head>
<body>
<h1>Liste des votants</h1>
[BLOCK_TABLE_VOTANTS+]
<div class="liste">
[BLOCK_VOTANT+]
{personne.sexe}
{outil.courriel}
<p class="nom_complet">{personne.nom}&nbsp;{personne.prenom}</p>
<p class="pseudo">{personne.pseudo}</p>
[BLOCK_VOTANT-]
</div>
[BLOCK_TABLE_VOTANTS-]
[BLOCK_PAS_VOTANT+]<p class="pas_de_votant_trouve">Pas de votant trouv&eacute;</p>[BLOCK_PAS_VOTANT-]

[BLOCK_TABLE_VOTANTS_MANQUANTS+]
<form action="ressource_votants.php" method="get">
<h1>Liste des votants manquants</h1>
<p>Pour pouvoir soumettre ce document, il vous suffit de sélectionner le/les membre(s) de l'équipe qui n'a(ont) pas encore voté(s). Puis de cliquer sur le lien &laquo;&nbsp;Je vote pour eux&nbsp;&raquo;.
<div class="liste">
[BLOCK_VOTANT_MANQUANT+]
<input type="checkbox" name="idPers[]" value="{personne.id}">
{personne.sexe}
{outil.courriel}
<p class="nom_complet">{personne.nom}&nbsp;{personne.prenom}</p>
<p class="pseudo">{personne.pseudo}</p>
[BLOCK_VOTANT_MANQUANT-]
<p id="voter"><a href="javascript: void(0);" onclick="return voter()" onfocus="blur()">je vote pour eux</a></p>
</div>
<input type="hidden" name="idResSA" value="{ressource.id}">
<input type="hidden" name="idEquipe" value="{equipe.id}">
</form>
[BLOCK_TABLE_VOTANTS_MANQUANTS-]
</body>
</html>
[SET_SEXE_MASCULIN+]<img src="commun://icones/boy.gif" width="15" height="26" border="0" class="sexe">[SET_SEXE_MASCULIN-]
[SET_SEXE_FEMININ+]<img src="commun://icones/girl.gif" width="15" height="26" border="0" class="sexe">[SET_SEXE_FEMININ-]
[SET_COURRIEL+]<a href="mailto:{personne.courriel}" title="Envoyer un courriel" onfocus="blur()" class="courriel"><img src="commun://icones/mail.gif" width="16" height="16" border="0"></a>[SET_COURRIEL-]
[SET_SANS_COURRIEL+]<img src="commun://icones/pas_mail.gif" width="16" height="16" border="0" class="courriel">[SET_SANS_COURRIEL-]
