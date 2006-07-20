<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://tooltip.css">
<link type="text/css" rel="stylesheet" href="theme://tableau_bord.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js"></script>
<script type="text/javascript" language="javascript" src="javascript://sous_activ.js"></script>
<script type="text/javascript" language="javascript" src="tableau_bord.js"></script>
<title>Tableau de bord</title>
</head>
<body>
<h1>{module.nom}</h1>
[BLOCK_RUBRIQUE+]
<h2>{rubrique.nom}</h2>
[BLOCK_BARRE_OUTILS+]<div id="barre_outils">{barre_outils}</div>[BLOCK_BARRE_OUTILS-]
<table border="0" cellspacing="1" cellpadding="2" width="100%">
<thead>
<tr>
<th class="largeur_1_pourcent">&nbsp;</th>
<th>[TXT_ETUDIANTS_INSCRITS_AU_COURS]</th>
[BLOCK_COLLECTICIEL_NOM+]<th id="{collecticiel.td.id}">{collecticiel.nom}</th>[BLOCK_COLLECTICIEL_NOM-]
[BLOCK_FORMULAIRE_NOM+]<th id="{formulaire.td.id}">{formulaire.nom}</th>[BLOCK_FORMULAIRE_NOM-]
[BLOCK_FORUM_NOM+]<th id="{forum.td.id}">{forum.nom}</th>[BLOCK_FORUM_NOM-]
[BLOCK_CHAT_NOM+]<th id="{chat.td.id}">{chat.parent.nom}<br><small>({chat.nom})</small></th>[BLOCK_CHAT_NOM-]
</tr>
</thead>
<tbody>
<tr>
<th>&nbsp;</th>
<td>&nbsp;</td>
[BLOCK_COLLECTICIEL_MODALITE+]<td class="cellule_modalite">{collecticiel.modalite}</td>[BLOCK_COLLECTICIEL_MODALITE-]
[BLOCK_FORMULAIRE_MODALITE+]<td class="cellule_modalite">{formulaire.modalite}</td>[BLOCK_FORMULAIRE_MODALITE-]
[BLOCK_FORUM_MODALITE+]<td class="cellule_modalite">{forum.modalite}</td>[BLOCK_FORUM_MODALITE-]
[BLOCK_CHAT_MODALITE+]<td class="cellule_modalite">{chat.modalite}</td>[BLOCK_CHAT_MODALITE-]
</tr>
</tbody>
[BLOCK_MESSAGE+]
[SET_AUCUN_INSCRIT+][TXT_AUCUN_INSCRIT_DANS_CETTE_UNITE][SET_AUCUN_INSCRIT-]
[SET_AUCUNE_EQUIPE+][TXT_AUCUNE_EQUIPE_TROUVEE_DANS_CETTE_UNITE][SET_AUCUNE_EQUIPE-]
<tbody>
<tr>
<th>&nbsp;</th>
<td colspan="{message.td.colspan}"><p class="aucune_equipe_trouvee">{message.texte}</p></td>
</tr>
</tbody>
[BLOCK_MESSAGE-]
[BLOCK_TABLEAU_BORD+]
[BLOCK_EQUIPE+]
<tbody>
<tr>
<th>&nbsp;</th>
<td colspan="{equipe.td.colspan}" class="cellule_equipe"><a href="javascript: void(0);" onclick="choix_courriel('?idEquipes={equipe.id}&amp;select=1'); return false;" onfocus="blur()" title="Envoyer un courriel">{equipe.nom}</a></td>
</tr>
</tbody>
[BLOCK_EQUIPE-]
<tbody id="tb{personne.td.id}">
<tr>
<th>&nbsp;{personne.index}&nbsp;</th>
<td id="{personne.td.id}" class="cellule_etudiant"><a href="javascript: void(0);" onclick="choix_courriel('?idPers={personne.id}&amp;select=1'); return false;" onfocus="blur()" title="[TLT_ENVOYER_COURRIEL]">{personne.nom}&nbsp;{personne.prenom}</a>[BLOCK_PERSONNE_INDICE+]&nbsp;<img src="theme://icones/etoile.gif" width="13" height="13" border="0">[BLOCK_PERSONNE_INDICE-]</td>
[BLOCK_COLLECTICIEL+]<td id="{collecticiel.td.id}">{collecticiel}{collecticiel.date}</td>[BLOCK_COLLECTICIEL-]
[BLOCK_FORMULAIRE+]<td id="{formulaire.td.id}">{formulaire}{formulaire.date}</td>[BLOCK_FORMULAIRE-]
[BLOCK_FORUM+]<td id="{forum.td.id}" title="[TLT_FORUM_NOMBRE_MESSAGES_FORUM]">{forum}{forum.date}</td>[BLOCK_FORUM-]
[BLOCK_CHAT+]<td id="{chat.td.id}" title="[TLT_CHAT_NOMBRE_MESSAGES_ARCHIVE]">{chat}</td>[BLOCK_CHAT-]
</tr>
</tbody>
[BLOCK_TABLEAU_BORD-]
</table>
[BLOCK_RUBRIQUE-]
</body>
</html>
[SET_COLLECTICIEL+]<a href="javascript: void(0);" onclick="return zone_de_cours('?idForm={formation.id}&amp;idMod={module.id}&amp;idUnite={rubrique.id}&amp;idActiv={activite.id}&amp;idSousActiv={sous_activite.id}{params.url}')" title="[TLT_CLIQUER_ICI_POUR_ACCEDER_AU_COLLECTICIEL]">{collecticiel}</a>[SET_COLLECTICIEL-]
[SET_FORMULAIRE+]<a href="javascript: void(0);" onclick="return zone_de_cours('?idForm={formation.id}&amp;idMod={module.id}&amp;idUnite={rubrique.id}&amp;idActiv={activite.id}&amp;idSousActiv={sous_activite.id}{params.url}')" title="[TLT_CLIQUER_ICI_POUR_ACCEDER_AU_FORMULAIRE]">{formulaire}</a>[SET_FORMULAIRE-]
[SET_FORUM+]{forum.nom}[SET_FORUM-]
[SET_CHAT+]{chat_archives.nom}[SET_CHAT-]