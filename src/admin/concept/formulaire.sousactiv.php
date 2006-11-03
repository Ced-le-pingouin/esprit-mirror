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
** Fichier ................: formulaire.sousactiv.php
** Description ............: 
** Date de création .......: 01/03/2002
** Dernière modification ..: 25/11/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**                           Cédric FLOQUET <cedric.floquet@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("formulaire.sousactiv.inc.php");
require_once(dir_database("formulaire.tbl.php"));

$url_iMode = (empty($_POST["mode"]) ? NULL : $_POST["mode"]);

// ---------------------
// Initialisations
// ---------------------
$ordre                = $oProjet->oSousActivCourante->retNumOrdre();
$sNomSousActiv        = $oProjet->oSousActivCourante->retNom(TRUE);
$descr                = $oProjet->oSousActivCourante->retDescr();
$dateDeb              = $oProjet->oSousActivCourante->retDateDeb();
$dateFin              = $oProjet->oSousActivCourante->retDateFin();
$aoModalitesAffichage = $oProjet->oSousActivCourante->retListeModes();
$sInfoBulle           = $oProjet->oSousActivCourante->retInfoBulle();
list($sDonnee,$url_iMode,$sIntitule) = explode(";",$oProjet->oSousActivCourante->retDonnees());

$amSousActiv = array();
$amSousActiv["type"]               = $oProjet->oSousActivCourante->retType();
$amSousActiv["modalite"]           = $oProjet->oSousActivCourante->retModalite();
$amSousActiv["modalite_affichage"] = $url_iMode;

// ---------------------
// Permissions
// ---------------------
$bPeutModifier = $oProjet->verifModifierModule();

$g_bModifier  = $oProjet->verifPermission("PERM_MOD_ELEMENT_ACTIF");
$g_bModifier &= $bPeutModifier;

$g_bModifierStatut  = $oProjet->verifPermission("PERM_MOD_STATUT_TOUS_COURS");
$g_bModifierStatut |= $oProjet->verifPermission("PERM_MOD_STATUT_ELEMENT_ACTIF");
$g_bModifierStatut &= $bPeutModifier;

unset($bPeutModifier);

// ---------------------
// ---------------------
$g_sRepTheme = dir_theme();

$sIntitule = mb_convert_encoding($sIntitule,"HTML-ENTITIES","UTF-8");

// Mettre dans un tableau la liste des fichiers qui se
// trouvent dans la racine du bloc d'activité
$asFichiers = array();

$sDestination = dir_cours($g_iActiv,$g_iFormation);

if (isset($sDestination) && is_dir($sDestination))
{
	$hf = @opendir($sDestination);
	while ($file = @readdir($hf))
		if ($file != "." && $file != ".." && is_file($sDestination.$file) && !eregi("\.php",$file))
				$asFichiers[] = $file;
	@closedir($hf);
	unset($hf);
	clearstatcache();
	// Trier le tableau
	sort($asFichiers);
}

afficherTitre(INTITULE_SOUS_ACTIV,$sNomSousActiv);

if ($g_bModifier || $g_bModifierStatut)
	echo "<form name=\"form_admin_modif\""
		." action=\"".$_SERVER["PHP_SELF"]."\""
		." method=\"post\""
		." enctype=\"multipart/form-data\""
		.">\n";
else
	echo "<form>\n";

?>

<table border="0" cellspacing="0" cellpadding="5" width="100%">

<?php

selectionnerNumeroOrdre("ORDRE",$oProjet->oSousActivCourante->retNombreLignes(),$ordre);

entrerNom("NOM",$sNomSousActiv,TRUE);
selectionnerStatut("STATUT",$oProjet->oSousActivCourante->retListeStatuts(),$oProjet->oSousActivCourante->retStatut());
?>
<tr><td>&nbsp;</td><td><input type="checkbox" name="PREMIERE_PAGE" onfocus="blur()"<?php echo (($oProjet->oSousActivCourante->retPremierePage()) ? " checked": NULL).($g_bModifier ? NULL : " disabled"); ?>>&nbsp;&nbsp;Premi&egrave;re&nbsp;page&nbsp;<img src="<?php echo $g_sRepTheme?>icones/etoile.gif" width="13" height="13" border="0"></td>
<!-- Type -->
<tr><td>&nbsp;</td><td><hr></td></tr>
<tr>
<td class="intitule">Type&nbsp;:</td>
<td>
<select name="TYPE" onchange="choisirType(aoType,this.value)"<?php echo ($g_bModifier ? NULL : " disabled"); ?>>
<?php

$iTmpType = $oProjet->oSousActivCourante->retType();

$aoTypes = $oProjet->oSousActivCourante->retListeTypes($g_iRubrique);

for ($i=0; $i<count($aoTypes); $i++)
	echo "\t<option value=\"".$aoTypes[$i][0]."\""
		.($iTmpType == $aoTypes[$i][0] ? " selected": NULL)
		.">".$aoTypes[$i][1]."</option>\n";

$iTmpType = NULL;

?>

</select>
</td>
</tr>

<tr>
<td>&nbsp;</td>
<td>

<!--[[ Affichage d'une page HTML (<?php echo LIEN_PAGE_HTML?>) -->
<div id="lien_page_html" class="Cacher">
<fieldset>
<legend>&nbsp;Affichage d'un document (html, doc, swf, gif, jpeg, pdf)&nbsp;</legend>
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
<td width="200"><div class="intitule">Choisir l'index&nbsp;:</div></td>
<td>
<select name="DONNEES[<?php echo LIEN_PAGE_HTML; ?>]"<?php echo($g_bModifier ? NULL : " disabled"); ?>>
<option value="">Pas de fichier actuellement</option>
<?php
for ($i=0; $i<count($asFichiers); $i++)
{
	echo "<option value=\"".$asFichiers[$i]."\""
		.(($asFichiers[$i] == $sDonnee) ? " style=\"background-color: #FFFFCC;\" selected" : NULL)
		.">".stripslashes($asFichiers[$i])."</option>\n";
}
?>
</select>&nbsp;<?php echo boutonDeposer()?>
</td>
</tr>
<?php selectionnerModalAff($aoModalitesAffichage,$url_iMode,"MODALITE_AFFICHAGE[".LIEN_PAGE_HTML."]","div_description"); ?>
</table>
</fieldset>
</div>
<!-- Affichage d'une page HTML ]]-->

<!--[[ Document à télécharger (<?php echo LIEN_DOCUMENT_TELECHARGER?>) -->
<div id="lien_document_telecharger" class="Cacher">
<fieldset>
<legend>&nbsp;Document à télécharger&nbsp;</legend>
<table border="0" cellspacing="0" cellpadding="2">
<tr>
<td width="200"><div class="intitule">Choisir le document&nbsp;:</div></td>
<td>
<select name="DONNEES[<?php echo LIEN_DOCUMENT_TELECHARGER; ?>]"<?php echo ($g_bModifier ? NULL : " disabled")?>>
<option value="">Pas de fichier actuellement</option>
<?php
for ($i=0; $i<count($asFichiers); $i++)
	echo "<option"
		." value=\"".$asFichiers[$i]."\"".($asFichiers[$i] == $sDonnee ? " style=\"background-color: #FFFFCC;\" selected" : NULL)
		.">$asFichiers[$i]</option>\n";
?>
</select>
<?php echo boutonDeposer()?>
</td>
</tr>
<!-- Modalité d'affichage -->
<tr>
<td><div class="intitule">Modalit&eacute; d'affichage&nbsp;:</div></td>
<td>
<select name="MODALITE_AFFICHAGE[<?php echo LIEN_DOCUMENT_TELECHARGER; ?>]"  onchange="javascript: MontrerCacher('div_description',this.selectedIndex);" <?php echo ($g_bModifier ? NULL : " disabled"); ?>>
<option value="<?php echo FRAME_CENTRALE_DIRECT; ?>">Directe (téléchargement en 1 temps)</option>
<option value="<?php echo FRAME_CENTRALE_INDIRECT; ?>"<?php echo ($url_iMode == FRAME_CENTRALE_INDIRECT ? " selected" : NULL); ?>>Indirecte (téléchargement en 2 temps)</option>
</select>
</td>
</tr>
</table>
</fieldset>
</div>
<!-- Document à télécharger ]]-->

<!--[[ Site Internet (<?php echo LIEN_SITE_INTERNET?>) -->
<div id="lien_site_internet" class="Cacher">
<fieldset>
<legend>&nbsp;Lien vers un site Internet&nbsp;</legend>
<table border="0" cellspacing="2" cellpadding="0" width="100%">
<tr>
<td width="200"><div class="intitule">http://</div></td>
<td><input type="text" name="DONNEES[<?php echo LIEN_SITE_INTERNET; ?>]" value="<?php echo $sDonnee; ?>" size="50"<?php echo ($g_bModifier ? NULL : " disabled")?>></td>
</tr>
<?php selectionnerModalAff($aoModalitesAffichage,$url_iMode,"MODALITE_AFFICHAGE[".LIEN_SITE_INTERNET."]","div_description"); ?>
</table>
</fieldset>
</div>
<!-- Site Internet ]]-->

<!--[[ Texte formaté (<?php echo LIEN_TEXTE_FORMATTE?>) -->
<?php $iTypeLien = LIEN_TEXTE_FORMATTE; ?>
<div id="lien_texte_formate" class="Cacher">
<fieldset>
<legend>&nbsp;Texte format&eacute;&nbsp;</legend>
<table border="0" cellspacing="2" cellpadding="0" width="100%">
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td><div class="intitule">Modalit&eacute; d'affichage&nbsp;:&nbsp;</div></td>
<td>
<select name="MODALITE_AFFICHAGE[<?php echo $iTypeLien?>]"<?php echo $g_bModifier ? NULL : " disabled"?>>
<option value="<?php echo FRAME_CENTRALE_DIRECT?>"<?php echo ($url_iMode == FRAME_CENTRALE_DIRECT ? " selected" : NULL)?>>Zone principale</option>
<option value="<?php echo NOUVELLE_FENETRE_DIRECT?>"<?php echo ($url_iMode == NOUVELLE_FENETRE_DIRECT ? " selected" : NULL)?>>Nouvelle fen&ecirc;tre</option>
</select>
</td>
</tr>
<?php echo entrerDescription("DESCRIPTION[{$iTypeLien}]",$descr,"Texte",urlencode(addslashes($sNomSousActiv)))?>
</table>
</fieldset>
</div>
<!-- Texte formaté ]]-->

<!--[[ Collecticiel (<?php echo LIEN_COLLECTICIEL?>) -->
<div id="lien_collecticiel" class="Cacher">
<fieldset>
<legend>&nbsp;Collecticiel&nbsp;</legend>
<table border="0" cellspacing="4" cellpadding="0">
<tr>
<td><div class="intitule">Modalit&eacute;&nbsp;:&nbsp;</div></td>
<td><?php echo selectionner_modalite($oProjet->oSousActivCourante->retListeModalites(),"MODALITE[".LIEN_COLLECTICIEL."]",$oProjet->oSousActivCourante->retModalite()); ?></td>
</tr>
<tr>
<td><div class="intitule">Fichier de base&nbsp;:&nbsp;</div></td>
<td>
<select name="DONNEES[<?php echo LIEN_COLLECTICIEL; ?>]" <?php echo ($g_bModifier ? NULL : " disabled"); ?>>
<option value="">Pas de fichier actuellement</option>
<?php
for ($i=0; $i<count($asFichiers); $i++)
	echo "<option value=\"".$asFichiers[$i]."\""
		.(($asFichiers[$i] == $sDonnee) ? " style=\"background-color: #FFFFCC;\" selected" : NULL)
		.">$asFichiers[$i]</option>\n";

if (empty($sIntitule))
	$sIntituleCollecticiel = mb_convert_encoding("Fichier de base à télécharger","HTML-ENTITIES","UTF-8");
else
	$sIntituleCollecticiel = $sIntitule;
?>
</select>
<?php echo boutonDeposer()?>
</td>
</tr>
<tr>
<td><div class="intitule">Intitulé&nbsp;du&nbsp;lien&nbsp;:&nbsp;</div></td>
<td><input type="text" size="50" name="INTITULE[<?php echo LIEN_COLLECTICIEL; ?>]" value="<?php echo $sIntituleCollecticiel; ?>"<?php echo ($g_bModifier ? NULL : " disabled"); ?>></td>
</tr>
<?php entrerDescription(("DESCRIPTION[".LIEN_COLLECTICIEL."]"),$descr,"Consignes",urlencode(addslashes($sNomSousActiv))); ?>
</table>
</fieldset>
</div>
<!-- Collecticiel ]]-->

<!--[[ Chat (<?php echo LIEN_CHAT?>) -->
<?php
$oTpl = new Template("div_chats.tpl");

$oSet_LienChatActif = $oTpl->defVariable("SET_LIEN_CHAT_ACTIF");
$oSet_LienChatPassif = $oTpl->defVariable("SET_LIEN_CHAT_PASSIF");
$oTpl->remplacer("{div->id}","lien_chat");
$oTpl->remplacer("{chat->url}",($g_bModifier ? $oSet_LienChatActif : $oSet_LienChatPassif));

$oTpl->remplacer("{sousactiv->id}",$g_iSousActiv);
$oTpl->remplacer("{sousactiv->type}",TYPE_SOUS_ACTIVITE);

$oBloc_Chat = new TPL_Block("BLOCK_CHAT",$oTpl);

// Composer la liste des chats
if (($oProjet->oSousActivCourante->initChats()) > 0)
{
	$oBloc_Chat->beginLoop();
	
	foreach ($oProjet->oSousActivCourante->aoChats as $oChat)
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
<!-- Chat ]]-->

<!--[[ Formulaire (<?php echo LIEN_FORMULAIRE?>) -->
<div id="lien_formulaire" class="Cacher">
<fieldset>
<legend>&nbsp;Activités en ligne&nbsp;</legend>
<table border="0" cellspacing="4" cellpadding="0">
<tr>
<td><div class="intitule">Modalit&eacute;&nbsp;:&nbsp;</div></td>
<td><?php echo selectionner_modalite($oProjet->oSousActivCourante->retListeModalites(),"MODALITE[".LIEN_FORMULAIRE."]",$oProjet->oSousActivCourante->retModalite()); ?></td>
</tr>
<tr>
<td><div class="intitule">D&eacute;roulement&nbsp;:&nbsp;</div></td>
<td><?php echo selectionner_modalite($oProjet->oSousActivCourante->retListeDeroulements(),"DEROULEMENT[".LIEN_FORMULAIRE."]",$url_iMode); ?></td>
</tr>
<tr>
<td><div class="intitule">Questionnaire de base&nbsp;:&nbsp;</div></td>
<td>
<select name="DONNEES[<?php echo LIEN_FORMULAIRE; ?>]" <?php echo ($g_bModifier ? NULL : " disabled"); ?>>
<option value="">Pas de questionnaire actuellement</option>
<?php
// on peut inclure aux activités ses propres formulaires, ou des formulaires publics, A CONDITION que leur statut soit TERMINE
$oFormulaire = new CFormulaire($oProjet->oBdd);
// retourne les formulaires de l'utilisateur, et ceux dont le statut est 'public'
$aoFormulairesVisibles = $oFormulaire->retListeFormulairesVisibles($g_iIdUtilisateur, 'public', NULL); // , 1);
foreach($aoFormulairesVisibles as $oFormulaireCourant)
	echo "<option value=\"".$oFormulaireCourant->retId()."\""
	  .(($oFormulaireCourant->retId() == $sDonnee) ? " style=\"background-color: #FFFFCC;\" selected" : NULL)
	  .">".convertBaliseMetaVersHtml($oFormulaireCourant->retTitre())."</option>\n";

if (empty($sIntitule))
	$sIntituleFormulaire = mb_convert_encoding("Questionnaire de base à compléter","HTML-ENTITIES","UTF-8");
else
	$sIntituleFormulaire = $sIntitule;
?>
</select>
</td>
</tr>
<tr>
<td><div class="intitule">Intitulé&nbsp;du&nbsp;lien&nbsp;:&nbsp;</div></td>
<td><input type="text" size="50" name="INTITULE[<?php echo LIEN_FORMULAIRE; ?>]" value="<?php echo $sIntituleFormulaire; ?>" <?php echo ($g_bModifier ? NULL : " disabled"); ?>></td>
</tr>
<?php entrerDescription(("DESCRIPTION[".LIEN_FORMULAIRE."]"),$descr,"Consignes"); ?>
</table>
</fieldset>
</div>
<!-- Formulaire ]]-->

<?php 

$sClassDescription = "Cacher";

switch ($oProjet->oSousActivCourante->retType())
{
	case LIEN_PAGE_HTML:
	case LIEN_DOCUMENT_TELECHARGER:
	case LIEN_SITE_INTERNET:
		if ($url_iMode == FRAME_CENTRALE_INDIRECT ||
			$url_iMode == NOUVELLE_FENETRE_INDIRECT)
			$sClassDescription = "Afficher";
		break;
}

?>

<!-- :DEBUT: Description des intitulés -->
<div id="div_description" class="<?php echo $sClassDescription; ?>">
<br>
<fieldset>
<legend>&nbsp;Modalité d'affichage indirect&nbsp;</legend>
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<tr>
<td width="200"><div class="intitule">Intitulé&nbsp;du&nbsp;lien&nbsp;:&nbsp;</div></td>
<td><input type="text" size="55" name="INTITULE[0]" value="<?php echo $sIntitule; ?>"<?php echo ($g_bModifier ? NULL : " disabled"); ?>></td>
</tr>
<?php entrerDescription("DESCRIPTION[0]",$descr,"Description&nbsp;de&nbsp;l'intitulé",urlencode(addslashes($sNomSousActiv))); ?>
</table>
</fieldset>
</div>
<!-- :FIN: Description des intitulés -->
<?php echo div_forum(); echo div_galerie(); echo div_glossaire(); // {{{ Tableau de bord
$sTableauDeBord = file_get_contents("tableau_de_bord.inc.htm");
echo str_replace(
	array("{sousactiv.type}"
		, "{modalite.individuel.value}"
		, "{modalite.individuel.selected}"
		, "{modalite.par_equipe.value}"
		, "{modalite.par_equipe.selected}"
		, "{modalite_affichage.frame_centrale_direct.value}"
		, "{modalite_affichage.frame_centrale_direct.selected}"
		, "{modalite_affichage.nouvelle_fenetre_direct.value}"
		, "{modalite_affichage.nouvelle_fenetre_direct.selected}"
		, "{description.disabled}"
		, "{description.texte}")
	, array(LIEN_TABLEAU_DE_BORD
		, MODALITE_INDIVIDUEL
		, ($amSousActiv["modalite"] == MODALITE_INDIVIDUEL ? " selected=\"selected\"" : NULL)
		, MODALITE_PAR_EQUIPE
		, ($amSousActiv["modalite"] == MODALITE_PAR_EQUIPE ? " selected=\"selected\"" : NULL)
		, FRAME_CENTRALE_DIRECT
		, ($amSousActiv["modalite_affichage"] == FRAME_CENTRALE_DIRECT ? " selected=\"selected\"" : NULL)
		, NOUVELLE_FENETRE_DIRECT
		, ($amSousActiv["modalite_affichage"] == NOUVELLE_FENETRE_DIRECT ? " selected=\"selected\"" : NULL)
		, ($amSousActiv["modalite_affichage"] != FRAME_CENTRALE_DIRECT ? " disabled=\"disabled\"" : NULL)
		, $descr)
	,$sTableauDeBord);
// }}}
?>
<p style="text-align: right; padding-right: 10px;"><a href="sousactiv_inv-index.php?idSousActiv=<?php echo $g_iSousActiv?>" onclick="return autorisation_action(this)" target="_blank">Acc&egrave;s</a></p>
</td>
</tr>
</table>
<br>
<script type="text/javascript" language="javascript">
<!--

var aoType = new Array();

if (document.getElementById)
{
	// aoType[num]: num = Select.value - 1
	aoType[0]  = document.getElementById("lien_page_html");
	aoType[1]  = document.getElementById("lien_document_telecharger");
	aoType[2]  = document.getElementById("lien_site_internet");
	aoType[3]  = document.getElementById("lien_chat");
	aoType[4]  = document.getElementById("lien_forum");
	aoType[5]  = document.getElementById("lien_galerie");
	aoType[6]  = document.getElementById("lien_collecticiel");
	aoType[8]  = document.getElementById("lien_formulaire");
	aoType[9]  = document.getElementById("lien_texte_formate");
	aoType[10] = document.getElementById("lien_glossaire");
	aoType[11] = document.getElementById("lien_tableau_de_bord");
}

choisirType(aoType,'<?php echo $amSousActiv["type"]?>');

//-->
</script>
