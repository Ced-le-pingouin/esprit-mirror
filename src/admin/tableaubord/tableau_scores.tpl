<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>AEL auto corrigé</title>
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
	margin: 35px 5px;
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
#csv
{
	float: left;
	margin-left: 5px;
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
</head>
<body>
<h1>{Titre}</h1>
<div id="contenu">
<table>
<tr>
	<th class="titrenom">Noms : </th>
[BLOCK_NOMS+]
	<th>{NOM}</th>
[BLOCK_NOMS-]
</tr>
[BLOCK_QUESTIONS+]
<tr>
	<td{ClassQuestion}>{Question}</td>
[BLOCK_SCORES+]
	<td{ClassScore}>{Score}</td>
[BLOCK_SCORES-]
</tr>
[BLOCK_QUESTIONS-]
</table>
</div>
<div id="barremenu"><a id="csv" href="tableau_scores.php?IdFormul={IdFormul}&amp;action=exportation">Exporter</a><a href="javascript: top.close();">Fermer</a>&nbsp;</div>
</body>
</html>
