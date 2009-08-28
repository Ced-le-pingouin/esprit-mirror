<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Exercice HotPotatoes</title>
<style type="text/css">
body
{
	background-color: rgb(238,234,221);
	font-family: Verdana, Tahoma, Arial, Bitstream Vera Sans, Time;
	font-size: 12px;
	margin: 0;
	padding: 0;
}
h1 
{ 
	background-color: rgb(174,165,138); 
	border-bottom: rgb(31,82,126) solid 1px; 
	color: rgb(255,255,255); 
	font-size: 14px; 
	padding: 5px;
	margin: 0 0 10px 0;
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
}
a
{
	color: rgb(255,255,255);
	text-decoration: none;
	font-weight: bold;
}
a:hover
{
	text-decoration: underline;
}
table
{
	background-color: rgb(240,240,240);
	border: rgb(127,157,185) solid 1px;
	padding: 1px;
	margin: 15px 5px;
}
p.information
{
	text-align:center;
	background-color: rgb(240,240,240);
	border: rgb(127,157,185) solid 1px;
	padding: 3px;
	margin: 35px 5px 0 0;
}
td
{
	background-color: rgb(255,255,255);
	text-align: center;
	padding: 3px 5px;
}
.grise
{
	background-color: rgb(245,245,245);
}
#barremenu
{
	margin: 0;
	padding: 3px 0;
	text-align: right;
	border-top: rgb(0,0,0) solid 1px;
	background-color: rgb(174,165,138);
	position: fixed;
	bottom: 0;
	width: 100%;
}
.div_details
{
	display:none;
	position:absolute;
	text-align:left;
	z-index:1000;
	background-color:#AEA58A;
	opacity:.95;
	filter:alpha(opacity=95);
	border:1px solid black;
	width:150px;
}
.Ancien_systemeHP {
	background-color:#ffa9ab;
}

th
{
    background-color: rgb(238, 234, 221);
    border-top: rgb(238, 234, 221) solid 1px;
    border-right: rgb(238, 234, 221) solid 1px;
    border-left: rgb(238, 234, 221) solid 1px;
    border-bottom: rgb(202, 195, 177) solid 1px;
    color: rgb(111, 105, 87);
    font-weight: normal;
    font-size: 11px;
    text-align: center;
    padding: 1px 2px;
}
.titrenom
{
	text-align: right;
}
table.AucunEtudiant, th.AucunEtudiant
{
	border: rgb(202, 195, 177) none 1px;
	width: 90%;
	background-color: #EEEADD;
	text-align: center;
}
th.AucunEtudiant
{
	color: red;
}
#csv
{
	float: left;
	margin-left: 5px;
}
td em
{
	font-size: smaller;
}
</style>
<!--[if lte IE 6]>
<style type="text/css">
html
{
	height: 100%;
	/* \*/ overflow: hidden; /**/
}
body
{
	height: 100%;
	width: 100%;
	overflow: hidden;
}
#barremenu
{
	position: absolute;
	bottom: 0;
	left: 0;
	height: 4%;
	margin: 0;
}
h1
{
	margin: 0;
	height: 5%;
}
table
{
	margin: 5px;
}
#contenu
{
	overflow: auto;
	width: 100%;
	height: 91%;
	margin: 0;
}
</style>
<![endif]-->
<script type="text/javascript"><!--
function Montrer_Details(evenement,numero)
{
	var IE=false;
	if(navigator.appName == "Microsoft Internet Explorer")
		IE = true;

	if(IE)
	{
	//alert(document.body.scrollLeft);
		
		x = evenement.clientX + document.documentElement.scrollLeft;
		y = evenement.clientY + document.documentElement.scrollTop;
	}
	else
	{
		x = evenement.pageX;
		y = evenement.pageY;
	}

/*
*	affichage du div près de la souris
*	si on dépasse de l'écran, on affiche le div en haut
*/

	obj = document.getElementById("details"+numero);
	obj.style.display = 'block';
	document.body.style.cursor = 'help';
	obj.style.left = (x + 25) + 'px';
	obj.style.top = y + 'px';
}

function Cacher_Details(numero)
{
	document.getElementById("details"+numero).style.display="none";
	document.body.style.cursor = 'auto';
}
--></script>
</head>
<body>
<h1>{Titre}</h1>
<div id="contenu">
<p class="information">Placez le curseur sur un score pour afficher le nombre d'exercices réalisés</p>

[BLOCK_TITRE+]
	{TitreNom}
[BLOCK_TITRE-]
[BLOCK_NOMS+]
	{NOM}
[BLOCK_NOMS-]
</tr>
[BLOCK_ESSAIS+]
<tr>
	<td>Essai {EssaiNb}</td>
[BLOCK_SCORES+]
	{Score}
[BLOCK_SCORES-]
</tr>
[BLOCK_ESSAIS-]
</table>
<br />
</div>


[BLOCK_DETAILS+]
<div id="details{idexercice}" class="div_details">
{nombre_exercice}
</div>
[BLOCK_DETAILS-]

<div id="barremenu"><a id="csv" href="tableau_scores_hotpot.php?IdHotpot={IdHotpot}&amp;action=exportation&amp;fichier={NomFichierHP}">Exporter</a><a href="javascript: top.close();">Fermer</a>&nbsp;</div>
</body>
</html>
