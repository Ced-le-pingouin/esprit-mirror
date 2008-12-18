<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

/*
** Fichier ................: inscription.php
** Description ............:
** Date de cr√©ation .......: 13/09/2002
** Derni√®re modification ..: 21/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unit√© de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Permissions
// ---------------------
$oProjet->verifPeutUtiliserOutils("PERM_OUTIL_INSCRIPTION");

$bPeutInscrire = TRUE;
if ($oProjet->oFormationCourante->retStatut()== STATUT_ARCHIVE)
{
	$bPeutInscrire = FALSE;
}

// ---------------------
// R√©cup√©rer les variables de l'url
// ---------------------
$url_iIdForm       = (isset($_GET["idform"]) ? $_GET["idform"] : 0);
$url_iSelectFiltre = (!empty($_GET["FILTRE"]) ? $_GET["FILTRE"] : -1);
$url_iSelectStatut = (!empty($_GET["STATUT_PERS"]) ? $_GET["STATUT_PERS"] : STATUT_PERS_ETUDIANT);

// ---------------------
// Filtres
// ---------------------
$asFiltre = array(
	array("Tous",-1)
	, array(STATUT_PERS_RESPONSABLE,STATUT_PERS_RESPONSABLE)
	, array(STATUT_PERS_CONCEPTEUR,STATUT_PERS_CONCEPTEUR)
	, array(STATUT_PERS_TUTEUR,STATUT_PERS_TUTEUR)
	, array(STATUT_PERS_ETUDIANT,STATUT_PERS_ETUDIANT)
);

$sOptionsFiltre = NULL;

for ($i=0; $i<count($asFiltre); $i++)
	$sOptionsFiltre .= "<option"
		." value=\"".$asFiltre[$i][1]."\""
		.($url_iSelectFiltre == $asFiltre[$i][1] ? " selected" : NULL)
		.">".($asFiltre[$i][1] < 1
			? $asFiltre[$i][0]
			: emb_htmlentities($oProjet->retTexteStatutUtilisateur($asFiltre[$i][0],"M")))
		."</options>\n";
	
unset($asFiltre,$url_iSelectFiltre);

// ---------------------
// Composer la liste des statuts
// ---------------------
$asStatutPers = array(
	array(STATUT_PERS_RESPONSABLE_POTENTIEL,"PERM_DESIGNE_RESPONSABLES_SESSION")
	, array(STATUT_PERS_RESPONSABLE,"PERM_ASS_RESP_SESSION")
	//, array(STATUT_PERS_CONCEPTEUR_POTENTIEL,"PERM_DESIGNE_CONCEPTEURS")
	, array(STATUT_PERS_CONCEPTEUR,"PERM_ASS_CONCEPT_COURS")
	, array(STATUT_PERS_TUTEUR,"PERM_ASS_TUTEUR_COURS")
	, array(STATUT_PERS_ETUDIANT,"PERM_ASS_ETUDIANT_COURS")
);

$sOptionsStatut = NULL;

for ($iIdxStatut=0; $iIdxStatut<count($asStatutPers); $iIdxStatut++)
	if ($oProjet->verifPermission($asStatutPers[$iIdxStatut][1]))
		$sOptionsStatut .= "<option"
			." value=\"".$asStatutPers[$iIdxStatut][0]."\""
			.($url_iSelectStatut == $asStatutPers[$iIdxStatut][0] ? " selected=\"selected\"" : NULL)
			.">".emb_htmlentities($oProjet->retTexteStatutUtilisateur($asStatutPers[$iIdxStatut][0],"M"))."</options>\n";

unset($asStatutPers,$url_iSelectStatut);

// *************************************
//
// *************************************

$asToolTip = array(
	_("Ajouter une/des personne(s) &agrave; la liste des personnes inscrites"),
	_("Enlever une personne de la liste des personnes inscrites")
);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title>Associer des personnes</title>
<?php inserer_feuille_style("commun/barre_outils.css; admin/personnes.css"); ?>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript('globals.js.php'); ?>"></script>
<script type="text/javascript" language="javascript" src="globals.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript('globals.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript('window.js'); ?>"></script>
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript('outils_admin.js'); ?>"></script>
<script type="text/javascript" language="javascript">
<!--

function association_multiple()
{
	var sUrl = "ass_multiple-index.php"
		+ "?ID_FORM=" + document.forms[0].elements["idform"].value
		+ "&STATUT_PERS=" + document.forms[0].elements["STATUT_PERS"].value;
	var sCaracteristique = centrerFenetre(700,540)
		+ ",resizable=0";
	var oWinAssMult = window.open(sUrl,"WinAssMultiple",sCaracteristique);
	oWinAssMult.focus();
}

function rechargerListeCours()
{
	// Recharger la liste des cours
	top.frames['Principal'].frames['FRM_COURS'].document.forms[0].elements["ENVOYER"].value = "2";
	majListeCours();
}

//-->
</script>
</head>
<body class="associer_personnes">
<form method="get">
<table border="0" cellpadding="4" cellspacing="0" width="100%">
<tr>
<td width="50%" height="1%">
<span class="intitule">Filtre&nbsp;:</span>
<select name="FILTRE" onchange="changerFiltre(value,STATUT_PERS.value,<?php echo $url_iIdForm?>,retIdModule(),FORMATION.checked)">
<?php echo $sOptionsFiltre; ?>
</select>
&nbsp;&nbsp;
<span><input type="checkbox" NAME="FORMATION" onchange="changerFiltre(FILTRE.value,STATUT_PERS.value,<?php echo $url_iIdForm?>,retIdModule(),checked)" onclick="blur()" checked>de cette session uniquement</span>
</td>
<td>&nbsp;</td>
<td class="intitule" height="1%">Statut&nbsp;des&nbsp;personnes&nbsp;:<br>
<select name="STATUT_PERS" onchange="changerStatut(value,<?php echo $url_iIdForm?>)">
<?php echo $sOptionsStatut; ?></select>
<span>&nbsp;</span>
</td>
</tr>

<tr>
<td rowspan="2" class="intitule" valign="top" align="right">
<table cellspacing="1" cellpadding="1" border="0">
<tr>
<td><table cellspacing="0" cellpadding="0" border="0"><tr>
<td style="width:8%">
<a href="javascript: top.frames['Principal'].frames['FRM_PERSONNE'].location.hash = 'top'; void(0);"target="Principal" onfocus="blur()">Haut</a>
</td>
<?php
// Liste alphabÈtique
$sListeAlphabet = NULL;

for ($a = 97; $a <= 122; $a++)
	echo "<td style=\"width:3%;text-align:center\"><a"
		." href=\"javascript: top.frames['Principal'].frames['FRM_PERSONNE'].location.hash = '#lettre_".chr($a)."'; void(0);\""
		." target=\"Principal\""
		." onfocus=\"blur()\""
		.">".chr($a)."</a></td>";
?>
</tr>
</table></td>
</tr>
<tr><td>
<iframe name="FRM_PERSONNE" src="liste_personnes.php?idform=<?php echo $url_iIdForm?>&FORMATION=1" width="99%" height="395" frameborder="0"></iframe>
</td></tr>
<tr>
<td height="1%">
<!-- <span><a href="javascript: void(0);" onclick="oFrmPersonne().location.reload(true)" onfocus="blur()">Rafra√Æchir</a> -->
<span class="intitule">Rechercher&nbsp;:&nbsp;<input type="text" name="nomPersonneRech" onkeyup="rechPersonne(value,self.frames['FRM_PERSONNE'])" value="" size="15">&nbsp;</span>
<?php
// si la formation est archivÈe, on ne peut ajouter de nouveaux utilisateurs.
if ($bPeutInscrire)
{
echo "
<span><a href=\"javascript: void(0);\" onclick=\"profil('?nv=1&titre=".rawurlencode('Nouvel utilisateur')."&formId=".$url_iIdForm."')\" onfocus=\"blur()\">Ajouter</a>&nbsp;|&nbsp;</span>
<span><a href=\"javascript: void(0);\" onclick=\"importer_liste_personnes()\" onfocus=\"blur()\">Importer</a></span>
<span id=\"enlever_personne\">&nbsp;|&nbsp;<a href=\"javascript: void(0);\" onclick=\"enlever_personne(".$url_iIdForm.")\" onfocus=\"blur()\">Enlever</a></span>";
}
else
echo "
<span class=\"typeA\"><del>Ajouter</del>&nbsp;|&nbsp;</span>
<span class=\"typeA\"><del>Importer</del></span>
<span class=\"typeA\">&nbsp;|&nbsp;<del>Enlever</del></span>";
?>
</td>
</tr>
</table>
</td>
<td align="center" width="1%">
<?php
// si la formation est archivÈe, on ne peut mettre de nouvelles personnes dans la formation.
$sBoutonActif = $bPeutInscrire ? "" : "disabled";
echo "
<span title=\"".$asToolTip[0]."\"><input type=\"button\" value=\"&nbsp;&raquo;&nbsp;\" onclick=\"envoyerPersonnes()\" $sBoutonActif></span><br><br><span title=\"".$asToolTip[1]."\"><input type=\"button\" value=\"&nbsp;&laquo;&nbsp;\" onclick=\"enleverPersonneInscrit()\" $sBoutonActif></span>
";

?>
</td>
<td valign="top">
<table border="0" cellspacing="1" cellpadding="1" width="100%">
<tr><td><span class="intitule">&#8250;&nbsp;Liste des personnes inscrites&nbsp;:</span></td></tr>
<tr><td><iframe name="FRM_INSCRIT" src="liste_inscrits.php?idform=<?php echo $url_iIdForm?>" width="100%" height="218" frameborder="0"></iframe></td></tr>
</table>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td class="intitule" valign="top">
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr><td><span class="intitule">&#8250;&nbsp;Liste des cours&nbsp;:</span></td></tr>

<tr><td><iframe name="FRM_COURS" src="" width="100%" height="140" frameborder="0"></iframe></td></tr>
<?php
// si la formation est archivÈe, on ne peut ajouter de nouveaux utilisateurs.
if ($bPeutInscrire)
{
echo "
<tr><td height=\"1%\">
<span><a href=\"javascript: association_multiple(); void(0);\" onfocus=\"blur()\">Associations&nbsp;multiples</a>&nbsp;</span>
<span>&nbsp;</span>
<span><a href=\"javascript: rechargerListeCours(); void(0);\" onfocus=\"blur()\">Appliquer les changements</a>&nbsp;</span>
</td></tr>
";
}
?>
</table>
</td>
</tr>

</table>
<input type="hidden" name="idform" value="<?php echo $url_iIdForm?>">
</form>
</body>
</html>

