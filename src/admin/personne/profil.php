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
** Date de crÃ©ation .......: 12/08/2002
** DerniÃ¨re modification ..: 04/04/2005
** Auteurs ................: Filippo PORCO <filippo.porco@umh.ac.be>
**
** UnitÃ© de Technologie de l'Education
** 18, Place du Parc
** 7000 MONS
*/

require_once("globals.inc.php");
include_once(dir_code_lib("mail.class.php"));

$oProjet = new CProjet();

// ---------------------
// RÃ©cupÃ©rer les variables de l'url
// ---------------------
$url_bSauver			= (empty($_POST["SAUVER"]) ? FALSE : TRUE);
$url_iIdForm			= (empty($_GET["formId"]) ? (empty($_POST["ID_FORM"]) ? 0 : $_POST["ID_FORM"]) : $_GET["formId"]);
$url_iNouvellePersonne	= (empty($_GET["nv"]) ? (empty($_POST["nv"]) ? 0 : $_POST["nv"]) : $_GET["nv"]);
$url_bCopieCourrier		= (empty($_POST["envoiMail"]) ? false : $_POST["envoiMail"]);

// si on valide le formulaire avec des erreurs présentes, on le rétablit avec les mêmes paramètres d'url.
$sFormAction =	$_SERVER['PHP_SELF']
				."?nv=$url_iNouvellePersonne"
				.(($url_iIdForm > 0) ? "&formId=$url_iIdForm" : null);

if ($url_iIdForm > 0) $oProjet->defFormationCourante($url_iIdForm);

// ---------------------
// DÃ©claration des fonctions locales
// ---------------------
function formatTexteErreur ($v_sTexteErreur)
{
	return ""
		."&nbsp;<span"
		." class=\"erreur\""
		." style=\"cursor: help;\""
		//." title=\"".emb_htmlentities($v_sTexteErreur)."\""
		." onmouseover=\"afficher_erreur('".rawurlencode("&#8212;&nbsp;".$v_sTexteErreur."&nbsp;&#8212;")."')\""
		." onmouseout=\"cacher_erreur()\""
		."\">Erreur</span>";
}

// ---------------------
// Initialisations
// ---------------------
$sNomForm	= $oProjet->oFormationCourante->retNom();
$iIdForm	= $oProjet->oFormationCourante->retId();
$iIdPers = 0;

/*
 * la variable $_GET["nv"], si elle est = 1 désigne une nouvelle inscription dans la formation
 * $_GET["nv"] = 0 la personne est déjà inscrite, on modifie juste son profil.
 */
if (isset($_GET["nv"]) && $_GET["nv"]>0)
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
			$asErreurs["nom"] = formatTexteErreur("Le nom ne peut pas &ecirc;tre vide");
		
		// PrÃ©nom
		$oPersonne->defPrenom($_POST["PRENOM_PERS"]);
		$sTmp = $oPersonne->retPrenom();
		if (empty($sTmp))
			$asErreurs["prenom"] = formatTexteErreur("Le pr&eacute;nom ne peut pas &ecirc;tre vide");

		// Email
		$sEmail = $_POST["EMAIL_PERS"];
		if (empty($sEmail))
			// Un  utilisateur doit absolument entrer un email
			$asErreurs["email"] = formatTexteErreur("l'adresse &eacute;lectronique ne doit pas être vide");
		elseif (!empty($sEmail) && !emailValide($sEmail))
			$asErreurs["email"] = formatTexteErreur("Cette adresse &eacute;lectronique n'est pas valable");
		$oPersonne->defEmail($sEmail);
		
		// Pseudo
		$oPersonne->defPseudo($_POST["PSEUDO_PERS"]);
		$sTmp = $oPersonne->retPseudo();
		if (empty($sTmp))
			$asErreurs["pseudo"] = formatTexteErreur("Le pseudo ne peut pas &ecirc;tre vide");
		/*else if (ereg("[^a-zA-Z0-9$]",$sTmp))
			$asErreurs["pseudo"] = formatTexteErreur("Les caract&egrave;res sp&eacute;ciaux ne sont pas accept&eacute;s (&eacute;&ecirc;&egrave;&agrave; ...)");*/
		else if (!$oPersonne->estPseudoUnique())
			$asErreurs["pseudo"] = formatTexteErreur("Ce pseudo a d&eacute;j&agrave;  &eacute;t&eacute; utilis&eacute;");
		
		// Date de naissance (format: AAAA-MM-JJ)
		//if (empty($_POST["DATE_NAISS_ANNEE_PERS"]) || $_POST["DATE_NAISS_ANNEE_PERS"]=="0000")
		//	$asErreurs["date_naissance"] = formatTexteErreur("La date de naissance doit &ecirc;tre valide et non nulle.");
		$sDateNaiss = (empty($_POST["DATE_NAISS_ANNEE_PERS"]) ? "0000" : $_POST["DATE_NAISS_ANNEE_PERS"])
			."-"
			.$_POST["DATE_NAISS_MOIS_PERS"]
			."-"
			.$_POST["DATE_NAISS_JOUR_PERS"];

		$oPersonne->defDateNaiss($sDateNaiss);

		/*
		 * VÃ©rifier que le nom + prÃ©nom est unique dans la table
		 */
		if ((!defined('UNICITE_NOM_PRENOM') || UNICITE_NOM_PRENOM===TRUE) && !$oPersonne->estUnique())
		{
			echo "<html>\n"
				."<head>"
			        ."<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n"
				.inserer_feuille_style("admin/personnes.css")
				."<script type=\"text/javascript\" language=\"javascript\"><!--\n"
				."function corriger() { top.frames[\"Bas\"].location = \"profil-menu.php?corriger=1\"; }\n"
				."</script>\n"
				."</head>\n"
				."<body onload=\"corriger()\" class=\"profil\">\n"
				."<br><br>"
				."<p align=\"center\">"
					."<b>"
					.emb_htmlentities("Inscription impossible: une personne avec ces informations est d&eacute;j&agrave;  inscrite")
					."</b>"
				."</p>"
				."<body>\n"
				."</html>\n";
			
			exit();
		}

		// Sexe
		if (isset($_POST["SEXE_PERS"]))
			$oPersonne->defSexe($_POST["SEXE_PERS"]);
		
		// Adresse
		$oPersonne->defAdresse($_POST["ADRESSE_PERS"]);
		
		// NumÃ©ro de tÃ©lÃ©phone
		$oPersonne->defNumTel($_POST["TELEPHONE_PERS"]);
		
		// Mot de passe
		$sMdp = trim($_POST["MDP_PERS"]);
		
		// Mot de passe de confirmation
		$sMdpConfirm = trim($_POST["MDP_CONFIRM_PERS"]);
		
		// VÃ©rification des mots de passe
		if ($iIdPers < 1 && empty($sMdp))
			// Un nouvel utilisateur doit absolument entrer un mot de passe
			$asErreurs["mdp"] = formatTexteErreur("Mot de passe vide");
		else if (!empty($sMdp) || !empty($sMdpConfirm))
		{
			if (ereg("[^a-zA-Z0-9$]",$sMdp))
				$asErreurs["mdp"] = formatTexteErreur("Les caract&egrave;res sp&eacute;ciaux ne sont pas accept&eacute;s (&eacute;&ecirc;&egrave;&agrave; ...)");
			else if ($sMdp !== $sMdpConfirm)
				$asErreurs["mdpConfirm"] = formatTexteErreur("Mot de passe non identique");
			else
				$oPersonne->defMdp($oProjet->retMdpCrypte($sMdp));
		}
		
		if (count($asErreurs) < 1)
		{
			// Il n'y a pas d'erreur on peut sauvegarder
			$oPersonne->enregistrer();
			$sPseudo = $oPersonne->retPseudo();
			$sNomComplet = $oPersonne->retNomComplet();
			$sPrenomExpediteur = $oProjet->oUtilisateur->retPrenom();
			$sNomExpediteur = $oProjet->oUtilisateur->retNom();
			$url_sAdresseServeurActuel = "http://".$_SERVER['SERVER_NAME'];
			
			/*
			 * si l'utilisateur est un nouvel inscrit, on le lie à la formation actuelle et on envoie un mail.
			 * On l'ajoute aussi dans le fichier "mdpncpte".
			 */ 
			if (isset($_POST["ID_FORM"]))
			{
				$oPersonne->lierPersForm($_POST["ID_FORM"]);

				/*
				 * On inscrit la personne dans le fichier "mdpncpte".
				 * Celà permet de récupérer le mot de passe même si la personne ne s'est jamais connectée au site.
				 */
				$sNomFichier = dir_tmp("mdpncpte",TRUE);

				$sLigne = date("Y-m-d H:i:s")
						." -- ".$sNomComplet
						.":".$sPseudo
						.":{$sMdp}"
						."\n\r";

				$fp = fopen($sNomFichier,"a");
				fwrite($fp,$sLigne,strlen($sLigne));
				fclose($fp);
				//chmod($sNomFichier,0200);

				// on envoie un mail si la case est cochée.
				if ($url_bCopieCourrier)
				{
					$sSujetCourriel = "Esprit-Inscription ('{$sNomForm}')";
					$sMessageCourrielTexte = "Bonjour,\r\n\r\nCe mail vous informe que vous avez bien &eacute;t&eacute; inscrit(e) &agrave; la formation\r\n"
						."'$sNomForm'\r\naccessible sur Esprit ($url_sAdresseServeurActuel).\r\n\r\n"
						."Pour acc&eacute;der &agrave; l'espace r&eacute;serv&eacute; &agrave; votre formation sur Esprit,\r\nintroduisez le pseudo et le mot de passe (en respectant scrupuleusement\r\n"
						."les majuscules, minuscules, caract&egrave;res accentu&eacute;s et espaces &eacute;ventuels) et\r\ncliquez sur Ok.\r\n\r\n"
						."Votre pseudo est : $sPseudo\r\nVotre mot de passe est : $sMdp\r\n\r\n"
						."Astuces :\r\n\r\n"
						."		* Apr&egrave;s connexion, vous pouvez modifier votre pseudo et mot de passe dans le\r\n"
						."		profil (cliquer sur le lien \"Profil\" en bas de l'&eacute;cran)\r\n\r\n"
    					."		* Si, un jour, vous oubliez votre pseudo et/ou votre mot de passe,\r\n"
    					."		cliquez sur le lien \"Oubli&eacute; ?\". Ce lien se trouve juste au-dessus de la zone\r\n"
    					."		\"Pseudo\", au niveau de la page d'accueil d'Esprit\r\n"
    					."		($url_sAdresseServeurActuel)."
    					."		Ceci vous permettra de r&eacute;cup&eacute;rer ces informations par courriel.\r\n\r\n"
    					."Bonne formation,\r\n\r\nPour l'&eacute;quipe Esprit,\r\n\r\n$sPrenomExpediteur $sNomExpediteur";

					$sMessageCourrielHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html><head><title>Inscription sur Esprit</title></head><body>'
						."Bonjour,<br /><br />Ce mail vous informe que vous avez bien &eacute;t&eacute; inscrit(e) &agrave; la formation '<strong>$sNomForm</strong>' accessible sur <a href =\"$url_sAdresseServeurActuel\">Esprit</a>.<br /><br />"
						."Pour acc&eacute;der &agrave; l'espace r&eacute;serv&eacute; &agrave; votre formation sur Esprit, introduisez le pseudo et le mot de passe (<ins>en respectant scrupuleusement les majuscules, minuscules, caract&egrave;res accentu&eacute;s et espaces &eacute;ventuels</ins>) et cliquez sur Ok.<br /><br />"
						."Votre pseudo est : <strong>$sPseudo</strong><br />Votre mot de passe est : <strong>$sMdp</strong><br /><br />"
						."Astuces :<br /><br />* Apr&egrave;s connexion, vous pouvez modifier votre pseudo et mot de passe dans le	profil (cliquer sur le lien \"Profil\" en bas de l'&eacute;cran)<br />"
						."* Si, un jour, vous oubliez votre pseudo et/ou votre mot de passe, <ins>cliquez sur le lien \"Oubli&eacute; ?\"</ins>. Ce lien se trouve juste au-dessus de la zone	\"Pseudo\", au niveau de la page d'accueil d'<a href =\"$url_sAdresseServeurActuel\">Esprit</a>. Ceci vous permettra de r&eacute;cup&eacute;rer ces informations par courriel.<br /><br />"
    					."Bonne formation.<br /><br />Pour l'&eacute;quipe Esprit,<br /><br />$sPrenomExpediteur $sNomExpediteur</body></html>";

					$sFrontiereEntreTexteHTML = '-----'.md5(uniqid(mt_rand()));

					//on insere d'abord le message au format texte
					$sMessageFinal	= 'This is a multi-part message in MIME format.'."\r\n";
 					$sMessageFinal .= '--'.$sFrontiereEntreTexteHTML."\r\n";
     				$sMessageFinal .= 'Content-Type: text/plain; charset=utf-8'."\r\n";
     				$sMessageFinal .= 'Content-Transfer-Encoding: 8bit'."\r\n\r\n";
     				$sMessageFinal .= $sMessageCourrielTexte."\r\n\r\n";
					//on ajoute le texte HTML
					$sMessageFinal .= '--'.$sFrontiereEntreTexteHTML."\r\n";
     				$sMessageFinal .= 'Content-Type: text/html; charset=utf-8'."\r\n";
     				$sMessageFinal .= 'Content-Transfer-Encoding: 8bit'."\r\n\r\n";
     				$sMessageFinal .= $sMessageCourrielHtml."\r\n\r\n";
     				//on ferme le message
     				$sMessageFinal .= '--'.$sFrontiereEntreTexteHTML.'--'."\r\n"; 

					$oMail = new CMail($sSujetCourriel,$sMessageFinal,$sEmail,$sNomComplet,$sFrontiereEntreTexteHTML);
					//$oMail->defExpediteur($oProjet->retEmail(), $oProjet->retNom());
					$oMail->defExpediteur($oProjet->oUtilisateur->retEmail(),$oProjet->oUtilisateur->retPrenom()." ".$oProjet->oUtilisateur->retNom());
					$oMail->envoyer($oProjet->oUtilisateur->retEmail());
				}
			}
			
			if ($bModifierCookie)
			{
				$oProjet->modifierInfosSession(SESSION_PSEUDO,$oPersonne->retPseudo(),TRUE);
				$oProjet->modifierInfosSession(SESSION_MDP,$oPersonne->retMdp(),TRUE);
			}

			// Timer de 3 secondes avant de fermer la page automatiquement
			echo "<html><body>"
				."<p>&nbsp;</p>"
				."<p>&nbsp;</p>"
				."<div align=\"center\">"
				."<p>"
				."<img src=\"".dir_theme("barre-de-progression.gif")."\">"
				."<br>Veuillez patienter pendant l'op&eacute;ration d'inscription des utilisateurs dans Esprit."
				."</p>"
				."</div>"
				."<script type=\"text/javascript\" language=\"javascript\">"
				."<!--\n setTimeout('top.close()',3000); \n//--></script>"
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

for ($i=1; $i<=31; $i++)
{
	$sTexteOption = $sValeurOption = ($i < 10 ? "0" : NULL).$i;
	
	$sOptionsDateNaissJour .= "<option"
		." value=\"{$sValeurOption}\""
		.($asDateNaiss["jour"] == $sValeurOption ? " selected" : NULL)
		.">{$sTexteOption}</option>";
}

// Mois
$sOptionsDateNaissMois = NULL;

$asMois = array("","Janvier","F&eacute;vrier","Mars","Avril","Mai","Juin","Juillet","Ao&ucirc;t","Septembre","Octobre","Novembre","D&eacute;cembre");

for ($i=1; $i<count($asMois); $i++)
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
<?php inserer_feuille_style("commun/dialog.css; admin/personnes.css"); ?>
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript("window.js"); ?>"></script>
<script type="text/javascript" language="javascript">
<!--
var i=false;
function GetId(id)
{
return document.getElementById(id);
}

function move(e) {
  if(i) {
    if (navigator.appName!="Microsoft Internet Explorer") {
    	GetId("id_erreur").style.left=e.pageX - 50 +"px";
    	GetId("id_erreur").style.top=e.pageY + 10+"px";
    }
    else {
    	if(document.documentElement.clientWidth>0) {
			GetId("id_erreur").style.left=event.x-50+document.documentElement.scrollLeft+"px";
			GetId("id_erreur").style.top=10+event.y+document.documentElement.scrollTop+"px";
    	} else {
			GetId("id_erreur").style.left=+event.x-50+document.body.scrollLeft+"px";
			GetId("id_erreur").style.top=10+event.y+document.body.scrollTop+"px";
		}
    }
  }
}

function afficher_erreur(v_sErreur)
{
	// document.getElementById('id_erreur').innerHTML = (v_sErreur != null && v_sErreur.length > 0 ? unescape(v_sErreur) : "&nbsp;");
	if(i==false) {
		GetId("id_erreur").style.visibility="visible";
		GetId("id_erreur").innerHTML = unescape(v_sErreur);
		i=true;
	}
}

function cacher_erreur() {
	if(i==true) {
		GetId("id_erreur").style.visibility="hidden";
		i=false;
	}
}

document.onmousemove=move;

function Attente_pour_Envoi() {
	if (self.document.getElementById('Block_Form'))
	{
		self.document.getElementById('Block_Form').style.display='none';
	}
	if (self.document.getElementById('Block_Attente'))
	{
		self.document.getElementById('Block_Attente').style.display='block';
	}
}
//-->
</script>
</head>
<body class="profil">
<div id="Block_Form">
<form action="<?php echo $sFormAction; ?>" method="POST">
<table border="0" cellspacing="0" cellpadding="2" width="100%" height="100%">
<?php
if ($url_iNouvellePersonne) 
	echo "
<tr>
<td class=\"intitule\"><div>Formation&nbsp;:</div></td>
<td class=\"largeur_fixe\">$sNomForm ($iIdForm)</td>
<td>&nbsp;</td>
</tr>";
?>
<tr>
<td class="intitule"><div>Nom&nbsp;:</div></td>
<td class="largeur_fixe"><input type="text" name="NOM_PERS" size="40" value="<?php echo $oPersonne->retNom(TRUE); ?>"></td>
<td class="champs_obligatoires">*<?php echo (isset($asErreurs["nom"]) ? $asErreurs["nom"] : NULL); ?></td>
</tr>

<tr>
<td class="intitule"><div>Pr&eacute;nom&nbsp;:</div></td>
<td class="largeur_fixe"><input type="text" name="PRENOM_PERS" size="40" value="<?php echo $oPersonne->retPrenom(TRUE); ?>"></td>
<td class="champs_obligatoires">*<?php echo (isset($asErreurs["prenom"]) ? $asErreurs["prenom"] : NULL); ?></td>
</tr>

<tr>
<td class="intitule"><div>Pseudo&nbsp;:</div></td>
<td class="largeur_fixe"><input type="text" name="PSEUDO_PERS" size="40" value="<?php echo $oPersonne->retPseudo(); ?>"></td>
<td class="champs_obligatoires">*<?php echo (isset($asErreurs["pseudo"]) ? $asErreurs["pseudo"] : NULL); ?></td>
</tr>

<tr>
<td class="intitule"><div>Date de naissance&nbsp;:</div></td>
<td><select name="DATE_NAISS_JOUR_PERS"><?php echo $sOptionsDateNaissJour?></select>&nbsp;-&nbsp;<select name="DATE_NAISS_MOIS_PERS"><?php echo $sOptionsDateNaissMois?></select>&nbsp;-&nbsp;<input type="text" name="DATE_NAISS_ANNEE_PERS" value="<?php echo $asDateNaiss['annee']?>" size="5" maxlength="4"></td>
<td>&nbsp;</td>
</tr>

<tr>
<td class="intitule"><div>Email&nbsp;:</div></td>
<td class="largeur_fixe"><input type="text" name="EMAIL_PERS" size="40" value="<?php echo $oPersonne->retEmail(); ?>"></td>
<td class="champs_obligatoires">*<?php echo (isset($asErreurs["email"]) ? $asErreurs["email"] : NULL); ?></td>
</tr>

<tr>
<td class="intitule"><div>Mot de passe&nbsp;:</div></td>
<td class="largeur_fixe"><input type="password" name="MDP_PERS" size="40" value=""></td>
<td class="champs_obligatoires"><?php echo ($iIdPers > 0 ? "&nbsp;" : "*"); echo (isset($asErreurs["mdp"]) ? $asErreurs["mdp"] : NULL); ?></td>
</tr>

<tr>
<td class="intitule"><div>Confirmation&nbsp;&nbsp;<br>du mot de passe&nbsp;:</div></td>
<td class="largeur_fixe"><input type="password" name="MDP_CONFIRM_PERS" size="40" value=""></td>
<td class="champs_obligatoires"><?php echo ($iIdPers > 0 ? "&nbsp;" : "*"); echo (isset($asErreurs["mdpConfirm"]) ? $asErreurs["mdpConfirm"] : NULL); ?></td>
</tr>

<tr>
<td class="intitule"><div>Sexe&nbsp;:</div></td>
<td><input type="radio" name="SEXE_PERS" value="M"<?php echo ($oPersonne->retSexe() == "M" ? " checked" : NULL); ?> onfocus="blur()">M&nbsp;<input type="radio" name="SEXE_PERS" value="F"<?php echo ($oPersonne->retSexe() == "F" ? " checked" : NULL); ?> onfocus="blur()">F&nbsp;</td>
<td>&nbsp;</td>
</tr>

<tr>
<td class="intitule"><div style="padding-top: 10px;">Adresse&nbsp;:</div></td>
<td class="largeur_fixe"><textarea name="ADRESSE_PERS" cols="40" rows="5" class="largeur_fixe"><?php echo $oPersonne->retAdresse(); ?></textarea></td>
<td>&nbsp;</td>
</tr>

<tr>
<td class="intitule"><div>Num&eacute;ro&nbsp;de&nbsp;t&eacute;l&eacute;phone&nbsp;:</div></td>
<td class="largeur_fixe"><input type="text" name="TELEPHONE_PERS" size="40" value="<?php echo $oPersonne->retNumTel(); ?>"></td>
<td>&nbsp;</td>
</tr>
<?php
// Pour une nouvelle personne inscrite, on peut faire un envoi de mail à la personne
if ($url_iNouvellePersonne == 1)
echo "<td>&nbsp;</td>"
	."<td class=\"intitule\" align=\"left\" title=\"En cochant cette case, un courriel sera envoy&eacute; &agrave; la personne.\">"
	."<input type=\"checkbox\" name=\"envoiMail\" id=\"copieCourriel\" value=\"1\" checked>"
	."<label class=\"afficher_curseur_aide\" for=\"copieCourriel\">Envoyer un mail &agrave; la personne</label></td>";
?>
<tr>
<td>&nbsp;</td>
<td class="champs_obligatoires" align="right">Champs&nbsp;obligatoires&nbsp;</td>
<td class="champs_obligatoires">*</td>
</tr>

</table>
<div id="id_erreur" class="info_erreur"></div>
<input type="hidden" name="ID_PERS" value="<?php echo $iIdPers; ?>">
<input type="hidden" name="ID_FORM" value="<?php echo $iIdForm ?>">
<input type="hidden" name="SAUVER" value="1">
<input type="hidden" name="nv" value="<?php echo $url_iNouvellePersonne; ?>">
</form>
</div>
<div id="Block_Attente">
		<p>&nbsp;</p><p>&nbsp;</p>
		<p>
		<img src="<?php echo dir_theme("barre-de-progression.gif") ?>">
		<br>Veuillez patienter pendant l'op&eacute;ration d'inscription des utilisateurs dans Esprit.
		</p>
</div>
</body>
</html>

