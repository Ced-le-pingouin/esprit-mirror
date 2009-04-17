<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js.php"></script>
<script type="text/javascript" language="javascript" src="javascript://selection_multiple.js"></script>
<script type="text/javascript" language="javascript">
<!--
var Affichage = true;
function Reduire(v_sNomBloc)
{
	if (document.getElementById(v_sNomBloc)) 
	{
		if (Affichage)
		{
			document.getElementById(v_sNomBloc).style.display = "none";
			Affichage = false;
		}
		else
		{
			document.getElementById(v_sNomBloc).style.display = "table-row";
			Affichage = true;
		}
	}
}
//-->
</script>
<style type="text/css">
<!--
td#id_table_entete_1 { width: 1%; }
td#id_table_entete_2 { width: 99%; text-align: left; }
span.sans_adresse_courrielle { color: rgb(150,150,150); }
-->
</style>
</head>
<body onload="init()">
{form}
[BLOCK_LISTE_STATUTS+]
[ARRAY_STATUTS+]Tous,,,Tous les responsables,,,,Tous les tuteurs,,Tous les &eacute;tudiants[ARRAY_STATUTS-]
<table border="0" cellspacing="1" cellpadding="0" width="100%">
[BLOCK_STATUT+]
<tr><td class="cellule_sous_titre"><input type="checkbox" name="idStatuts{statut.id}[]" onclick="selectionner(this,'idStatuts{statut.id}',-1,true)" onfocus="blur()" value="-1"></td><td class="cellule_sous_titre" id="id_table_entete_2" onclick="javascript:Reduire('liste_personne_{statut.id}');" onmouseover="document.body.style.cursor='pointer';" onmouseout="document.body.style.cursor='auto';">&nbsp;<b>{statut.nom} {type.activite} </b><br /><small>(cliquez pour r&eacute;duire/afficher)</small></td></tr>
<tr id="liste_personne_{statut.id}"><td>&nbsp;</td><td>{liste_membres}</td></tr>
[BLOCK_STATUT-]
</table>
[BLOCK_LISTE_STATUTS-]

[BLOCK_LISTE_STATUTS_FORMATION+]
[ARRAY_STATUTS_FORMATION+]Tous,,,<b>Tous les responsables de la formation</b>,,,,<b>Tous les tuteurs de la formation</b>,,<b>Tous les &eacute;tudiants de la formation</b>[ARRAY_STATUTS_FORMATION-]
<table border="0" cellspacing="1" cellpadding="0" width="100%">
[BLOCK_STATUT_FORMATION+]
<tr><td class="cellule_sous_titre"><input type="checkbox" name="idStatuts{statut.id}[]" onclick="selectionner(this,'idStatuts{statut.id}',-1,true)" onfocus="blur()" value="-1"></td><td class="cellule_sous_titre" id="id_table_entete_2" onclick="javascript:Reduire('liste_personne_{statut.id}');" onmouseover="document.body.style.cursor='pointer';" onmouseout="document.body.style.cursor='auto';">&nbsp;{statut.nom} <br /><small>(cliquez pour r&eacute;duire/afficher)</small></td></tr>
<tr id="liste_personne_{statut.id}"><td>&nbsp;</td><td>{liste_membres}</td></tr>
[BLOCK_STATUT_FORMATION-]
</table>
[BLOCK_LISTE_STATUTS_FORMATION-]

[BLOCK_LISTE_EQUIPES+]
<table border="0" cellspacing="1" cellpadding="0" width="100%">
<tr><td class="cellule_sous_titre" valign="top"><input type="checkbox" name="idEquipes" onclick="selectionner(this,'idEquipes',-1,true)" onfocus="blur()" value="-1"></td><td id="id_table_entete_2" class="cellule_sous_titre" colspan="2">&nbsp;<b>Toutes les &eacute;quipes {type.activite}</b></td></tr>
[BLOCK_EQUIPE+]
<tr><td>&nbsp;</td><td class="cellule_sous_titre" valign="top"><input type="checkbox" name="idEquipes[]" onclick="selectionner(this,'idEquipes',0,true)" onfocus="blur()" value="0"{equipe.checked}></td><td id="id_table_entete_2" class="cellule_sous_titre">&nbsp;{equipe.nom}</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td><td>{liste_membres}</td></tr>
[BLOCK_EQUIPE-]
</table>
[BLOCK_LISTE_EQUIPES-]

[BLOCK_LISTE_PERSONNES+]
<table border="0" cellspacing="1" cellpadding="0" width="100%">
[BLOCK_PERSONNE+]
<tr><td class="cellule_sous_titre" valign="top"><input type="checkbox" name="idTous" onclick="selectionner(this,'',-2,true)" onfocus="blur()" value="-2"></td><td id="id_table_entete_2" class="cellule_sous_titre">&nbsp;<b>{personne.nombre} {type.activite}</b></td></tr>
<tr><td>&nbsp;</td><td>{liste_membres}</td></tr>
[BLOCK_PERSONNE-]
</table>
[BLOCK_LISTE_PERSONNES-]

[BLOCK_AUCUN_INSCRIT+]
<table border="0" cellspacing="50" cellpadding="0" width="100%" height="100%">
<tr><td class="attention">Aucun inscrit &agrave; ce cours</td></tr>
</table>
[BLOCK_AUCUN_INSCRIT-]

{/form}
</body>
</html>
[SET_ELEMENT_STATUT+]
[SET_ELEMENT_STATUT-]

[SET_LISTE_MEMBRES+]
[VAR_MEMBRE+]{membre.nom}&nbsp;{membre.prenom}&nbsp;<small>({membre.pseudo})</small>[VAR_MEMBRE-]
<div style="background-color: rgb(255,255,255); margin: 0px;">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
[BLOCK_MEMBRE+]<tr><td><input type="checkbox" name="idPers[]" onclick="selectionner(this,'{parent}',1,false)" value="{membre.id}"{membre.checkbox.disabled}{membre.checkbox.checked}></td><td style="border: rgb(180,180,180) none 1px; border-bottom-style: dashed; width: 99%; font-size: 9pt">&nbsp;{membre}</td></tr>[BLOCK_MEMBRE-]
</table>
</div>
[SET_LISTE_MEMBRES-]

