<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr"
 id="fixes">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Copier/coller</title>
<link rel="stylesheet" type="text/css" 
 href="theme://styles/CopierCollerFormation.css" />
<script type="text/javascript" src="theme://scripts/fenetreErreurs.js"></script>
<script type="text/javascript" src="theme://scripts/insFermer.js"></script>
<script type="text/javascript" src="CopierCollerFormation.js"></script>
</head>
<body>
<div id="contenuPrincipal">

<div class="erreurs" id="erreurs">[erreurs+][erreurs-]</div>

[pasErreur+]
<div id="cadreOnglets">
<form name="formCopierCollerFormation" action="" method="post">
<input type="hidden" name="ongletCourant" id="ongletCourant"
 value ="{ongletCourant}" />

<div id="cadreCopier">
<h3>
<label for="idFormationSrcId">Copier de la formation&nbsp;:</label>
<select name="idFormationSrc" id="idFormationSrcId">
  [formSrc_liste+]
  <option value="{formation.id}">{formation.titre}</option>
  [formSrc_liste-]
</select>
<input type="submit" name="changerFormationSrc" value="Choisir" class="bouton"
 title="Choisir la formation source de la copie" />
</h3>
<div class="cadreScrollable">
[formSrc_branche+]<ul>
  [formSrc_branche_el+]<li>
    <span>
    <em>{branche.symbole}</em>
  	<input type="checkbox" name="branchesSrcSel[]" id="{branche.id}"
     value="{branche.val}" />
      <label for="{branche.id}"><strong>{branche.intitule}</strong> {branche.titre}</label>
    </span>
      [@formSrc_branche]
  </li>[formSrc_branche_el-]
</ul>[formSrc_branche-]
</div><!--cadreScrollable-->
<input type="submit" name="copier" id="copier" value="Copier" class="lien"
 title="copier les éléments cochés vers le presse-papiers" />
<input type="submit" name="collerDesactive" id="collerDesactive"
 value="Coller (après)" class="lienDesactive" disabled="disabled" />
<sup class="renvoi">*</sup>
<input type="submit" name="supprimerCopier" id="supprimerCopier"
 value="Supprimer" class="lienDesactive" disabled="disabled" />
</div><!--cadreCopier-->

<div id="cadreColler">
<h3>
<label for="idFormationDestId">Coller dans la formation&nbsp;:</label>
<select name="idFormationDest" id="idFormationDestId">
  [formDest_liste+]
  <option value="{formation.id}">{formation.titre}</option>
  [formDest_liste-]
</select>
<input type="submit" name="changerFormationDest" value="Choisir" class="bouton"
 title="Choisir la formation cible de la copie" />
</h3>
<div class="cadreScrollable">
[formDest_branche+]<ul>
  [formDest_branche_el+]<li>
    <span>
    <em>{branche.symbole}</em>
    <input type="radio" name="brancheDestSel" id="{branche.id}"
     value="{branche.val}" />
      <label for="{branche.id}"><strong>{branche.intitule}</strong> {branche.titre}</label>
    </span>
      [@formDest_branche]
  </li>[formDest_branche_el-]
</ul>[formDest_branche-]
</div><!--cadreScrollable-->
<input type="submit" name="copierDesactive" id="copierDesactive"
 value="Copier" class="lienDesactive" disabled="disabled" />
<input type="submit" name="coller" id="coller" value="Coller (après)" class="lien"
 title="coller les éléments sélectionnés du presse-papiers vers la formation cible" />
<sup class="renvoi">*</sup>
<input type="submit" name="supprimerColler" id="supprimerColler"
 value="Supprimer" class="lien" title="Supprimer l'élément sélectionné" />
<p class="renvoi">(*) Au niveau approprié (un cours après un cours, une unité 
après une unité, etc.)</p>
</div><!--cadreColler-->

<div id="cadrePressePapiers">
<h3>Presse-papiers</h3>
<div class="cadreScrollable">
<ul>
  [pp_element+]<li class="niv{pp.numNiv}">
    <span>
    <em>{pp.symbole}</em>
    <input type="radio" name="elemPpSel" id="{pp.id}" value="{pp.val}" />
    <label for="{pp.id}"><strong>{pp.intitule}</strong> {pp.titre}</label>
    </span>
  </li>[pp_element-]
</ul>
</div><!--cadreScrollable-->
<input type="submit" name="supprimerElemPp" id="supprimerElemPp" class="lien"
 value="Supprimer" title="supprimer les éléments cochés du presse-papiers" />
<input type="submit" name="viderPp" id="viderPp" value="Vider"
 title="vider complètement presse-papiers" class="lien" />
</div><!-- cadrePressePapiers -->

</form>
</div><!--cadreOnglets-->
[pasErreur-]

</div><!-- contenuPrincipal -->

</body>
</html>