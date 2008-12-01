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
** Description ............: Importation d'une liste d'√©tudiants (format Excel)
** Date de cr√©ation .......: 12/12/2006
*/

require_once("globals.inc.php");
include_once(dir_code_lib("mail.class.php"));

$oProjet	= new CProjet();
$oPersonne	= new CPersonne($oProjet->oBdd);

$url_bCopieCourrier		= (empty($_POST["envoiMail"]) ? false : $_POST["envoiMail"]);

// ---------------------
// D√©claration des fonctions locales
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
// $tab[1..8] : nom, pr√©nom, pseudo, mdp, email, sexe, date de naissance, numero de formation
	if (empty($tab[1]))
		return false;
	$oPersonne->defNom(addslashes(mb_strtoupper($tab[1], "utf-8")));
	
	if (empty($tab[2]))
		return false;
	$oPersonne->defPrenom($tab[2]);

	if (empty($tab[3]))
		return "<span class=\"importErreur\">Erreur!</span> Pas de pseudo.";
	$oPersonne->defPseudo($tab[3]);
	
	$sIdFormation = trim ($tab[8]);
	if (!empty($sIdFormation) && !preg_match('/[0-9]/',$sIdFormation))
		return "<span class=\"importErreur\">Erreur!</span> Le num&eacute;ro de formation doit &ecirc;tre num&eacute;rique.";

	// le couple est deja dans la DB, on ajoute juste le numero de formation si le champs est rempli
	if ((!defined('UNICITE_NOM_PRENOM') || UNICITE_NOM_PRENOM===TRUE) && !$oPersonne->estUnique())
	{	
		// le numÈro de formation est nul -> on affiche un message d'erreur, sinon juste un avertissement
		if 	($sIdFormation == null) $sMessage = "<span class=\"importErreur\">Erreur!</span> ";
		
		$sMessage .= $oPersonne->retPrenom()." ".$oPersonne->retNom()." est d&eacute;j&agrave; inscrit sur Esprit!<br />";
		$sMessage .= $oPersonne->lierPersForm($sIdFormation);
	}

$sPrenomNom = $oPersonne->retPrenom()." ".$oPersonne->retNom();

	if ($sMessage==null)
	{
		if (!$oPersonne->estPseudoUnique() && $sMessage==null) {
			$a = '';
			$hResult = $oProjet->oBdd->executerRequete(
				"SELECT CONCAT(Nom,' ',Prenom) AS NomC FROM Personne "
				. "WHERE Pseudo='". $tab[3] ."'"
				);
			if ($oEnreg = $oProjet->oBdd->retEnregSuiv($hResult))
				$a = ' √† "' . $oEnreg->NomC .'"';
			return "<span class=\"importErreur\">Erreur!</span> Le pseudo '".$tab[3]."' est d√©j√† attribu√©$a.";
		}
	
		$sMdp = trim($tab[4]);
		if (preg_match('/[^a-zA-Z0-9]/',$sMdp))
			return "<span class=\"importErreur\">Erreur!</span> ".$sPrenomNom.". Le mot de passe <em>$sMdp</em> doit √™tre alpha-num√©rique.";
		else
			$oPersonne->defMdp($oProjet->retMdpCrypte($sMdp));

		if (empty($tab[5]))
			return "<span class=\"importErreur\">Erreur!</span> ".$sPrenomNom.". Pas d'adresse e-mail alors que ce champ est obligatoire.";
		$oPersonne->defEmail($tab[5]);

		if (!$tab[6])
			$sexe = "M";
		$oPersonne->defSexe($tab[6]);

		// ajout de la date de naissance en colonne 7
		// Date de naissance (format: AAAA-MM-JJ)
		if (empty($tab[7]))
			return "<span class=\"importErreur\">Erreur!</span> ".$sPrenomNom.". La date de naissance est obligatoire.";
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
</script>
</head>
<body class=\"profil\">
<h1>Inscription group&eacute;e</h1>";
	$sAfficherLog = "\n<ol>";
	$inscrits = $avertissements = $erreurs = $inscritsAffectes = 0;
	$total=0;
	$sSujetCourriel = null;
	
// print_r($data->sheets);
	for ($nrow=6; $nrow<$data->sheets[0]['numRows']; $nrow++) {
		// colonnes 1 √† 8, √† partir de la ligne 6
		// nom, pr√©nom, pseudo, mdp, email, sexe, date de naissance, numero de formation
		if (empty($data->sheets[0]['cells'][$nrow][1]) or empty($data->sheets[0]['cells'][$nrow][2]))
			continue;
		$nom = mb_strtoupper($data->sheets[0]['cells'][$nrow][1], "utf-8");
		$prenom = $data->sheets[0]['cells'][$nrow][2];
		$sPseudo = $oPersonne->retPseudo();
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
		
		$sMessageCourrielTexte = "Bonjour,\r\n\r\n"
			."Ce mail vous informe que vous avez bien ÈtÈ inscrit(e) ‡ la formation\r\n"
			."'$sNomFormation'\r\n"
			."accessible sur Esprit (http://flodi.grenet.fr/esprit).\r\n\r\n"
			."Pour accÈder ‡ l'espace rÈservÈ ‡ votre formation sur Esprit,\r\n"
			."introduisez le pseudo et le mot de passe (mdp) (en respectant scrupuleusement,\r\n"
			."les majuscules, minuscules, caractËres accentuÈs et espaces Èventuels) et\r\n"
			."cliquez sur Ok.\r\n\r\n"
			."Votre pseudo est : $sPseudo\r\n"
			."Votre mot de passe est : $sMdp\r\n\r\n"
			."Astuces :\r\n\r\n"
			."		* AprËs connexion, vous pouvez modifier votre pseudo et mot de passe dans le\r\n"
			."		profil (cliquer sur le lien \"Profil\" en bas de l'Ècran)\r\n\r\n"
    		."		* Si, un jour, vous oubliez votre pseudo et/ou votre mot de passe,\r\n"
    		."		cliquez sur le lien \"OubliÈ ?\". Ce lien se trouve juste au-dessus de la zone\r\n"
    		."		\"Pseudo\", au niveau de la page d'accueil d'Esprit\r\n"
    		."		(http://flodi.grenet.fr/esprit).\r\n"
    		."		Ceci vous permettra de rÈcupÈrer ces informations par courriel.\r\n\r\n"
    		."Bonne formation,\r\n\r\nPour l'Èquipe Esprit,\r\n\r\n$oProjet->oUtilisateur->retPrenom() $oProjet->oUtilisateur->retNom()";

		if ($res===true) {
			// tout va bien
			if ($sIdFormation!="" && $sNomFormation!="") {
				$sMessage = " et ajout&eacute; &agrave; la formation '<em>".$sNomFormation."</em>'!</span>";

				// on envoie un mail aux nouvelles personnes inscrites dans une formation
				if ($url_bCopieCourrier)
				{
					$oMail = new CMail($sSujetCourriel,$sMessageCourriel,$tab[5],$nom.$prenom);
					$oMail->defExpediteur($oProjet->oUtilisateur->retEmail(),$oProjet->oUtilisateur->retPrenom()." ".$oProjet->oUtilisateur->retNom());
					$oMail->envoyer();
				}
				
				$inscritsAffectes++;
			}
			$inscrits++;
			$sAfficherLog .= "<li name=\"listeOK\" id=\"listeOK\"><span class=\"importOK\">OK</span> : <em>$prenom $nom</em> a √©t√© inscrit".$sMessage.".</li>";
			// ...
		}
		elseif (($sIdFormation!="") && !preg_match('/importErreur/',$res)) {
			// Un avertissement lors de l'inscription : la personne est deja inscrite, mais ajoutÈe ‡ une formation.
			$sAfficherLog .= "<li name=\"listeAvert\" id=\"listeAvert\"><span class=\"importAvert\">Avertissement!</span> ".$res."</li>";

			// on envoie un mail aux personnes ajoutÈes ‡ la formation
			if ($url_bCopieCourrier)
			{
				$oMail = new CMail($sSujetCourriel,$sMessageCourriel,$tab[5],$nom.$prenom);
				$oMail->defExpediteur($oProjet->retEmail(), $oProjet->retNom());
				$oMail->envoyer();
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
			echo "<p>Sur un total de $total ".($total>1 ? "inscriptions" : "inscription")." :</p>"
				."<p><a href=\"javascript: Restaurer('listeOK');Restaurer('listeAvert');Restaurer('listeErreur');\">Tout afficher</a></p>"
				."<p  class=\"typeA\">"
				.($inscrits>0?"<a href=\"javascript: Cacher('listeAvert', 'listeErreur'); Restaurer('listeOK');\">":null)."$inscrits ".($inscrits>1 ? "nouvelles inscriptions" : "nouvelle inscription")." sur Esprit".($inscrits>0?"</a>":null)
				." (dont $inscritsAffectes ".($inscritsAffectes>1 ? "nouvelles affectations" : "nouvelle affectation")."),"
				."<br />".($avertissements>0?"<a href=\"javascript: Cacher('listeOK', 'listeErreur'); Restaurer('listeAvert');\">":null)."$avertissements ".($avertissements>1 ? "avertissements" : "avertissements").($avertissements>0?"</a>":null)
				." (dont $avertissements ".($avertissements>1 ? "nouvelles affectations" : "nouvelle affectation")."),"
				."<br />".($erreurs>0?"<a href=\"javascript: Cacher('listeOK', 'listeAvert'); Restaurer('listeErreur');\">":null)."$erreurs ".($erreurs>1 ? "erreurs" : "erreur").($erreurs>0?"</a>":null).".</p>\n";
	}
	echo $sAfficherLog;
	echo "<p><a href=\"$_SERVER[PHP_SELF]\">Revenir √† la page pr√©c√©dente</a></p>\n</body>\n</html>\n";
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
<h1>Inscription group√©e</h1>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="fichier" method="POST" enctype="multipart/form-data">
<p>
<label for="importFile">S√©lectionner le fichier contenant la liste des √©tudiants √† inscrire :</label><br />
<input type="file" name="importFile" size="40" /><br />
<div style="text-align:center"><button type="submit" name="importer" value="1">Importer</button></div>
<div>&nbsp;</div>
<div>
<input type="checkbox" name="envoiMail" id="copieCourriel" value="1" checked>
<label class="afficher_curseur_aide" for="copieCourriel">Envoyer un mail &agrave; toutes les nouvelles personnes inscrites sur Esprit et &agrave; celles ajout&eacute;es dans une formation.</label></div>
</p>
</form>

<hr>

<h1>Fichier de mod√®le</h1>
T√©l√©charger le mod√®le de feuille de tableur
<ul>
  <li>au format <a href="esprit_inscriptions.xls">Excel</a></li>
<?php /*
       * <li>au format <a href="esprit_inscriptions.csv">CSV</a></li>
       * BUGS IN PARSER <li>au format <a href="esprit_inscriptions.ods">ODS</a></li> */ ?>
</ul>
<p>Attention √† ne pas modifier les <strong>5 premi√®res lignes</strong> de ces mod√®les.</p>
<!-- <p>Le fichier CSV doit utiliser le jeu de caract√®res <em>UTF-8</em>. Si les accents des permi√®res lignes s'affichent mal, ce n'est pas le cas.</p> -->
</body>
</html>

