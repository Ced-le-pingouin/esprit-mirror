<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Scores</title>
<style type="text/css">
td { padding: 3px 1ex; }
</style>
</head>
<body>
<h1>Tableau des scores</h1>
<h2>Exercice Hotpotatoes : <em><?php echo $oSousActiv->oHotpotatoes->retTitre(); ?></em></h2>
<h3>Etudiant : <em><?php echo $oEtudiant->retNomComplet(); ?></em></h3>
<table border="1">
	<thead>
		<tr><td>Date de début de l'exercice</td><td>Date de soumission</td><td>Exercice fini</td><td>Score</td></tr>
	</thead>
	<tbody>
<?php
foreach ($oHotpotScores as $oScore) {
	echo '<tr>'
		.'<td>'.retDateFormatter($oScore->retDateDebut(),'d/m/Y H:i').'</td>'
		.'<td>'.retDateFormatter($oScore->retDateModif(),'d/m/Y H:i').'</td>'
		.'<td>'.($oScore->retFini() ? "Fini" : "").'</td>'
		.'<td>'.$oScore->retScore().'</td>'
		.'</tr>';
}
?>
	</tbody>
</table>
</body>
</html>
