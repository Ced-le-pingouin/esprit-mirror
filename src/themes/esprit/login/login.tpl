<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Esprit : page d'accueil</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type="text/javascript" language="javascript" src="/js/window.js"></script>
<script type="text/javascript" language="javascript" src="themes/commun/js/login.js.php"></script>
<script type="text/javascript" language="javascript" src="theme://propos/propos.js"></script>
<script language="javascript" type="text/javascript">
<!--
function GPL()
{
	var iLargeurFenetre = 750;
	var iHauteurFenetre = 500;
	var iPositionGauche = (screen.width-iLargeurFenetre)/2;
	var iPositionHaut = (screen.height-iHauteurFenetre)/2;
	var sCaracteristiques = "left=" + iPositionGauche
		+ ",top=" + iPositionHaut
		+ ",width=" + iLargeurFenetre
		+ ",height=" + iHauteurFenetre
		+ "menubar=no,scrollbars=no,statusbar=no,resizable=yes";
	var w =	window.open("gpl.php","GPL",sCaracteristiques);
	w.focus();
}
//-->
</script>
<link rel="stylesheet" type="text/css" href="theme://login/login.css" />
<!--[if lt IE 7]>
<link rel="stylesheet" type="text/css" href="theme://login/login-ie.css">
<![endif]-->
</head>
<body>
<div id="wrap">
<div id="bandeau">
	<h1>Environnement scénarisé d'apprentissage interactif à distance</h1>
	<div><img id="logo" src="theme://login/images/esprit.gif" border="0" alt="Esprit" /></div>
</div>
<div id="content-wrap">
<div id="formlogin">
	{form}
	<img id="tete" src="theme://login/images/login-tete.jpg" width="201" height="165" border="0" alt="Logo: tête" />
	[BLOCK_ERREUR_LOGIN+]<p id="erreur_login">Votre pseudo ou votre mot de passe est incorrect.</p>[BLOCK_ERREUR_LOGIN-]
	<p>Si vous êtes inscrit, introduisez votre pseudo et mot de passe.</p>
	<p class="aligndroite"><a href="javascript: void(0);" onclick="return mdp_oublier()" onfocus="blur()" style="font-size: 7pt;">Oubli&eacute;&nbsp;?</a></p>
	<p class="aligndroite"><label for="idPseudo">Pseudo&nbsp;:</label><input type="text" size="13" name="idPseudo" id="idPseudo" /></p>
	<p class="aligndroite"><label for="idMdp">Mot&nbsp;de&nbsp;passe&nbsp;:</label><input type="password" size="13" name="idMdp" id="idMdp" /></p>
	<p class="aligndroite"><input class="btn_ok" type="submit" value="&nbsp;Ok&nbsp;" /></p>
	{/form}
	[BLOCK_AVERTISSEMENT_LOGIN+]<p id="avertissement_login">{login.avertissement}</p>[BLOCK_AVERTISSEMENT_LOGIN-]
</div>

<div id="contenu">
	<table border="0" cellspacing="0" cellpadding="5">
	<tr>
	<td width="55%" valign="top">
	<div id="bienvenue" class="BlockContent">
[BLOCK_TEXTE+]
   <p>{texte->info}</p>
[BLOCK_TEXTE-]
	</div>

	[BLOCK_INFOS_PLATEFORME+]
	<div id="formations">
		<p>Vous êtes déjà inscrit ? Introduisez votre pseudo et mot de passe dans la zone située à gauche de cet écran 
		(Si cette zone n'apparaît pas complètement,	appuyez alors sur la touche F11 pour passer en mode plein écran).</p>
		[BLOCK_LISTE_FORMATIONS+]
		<p>Vous n'êtes pas inscrit ? Vous pouvez néanmoins vous faire une idée des possibilités d'Esprit en sélectionnant
		une des formations proposées ci-dessous à titre d'exemple. Vous aurez la possibilité, en parcourant l'une ou l'autre 
		de ces formations, de découvrir la manière dont est structurée un cours sur Esprit.</p>
		<ul>
		[BLOCK_FORMATION+]
		<li>{formation->url}</li>
		[BLOCK_FORMATION-]
		</ul>
		[BLOCK_LISTE_FORMATIONS-]
	</div>
	[BLOCK_INFOS_PLATEFORME-]
	
	</td>
	<td valign="top">
	<div id="breves">
		<h4>Les brèves</h4>
[BLOCK_BREVE+]
	<div class="news-texte">{breve->info}</div>
	<img src="theme://login/images/separateur_news.gif" class="breve-centered" width="178" height="3" alt=" - - - - - - - - - - - - - - " />
[BLOCK_BREVE-]
	</div>

	<div id="ressources">
		<h4>Les liens</h4>
[BLOCK_LIEN+]
	<p>{lien->info}</p>
[BLOCK_LIEN-]
	</div>

	</td>
	</tr>
	</table>
</div>
</div>

<div id="pieddepage">
	<div id="hautpieddepage">&nbsp;</div>
	<div id="baspieddepage">
	<a href="javascript: void(propos('theme://'));" onfocus="blur()">ESPRIT  (C) Unité de Technologie de L'Education, Université de Mons-Hainaut (Belgique), 2001-2007 et Grenoble Universités, projet Flodi, 2006-2007.</a>
	</div>
</div>
</div>
</body>
</html>
