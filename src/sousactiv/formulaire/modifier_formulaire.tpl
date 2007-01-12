<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Activité en ligne</title>
<link type="text/css" rel="stylesheet" href="theme://formulaire/formulaire.css" />
<style type="text/css">
<!--
body
{
	margin: 0;
	padding: 45px 0 0 0;
}
form
{
	margin-left: {sLargeur};
	margin-right: {sLargeur};
	margin-bottom: 30px;
	margin-top: 20px;
	background-color: rgb(249,251,251);
	border: 1px dotted rgb(220,230,230);
	padding: 7px 5px;
	-moz-border-radius: 5px;
}
input, select
{
	margin: 0 5px;
}
.p  { line-height: 10.5pt; font-family: Arial,sans-serif; font-size: 10pt; color: black; margin-top: 6px; margin-bottom: 6px; }
.InterER  { margin-top: {iInterEnonRep}px; }
.InterObj  { margin-top: {iInterElem}px; }
.titre { font-size: 1.4em; font-weight: bold; }
#barremenu
{
	margin: 0;
	padding: 4px 0;
	text-align: center;
	border-top: rgb(0,0,0) solid 1px;
	background-color: rgb(174,165,138);
	position: fixed;
	bottom: 0;
	width: 100%;
}
* html #barremenu
{
	position: static;
}
a:link, a:active, a:visited
{
	font-size: 11px;
	color: rgb(255,255,255);
	text-decoration: none;
	font-weight: bold;
}
a:hover
{
	color: rgb(255,255,255);
	text-decoration: underline;
}
#Eval
{
	margin: 20px 0 0 0;
	padding: 0;
	width: 63%;
	border: rgb(222,230,230) 1px solid;
	background-color: rgb(250,250,250);
	float: left;
	-moz-border-radius-topright: 5px;
	-moz-border-radius-bottomright: 5px;
}
#Etat
{
	margin: 20px 0 0 0;
	padding: 0;
	width: 33%;
	border: rgb(222,230,230) 1px solid;
	background-color: rgb(250,250,250);
	float: right;
	-moz-border-radius-topleft: 5px;
	-moz-border-radius-bottomleft: 5px;
}
#Eval h3, #Etat h3
{
	margin: 0;
	padding: 3px;
	font-size: 12px;
	font-weight: bold;
	border-bottom: rgb(213,204,189) 1px solid;
	background-color: rgb(238,234,221);
	color: rgb(111,105,87);
}
#Eval p, #Etat p
{
	margin: 3px;
	padding: 0;
}
#entete h3
{
	font-size: 12px;
	font-weight: bold;
	margin: 0;
	padding: 5px;
	color: rgb(255,255,255);
}
#fermer
{
	margin: 0 10px;
}
.statut_ael
{
	color: rgb(255,151,50);
}
.separvert
{
	border-right:  rgb(213,204,189) 1px solid;
}
.separhori
{
	border-top:  rgb(213,204,189) 1px solid;
}
#tab_etat
{
	margin: 0;
	padding: 0;
	border-collapse: collapse;
	width: 100%;
}
#tab_etat tr
{
	margin: 0;
	padding: 0;
}
#tab_etat td
{
	margin: 0;
	padding: 5px;
}
.feedback
{
	display: none;
	border: 1px solid rgb(213,204,189);
	background-color: rgb(248,244,231);
	margin: 7px 0;
	padding: 0;
	height: 3.5em;
	overflow: auto;
	-moz-border-radius-topright: 3px;
	-moz-border-radius-bottomright: 3px;
}
.feedback_titre
{
	display: none;
	background-color: rgb(238,234,221);
	margin: 7px 0 0 0;
	float: left;
	padding: 0 3px 0 0;
	height: 3.5em;
	width: 100px;
	text-align: center;
	border: 1px solid rgb(213,204,189);
	border-right: none;
	-moz-border-radius-topleft: 3px;
	-moz-border-radius-bottomleft: 3px;
}
* html .feedback_titre
{
	margin-right: -3px;
}
.feedback p, .feedback_titre p
{
	margin: 3px;
}

-->
</style>
<script src="{formulaire_js}" type="text/javascript"></script>
<script src="{general_js_php}" type="text/javascript"></script>
<script type="text/javascript" language="javascript">
<!--

function GestionFeedback(idPropRep,aFermer) 
{
	if(document.getElementById("FB"+idPropRep).style.display == "block")
	{
		document.getElementById("FB"+idPropRep).style.display = "none";
		document.getElementById("FBT"+idPropRep).style.display = "none";
	}
	else
	{
		if(aFermer)
		{
			for (var i=0;i<aFermer.length;i++)
			{
				document.getElementById("FB"+aFermer[i]).style.display = "none";
				document.getElementById("FBT"+aFermer[i]).style.display = "none";
			}
		}
		document.getElementById("FB"+idPropRep).style.display = "block";
		document.getElementById("FBT"+idPropRep).style.display = "block";
	}
}

//-->
</script>
</head>
<body>
<div id="entete"><h3>{Nom_etudiant}{Info_ael}</h3></div>
<table {sEncadrer} align="center" class="titre">
<tr>
	<td>
		{sTitre}
	</td>
</tr>
</table>
[BLOCK_EVAL_ETAT+]
<div id="Eval">
<h3>{Eval_Globale}</h3>
<p>{txt_eval}</p>
</div>
<div id="Etat">
<h3>Etat de l'activité : </h3>
{txt_etat}
</div>
[BLOCK_EVAL_ETAT-]
<br style="clear: both;" />
<form name="questionnaire" action="modifier_formulaire.php" method="post" enctype="text/html">
[BLOCK_FORMULAIRE+]
<input type="hidden" name="idFormulaire" value="{iIdFormulaire}" />
{input_ss_activ}
{ListeObjetFormul}
</form>
<div id="barremenu">
{bouton_fermer}{bouton_valider}
</div>
[BLOCK_FORMULAIRE-]
[BLOCK_FERMER+]
<script language="javascript" type="text/javascript">
	top.opener.location = top.opener.location;
	top.close();
</script>
[BLOCK_FERMER-]
</body>
</html>
