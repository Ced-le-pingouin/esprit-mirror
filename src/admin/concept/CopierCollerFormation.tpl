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
<form name="formCopierCollerFormation" action="" method="post">

<div id="cadreCopier">
<label for="idFormationSrcId">Copier de la formation &lt;</label>
<select name="idFormationSrc" id="idFormationSrcId">
  [formSrc_liste+]
  <option value="{formation.id}">{formation.titre}</option>
  [formSrc_liste-]
</select>
<input type="submit" name="changerFormationSrc" value="Choisir"
 title="Choisir la formation source de la copie" />
<table border="1">
  [formSrc_branche+]<tr>
  	<th>{branche.niv} {branche.type}</th>
    <td>
      <input type="checkbox" name="branchesSrcSel[]" id="{branche.id}"
       value="{branche.val}" />
      <label for="{branche.id}">{branche.titre}</label>
    </td>
  </tr>[formSrc_branche-]
</table>
<input type="submit" name="copier" value="Copier"
 title="copier les éléments cochés vers le presse-papiers" />
</div><!--cadreCopier-->

<div id="cadreColler">
<label for="idFormationDestId">Copier dans la formation &gt;</label>
<select name="idFormationDest" id="idFormationDestId">
  [formDest_liste+]
  <option value="{formation.id}">{formation.titre}</option>
  [formDest_liste-]
</select>
<input type="submit" name="changerFormationDest" value="Choisir"
 title="Choisir la formation cible de la copie" />
<table border="1">
  [formDest_branche+]<tr>
  	<th>{branche.niv} {branche.type}</th>
    <td>
      <input type="radio" name="brancheDestSel" id="{branche.id}"
       value="{branche.val}" />
      <label for="{branche.id}">{branche.titre}</label>
    </td>
  </tr>[formDest_branche-]
</table>
<input type="submit" name="coller" value="Coller"
 title="coller les éléments sélectionnés du presse-papiers vers la formation cible" />
</div><!--cadreColler-->

<div id="cadrePressePapiers">
<h3>Contenu du presse-papiers</h3>
<table border="1">
  [pp_element+]<tr>
    <th>{pp.niv} {pp.type}</th>
    <td>
      <input type="radio" name="elemPpSel" id="{pp.id}"
       value="{pp.val}" />
      <label for="{pp.id}">{pp.titre}</label>
    </td>
  </tr>[pp_element-]
</table>
<input type="submit" name="supprimerElemPp" value="Supprimer"
 title="supprimer les éléments cochés du presse-papiers" />
<input type="submit" name="viderPp" value="Vider"
 title="vider complètement presse-papiers" />
</div><!-- cadrePressePapiers -->

</form>
[pasErreur-]

</div><!-- contenuPrincipal -->

</body>
</html>