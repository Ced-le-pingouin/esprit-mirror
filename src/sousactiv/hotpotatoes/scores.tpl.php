<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Scores</title>
<style type="text/css">
html { font-family: Arial; }
table { empty-cells: hide; border-collapse: collapse; background-color: white; border: 2px solid black; }
th, td { padding: 3px 1.5ex; border: 1px solid black; border-top: 1px dotted black; border-bottom: 1px dotted black; }
td.score { text-align: right; }
th { background-color: rgb(208,208,191); border-bottom: 2px solid black; }
tr.fini td { background-color: rgb(251,249,238); border-bottom: 2px solid black; }
tr.fini td.date { background-color: white; }
tr.suite td.date { border-top: hidden; }
tr:hover,
tr.fini:hover td { background-color: rgb(238,234,221); }
</style>
</head>
<body>
<h1>Tableau des scores</h1>
<h2>Exercice Hotpotatoes : <em><?php echo $oSousActiv->oHotpotatoes->retTitre(); ?></em></h2>
<h3>Etudiant : <em><?php echo $oEtudiant->retNomComplet(); ?></em></h3>
<?php
if (empty($oHotpotScores)) {
	echo "<p>Aucun score Hotpotatoes.</p>";
} else {
?>
<table border="1">
	<thead>
		<tr><th>Date de début de l'exercice</th><th>Durée</th><th>Score</th></tr>
	</thead>
	<tbody>
<?php
$derDate = 0;
foreach ($oHotpotScores as $oScore) {
	$suite = ($oScore->retDateDebut()===$derDate ? true : false);
	echo '<tr>'
		.'<td class="date">'.( $suite ? '' : date('d/m/Y H:i',$oScore->retDateInitiale()) ).'</td>'
		.'<td>'.$oScore->retDuree().'</td>'
		.'<td class="score">'.$oScore->retScore().' %</td>'
		.'</tr>';
	$derDate = $oScore->retDateDebut();
}
?>
	</tbody>
<?php } ?>
</table>
</body>
</html>
