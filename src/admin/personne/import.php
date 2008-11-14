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

$oProjet = new CProjet();

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
// $tab[1..8] : nom, prénom, pseudo, mdp, email, sexe, date de naissance, numero de formation
	$sIdFormation = trim ($tab[8]);
	$oPersonne = new CPersonne($oProjet->oBdd);
	
	if (empty($tab[1]))
		return false;
	$oPersonne->defNom(addslashes(mb_strtoupper($tab[1], "utf-8")));
	
	if (empty($tab[2]))
		return false;
	$oPersonne->defPrenom($tab[2]);

	if (empty($tab[3]))
		return "Pas de pseudo.";
	$oPersonne->defPseudo($tab[3]);
	
	if ((!defined('UNICITE_NOM_PRENOM') || UNICITE_NOM_PRENOM===TRUE) && !$oPersonne->estUnique())
	{
		$sMessage = $oPersonne->retNom()." ".$oPersonne->retPrenom()." est d&eacute;j&agrave; inscrit sur Esprit!<br>";
		// le couple est deja dans la DB, on ajoute juste le numero de formation si le champs est rempli
		$sMessage .= $oPersonne->lierPersForm($sIdFormation);
		return $sMessage;
	}

	if (!$oPersonne->estPseudoUnique()) {
		$a = '';
		$hResult = $oProjet->oBdd->executerRequete(
			"SELECT CONCAT(Nom,' ',Prenom) AS NomC FROM Personne "
			. "WHERE Pseudo='". $tab[3] ."'"
			);
		if ($oEnreg = $oProjet->oBdd->retEnregSuiv($hResult))
			$a = ' à "' . $oEnreg->NomC .'"';
		return "Le pseudo '".$tab[3]."' est déjà attribué$a.";
	}
	
	$sMdp = trim($tab[4]);
	if (preg_match('/[^a-zA-Z0-9]/',$sMdp))
		return "Le mot de passe <em>$sMdp</em> doit être alpha-numérique.";
	else
		$oPersonne->defMdp($oProjet->retMdpCrypte($sMdp));

	if (empty($tab[5]))
		return "Pas d'adresse e-mail alors que ce champ est obligatoire.";
	$oPersonne->defEmail($tab[5]);

	if (!$tab[6])
		$sexe = "M";
	$oPersonne->defSexe($tab[6]);

	// ajout de la date de naissance en colonne 7
	// Date de naissance (format: AAAA-MM-JJ)
	if (empty($tab[7]))
		return "La date de naissance est obligatoire.";
	$sDateNaissanceTemp = array_reverse(explode('/',$tab[7]));
	$sDateNaissance = $sDateNaissanceTemp[0]."-".$sDateNaissanceTemp[1]."-".$sDateNaissanceTemp[2];
	$oPersonne->defDateNaiss($sDateNaissance);
	
	if ($enreg) {
		$oPersonne->enregistrer();
		$oPersonne->lierPersForm($sIdFormation);
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
</script>
</head>
<body class=\"profil\">
<h1>Inscription groupée</h1>";
	echo "\n<ol>";
	$inscrits = $affectes = $erreurs =0;
	$total=0;
	
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

		if ($data->sheets[0]['cells'][$nrow][8] && preg_match('/[0-9]/',$data->sheets[0]['cells'][$nrow][9]))
		{
			$sIdFormation = $data->sheets[0]['cells'][$nrow][8];
			$oFormation = new CFormation($oProjet->oBdd,$sIdFormation);
			$sNomFormation = $oFormation->retNom();
		}

		$total++;
		$res = insererPersonne($data->sheets[0]['cells'][$nrow]);
		
		if ($res===true) {
			// tout va bien
			if ($sIdFormation!="" && $sNomFormation!="") {
				$sMessage = " et ajout&eacute; &agrave; la formation '<em>".$sNomFormation."</em>'!</span>";
				$affectes++;
			}
			$inscrits++;
			echo "<br/><li><span class=\"importOK\">OK</span> : <em>$prenom $nom</em> a été inscrit".$sMessage.".</li>\n";
			// ...
		}
		elseif (($sIdFormation!="") && !preg_match('/Cette personne existe/', $res)) {
			// Un avertissement lors de l'inscription : la personne est deja inscrite, mais ajoutee a une formation.
			echo "<br/><li><span class=\"importAvert\">Avertissement!</span> ".$res."</li>\n";
			$affectes++;
			// ...
		}
		else {
			// Un pb avec cette inscription
			echo "<br/><li><span class=\"importErreur\">Erreur!</span> ".$res."</li>\n";
			$erreurs++;
			// ...
		}
	}
	echo "</ol>\n";
	if ($total) {
			echo "<p>Sur un total de $total ".($total>1 ? "inscriptions" : "inscription")
				." :<br />$inscrits ".($inscrits>1 ? "nouvelles inscriptions" : "nouvelle inscription")." sur Esprit;"
				."<br />$affectes ".($affectes>1 ? "nouvelles affectations" : "nouvelle affectation")." &agrave; des formations;"
				."<br />$erreurs ".($erreurs>1 ? "erreurs" : "erreur").".</p>\n";
	}
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

