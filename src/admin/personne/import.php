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
	$sIdFormation = trim ($tab[8]);

	if (empty($tab[1]))
		return false;
	$oPersonne->defNom(addslashes(mb_strtoupper($tab[1], "utf-8")));
	
	if (empty($tab[2]))
		return false;
	$oPersonne->defPrenom($tab[2]);

	if (empty($tab[3]))
		return "<span class=\"importErreur\">Erreur!</span> Pas de pseudo.";
	$oPersonne->defPseudo($tab[3]);

	if ((!defined('UNICITE_NOM_PRENOM') || UNICITE_NOM_PRENOM===TRUE) && !$oPersonne->estUnique())
	{
		$sMessage = $oPersonne->retPrenom()." ".$oPersonne->retNom()." est d&eacute;j&agrave; inscrit sur Esprit!<br />";
		// le couple est deja dans la DB, on ajoute juste le numero de formation si le champs est rempli
		$sMessage .= $oPersonne->lierPersForm($sIdFormation);
	}

	if ($sMessage==null)
	{
		if (!$oPersonne->estPseudoUnique() && $sMessage==null) {
			$a = '';
			$hResult = $oProjet->oBdd->executerRequete(
				"SELECT CONCAT(Nom,' ',Prenom) AS NomC FROM Personne "
				. "WHERE Pseudo='". $tab[3] ."'"
				);
			if ($oEnreg = $oProjet->oBdd->retEnregSuiv($hResult))
				$a = ' à "' . $oEnreg->NomC .'"';
			return "<span class=\"importErreur\">Erreur!</span> Le pseudo '".$tab[3]."' est déjà attribué$a.";
		}
	
		$sMdp = trim($tab[4]);
		if (preg_match('/[^a-zA-Z0-9]/',$sMdp))
			return "<span class=\"importErreur\">Erreur!</span> Le mot de passe <em>$sMdp</em> doit être alpha-numérique.";
		else
			$oPersonne->defMdp($oProjet->retMdpCrypte($sMdp));

		if (empty($tab[5]))
			return "<span class=\"importErreur\">Erreur!</span> Pas d'adresse e-mail alors que ce champ est obligatoire.";
		$oPersonne->defEmail($tab[5]);

		if (!$tab[6])
			$sexe = "M";
		$oPersonne->defSexe($tab[6]);

		// ajout de la date de naissance en colonne 7
		// Date de naissance (format: AAAA-MM-JJ)
		if (empty($tab[7]))
			return "<span class=\"importErreur\">Erreur!</span> La date de naissance est obligatoire.";
		$sDateNaissanceTemp = array_reverse(explode('/',$tab[7]));
		$sDateNaissance = $sDateNaissanceTemp[0]."-".$sDateNaissanceTemp[1]."-".$sDateNaissanceTemp[2];
		$oPersonne->defDateNaiss($sDateNaissance);
	}

	if ($enreg && $sMessage==null) {
		$oPersonne->enregistrer();
		$oPersonne->lierPersForm($sIdFormation);
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
top.opener.top.frames['Principal'].frames['FRM_PERSONNE'].location.reload(true);
top.opener.top.frames['Principal'].frames['FRM_INSCRIT'].location.reload(true);
function Cacher(element1, element2)
{
	if (self.document.getElementById(element1))
	{
		self.document.getElementById(element1).style.display = 'none';
	}
		
	if (self.document.getElementById(element2))
	{
		self.document.getElementById(element2).style.display = 'none';
	}
}
function Restaurer(element)
{
	if (self.document.getElementById(element))
	{
		self.document.getElementById(element).style.display = 'list-item';
	}
}
</script>
</head>
<body class=\"profil\">
<h1>Inscription group&eacute;e</h1>";
	$sAfficherLog = "\n<ol>";
	$inscrits = $affectes = $erreurs =0;
	$total=0;
	$sSujetCourriel = null;
	
// print_r($data->sheets);
	for ($nrow=6; $nrow<$data->sheets[0]['numRows']; $nrow++) {
		// colonnes 1 à 8, à partir de la ligne 6
		// nom, prénom, pseudo, mdp, email, sexe, date de naissance, numero de formation
		if (empty($data->sheets[0]['cells'][$nrow][1]) or empty($data->sheets[0]['cells'][$nrow][2]))
			continue;
		$nom = mb_strtoupper($data->sheets[0]['cells'][$nrow][1], "utf-8");
		$prenom = $data->sheets[0]['cells'][$nrow][2];
		$sIdFormation = $sNomFormation = "";
		$sMessage = "";
		if ($data->sheets[0]['cells'][$nrow][8] && preg_match('/[0-9]/',$data->sheets[0]['cells'][$nrow][8]))
		{
			$sIdFormation = $data->sheets[0]['cells'][$nrow][8];
			$oFormation = new CFormation($oProjet->oBdd,$sIdFormation);
			$sNomFormation = $oFormation->retNom();
			$sSujetCourriel = "Esprit-Inscription ('{$sNomFormation}')";
		}

		$total++;
		$res = insererPersonne($data->sheets[0]['cells'][$nrow]);
		
		$sMessageCourriel = "Ce mail est envoy� par la plateforme Esprit pour vous signaler que vous venez d'�tre inscrit � la formation : \r\n"
		.$sNomFormation."\r\n"
		."avec les informations suivantes :\r\n"
		."Pseudo : {$oPersonne->retPseudo()}\r\n"
		."Mot de passe : {$sMdp}"
		."\r\n\r\n"
		."Merci de nous signaler les �ventuelles erreurs en r�pondant � ce mail.";

		if ($res===true) {
			// tout va bien
			if ($sIdFormation!="" && $sNomFormation!="") {
				$sMessage = " et ajout&eacute; &agrave; la formation '<em>".$sNomFormation."</em>'!</span>";

				// on envoie un mail aux personnes ajout�es � la formation				
				$oMail = new CMail($sSujetCourriel,$sMessageCourriel,$tab[5],$nom.$prenom);
				$oMail->defExpediteur($oProjet->retEmail(), $oProjet->retNom());
				$oMail->envoyer();
				$affectes++;
			}
			$inscrits++;
			$sAfficherLog .= "<li id=\"importOK\"><span class=\"importOK\">OK</span> : <em>$prenom $nom</em> a été inscrit".$sMessage.".</li><br />";
			// ...
		}
		elseif (($sIdFormation!="") && !preg_match('/importErreur/',$res)) {
			// Un avertissement lors de l'inscription : la personne est deja inscrite, mais ajout�e � une formation.
			$sAfficherLog .= "<li id=\"importAvert\"><span class=\"importAvert\">Avertissement!</span> ".$res."</li><br />";

			// on envoie un mail aux personnes ajout�es � la formation
			$oMail = new CMail($sSujetCourriel,$sMessageCourriel,$tab[5],$oPersonne->retNomComplet());
			$oMail->defExpediteur($oProjet->retEmail(), $oProjet->retNom());
			$oMail->envoyer();

			$affectes++;
			// ...
		}
		else {
			// Un pb avec cette inscription
			$sAfficherLog .= "<li id=\"importErreur\">".$res."</li><br />";
			$erreurs++;
			// ...
		}
	}
	$sAfficherLog .= "</ol>\n";
	if ($total) {
			echo "<p>Sur un total de $total ".($total>1 ? "inscriptions" : "inscription")." :</p>"
				."<p><a href=\"javascript: Restaurer('importOK');Restaurer('importAvert');Restaurer('importErreur');\">Tout afficher</a></p>"
				."<p  class=\"typeA\">"
				.($inscrits>0?"<a href=\"javascript: Cacher('importAvert', 'importErreur'); Restaurer('importOK');\">":null)."$inscrits ".($inscrits>1 ? "nouvelles inscriptions" : "nouvelle inscription")." sur Esprit".($inscrits>0?"</a>":null).","
				."<br />".($affectes>0?"<a href=\"javascript: Cacher('importOK', 'importErreur'); Restaurer('importAvert');\">":null)."$affectes ".($affectes>1 ? "nouvelles affectations" : "nouvelle affectation")." &agrave; des formations".($affectes>0?"</a>":null).","
				."<br />".($erreurs>0?"<a href=\"javascript: Cacher('importOK', 'importAvert'); Restaurer('importErreur');\">":null)."$erreurs ".($erreurs>1 ? "erreurs" : "erreur").($erreurs>0?"</a>":null).".</p>\n";
	}
	echo $sAfficherLog;
	echo "<p><a href=\"$_SERVER[PHP_SELF]\">Revenir à la page précédente</a></p>\n</body>\n</html>\n";
	exit();
}

// Afficher le formulaire d'importation
?><html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php inserer_feuille_style("commun/dialog.css; admin/personnes.css"); ?>
<script type="text/javascript" language="javascript" src="<?php echo dir_javascript("window.js"); ?>"></script>
</head>
<body class="profil">
<h1>Inscription groupée</h1>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="fichier" method="POST" enctype="multipart/form-data">
<p>
<label for="importFile">Sélectionner le fichier contenant la liste des étudiants à inscrire :</label><br />
<input type="file" name="importFile" size="40" /><br />
<div style="text-align:center"><button type="submit" name="importer" value="1">Importer</button></div>
</p>
</form>

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

