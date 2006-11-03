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
** Fichier ................: personne.php
** Description ............: 
** Date de création .......: 12/08/2002
** Dernière modification ..: 04/04/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** Unité de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");

$oProjet = new CProjet();

// ---------------------
// Récupérer les variables de l'url
// ---------------------
$url_bSauver = (empty($_POST["SAUVER"]) ? FALSE : TRUE);

// ---------------------
// Déclaration des fonctions locales
// ---------------------
function formatTexteErreur ($v_sTexteErreur)
{
	return ""
		."&nbsp;<span"
		." class=\"erreur\""
		." style=\"cursor: help;\""
		." title=\"".mb_convert_encoding($v_sTexteErreur,"HTML-ENTITIES","UTF-8")."\""
		." onmouseover=\"afficher_erreur('".rawurlencode("&#8212;&nbsp;".$v_sTexteErreur."&nbsp;&#8212;")."')\""
		." onmouseout=\"afficher_erreur()\""
		."\">Erreur</span>";
}

// ---------------------
// Initialisations
// ---------------------
$iIdPers = 0;

if (isset($_GET["nv"]))
{
	$bModifierCookie = FALSE;
}
else if (isset($_GET["idPers"]))
{
	$iIdPers = $_GET["idPers"];
}
else if (isset($_POST["ID_PERS"]))
{
	if ($_POST["ID_PERS"] == 0)
		$url_bSauver = TRUE;

	$iIdPers = $_POST["ID_PERS"];
}
else
	$iIdPers = $oProjet->oUtilisateur->retId();

$bModifierCookie = ($iIdPers == $oProjet->oUtilisateur->retId());

$asErreurs = array();

$oPersonne = new CPersonne($oProjet->oBdd,$iIdPers);

if ($iIdPers >= 0)
{
	if ($url_bSauver)
	{
		// Nom
		$oPersonne->defNom($_POST["NOM_PERS"]);
		
		$sTmp = $oPersonne->retNom();
		
		if (empty($sTmp))
			$asErreurs["nom"] = formatTexteErreur("Le nom ne peut pas être vide");
		
		// Prénom
		$oPersonne->defPrenom($_POST["PRENOM_PERS"]);
		
		$sTmp = $oPersonne->retPrenom();
		
		if (empty($sTmp))
			$asErreurs["prenom"] = formatTexteErreur("Le prénom ne peut pas être vide");
		
		// Vérifier que le nom + prénom est unique dans la table
		if (!$oPersonne->estUnique())
		{
			echo "<html>\n"
				."<head>"
			        ."<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n"
				.inserer_feuille_style("profil.css")
				."<script type=\"text/javascript\" language=\"javascript\"><!--\n"
				."function corriger() { top.frames[\"Bas\"].location = \"personne-bas.php?corriger=1\"; }\n"
				."</script>\n"
				."</head>\n"
				."<body onload=\"corriger()\">\n"
				."<br><br>"
				."<p align=\"center\">"
					."<b>"
					.mb_convert_encoding("Inscription impossible: une personne portant ces nom et prénom est déjà inscrite.","HTML-ENTITIES","UTF-8")
					."</b>"
				."</p>"
				."<body>\n"
				."</html>\n";
			
			exit();
		}
		
		// Pseudo
		$oPersonne->defPseudo($_POST["PSEUDO_PERS"]);
		
		$sTmp = $oPersonne->retPseudo();
		
		if (empty($sTmp))
			$asErreurs["pseudo"] = formatTexteErreur("Le pseudo ne peut pas être vide");
		/*else if (ereg("[^a-zA-Z0-9$]",$sTmp))
			$asErreurs["pseudo"] = formatTexteErreur("Les caractères spéciaux ne sont pas acceptés (éêèà...)");*/
		else if (!$oPersonne->estPseudoUnique())
			$asErreurs["pseudo"] = formatTexteErreur("Ce pseudo a déjà été utilisé");
		
		// Date de naissance (format: AAAA-MM-JJ)
		$sDateNaiss = (empty($_POST["DATE_NAISS_ANNEE_PERS"]) ? "0000" : $_POST["DATE_NAISS_ANNEE_PERS"])
			."-"
			.$_POST["DATE_NAISS_MOIS_PERS"]
			."-"
			.$_POST["DATE_NAISS_JOUR_PERS"];
			
		$oPersonne->defDateNaiss($sDateNaiss);
		
		// Sexe
		if (isset($_POST["SEXE_PERS"]))
			$oPersonne->defSexe($_POST["SEXE_PERS"]);
		
		// Adresse
		$oPersonne->defAdresse($_POST["ADRESSE_PERS"]);
		
		// Numéro de téléphone
		$oPersonne->defNumTel($_POST["TELEPHONE_PERS"]);
		
		// Email
		if (!empty($_POST["EMAIL_PERS"]) && !emailValide($_POST["EMAIL_PERS"]))
			$asErreurs["email"] = formatTexteErreur("Cette adresse électronique n'est pas valable");
		
		$oPersonne->defEmail($_POST["EMAIL_PERS"]);
		
		// Mot de passe
		$sMdp = trim($_POST["MDP_PERS"]);
		
		// Mot de passe de confirmation
		$sMdpConfirm = trim($_POST["MDP_CONFIRM_PERS"]);
		
		// Vérification des mots de passe
		if ($iIdPers < 1 && empty($sMdp))
			// Un nouvel utilisateur doit absolument entrer un mot de passe
			$asErreurs["mdp"] = formatTexteErreur("Mot de passe vide");
		else if (!empty($sMdp) || !empty($sMdpConfirm))
		{
			if (ereg("[^a-zA-Z0-9$]",$sMdp))
				$asErreurs["mdp"] = formatTexteErreur("Les caractères spéciaux ne sont pas acceptés (éêèà...)");
			else if ($sMdp !== $sMdpConfirm)
				$asErreurs["mdp"] = formatTexteErreur("Mot de passe non identique");
			else
				$oPersonne->defMdp($oProjet->retMdpCrypte($sMdp));
		}
		
		if (count($asErreurs) < 1)
		{
			// Il n'y a pas d'erreur on peut sauvegarder
			$oPersonne->enregistrer();
			
			if ($bModifierCookie)
			{
				$oProjet->modifierInfosSession(SESSION_PSEUDO,$oPersonne->retPseudo(),TRUE);
				$oProjet->modifierInfosSession(SESSION_MDP,$oPersonne->retMdp(),TRUE);
			}
			
			echo "<html><body>"
				."<script type=\"text/javascript\" language=\"javascript\">"
				."<!--\n top.close(); \n//--></script>"
				."</body></html>";
			
			exit();
		}
	}
}

// ---------------------
// Date de naissance
// ---------------------

$asDateNaiss = $oPersonne->retTableauDateNaiss();

// Jour
$sOptionsDateNaissJour = NULL;

for ($i=0; $i<=31; $i++)
{
	$sTexteOption = $sValeurOption = ($i < 10 ? "0" : NULL).$i;
	
	$sOptionsDateNaissJour .= "<option"
		." value=\"{$sValeurOption}\""
		.($asDateNaiss["jour"] == $sValeurOption ? " selected" : NULL)
		.">{$sTexteOption}</option>";
}

// Mois
$sOptionsDateNaissMois = NULL;

$asMois = array("00","Janvier","F&eacute;vrier","Mars","Avril","Mai","Juin","Juillet","Ao&ucirc;t","Septembre","Octobre","Novembre","D&eacute;cembre");

for ($i=0; $i<count($asMois); $i++)
{
	$sTexteOption  = $asMois[$i];
	$sValeurOption = ($i < 10 ? "0" : NULL).$i;
	
	$sOptionsDateNaissMois .= "<option"
		." value=\"{$sValeurOption}\""
		.($asDateNaiss["mois"] == $sValeurOption ? " selected" : NULL)
		.">{$sTexteOption}</option>";
}

$asMois = $sTexteOption = $sValeurOption = NULL;

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style("dialog.css; profil.css"); ?>
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript("window.js"); ?>"></script>
<script type="text/javascript" language="javascript">
<!--
function afficher_erreur(v_sErreur)
{
	// document.getElementById('id_erreur').innerHTML = (v_sErreur != null && v_sErreur.length > 0 ? unescape(v_sErreur) : "&nbsp;");
}
//-->
</script>
</head>
<body>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<table border="0" cellspacing="0" cellpadding="2" width="100%" height="100%">

<tr>
<td><div class="intitule">Nom&nbsp;:</div></td>
<td><input type="text" name="NOM_PERS" size="40" value="<?php echo $oPersonne->retNom(TRUE); ?>" class="largeur_fixe"></td>
<td><span class="champs_obligatoires">*</span><?php echo (isset($asErreurs["nom"]) ? $asErreurs["nom"] : NULL); ?></td>
</tr>

<tr>
<td><div class="intitule">Pr&eacute;nom&nbsp;:</div></td>
<td><input type="text" name="PRENOM_PERS" size="40" value="<?php echo $oPersonne->retPrenom(TRUE); ?>" class="largeur_fixe"></td>
<td><span class="champs_obligatoires">*</span><?php echo (isset($asErreurs["prenom"]) ? $asErreurs["prenom"] : NULL); ?></td>
</tr>

<tr>
<td><div class="intitule">Pseudo&nbsp;:</div></td>
<td><input type="text" name="PSEUDO_PERS" size="40" value="<?php echo $oPersonne->retPseudo(); ?>" class="largeur_fixe"></td>
<td><span class="champs_obligatoires">*</span><?php echo (isset($asErreurs["pseudo"]) ? $asErreurs["pseudo"] : NULL); ?></td>
</tr>

<tr>
<td><div class="intitule">Date de naissance&nbsp;:</div></td>
<td><select name="DATE_NAISS_JOUR_PERS"><?php echo $sOptionsDateNaissJour?></select>&nbsp;-&nbsp;<select name="DATE_NAISS_MOIS_PERS"><?php echo $sOptionsDateNaissMois?></select>&nbsp;-&nbsp;<input type="text" name="DATE_NAISS_ANNEE_PERS" value="<?php echo $asDateNaiss['annee']?>" size="5" maxlength="4"></td>
<td>&nbsp;</td>
</tr>

<tr>
<td><div class="intitule">Mot de passe&nbsp;:</div></td>
<td><input type="password" name="MDP_PERS" size="40" value="" class="largeur_fixe"></td>
<td><?php echo ($iIdPers > 0 ? "&nbsp;" : "<span class=\"champs_obligatoires\">*</span>"); echo (isset($asErreurs["mdp"]) ? $asErreurs["mdp"] : NULL); ?></td>
</tr>

<tr>
<td><div class="intitule">Confirmation&nbsp;&nbsp;<br>du mot de passe&nbsp;:</div></td>
<td><input type="password" name="MDP_CONFIRM_PERS" size="40" value="" class="largeur_fixe"></td>
<td><?php echo ($iIdPers > 0 ? "&nbsp;" : "<span class=\"champs_obligatoires\">*</span>"); ?></td>
</tr>

<tr>
<td><div class="intitule">Sexe&nbsp;:</div></td>
<td><input type="radio" name="SEXE_PERS" value="M"<?php echo ($oPersonne->retSexe() == "M" ? " checked" : NULL); ?> onfocus="blur()">M&nbsp;<input type="radio" name="SEXE_PERS" value="F"<?php echo ($oPersonne->retSexe() == "F" ? " checked" : NULL); ?> onfocus="blur()">F&nbsp;</td>
<td>&nbsp;</td>
</tr>

<tr>
<td class="intitule"><div class="intitule" style="padding-top: 10px;">Adresse&nbsp;:</div></td>
<td><textarea name="ADRESSE_PERS" cols="40" rows="5" class="largeur_fixe"><?php echo $oPersonne->retAdresse(); ?></textarea></td>
<td>&nbsp;</td>
</tr>

<tr>
<td><div class="intitule">Num&eacute;ro&nbsp;de&nbsp;t&eacute;l&eacute;phone&nbsp;:</div></td>
<td><input type="text" name="TELEPHONE_PERS" size="40" value="<?php echo $oPersonne->retNumTel(); ?>" class="largeur_fixe"></td>
<td>&nbsp;</td>
</tr>

<tr>
<td><div class="intitule">Email&nbsp;:</div></td>
<td><input type="text" name="EMAIL_PERS" size="40" value="<?php echo $oPersonne->retEmail(); ?>" class="largeur_fixe"></td>
<td><?php echo (empty($asErreurs["email"]) ? "&nbsp;" : $asErreurs["email"]); ?></td>
</tr>

<tr>
<td><span id="id_erreur">&nbsp;</span></td>
<td class="champs_obligatoires" align="right">Champs&nbsp;obligatoires&nbsp;</td>
<td class="champs_obligatoires">*</td>
</tr>

</table>
<input type="hidden" name="ID_PERS" value="<?php echo $iIdPers; ?>">
<input type="hidden" name="SAUVER" value="1">
</form>
</body>
</html>

