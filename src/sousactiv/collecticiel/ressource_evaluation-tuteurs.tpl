<html>
<head>
<link type="text/css" rel="stylesheet" href="theme://globals.css">
<link type="text/css" rel="stylesheet" href="theme://barre_outils.css">
<link type="text/css" rel="stylesheet" href="ressource_evaluation-tuteurs.css">
<link type="text/css" rel="stylesheet" href="theme://ressource_evaluation.css">
<script type="text/javascript" language="javascript">
<!--
var g_sIdTuteur = null;

function changer_tuteur(v_sParam,v_sIdTuteur)
{
	if (document.getElementById(v_sIdTuteur))
	{
		if (g_sIdTuteur != null)
			document.getElementById(g_sIdTuteur).className = "cellule_icone_normale";
		
		document.getElementById(v_sIdTuteur).className = "cellule_icone_surbrillante";
		
		g_sIdTuteur = v_sIdTuteur;
	}
	
	top.document.getElementsByName("Principale").item(0).setAttribute("src","ressource_evaluation.php" + v_sParam);
	
	return false;
}

function init()
{
	changer_tuteur("?idPers={tuteur.id}&idResSA={ressource.id}","id_tuteur_{tuteur.id}");
}
//-->
</script>
</head>
<body onload="init()">
<div id="tuteurs">Tuteurs&nbsp;:&nbsp;</div>
[BLOCK_TUTEUR+][BLOCK_SEPARATEUR_TUTEURS+]<div class="cellule_separateur">::</div>[BLOCK_SEPARATEUR_TUTEURS-]<div id="id_tuteur_{tuteur.id}" class="cellule_icone_normale"><a href="javascript: void(0);" onclick="return changer_tuteur('?idPers={tuteur.id}&amp;idResSA={ressource.id}','id_tuteur_{tuteur.id}')" title="Cliquer ici pour voir l'évaluation de ce tuteur" onfocus="blur()">{tuteur.nom}&nbsp;{tuteur.prenom}</a></div>[BLOCK_TUTEUR-]
</body>
</html>

