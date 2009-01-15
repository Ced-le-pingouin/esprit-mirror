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
** Fichier ................: import.php
** Description ............: Importation d'une liste d'étudiants (format Excel)
** Date de création .......: 12/12/2006
*/

require_once("globals.inc.php");
include_once(dir_code_lib("mail.class.php"));

$oProjet	= new CProjet();
$oPersonne	= new CPersonne($oProjet->oBdd);

$url_bCopieCourrier		= (empty($_POST["envoiMail"]) ? false : $_POST["envoiMail"]);

// ---------------------
// Déclaration des fonctions locales
// ---------------------
function formatTexteErreur ($v_sTexteErreur)
{
	return ""
		."&nbsp;<span"
		." class=\"erreur\""
		." style=\"cursor: help;\""
		." title=\"".emb_htmlentities($v_sTexteErreur)."\""
		." onmouseover=\"afficher_erreur('".rawurlencode("&#8212;&nbsp;".$v_sTexteErreur."&nbsp;&#8212;")."')\""
		." onmouseout=\"afficher_erreur()\""
		."\">Erreur</span>";
}
function insererPersonne ($tab, $enreg=true)
{
	global $oProjet;
	global $oPersonne;
	$sMessage = null;
// $tab[1..8] : nom, prénom, pseudo, mdp, email, sexe, date de naissance, numero de formation

	// Nom
	if (empty($tab[1]))
		return false;
	$oPersonne->defNom(addslashes(mb_strtoupper($tab[1], "utf-8")));

	// Pr�nom
	if (empty($tab[2]))
		return false;
	$oPersonne->defPrenom($tab[2]);

	// Pseudo
	if (empty($tab[3]))
		return "<span class=\"importErreur\">Erreur!</span> Pas de pseudo.";
	$oPersonne->defPseudo($tab[3]);

	// Num�ro de formation
	$sIdFormation = trim ($tab[8]);
	if (!empty($sIdFormation) && !preg_match('/[0-9]/',$sIdFormation))
		return "<span class=\"importErreur\">Erreur!</span> Le num&eacute;ro de formation doit &ecirc;tre num&eacute;rique.";		

	// Email
	if (empty($tab[5]))
		return "<span class=\"importErreur\">Erreur!</span> ".$sPrenomNom.". Pas d'adresse e-mail alors que ce champ est obligatoire.";
	$oPersonne->defEmail($tab[5]);
	
	// Date de naissance (format: AAAA-MM-JJ)
	if (!empty($tab[7]))
	{
	//	return "<span class=\"importErreur\">Erreur!</span> ".$sPrenomNom.". La date de naissance est obligatoire.";
		/*
		 * on r�cup�re le champs date de naissance au format jj/mm/aaaa
		 * et on le met au format aaaa-mm-jj
		 */
		$sDateNaissanceTemp = array_reverse(explode('/',$tab[7]));
		$sDateNaissance = $sDateNaissanceTemp[0]."-".$sDateNaissanceTemp[1]."-".$sDateNaissanceTemp[2];
		$oPersonne->defDateNaiss($sDateNaissance);
	}

	// le couple est deja dans la DB, on ajoute juste le numero de formation si le champs est rempli
	if ((!defined('UNICITE_NOM_PRENOM') || UNICITE_NOM_PRENOM===TRUE) && !$oPersonne->estUnique())
	{
		// le num�ro de formation est nul -> on affiche un message d'erreur, sinon juste un avertissement
		if 	($sIdFormation == null) $sMessage = "<span class=\"importErreur\">Erreur!</span> ";
		
		$sMessage .= $oPersonne->retPrenom()." ".$oPersonne->retNom()." &eacute;tait d&eacute;j&agrave; inscrit sur Esprit!<br />";
		$sMessage .= $oPersonne->lierPersForm($sIdFormation);
	}

$sPrenomNom = $oPersonne->retPrenom()." ".$oPersonne->retNom();

	if ($sMessage==null)
	{
		if (!$oPersonne->estPseudoUnique() && $sMessage==null)
		{
			$a = '';
			$hResult = $oProjet->oBdd->executerRequete(
				"SELECT CONCAT(Nom,' ',Prenom) AS NomC FROM Personne "
				. "WHERE Pseudo='". $tab[3] ."'"
				);
			if ($oEnreg = $oProjet->oBdd->retEnregSuiv($hResult))
				$a = ' à "' . $oEnreg->NomC .'"';
			return "<span class=\"importErreur\">Erreur!</span> Le pseudo '".$tab[3]."' est déjà attribué$a.";
		}

		// Mot de passe
		$sMdp = trim($tab[4]);
		if (preg_match('/[^a-zA-Z0-9]/',$sMdp))
			return "<span class=\"importErreur\">Erreur!</span> ".$sPrenomNom.". Le mot de passe <em>$sMdp</em> doit être alpha-numérique.";
		else
			$oPersonne->defMdp($oProjet->retMdpCrypte($sMdp));

		// Sexe
		if (!$tab[6])
			$sexe = "M";
		$oPersonne->defSexe($tab[6]);
	}

	if ($enreg && $sMessage==null) {
		$oPersonne->enregistrer();
		$oPersonne->lierPersForm($sIdFormation,TRUE);
	}
	elseif ($sMessage!=null)
	{
		return $sMessage;
	}
	return true;
}

// ---------------------
// Importation
// ---------------------
if (!empty($_POST['importer'])) {
	require_once(dir_lib('phpexcelreader/reader.php',TRUE));
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('UTF-8');
	$data->setUTFEncoder('mb');
	if (strcasecmp(substr($_FILES['importFile']['name'],-4),".csv")===0) {
		$data->readCSV($_FILES['importFile']['tmp_name']);
	}
	elseif (strcasecmp(substr($_FILES['importFile']['name'],-4),".ods")===0) {
		$data->readODS($_FILES['importFile']['tmp_name']);
	}
	else {			
		$data->read($_FILES['importFile']['tmp_name']);
	}
	echo "<head>
<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
". inserer_feuille_style("commun/dialog.css; admin/personnes.css",false). "
<script type=\"text/javascript\" language=\"javascript\" src=\"". dir_javascript("window.js") ."\"></script>
<script type=\"text/javascript\" language=\"javascript\">
function Cacher(element1, element2)
{
	if (self.document.getElementsByName(element1))
	{
		listeElements1 = self.document.getElementsByName(element1);
		for (i=0; i<listeElements1.length;i++)
			listeElements1[i].style.display = 'none';
	}
		
	if (self.document.getElementsByName(element2))
	{
		listeElements2 = self.document.getElementsByName(element2);
		for (j=0; j<listeElements2.length;j++)
			listeElements2[j].style.display = 'none';
	}
}
function Restaurer(element)
{
	if (self.document.getElementsByName(element))
	{
		listeElements = self.document.getElementsByName(element);
		for (k=0; k<listeElements.length;k++)
		listeElements[k].style.display = 'list-item';
	}
}
function rafraichir_Parent()
{
	top.opener.top.frames['Principal'].frames['FRM_PERSONNE'].location.reload(true);
	top.opener.top.frames['Principal'].frames['FRM_INSCRIT'].location.reload(true);
}
</script>
</head>
<body class=\"profil\" onload=\"rafraichir_Parent()\">
<h1>Inscription group&eacute;e</h1>";
	$sAfficherLog = "\n<ol>";
	$inscrits = $avertissements = $erreurs = $inscritsAffectes = $avertissementsAffectes = 0;
	$total=0;
	$sSujetCourriel = null;
	
// print_r($data->sheets);
	for ($nrow=6; $nrow<$data->sheets[0]['numRows']; $nrow++) {
		// colonnes 1 à 8, à partir de la ligne 6
		// nom, prénom, pseudo, mdp, email, sexe, date de naissance, numero de formation
		if (empty($data->sheets[0]['cells'][$nrow][1]) or empty($data->sheets[0]['cells'][$nrow][2]))
			continue;
		$nom = mb_strtoupper($data->sheets[0]['cells'][$nrow][1], "utf-8");
		$prenom				= $data->sheets[0]['cells'][$nrow][2];
		$sPrenomExpediteur	= $oProjet->oUtilisateur->retPrenom();
		$sNomExpediteur		= $oProjet->oUtilisateur->retNom();
		$sIdFormation = $sNomFormation = "";
		$url_sAdresseServeurActuel = "http://".$_SERVER['SERVER_NAME'];
		$sMessage = "";
		$sSujetCourriel = "Esprit-Inscription";

		if ($data->sheets[0]['cells'][$nrow][8] && preg_match('/[0-9]/',$data->sheets[0]['cells'][$nrow][8]))
		{
			$sIdFormation = $data->sheets[0]['cells'][$nrow][8];
			$oFormation = new CFormation($oProjet->oBdd,$sIdFormation);
			$sNomFormation = $oFormation->retNom();
			if ($sNomFormation != "") $sSujetCourriel = "Esprit-Inscription ('{$sNomFormation}')";
		}

		$total++;
		$res = insererPersonne($data->sheets[0]['cells'][$nrow]);
		
		if (preg_match('/importOKPetit/', $res))
		{
			$sPseudo	 = $oPersonne->retPseudo();
			/*
			 * on r�cup�re l'ancien mot de passe dans le fichier 'mdpncpte'
			 */			
			$asLignesFichierMdp = array();
			$sFichierMdp = dir_tmp("mdpncpte",TRUE);
			
			if (is_file($sFichierMdp))
			{
				if (!is_readable($sFichierMdp)) {
					chmod($sFichierMdp,0600);
					$asLignesFichierMdp = file($sFichierMdp);
					chmod($sFichierMdp,0200);
				} else {
					$asLignesFichierMdp = file($sFichierMdp);
				}
			}
			// 

			for ($i=count($asLignesFichierMdp)-1; $i >= 0; $i--)
				if (strstr($asLignesFichierMdp[$i],":{$sPeudo}:"))
					break;

			if ($i >= 0)
			{
				$sMdp = trim(substr(strrchr($asLignesFichierMdp[$i],":"),1));

				if (strlen($sMdp) > 0)
				{
					$sMotDePasse = $sMdp;
				}
				
			}
			else $sMotDePasse = " (votre mot de passe n'a pas &eacute;t&eacute; chang&eacute;)";
		}
		else
		{
			$sPseudo	 = $data->sheets[0]['cells'][$nrow][3];
			$sMotDePasse = $data->sheets[0]['cells'][$nrow][4];
		}
		
		$sMessageCourrielTexte = "Bonjour,\r\n\r\nCe mail vous informe que vous avez bien &eacute;t&eacute; inscrit(e)";
		if ($sNomFormation!="") $sMessageCourrielTexte .= " &agrave; la formation\r\n '$sNomFormation'\r\naccessible";
		$sMessageCourrielTexte .= "sur Esprit ($url_sAdresseServeurActuel).\r\n\r\n"
			."Pour acc&eacute;der &agrave; l'espace r&eacute;serv&eacute; &agrave; votre formation sur Esprit,\r\nintroduisez le pseudo et le mot de passe (en respectant scrupuleusement\r\n"
			."les majuscules, minuscules, caract&egrave;res accentu&eacute;s et espaces &eacute;ventuels) et\r\ncliquez sur Ok.\r\n\r\n"
			."Votre pseudo est : $sPseudo\r\nVotre mot de passe est : ".$sMotDePasse."\r\n\r\n"
			."Astuces :\r\n\r\n"
			."		* Apr&egrave;s connexion, vous pouvez modifier votre pseudo et mot de passe dans le\r\n"
			."		profil (cliquer sur le lien \"Profil\" en bas de l'&eacute;cran)\r\n\r\n"
    		."		* Si, un jour, vous oubliez votre pseudo et/ou votre mot de passe,\r\n"
    		."		cliquez sur le lien \"Oubli&eacute; ?\". Ce lien se trouve juste au-dessus de la zone\r\n"
    		."		\"Pseudo\", au niveau de la page d'accueil d'Esprit\r\n"
    		."		($url_sAdresseServeurActuel)."
    		."		Ceci vous permettra de r&eacute;cup&eacute;rer ces informations par courriel.\r\n\r\n"
    		."Bonne formation.\r\n\r\nPour l'&eacute;quipe Esprit,\r\n\r\n$sPrenomExpediteur $sNomExpediteur";

		$sMessageCourrielHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html><head><title>Inscription sur Esprit</title></head><body>'
			."Bonjour,<br /><br />Ce mail vous informe que vous avez bien &eacute;t&eacute; inscrit(e)";
		if ($sNomFormation!="") $sMessageCourrielHtml .= "&agrave; la formation '<strong>$sNomFormation</strong>' accessible";
			//.($sNomFormation!="") ? ("� la formation '<strong>$sNomFormation</strong>' accessible"): NULL
		$sMessageCourrielHtml .= " sur <a href =\"$url_sAdresseServeurActuel\">Esprit</a>.<br /><br />"
			."Pour acc&eacute;der &agrave; l'espace r&eacute;serv&eacute; &agrave; votre formation sur Esprit, introduisez le pseudo et le mot de passe (<ins>en respectant scrupuleusement les majuscules, minuscules, caract&egrave;res accentu&eacute;s et espaces &eacute;ventuels</ins>) et cliquez sur Ok.<br /><br />"
			."Votre pseudo est : <strong>$sPseudo</strong><br />Votre mot de passe est : <strong>".$sMotDePasse."</strong><br /><br />"
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

		$sDestinataire = $data->sheets[0]['cells'][$nrow][5];

		if ($res===true) {
			// tout va bien
			if ($sIdFormation!="" && $sNomFormation!="") {
				$sMessage = " et ajout&eacute; &agrave; la formation '<em>".$sNomFormation."</em>'</span>";
				$inscritsAffectes++;
			}
			else if ($sIdFormation!="" && $sNomFormation=="")
				$sMessage = ".<br /><span class=\"importAvertPetit\">La formation n&deg; <em>$sIdFormation</em> n'existe pas</span>";

			// on envoie un mail aux nouvelles personnes inscrites sur la PF
			if ($url_bCopieCourrier && $sDestinataire)
			{
				$oMail = new CMail($sSujetCourriel,$sMessageFinal,$sDestinataire,$nom.$prenom,$sFrontiereEntreTexteHTML);
				$oMail->defExpediteur($oProjet->oUtilisateur->retEmail(),$oProjet->oUtilisateur->retPrenom()." ".$oProjet->oUtilisateur->retNom());
				$oMail->envoyer($oProjet->oUtilisateur->retEmail());
			}
				
			$inscrits++;
			$sAfficherLog .= "<li name=\"listeOK\" id=\"listeOK\"><span class=\"importOK\">OK</span> : <em>$prenom $nom</em> a été inscrit sur Esprit".$sMessage.".</li>";
			// ...
		}
		elseif (($sIdFormation!="") && !preg_match('/importErreur/',$res)) {
			// Un avertissement lors de l'inscription : la personne est deja inscrite, mais ajout�e � une formation.
			$sAfficherLog .= "<li name=\"listeAvert\" id=\"listeAvert\"><span class=\"importAvert\">Avertissement!</span> ".$res."</li>";

			if ($sNomFormation!=""){
				$avertissementsAffectes++;
			}

			// on envoie un mail aux personnes ajout�es � la formation
			if ($url_bCopieCourrier && !preg_match('/importOKPetit1/', $res))
			{
				$oMail = new CMail($sSujetCourriel,$sMessageFinal,$sDestinataire,$nom.$prenom,$sFrontiereEntreTexteHTML);
				$oMail->defExpediteur($oProjet->oUtilisateur->retEmail(),$oProjet->oUtilisateur->retPrenom()." ".$oProjet->oUtilisateur->retNom());
				$oMail->envoyer($oProjet->oUtilisateur->retEmail());
			}
			$avertissements++;
			// ...
		}
		else {
			// Un pb avec cette inscription
			$sAfficherLog .= "<li name=\"listeErreur\" id=\"listeErreur\">".$res."</li>";
			$erreurs++;
			// ...
		}
	}
	$sAfficherLog .= "</ol>\n";
	if ($total) {
			echo "<div class=\"historique_import\" id=\"historique_import\"><p>Sur un total de $total ".($total>1 ? "inscriptions" : "inscription")." :</p>"
				."<p><a href=\"javascript: Restaurer('listeOK');Restaurer('listeAvert');Restaurer('listeErreur');\">Tout afficher</a></p>"
				."<p  class=\"typeA\">"
				.($inscrits>0?"<a href=\"javascript: Cacher('listeAvert', 'listeErreur'); Restaurer('listeOK');\">":null)."$inscrits ".($inscrits>1 ? "nouvelles inscriptions" : "nouvelle inscription")." sur Esprit".($inscrits>0?"</a>":null)
				." (dont $inscritsAffectes ".($inscritsAffectes>1 ? "nouvelles affectations" : "nouvelle affectation")."),"
				."<br />".($avertissements>0?"<a href=\"javascript: Cacher('listeOK', 'listeErreur'); Restaurer('listeAvert');\">":null)."$avertissements ".($avertissements>1 ? "avertissements" : "avertissements").($avertissements>0?"</a>":null)
				." (dont $avertissementsAffectes ".($avertissementsAffectes>1 ? "nouvelles affectations" : "nouvelle affectation")."),"
				."<br />".($erreurs>0?"<a href=\"javascript: Cacher('listeOK', 'listeAvert'); Restaurer('listeErreur');\">":null)."$erreurs ".($erreurs>1 ? "erreurs" : "erreur").($erreurs>0?"</a>":null).".</p>\n";
	}
	echo $sAfficherLog."</div>";
	exit();
}

// Afficher le formulaire d'importation
?><html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style("commun/dialog.css; admin/personnes.css"); ?>
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript("window.js"); ?>"></script>
<script type="text/javascript" language="javascript">
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
</script>
</head>
<body class="profil">
<h1>Inscription groupée</h1>
<div id="Block_Form">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="fichier" method="POST" enctype="multipart/form-data">
<p>
<label for="importFile">Sélectionner le fichier contenant la liste des étudiants à inscrire :</label><br />
<input type="file" name="importFile" size="40" /><br />
<div style="text-align:center"><button type="submit" name="importer" value="1" onclick="Attente_pour_Envoi()">Importer</button></div>
<div>&nbsp;</div>
<div>
<input type="checkbox" name="envoiMail" id="copieCourriel" value="1" checked>
<label class="afficher_curseur_aide" for="copieCourriel">Envoyer un mail &agrave; toutes les nouvelles personnes inscrites sur Esprit et &agrave; celles ajout&eacute;es dans une formation.</label></div>
</p>
</form>
</div>

<div id="Block_Attente">
		<p>&nbsp;</p><p>&nbsp;</p>
		<p>
		<img src="<?php echo dir_theme("barre-de-progression.gif") ?>">
		<br>Veuillez patienter pendant l'op&eacute;ration d'inscription des utilisateurs dans Esprit.
		</p>
</div>
<hr>

<h1>Fichier de modèle</h1>
Télécharger le modèle de feuille de tableur
<ul>
  <li>au format <a href="esprit_inscriptions.xls">Excel</a></li>
<?php /*
       * <li>au format <a href="esprit_inscriptions.csv">CSV</a></li>
       * BUGS IN PARSER <li>au format <a href="esprit_inscriptions.ods">ODS</a></li> */ ?>
</ul>
<p>Attention à ne pas modifier les <strong>5 premières lignes</strong> de ces modèles.</p>
<!-- <p>Le fichier CSV doit utiliser le jeu de caractères <em>UTF-8</em>. Si les accents des permières lignes s'affichent mal, ce n'est pas le cas.</p> -->
</body>
</html>

