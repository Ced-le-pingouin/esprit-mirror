<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr"
 id="fixes">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Navigateur de fichiers</title>
<link rel="stylesheet" type="text/css" href="theme://styles/NavigateurFichiers.css" />
<script type="text/javascript" src="theme://scripts/insFermer.js"></script>
<script type="text/javascript" src="racine://include/nav_fichiers/NavigateurFichiers.js"></script>
</head>
<body>

<div id="contenuPrincipal">
<div class="erreurs">[erreurs+][erreurs-]</div>
<div class="erreurs">
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
[erreurFichiersProteges+]<p>Certains fichiers ou dossiers n'ont pas été pris en compte pour l'opération 
demandée, car leur nom a une signification spéciale pour la plate-forme Esprit: 
activ_..., chatlog, forum, html.php, ressources, rubriques, et tableaudebord.csv<br />
Toutefois, si l'opération impliquait plusieurs fichiers, ceux non concernés par cette remarque ont 
bien été traités</p>[erreurFichiersProteges-]
</div>

[pasErreur+]
<div id="cadreNavFichiers">
<form name="formNavFichiers" action="" method="post" enctype="multipart/form-data">

<div class="barreOutils">
<div id="actionsFichiers">
<input type="submit" name="copier" value="Copier" title="copier les éléments cochés dans le presse-papiers" />
<input type="submit" name="couper" value="Couper" title="couper les éléments cochés dans le presse-papiers" />
<input type="submit" name="supprimer" value="Supprimer" title="supprimer les éléments cochés" />
</div><!--actionsFichiers-->

<div id="actionsPressePapiers">
<input type="submit" name="coller" value="Coller" title="coller le contenu du presse-papiers dans le dossier courant" />
<input type="submit" name="viderPressePapiers" value="Vider" title="vider le presse-papiers" />
</div><!--actionsPressePapiers-->

<div id="actionsCreer">
<label for="nomDossierACreerId">Créer un dossier:</label>
<input type="text" name="nomDossierACreer" id="nomDossierACreerId" value="" />
<input type="submit" name="creerDossier" value="Créer" />
</div><!--actionsCreer-->

<div id="actionsDeposer">
<!--<input type="hidden" name="MAX_FILE_SIZE" value="80000000" />-->
<label for="fichierDeposeId">Déposer un fichier:</label>
<input type="file" name="fichierDepose" id="fichierDeposeId" />
<input type="submit" name="deposer" value="Déposer" />
<input type="checkbox" name="dezipperFichierDepose" value="1" id="dezipperFichierDeposeId" />
<label for="dezipperFichierDeposeId">dézipper si fichier .zip</label>
</div><!--actionsDeposer-->
</div><!-- barreOutils -->

<div id="cadreArborescence">
<h3>Dossiers</h3>
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
<h3>Contenu du dossier <em>{g:dossierCourant}</em></h3>
<div class="listeFichiers">
<ul>
  [liste_contenu+]<li>
    <input type="checkbox" name="fichiers[]" id="{fichier.id}" value="{fichier.cheminComplet}" />
    [lc_normal+]
    <label for="{fichier.id}" class="fichier">{fichier.nom}</label>
    <span class="ligneOutils">
    [lc_btn_tel+]<input type="submit" name="telecharger[{fichier.nom}]" value="Télécharger" />[lc_btn_tel-]
    [lc_btn_ren+]<input type="submit" name="renommer[{fichier.nom}]" value="Renommer" />[lc_btn_ren-]
    </span>
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

<div id="cadrePressePapiers">
<h3>Contenu du presse-papiers</h3>
<table border="1">
[pp_element+]<tr><td>{pp.fichier}</td><td>{pp.action}</td></tr>[pp_element-]
</table>
</div> <!-- cadrePressePapiers -->

</form>
</div> <!-- cadreNavFichiers -->
[pasErreur-]
</div><!-- contenuPrincipal -->

</body>
</html>