<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr"
 id="fixes">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Copier/Coller dans les formations</title>
<link rel="stylesheet" type="text/css" 
 href="theme://styles/CopierCollerFormation.css" />
<script type="text/javascript" src="theme://scripts/insFermer.js"></script>
<script type="text/javascript" src="CopierCollerFormation.js"></script>
</head>
<body>

<div id="contenuPrincipal">

<div class="erreurs">[erreurs+][erreurs-]</div>

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
<div class="cadreScrollable"><table border="1">
  [formSrc_branche+]<tr>
  	<th>{branche.niv} {branche.type}</th>
    <td class="niv{branche.numNiv}">
      <input type="checkbox" name="branchesSrcSel[]" id="{branche.id}"
       value="{branche.val}" />
      <label for="{branche.id}">{branche.titre}</label>
    </td>
  </tr>[formSrc_branche-]
</table></div><!--cadreScrollable-->
<input type="submit" name="copier" id="copier" value="Copier" class="lien"
 title="copier les éléments cochés vers le presse-papiers" />
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
<div class="cadreScrollable"><table border="1">
  [formDest_branche+]<tr>
  	<th>{branche.niv} {branche.type}</th>
    <td class="niv{branche.numNiv}">
      <input type="radio" name="brancheDestSel" id="{branche.id}"
       value="{branche.val}" />
      <label for="{branche.id}">{branche.titre}</label>
    </td>
  </tr>[formDest_branche-]
</table></div><!--cadreScrollable-->
<input type="submit" name="coller" id="coller" value="Coller" class="lien"
 title="coller les éléments sélectionnés du presse-papiers vers la formation cible" />
</div><!--cadreColler-->

<div id="cadrePressePapiers">
<h3>Presse-papiers</h3>
<div class="cadreScrollable"><table border="1">
  [pp_element+]<tr>
    <th>{pp.niv} {pp.type}</th>
    <td>
      <input type="radio" name="elemPpSel" id="{pp.id}"
       value="{pp.val}" />
      <label for="{pp.id}">{pp.titre}</label>
    </td>
  </tr>[pp_element-]
</table></div><!--cadreScrollable-->
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