<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Esprit : les brèves</title>
<style type="text/css">
body{
	margin:0px;
	padding:0px;
	overflow:hidden;
	height:100%
}
html{ height:100% }
#wrap {
	height:100%;
	overflow:auto;
}
h4
{
	font-weight: bold;
	font-size: 12px;
	color: white;
	background-color: rgb(104,108,71);
	margin: 0;
	padding: 4px;
}
#breves
{
	background-image: url("images/news.gif");
	background-repeat: no-repeat;
	margin: 3px;
	padding-bottom: 1.5em;
}
.breve-centered
{
	padding: 0;
	margin: 5px auto;
	display: block;
	text-align: center;
}
.news-texte
{
	font-style: normal;
	font-weight: normal;
	color: black;
	background-color: rgb(255,245,213);
	margin: 0;
	padding: 3px;
}
#pieddepage {
	position: absolute;
	bottom: 0;
	left: 0;
	height: 1.5em;
	width: 100%;
	background-color: rgb(69,63,48);
	text-align: right;
	z-index:1;
}
#pieddepage span {
	margin-right: 1em;
	color: white;
}
#pieddepage span:hover {
	text-decoration: underline;
	cursor: pointer;
}
</style>
</head>
<body>
<div id="wrap">
<div id="breves">
	<h4>{breves->titre}</h4>
[BLOCK_BREVE+]
	<div class="news-texte">{breve->info}</div>
	<img src="theme://login/images/separateur_news.gif" class="breve-centered" width="178" height="3" alt=" - - - - - - - - - - - - - - " />
[BLOCK_BREVE-]
</div>
</div>
<div id="pieddepage">
	<span onclick="window.close()">Fermer</span>
</div>
</body>
</html>
