<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Navigateur de fichiers</title>
<link rel="stylesheet" type="text/css" href="NavigateurFichiers.css" />
</head>
<body>

<div id="erreurs">
[erreurDossierRacine+]<p>Accès au dossier racine impossible</p>[erreurDossierRacine-]
[erreurFichiersSel+]<p>Aucun fichier ou dossier n'a été sélectionné comme cible de l'opération</p>[erreurFichiersSel-]
[erreurPressePapiersVide+]<p>L'opération "coller" est impossible: le presse-papiers est vide</p>[erreurPressePapiersVide-]
[erreurDossierACreerVide+]<p>Le nom du dossier à créer ne peut être vide</p>[erreurDossierACreerVide-]
[erreurFichierARenommer+]<p>Accès au fichier ou dossier à renommer impossible</p>[erreurFichierARenommer-]
[erreurFichierRenomme+]<p>Le nom du fichier ou dossier renommé existe déjà</p>[erreurFichierRenomme-]
[erreurTelechargement+]<p>Une erreur s'est produite pendant le téléchargement</p>[erreurTelechargement-]
[erreurTelechargementInterdit+]<p>Ce fichier ne peut être téléchargé</p>[erreurTelechargementInterdit-]
[erreurDeposer+]<p>Une erreur s'est produite lors du dépôt de fichier</p>[erreurDeposer-]
[erreurDezip+]<p>Une erreur s'est produite lors du dézippage du fichier</p>[erreurDezip-]
</div>

[pasErreur+]
<div id="cadreNavFichiers">

<div id="cadreActionsSuppl">
<form name="formNavFichiersCreerDossier" action="" method="post"> <!--enctype="multipart/form-data"-->
<label for="nomDossierACreerId">Créer un dossier:</label>
<input type="text" name="nomDossierACreer" id="nomDossierACreerId" value="" />
<input type="submit" name="creerDossier" value="Créer" />
</form>
<form name="formNavFichiersDeposer" action="" method="post" enctype="multipart/form-data">
<!--<input type="hidden" name="MAX_FILE_SIZE" value="80000000" />-->
<label for="fichierDeposeId">Déposer un fichier:</label>
<input type="file" name="fichierDepose" id="fichierDeposeId" />
<input type="submit" name="deposer" value="Déposer" />
<input type="checkbox" name="dezipperFichierDepose" value="1" id="dezipperFichierDeposeId" />
<label for="dezipperFichierDeposeId">dézipper si fichier .zip</label>
</form>
</div><!-- cadreActionsSuppl -->

<form name="formNavFichiers" action="" method="post">

<div id="cadreActions">
<input type="submit" name="copier" value="Copier" />
<input type="submit" name="couper" value="Couper" />
<input type="submit" name="coller" value="Coller" />
<input type="submit" name="supprimer" value="Supprimer" />
</div><!-- cadreActions -->

<div id="cadreNavCentrale">
<div id="cadreArborescence">
<h3>Arborescence</h3>
<div class="listeFichiers">
[liste_dossiers+]
<ul>
  [liste_dossiers_el+]<li>
    <a href="?r={g:racine}&d={dossier.url}" class="dossier">{dossier.nom}</a>
    [@liste_dossiers]
  </li>[liste_dossiers_el-]
</ul>
[liste_dossiers-]
</div><!-- listeFichiers -->
</div><!-- cadreArborescence -->

<div id="cadreContenu">
<h3>Contenu</h3>
<div class="listeFichiers">
<ul>
  [liste_contenu+]<li>
    <input type="checkbox" name="fichiers[]" id="{fichier.id}" value="{fichier.cheminComplet}" />
    [lc_normal+]
    <label for="{fichier.id}" class="fichier">{fichier.nom}</label>
    [lc_btn_ren+]<input type="submit" name="renommer[{fichier.nom}]" value="Renommer" />[lc_btn_ren-]
    [lc_btn_tel+]<input type="submit" name="telecharger[{fichier.nom}]" value="Télécharger" />[lc_btn_tel-]
    [lc_normal-]
    [lc_ren+]
    <input type="text" name="fichierRenomme" value="{fichier.nom}" class="fichier" />
    <input type="submit" name="renommer[{fichier.nom}]" value="Ok" />
    <input type="submit" name="annuler" value="Annuler" />
    [lc_ren-]
  </li>[liste_contenu-]
</ul>
</div><!-- listeFichiers -->
</div><!-- cadreContenu -->
</div><!-- cadreNavCentrale -->

</form>

<div id="cadrePressePapiers">
<form name="formNavFichiersPressePapiers" action="" method="post">
<h3>Presse-papiers</h3>
<table border="1">
<tr><th>Fichier</th><th>Action</th></tr>
[pp_element+]<tr><td>{pp.fichier}</td><td>{pp.action}</td></tr>[pp_element-]
</table>
<input type="submit" name="viderPressePapiers" value="Vider" />
</form>
</div> <!-- cadrePressePapiers -->

</div> <!-- cadreNavFichiers -->
[pasErreur-]

</body>
</html>