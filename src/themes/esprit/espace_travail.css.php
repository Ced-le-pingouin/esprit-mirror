<?php

header("Content-type: text/css");

$sTmpRepert = dirname($PHP_SELF);

?>

/*
** Titre de l'Espace de travail
**
*/

/* Titre principal de la page HTML*/
.Activite
{
	color: #ffffff;
	font-size: 15pt;
	font-weight: bold;
	background-color: #698084;
}

/* Sous-titre*/
.Espace_travail
{
	color: #000000;
	font-size: 10pt;
	font-weight: bold;
	background-color: #978DBF;
}

/*
** Tableau des documents
**
*/

/* Titre principal */
.doc-main-title-color
{
	font-size: 12pt;
	text-align: right;
	color: #6C648C;
	font-weight: bold;
	background-color: transparent;
}

/* Les colonnes d'en-têtes */
.doc-header-color
{
	color: #B25A47;
	font-size: 8pt;
	font-weight: bold;
	background: url("<?php echo $sTmpRepert?>/th_fill.gif");
}

/* Les documents */
.doc-color-1
{
	color: #9C8276;
	background-color: #EBEBEC;
	font-size: 8pt;
}

.doc-color-2
{
	color: #9C8276;
	background-color: #D1D3CB;
	font-size: 8pt;
}
