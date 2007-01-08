<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="fr">
<head>
<title>La page d'accueil d'Esprit</title>
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
</head>
<body>
<div id="bandeau">
	<div id="bandeau-logo"><img id="logo" src="theme://login/images/esprit.gif" border="0" alt="Esprit" /></div>
	<h1 id="titre">Environnement scénarisé d'apprentissage interactif à distance</h1>
</div>
<div id="contenu">
	<table border="0" cellspacing="0" cellpadding="5">
	<tr>
	<td width="55%" valign="top">
	<div id="bienvenue" class="BlockContent">
	<!--	<h2>Bienvenue sur la plateforme de formation Esprit</h2>-->
	<!--	<p>Esprit est une plateforme de formation à distance qui permet aux étudiants de réaliser des activités d'apprentissage, 
		seuls ou en petits groupes, sous la supervision de tuteurs en ligne.</p>
		<p>Esprit permet à l'enseignant-concepteur de créer son cours de manière autonome et totalement	à distance. Ce cours 
		est envisagé comme une série d'activités d'apprentissage structurées selon un scénario pédagogique.</p>
		<p>Des outils permettent aux tuteurs d'encadrer et d'animer le travail 	réalisé par les apprenants. Le travail 
		collaboratif est nettement favorisé et facilité par certaines fonctionnalités offertes par la plateforme.</p>
		<p>Esprit est disponible en open source. Si vous êtes intéressé d'obtenir ces sources <a href="javascript: GPL();">cliquez ici</a>.</p>-->

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
	
<!--	<div id="plateforme" class="BlockContent">
		<h3>Encore une plateforme de formation à distance ?</h3>
		<p>Il existe un grand nombre de plateformes et pourtant aucune d'entre elles ne répond aux exigences pédagogiques que 
		nous avons définies. Certaines semblent pourtant assez intéressantes mais à l'usage nous nous sommes rendu compte 
		qu'elles ne correspondaient pas réellement à nos besoins. Que ce soit pour des questions de complexité, de rigidité,
		de coût ou de limitation des possibilités pédagogiques, nous avons à chaque fois été confrontés à l'un ou l'autre 
		problème qui nous a amenés à abandonner la plateforme un moment pressentie.</p>
		<p>Nous voulions une plateforme qui soit facile à utiliser mais qui ne fasse aucune concession au niveau des 
		possibilités pédagogiques, qui favorise le travail collaboratif mais qui permette également une approche centrée sur 
		l'apprenant travaillant individuellement, qui prenne appui sur le tuteur dans son rôle d'animateur, de régulateur et 
		d'évaluateur mais qui autorise également le travail autonome sans supervision et enfin qui facilite la communication 
		entre les membres de la communauté d'apprentissage.</p>
		<p>En créant Esprit, nous souhaitions aussi disposer d'une plateforme « orientée recherche » qui nous permette de 
		valider des dispositifs originaux de formation à distance, d'enregistrer des données de suivi (tracking), de tester 
		des idées.</p>
		<p>Compte tenu de cela, nous avons donc décidé de développer une plateforme « maison ». Ce fut la première ébauche
		d'Esprit. Elle devait rester un outil de laboratoire, une machine à idées à utiliser en interne dans le cadre de nos 
		formations. Actuellement, elle est devenue un outil au service de la communauté qui sera bientôt disponible en 
		« open source » dans le cadre d'une collaboration avec l'université de Grenoble.</p>
		<p>Quelques idées forces nous ont guidés dans la conception de cet environnement. L'idée principale était de se doter 
		d'un outil de conception de cours en ligne qui soit à la fois puissant et souple, permettant de mettre à distance
		une diversité importante de scénarios de formation, des plus simples aux plus complexes.</p>
		<p>Nous avons prêté une attention toute particulière à l'ergonomie de la plateforme et sa facilité d'utilisation a 
		été une préoccupation constante. Pour l'apprenant, la prise en main est immédiate. Grâce à l'interface très simple 
		et très intuitive, l'apprenant a accès à l'ensemble des fonctionnalités qui le concernent et il peut prendre connaissance 
		des différentes ressources placées par l'enseignant-concepteur (activités, espace collaboratif, documents, forum, chat...).</p>
		<p><img src="theme://login/images/Menu-esprit.gif" width="384" height="457" alt="Menu d'Esprit" /></p>
		<p>Les outils d'encadrement mis à la disposition des tuteurs et l'outil de conception en ligne (eConcept) demandent, 
		par contre, une période de prise en main un peu plus longue. Un manuel d'utilisation de l'outil de conception (eConcept) 
		est disponible à partir de cette page. Il permet de se faire une meilleure idée des multiples possibilités de la plateforme.</p>
		<p>Nous ne prétendons pas qu'Esprit répond à toutes les situations de formation. Nous avons d'ailleurs, parallèlement 
		à Esprit, développé différentes plateformes adaptées à d'autres projets de formation comme c'est le cas, par exemple, 
		de la plateforme Galanet (<a href="http://www.galanet.be/" target="_blank">http://www.galanet.be</a>) qui a été bâtie autour de la notion de 
		pédagogie par projet et de communauté d'apprentissage. Nous pensons néanmoins qu'Esprit constitue un outil précieux 
		lorsqu'il s'agit de mettre sur pied une formation qui réponde aux besoins habituels des concepteurs pédagogiques.</p>
	</div>-->
	
<!--	<div id="principes" class="BlockContent">
		<h3>Les principes qui ont conduit à la conception d'Esprit</h3>
		<ul>
		<li>L'enseignant doit disposer de tous les outils pour concevoir son cours de façon autonome. Il doit également avoir 
		à sa disposition les fonctionnalités nécessaires à l'inscription des étudiants, à la composition des groupes, à l'animation 
		du travail collaboratif et au suivi du travail réalisé.</li>
		<li>L'outil de conception de cours à distance (eConcept) doit allier souplesse et performance, un maximum de liberté 
		dans la conception des activités avec une possibilité de les modifier instantanément ainsi qu'une capacité à répondre 
		aux idées de scénario de formation les plus variées.</li>
		<li>La plateforme doit réserver une place privilégiée à l'activité d'apprentissage réalisée par l'apprenant, seul ou 
		en petits groupes. A ce niveau, même s'il est tout à fait possible de placer des exercices autocorrectifs en ligne, 
		les activités dans Esprit sont d'abord conçues pour être animées et évaluées par l'enseignant tuteur.</li>
		<li>La conception de cours en ligne sous Esprit se réalise dans une démarche qui s'élabore autour d'un scénario pédagogique 
		constitué d'actions de formation proposées aux étudiants et inscrites à l'intérieur d'une structure composée de cours 
		(ou modules) et d'unités d'apprentissage, le tout intégrée dans la notion de session de formation.</li>
		<li>Esprit propose une structure modulaire calquée sur la réalité des formations que nos partenaires et nous même 
		rencontrons le plus souvent c'est-à-dire des formations organisées sous forme de sessions, pour un groupe d'étudiants 
		pendant une période de temps donnée et composées d'un ou de plusieurs cours eux-mêmes divisés en unités d'apprentissage 
		(chapitres de cours en quelque sorte). Les ressources pédagogiques (syllabus en ligne, lien Internet, document à télécharger...), 
		les activités proposées aux apprenants (travaux à  remettre	individuellement ou par équipe, exercices en ligne...)
		et les outils de communication et de collaboration (forum, chat, awareness, collecticiel...) sont organisés par l'
		enseignant concepteur à  l'intérieur d'une unité d'apprentissage.</li>
		<li>Les activités d'apprentissage réalisées en petits groupes privilégient le travail collaboratif encadré et animé 
		par l'enseignant tuteur.</li>
		<li>Les différents rôles des intervenants dans la formation ne doivent pas se résumer, comme c'est le cas sur bon
		nombre de plateformes, à la trilogie administrateur/enseignant/apprenant. D'autres rôles sont nécessaires de manière 
		à offrir un éventail de possibilités qui permette de faire face à une très large variété de situations. Esprit s'organise 
		autour des statuts d'administrateur, responsable de formation, enseignant concepteur de cours, enseignant tuteur, 
		apprenant et visiteur.</li>
		</ul>
	</div>-->
	
	<!--<div id="structure" class="BlockContent">
		<h3>La structure d'une formation sous Esprit</h3>
		<p>La structure d'une formation sous Esprit se compose de plusieurs niveaux : une formation comprend une série de cours, 
		eux-mêmes divisés en unités d'apprentissage. L' « enseignement-apprentissage » se déroule à l'intérieur de l'unité sous 
		forme d'actions de formation que l'on ordonne selon le scénario	pédagogique que l'enseignant veut voir supporter à distance.</p>
		<p><img src="theme://login/images/Structure des niveaux (à 4 niveaux).gif" width="484" height="116" alt="Structure d'une formation" /></p>
	</div>-->
	
<!--	<div id="conception" class="BlockContent">
		<h3>La conception d'une plateforme se fonde sur des orientations pédagogiques claires</h3>
		<p>Même s'il est possible d'utiliser cette plateforme de différentes manières, Esprit ne se réduit pas à une plateforme 
		de mise en ligne de cours ni à un intégrateur de ressources multimédias interactives, ni encore à un environnement de 
		communication communautaire. Esprit regroupe les fonctionnalités que l'on peut retrouver dans ces différentes catégories 
		de dispositifs de manière à permettre la mise en oeuvre de scénarios d'apprentissage qui supportent des actions de 
		formation dans lesquelles l'activité auto et socio régulée de l'apprenant occupe une place centrale. Esprit s'inscrit 
		dans une approche socio-constructiviste du développement cognitif, l'apprentissage étant perçu comme un processus 
		actif impliquant l'étudiant dans des activités qui ont un sens (activités signifiantes) et qui lui permettent d'acquérir
		les compétences	visées par le processus  de formation mis en oeuvre. Cette acquisition lui demande de réorganiser et 
		d'adapter ses connaissances antérieures (constructivisme) en relation avec son environnement social et avec le 
		contexte historique et culturel dans lequel cet apprentissage s'inscrit.</p>
	</div>
	-->
<!--
	<div id="choix" class="BlockContent">
		<h3>Les choix technologiques et les options de développement adoptés</h3>
		<ul>
		<li>Hébergement de la plateforme sur un serveur Internet libre de droits et répandu (Apache)</li>
		<li>Utilisation du couple PHP/MySQL, solution gratuite et puissante</li>
		<li>Développement d'une plateforme légère, utilisable à partir de configuration informatique "apprenant" de base</li>
		<li>Plateforme ouverte et évolutive</li>
		<li>Intégration progressive des outils nécessaires à la	conception et à la gestion d'une formation  à distance.
		Contrairement à la plupart des plateformes, nous avons opté pour un développement « maison » de l'ensemble des outils 
		de formation ou de communication, collecticiel, galerie, éditeur de texte en ligne, chat, forum, awareness...,
		ce qui permet d'avoir le contrôle de l'ensemble et de garantir l'évolution constante des outils et leur intégration 
		harmonieuse	dans le dispositif</li>
		</ul>
	</div>-->
	</td>
	<td valign="top">
	<div id="breves">
		<h4>Les brèves</h4>
		<!--<div class="news_texte">
			<strong>Astuce !</strong>Pour ceux qui ne verraient pas la zone qui permet d'entrer son pseudo et son mot de 
			passe, appuyez sur la touche <span class="touchesF">F11</span> ou utilisez le menu Affichage 
			et sélectionnez l'option "Plein écran" (10/09/03)
		</div>
		<img src="theme://login/images/separateur_news.gif" class="breve_separateur" width="178" height="3" alt=" - - - - - - - - - - - - - - " />-->

[BLOCK_BREVE+]
	<div class="news_texte">
	<p>{breve->info}</p>
</div>
		<img src="theme://login/images/separateur_news.gif" class="breve_separateur" width="178" height="3" alt=" - - - - - - - - - - - - - - " />
[BLOCK_BREVE-]

		<!--<div class="news_texte">
			Une nouvelle version est disponible	(version 1.1). Elle propose différents outils supplémentaires pour l'enseignant 
			concepteur (possibilité d'introduire directement du texte à partir de la plateforme, des possibiltés de mise 
			en page, une refonte de l'ergonomie d'eConcept...) (15/07/04)
		</div>
		<img src="theme://login/images/separateur_news.gif" class="breve_separateur" width="178" height="3" alt=" - - - - - - - - - - - - - - " />
		<div class="news_texte">
			Un premier manuel a été réalisé essentiellement à l'intention des enseignants concepteurs mais pas uniquement : 
			ce document permet également d'avoir une idée précise des possibilités de la plateforme... et elles sont nombreuses. 
			<a href="fichiers/Esprit-manuel-concepteur.pdf" target="_blank">Jetez-y	un oeil</a> (15/07/04)
		</div>
		<img src="theme://login/images/separateur_news.gif" class="breve_separateur" width="178" height="3" alt=" - - - - - - - - - - - - - - " />
		<div class="news_texte">
			Vous êtes intéressé par l'utilisation d'Esprit dans le cadre de vos formations ? Contactez 
			<a href="mailto:jean-jacques.quintin@umh.ac.be">Jean-Jacques.Quintin@umh.ac.be</a>.
		</div>-->
	</div>

	<div id="ressources">
        <h4>Les liens</h4>

[BLOCK_LIEN+]
	
	<p>{lien->info}</p>

[BLOCK_LIEN-]





	<!--	<h4>Le coin des ressources</h4>
		<p><strong>Comment suivre une formation sous Esprit ?</strong></p>
		<ul>
			<li><a href="fichiers/Esprit-manuel-utilisation.pdf" target="_blank">Manuel
			d'utilisation (version acrobat - pdf)</a></li>
			<li><a href="fichiers/Esprit-manuel-utilisation.doc" target="_blank">Manuel
			d'utilisation (version word - doc)</a></li>
		</ul>
		<p><strong>Comment concevoir une formation sous Esprit ?</strong></p>
		<ul>
			<li><a href="fichiers/Esprit-manuel-concepteur.pdf" target="_blank">Manuel
			de l'enseignant concepteur (version acrobat - pdf)</a></li>
			<li><a href="fichiers/Esprit-manuel-concepteur.doc" target="_blank">Manuel
			de l'enseignant concepteur (version word - doc)</a></li>
		</ul>
		<p><strong>Inscrire un nombre important d'utilisateurs "à la volée"</strong>
		<a href="fichiers/inscriptions2.xls" target="_self" class="discret">(Tableau excel d'inscription des étudiants)</a></p>
		<p class="discret">Vous pouvez inscrire les étudiants directement à partir d'Esprit. Ces inscriptions se réalisent 
		étudiant par étudiant ce qui peut se révéler fastidieux lorsqu'il s'agit d'un nombre important d'utilisateurs. 
		Vous pouvez si vous le préférez utiliser ce fichier excel, introduire les coordonnées à la volée et le tranmettre 
		à l'administrateur du site <a href="mailto:Filippo.Porco@umh.ac.be" class="discret">(Filippo.Porco@umh.ac.be)</a></p>

-->
	</div>

<!--	<div id="adresses">
		<h4>Les bonnes adresses</h4>
		<p><strong>Quelques plateformes francophones de formation à distance</strong></p>
		<p><a href="http://www.galanet.be" target="_blank">Galanet</a> (Galanet est une plateforme de formation à l'intercompréhension
		en langues romanes basée sur la pédagogie par projets et la notion de communauté d'apprentissage. Un ensemble
		d'étudiants et d'animateurs se retrouvent dans une bâtiment virtuel (Centre de Langues) pour réaliser ensemble un 
		projet. Superbe interface et convivialité assurée.)</p>
		<p><a href="http://www.icampus.ucl.ac.be/" target="_blank">Claroline</a> (Claroline est un site de formation réalisé 
		par et pour les membres de la communauté universitaire de l'UCL. Très simple d'utilisation, elle offre plutôt des 
		espaces de travail, un par cours, constitués autour d'un forum et d'une zone de dépôt de documents plutôt qu'une 
		réelle mise à distance de formation complète. Intéressant malgré tout.)</p>
		<p><a href="http://www.anemalab.org/" target="_blank">Ganesha</a> (Ganesha est une plateforme de téléformation 
		(Learning Management System, LMS) développée par la société privée Anemalab)</p>
		<p><a href="http://acolad.u-strasbg.fr/" target="_blank">Acolad</a> (Acolad	est une plateforme de formation développée 
		par l'Université Louis Pasteur de Strasbourg. Elle permet la mise à disposition de cours, mais aussi l'apprentissage 
		en petits groupes et le développement de projets personnels par les étudiants. Le travail en petits groupes sous 
		la supervision d'un tuteur est mis à l'honneur.)</p>
		<p>Tout comme Esprit (et hormis Galanet qui appartient à un	partenariat européen), ces plateformes offrent la possibilité,
		moyennant accord dans certains cas, sur simple inscription en ligne dans d'autres cas, de réaliser gratuitement 
		ses propres	formations hébergées soit sur le serveur d'origine ou rapatriées sur votre serveur.</p>
		<p><strong>Quelques adresses utiles de revues en ligne ou de portails consacrées aux TICE</strong></p>
		<p><a href="http://www.educnet.education.fr/documentation/bibliotic/00/for.htm" target="_blank">BiblioTIC</a> (BiblioTIC
		est la partie du site d'EducNet du Ministère français de l'Education Nationale consacrée à l'actualité documentaire 
		sur les TIC : articles, rapports, ouvrages, revues sont disponibles la plupart du temps en ligne)</p>
		<p><a href="http://www.cndp.fr/dossiersie/" target="_blank">Les	dossiers de l'ingénierie éducative</a> (Revue visant 
		à favoriser	l'intégration des technologies de l'information et de la communication dans l'enseignement. Liens et 
		sélection d'articles disponibles en ligne)</p>
		<p><a href="http://cursus.cursus.edu/" target="_blank">Cursus</a> (Répertoire de formations disponibles à distance 
		dans des champs aussi divers que l'art et la culture, l'Education et la formation, les sciences administratives...)</p>
		<p><a href="http://www.educnet.education.fr" target ="_blank">EducNet</a> (Portail du Ministère français de la Jeunesse, 
		de l'Education et de la Recherche consacré à la formation ouverte et à distance. Une mine d'informations bien tenues
		à jour)</p>
		<p><a href="http://cqfd.teluq.uquebec.ca/" target ="_blank">CQFD</a> (Portail du Conseil québécois de la formation 
		à distance. Vous y trouverez entre autres, une revue en ligne mais dont le dernier numéro date de ... 2001)</p>
		<p><a href="http://www.algora.org" target ="_blank">ALGORA</a> (ALGORA est le portail d'une association soutenue 
		par le ministère français du travail, des affaires sociales et de la solidarité (DGEFP) dont l'objet est de promouvoir 
		le développement de la formation ouverte et à distance dans les systèmes de formation professionnelle)</p>
		<p><a href="http://www.preau.ccip.fr" target ="_blank">Le Préau</a> (Le Préau est un centre de ressources et de veille 
		sur	les technologies éducatives (TICE), fondée par la Chambre de Commerce et d'Industrie de Paris, l'École Nationale
		Supérieure des Télécommunications, l'Université de Technologie de Compiègne et Paribas)</p> 
	</div>
	-->
<!--
	<div id="actus">
		<h4>Les actualités</h4>
		
		<p>Ces actualités sont issues de portails extérieurs. Dans cette mesure, nous ne pouvons pas garantir l'absolue 
		exactitude des informations présentées ci-dessous.</p>
		<p>
		<script src="http://ntic.org/nouvelles/nouvelles_export.php?fmt=javascript&amp;inclure_description=true&amp;inclure_vitrine=true&amp;inclure_isef=false&amp;inclure_biblio=false" type="text/javascript" charset="iso-8859-1"></script>
		</p>
	</div>
	-->
	</td>
	</tr>
	</table>
</div>

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

<div id="pieddepage">
	<div id="hautpieddepage">&nbsp;</div>
	<div id="baspieddepage">
	<a href="javascript: void(propos('theme://'));" onfocus="blur()">ESPRIT a été développé par l'Unité de Technologie de L'Education de l'Université de Mons-Hainaut (Belgique)</a>
	</div>
</div>

</body>
</html>
