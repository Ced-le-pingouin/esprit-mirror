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
** Fichier ................: form_rubr.php
** Description ............: 
** Date de création .......: 01/02/2001
** Dernière modification ..: 21/03/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

// ---------------------
// Initialisations
// ---------------------
$iIdRubrique  = $oProjet->oRubriqueCourante->retId();
$iNumeroOrdre = $oProjet->oRubriqueCourante->retNumOrdre();
$sNomRub      = $oProjet->oRubriqueCourante->retNom(TRUE);
$iType        = $oProjet->oRubriqueCourante->retType();
$sDescrRub    = $oProjet->oRubriqueCourante->retDescr();

// ---------------------
// Permissions
// ---------------------
$bPeutModifier = $oProjet->verifModifierModule();

$g_bModifier  = $oProjet->verifPermission("PERM_MOD_RUBRIQUE");
$g_bModifier &= $bPeutModifier;

$g_bModifierStatut  = $oProjet->verifPermission("PERM_MOD_STATUT_TOUS_COURS");
$g_bModifierStatut |= $oProjet->verifPermission("PERM_MOD_STATUT_RUBRIQUE");
$g_bModifierStatut &= $bPeutModifier;

unset($bPeutModifier);

// ---------------------
// ---------------------
afficherTitre(NULL,$sNomRub);

if ($g_bModifier || $g_bModifierStatut)
	echo "<form name=\"form_admin_modif\""
		." action=\"".$HTTP_SERVER_VARS["PHP_SELF"]."\""
		." method=\"post\""
		." enctype=\"multipart/form-data\""
		.">\n";
else
	echo "<form>\n";

?>

<table border="0" cellspacing="0" cellpadding="5" width="100%">
<?php
selectionnerNumeroOrdre("ordre_rubrique",$oProjet->oRubriqueCourante->retNombreLignes(),$iNumeroOrdre,1);
adminEntrerNom("nom_rubrique",$sNomRub);
selectionnerStatut("statut_rubrique",$oProjet->oRubriqueCourante->retListeStatuts(),$oProjet->oRubriqueCourante->retStatut());
?>

<!-- Type -->
<tr><td>&nbsp;</td><td><hr></td></tr>
<tr>
<td style="vertical-align: top;"><div class="intitule" style="padding-top: 3px;">Type&nbsp;:</div></td>
<td>
<select name="type_rubrique" onchange="javascript: afficherElementType('div_donnee_',this.options[this.selectedIndex].value,'<?=LIEN_GLOSSAIRE?>');" <?php echo ($g_bModifier ? NULL : " disabled"); ?>>
<?php
$asTypesUnite = $oProjet->oRubriqueCourante->retListeTypes();
for ($i=0; $i<count($asTypesUnite); $i++)
	echo "<option"
		." value=\"{$asTypesUnite[$i][0]}\""
		.($asTypesUnite[$i][0] == $iType ? " selected" : NULL)
		.">{$asTypesUnite[$i][1]}</option>";
?>
</select>

<?php

// *************************************
// Afficher les éléments pour le rubrique de type 'Lien'
// *************************************

$sDonnee = explode(":",$oProjet->oRubriqueCourante->retDonnee());

$iNumDiv = 0;

// ---------------------------
// Affichage d'une page HTML (du serveur)
// ---------------------------
$sStyle = "position: relative;"
	." visibility: ".($iType == LIEN_PAGE_HTML ? "visible; display: block;" : "hidden; display: none;");

$sFichierDeposer = strlen($sDonnee[0])
	? "&nbsp;{$sDonnee[0]}"
	: "Pas de fichier actuellement";

echo "<!-- Donnée -->\n"
	."<div id=\"div_donnee_".LIEN_PAGE_HTML."\" style=\"{$sStyle}\">\n"
	."<fieldset>"
	."<legend>&nbsp;Affichage d'une page HTML&nbsp;</legend>"
	."<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\">\n"
	."<tr>"
	."<td><div class=\"intitule\">Donnée&nbsp;:</div></td>\n"
	."<td>"
	.($g_bModifier ? "<input type=\"file\" name=\"fichier_rubrique[]\" size=\"38\">" : $sFichierDeposer)
	."</td>"
	."</tr>"
	."<tr>"
	."<td>&nbsp;</td>"
	."<td>".($g_bModifier ? "Fichier actuel&nbsp;:{$sFichierDeposer}" : "&nbsp;")."</td>"
	."</tr>"
	."</table>"
	."</fieldset>"
	."</div>\n";

// ---------------------------
// Lien vers un site Internet
// ---------------------------
$sStyle = "position: relative;"
	." visibility: ".($iType == LIEN_SITE_INTERNET ? "visible; display: block;" : "hidden; display: none;");

echo "<div id=\"div_donnee_".LIEN_SITE_INTERNET."\" style=\"{$sStyle}\">\n"
	."<fieldset>"
	."<legend>&nbsp;Lien vers un site Internet&nbsp;</legend>"
	."<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\">"
	."<tr>"
	."<td><div class=\"intitule\">http://</div></td>"
	."<td>"
	."<input type=\"text\" name=\"LIEN_SITE_INTERNET\" size=\"38\" value=\"".rawurldecode($sDonnee[0])."\"".($g_bModifier ? NULL : " disabled=\"disabled\"").">"
	."</td>\n</tr>"
	."</table>"
	."</fieldset>"
	."</div>\n";

// ---------------------------
// Intitule de la rubrique
// ---------------------------
$sTplOptions = NULL;

$sStyle = "position: relative;"
	." visibility: "
	.($iType == LIEN_UNITE
		? "visible; display: block;"
		: "hidden; display: none;");

$sFormSelect = "<div id=\"div_donnee_".LIEN_UNITE."\" style=\"{$sStyle}\">"
	."<fieldset>"
	."<legend>&nbsp;".INTITULE_RUBRIQUE."&nbsp;</legend>"
	."<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\">"
	."<tr>"
	."<td><div class=\"intitule\">Intitul&eacute;&nbsp;:</div></td>"
	."<td>"
	."<select name=\"intitule_rubrique\" size=\"1\"".($g_bModifier ? NULL : " disabled").">"
	."<option value=\"0\">Pas d'intitul&eacute;</option>"
	."[TPL_OPTIONS]"
	."</select>&nbsp;"
	."<input name=\"numdepart_rubrique\" size=\"3\" maxlength=\"3\" value=\"".$oProjet->oRubriqueCourante->retNumDepart()."\"".($g_bModifier ? NULL : " disabled").">";

if ($g_bModifier)
	$sFormSelect .= "&nbsp;[&nbsp;<a href=\"javascript: ouvrir_dico_intitules('".TYPE_RUBRIQUE."'); void(0);\""
	.">Ajouter</a>&nbsp;]";

$sFormSelect .= "</td>"
	."</tr></table>"
	."</fieldset>"
	."</div>\n";

// ---------------------
// Rechercher la liste des intitulés des rubriques
// ---------------------
$iIdIntituleActuel = $oProjet->oRubriqueCourante->retIdIntitule();

$oIntitule = new CIntitule($oProjet->oBdd);

$iNbrIntitules = $oIntitule->initIntitules(TYPE_RUBRIQUE);

for ($i=0; $i<$iNbrIntitules; $i++)
{
	$iIdIntitule = $oIntitule->aoIntitules[$i]->retId();
	$sNomIntitule = $oIntitule->aoIntitules[$i]->retNom(FALSE);
	
	$sTplOptions .= "<option"
		." value=\"{$sNomIntitule}\""
		.($iIdIntitule == $iIdIntituleActuel ? " selected" : NULL)
		.">".(empty($sNomIntitule) ? "Intitule non trouv&eacute;" : $sNomIntitule)
		."&nbsp;&nbsp;"
		."</option>";
}

echo str_replace("[TPL_OPTIONS]",$sTplOptions,$sFormSelect);

// ---------------------------
// Document à télécharger
// ---------------------------
$sStyle = "position: relative;"
	." visibility: "
	.($iType == LIEN_DOCUMENT_TELECHARGER
		? "visible; display: block;" 
		: "hidden; display: none;");

$sFichierDeposer = strlen($sDonnee[0])
	? "&nbsp;{$sDonnee[0]}"
	: "Pas de fichier actuellement";

echo "<div id=\"div_donnee_".LIEN_DOCUMENT_TELECHARGER."\" style=\"{$sStyle}\">\n"
	."<fieldset>"
	."<legend>&nbsp;Document à télécharger&nbsp;</legend>"
	."<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\">\n"
	."<tr>\n"
	."<td><div class=\"intitule\">Donn&eacute;e&nbsp;:</div></td>\n"
	."<td>"
	.($g_bModifier ? "<input type=\"file\" name=\"fichier_rubrique[]\" size=\"38\">" : $sFichierDeposer)
	."</td>"
	."</tr>"
	."<tr>"
	."<td>&nbsp;</td>"
	."<td>".($g_bModifier ? "Fichier actuel&nbsp;:{$sFichierDeposer}" : "&nbsp;")."</td>"
	."</tr>"
	."</table>\n"
	."</div>\n";

// ---------------------------
// Forum
// ---------------------------
$sStyle = "position: relative;"
	." visibility: ".($iType == LIEN_FORUM 
		? "visible; display: block;" 
		: "hidden; display: none;");

$oForum = new CForum($oProjet->oBdd);
$oForum->initForumParType(TYPE_RUBRIQUE,$g_iRubrique);
$aaModalitesForum = $oForum->retListeModalites();

echo "<!-- Forum -->"
	."<div id=\"div_donnee_".LIEN_FORUM."\" style=\"{$sStyle}\">\n"
	."<br>"
	."<fieldset>"
	."<legend>"
	."&nbsp;Forum&nbsp;"
	."&nbsp;[&nbsp;<a href=\"javascript: void(0);\" onclick=\"return forum('?idNiveau={$iIdRubrique}&typeNiveau=".TYPE_RUBRIQUE."','WinForumRub{$iIdRubrique}')\" onfocus=\"blur()\">Modifier</a>&nbsp;]&nbsp;"
	."</legend>"
	."<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\">\n"
	."<tr>\n"
	."<td><div class=\"intitule\">Modalit&eacute;&nbsp;:</div></td>\n"
	."<td width=\"99%\">"
	.adminRetListeModalites("modalite_forum",$aaModalitesForum)
	."</td>\n"
	."</tr>\n"
	."<tr>"
	."<td colspan=\"2\" align=\"right\">"
	."<input type=\"checkbox\" name=\"accessible_visiteurs_forum\"".($oForum->retAccessibleVisiteurs() ? " checked" : NULL)
	.($g_bModifier ? NULL : " disabled")
	.">"
	."&nbsp;&nbsp;"
	.htmlentities("Accessible aux visiteurs",ENT_COMPAT,"UTF-8")
	."</td>"
	."</tr>\n"
	."</table>\n"
	."</fieldset>"
	."</div>\n";

// ---------------------
// Chat
// ---------------------
$oTpl = new Template("div_chats.tpl");

$oSet_LienChatActif = $oTpl->defVariable("SET_LIEN_CHAT_ACTIF");
$oSet_LienChatPassif = $oTpl->defVariable("SET_LIEN_CHAT_PASSIF");

$oTpl->remplacer("{div->id}","div_donnee_".LIEN_CHAT);

$oTpl->remplacer("{chat->url}",($g_bModifier ? $oSet_LienChatActif : $oSet_LienChatPassif));

$oTpl->remplacer("{sousactiv->id}",$g_iRubrique);
$oTpl->remplacer("{sousactiv->type}",TYPE_RUBRIQUE);

$oBloc_Chat = new TPL_Block("BLOCK_CHAT",$oTpl);

// Composer la liste des chats
if (($oProjet->oRubriqueCourante->initChats()) > 0)
{
	$oBloc_Chat->beginLoop();
	
	foreach ($oProjet->oRubriqueCourante->aoChats as $oChat)
	{
		$oBloc_Chat->nextLoop();
		$oBloc_Chat->remplacer("{chat->nom}",$oChat->retNom());
		$oBloc_Chat->remplacer("{chat->couleur->valeur}","rgb(".$oChat->retValeurCouleur().")");
		$oBloc_Chat->remplacer("{chat->couleur->nom}",$oChat->retNomCouleur());
	}
}
else
{
	$oBloc_Chat->remplacer("{chat->nom}",CHAT_NOM_DEFAUT);
	$oBloc_Chat->remplacer("{chat->couleur->nom}",CHAT_NOM_COULEUR_DEFAUT);
	$oBloc_Chat->remplacer("{chat->couleur->valeur}","rgb(".CHAT_RVB_COULEUR_DEFAUT.")");
}

$oBloc_Chat->afficher();

$oTpl->afficher();

unset($oTpl,$oBloc_Chat,$oSet_LienChatActif,$oSet_LienChatPassif);

?>

<!-- Début Lien vers un texte formaté -->
<!-- Identifiant du type : <?=LIEN_TEXTE_FORMATTE?> -->
<div id="div_donnee_<?=LIEN_TEXTE_FORMATTE?>" class="Cacher">
<fieldset>
<legend>&nbsp;Texte format&eacute;&nbsp;</legend>
<table border="0" cellspacing="2" cellpadding="0" width="100%">
<tr><td colspan="2">&nbsp;</td></tr>
<?=entrerDescription("DESCRIPTION[".LIEN_TEXTE_FORMATTE."]",$sDescrRub,"Texte",urlencode(addslashes($sNomRub)))?>
</table>
</fieldset>
</div>
<!-- Fin Lien vers un texte formaté -->

</td>
</tr>
<tr><td><img src="<?=dir_theme('espacer.gif')?>" width="120" height="1" border="0"></td><td width="99%">&nbsp;</td></tr>
</table>
<script type="text/javascript" language="javascript">
<!--
var g_sNomHtmlSelectIntitules = "intitule_rubrique";
<?php
switch ($iType)
{
	case LIEN_CHAT:
	case LIEN_TEXTE_FORMATTE:
		echo "document.getElementById('div_donnee_{$iType}').className = 'Afficher';\n";
	break;
}
?>
//-->
</script>