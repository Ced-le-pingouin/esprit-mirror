<?php require_once("globals.inc.php"); ?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php echo inserer_feuille_style()?>
</head>
<body>
<form>
<table border="0" cellspacing="0" cellpadding="0">
<tr><td><input type="checkbox" name="CHAMPS[]" value="Nom" checked></td><td>Nom</td></tr>
<tr><td><input type="checkbox" name="CHAMPS[]" value="Prenom" checked></td><td>Pr&eacute;nom</td></tr>
<tr><td><input type="checkbox" name="CHAMPS[]" value="Pseudo" checked></td><td>Pseudo</td></tr>
<tr><td><input type="checkbox" name="CHAMPS[]" value="DateNaiss" checked></td><td>Date de naissance</td></tr>
<tr><td><input type="checkbox" name="CHAMPS[]" value="Sexe" checked></td><td>Sexe</td></tr>
<tr><td><input type="checkbox" name="CHAMPS[]" value="Adresse" checked></td><td>Adresse</td></tr>
<tr><td><input type="checkbox" name="CHAMPS[]" value="NumTel" checked></td><td>Num&eacute;ro de t&eacute;l&eacute;phone</td></tr>
<tr><td><input type="checkbox" name="CHAMPS[]" value="Email" checked></td><td>Mail</td></tr>
<tr><td><input type="checkbox" name="CHAMPS[]" value="UrlPerso" checked></td><td>URL personnel</td></tr>
<tr><td><input type="checkbox" name="CHAMPS[]" value="Mdp"></td><td>Mot de passe</td></tr>
</table>
</form>
</body>
</html>
