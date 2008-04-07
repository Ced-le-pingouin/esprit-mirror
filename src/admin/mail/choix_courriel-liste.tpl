<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://onglet/onglet.css">
<script type="text/javascript" language="javascript" src="javascript://globals.js"></script>
<script type="text/javascript" language="javascript" src="javascript://window.js"></script>
<script type="text/javascript" language="javascript" src="javascript://outils_admin.js.php"></script>
<script type="text/javascript" language="javascript">
<!--
var g_sMenu = "";

function selectionner(v_oThis,v_sParent,v_iId,v_bBoucle)
{
	var bChecked = v_oThis.checked;
	var oElems = v_oThis.form.elements;
	var i, j;
	
	v_iId = parseInt(v_iId);
	
	for (i=0; i<oElems.length; i++)
	{
		if (v_oThis == oElems[i])
		{
			if (v_bBoucle)
			{
				for (j=i+1; j<oElems.length; j++)
				{
					if ((oElems[j].name.indexOf(v_sParent) > -1 && v_iId == 0) ||
						(oElems[j].name.indexOf(v_sParent) == -1 &&	oElems[j].value < 1))
						break;
					
					if (!oElems[j].disabled)
						oElems[j].checked = bChecked;
				}
				
				if (i>0) selectionner(oElems[i],v_sParent,oElems[i].value,false);
			}
			else
			{
				var max = 0;
				var count = 0;
				
				if (v_iId < 1)
				{
					// Trouver le dernier de la liste
					for (j=i+1; j<oElems.length; j++)
					{
						if (v_iId >= parseInt(oElems[j].value)) break;
						if (!oElems[j].disabled) { max++; if (oElems[j].checked) count++; }
					}
					
					bChecked = (max == count);
				}
				
				if (!oElems[i].disabled)
					oElems[i].checked = bChecked;
				
				// Rechercher son parent
				for (j=i-1; j>=0; j--)
				{
					if (oElems[j].type == "checkbox" &&
						parseInt(oElems[j].value) < 1 &&
						parseInt(oElems[j].value) < v_iId)
					{
						selectionner(oElems[j],v_sParent,oElems[j].value,false);
						break;
					}
				}
			}
			
			break;
		}
	}
	
	changerMenu();
}

function changerMenu()
{
	var sMenu = "";
	var oElems = document.forms[0].elements;
	
	for (var i=0; i<oElems.length; i++)
		if (oElems[i].name == "idPers[]" && oElems[i].checked)
		{
			if (g_sMenu != "" && g_sMenu == sMenu) return;
			sMenu = "?menu=1";
			break;
		}
	
	top.rafraichir_menu(sMenu);
	
	g_sMenu = sMenu;
}

function verifier_selectionner()
{
	var elems = document.getElementsByName("idPers[]");
	var s1 = null;
	var s2 = null;
	
	for (var i=0; i<elems.length; i++)
	{
		if (elems.item(i).checked)
		{
			s2 = elems.item(i).onclick.toString();
			
			if (s1 == s2)
				continue;
			
			s1 = s2;
			
			elems.item(i).checked = !elems.item(i).checked;
			elems.item(i).click();
		}
	}
}

function init()
{
	verifier_selectionner();
	changerMenu();
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
[ARRAY_STATUTS+]Tous,,,<b>Tous les responsables</b>,,,,<b>Tous les tuteurs</b>,,<b>Tous les &eacute;tudiants</b>[ARRAY_STATUTS-]
<table border="0" cellspacing="1" cellpadding="0" width="100%">
[BLOCK_STATUT+]
<tr><td class="cellule_sous_titre"><input type="checkbox" name="idStatuts{statut.id}[]" onclick="selectionner(this,'idStatuts{statut.id}',-1,true)" onfocus="blur()" value="-1"></td><td class="cellule_sous_titre" id="id_table_entete_2">&nbsp;{statut.nom}</td></tr>
<tr><td>&nbsp;</td><td>{liste_membres}</td></tr>
[BLOCK_STATUT-]
</table>
[BLOCK_LISTE_STATUTS-]

[BLOCK_LISTE_EQUIPES+]
<table border="0" cellspacing="1" cellpadding="0" width="100%">
<tr><td class="cellule_sous_titre" valign="top"><input type="checkbox" name="idEquipes" onclick="selectionner(this,'idEquipes',-1,true)" onfocus="blur()" value="-1"></td><td id="id_table_entete_2" class="cellule_sous_titre" colspan="2">&nbsp;<b>Toutes les &eacute;quipes</b></td></tr>
[BLOCK_EQUIPE+]
<tr><td>&nbsp;</td><td class="cellule_sous_titre" valign="top"><input type="checkbox" name="idEquipes[]" onclick="selectionner(this,'idEquipes',0,true)" onfocus="blur()" value="0"{equipe.checked}></td><td id="id_table_entete_2" class="cellule_sous_titre">&nbsp;{equipe.nom}</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td><td>{liste_membres}</td></tr>
[BLOCK_EQUIPE-]
</table>
[BLOCK_LISTE_EQUIPES-]

[BLOCK_LISTE_PERSONNES+]
<table border="0" cellspacing="1" cellpadding="0" width="100%">
[BLOCK_PERSONNE+]
<tr><td class="cellule_sous_titre" valign="top"><input type="checkbox" name="idTous" onclick="selectionner(this,'',-2,true)" onfocus="blur()" value="-2"></td><td id="id_table_entete_2" class="cellule_sous_titre">&nbsp;Toutes les personnes</td></tr>
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
[BLOCK_MEMBRE+]<tr><td><input type="checkbox" name="idPers[]" onclick="selectionner(this,'{parent}',1,false)" value="{membre.id}"{membre.checkbox.disabled}{membre.checkbox.checked}></td><td style="border: rgb(180,180,180) none 1px; border-bottom-style: dashed; width: 99%;">&nbsp;{membre}</td></tr>[BLOCK_MEMBRE-]
</table>
</div>
[SET_LISTE_MEMBRES-]

