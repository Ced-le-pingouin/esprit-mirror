<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="css://informations_generales.css" />
<title>La plate-forme Esprit devient un logiciel libre</title>
<style type="text/css">
#champs
{
	margin-top: 5px;
	font-size: 11px;
	[FORMAT_TEXTE]
}
</style>
</head>
<body class="gpl">
<div id="principal">
<h1>La plate-forme Esprit est un logiciel libre</h1>
<h2>1. Qu'est-ce qu'un logiciel libre ?</h2>
<h3>1.1. Définitions et le choix pour Esprit</h3>
<p>Un <strong>logiciel libre</strong> est techniquement  similaire à un autre type de logiciel. Ce qui détermine sa spécificité 
n'est donc pas un critère technique, mais sa licence d'utilisation, qui conditionne ce que vous avez le droit de faire avec. 
Pour qu'un logiciel soit dit libre<a href="#_ftn1" name="_ftnref1" title="" id="_ftnref1"><sup>1</sup></a>, sa licence doit autoriser à 
l'utilisateur ces quatre libertés fondamentales : </p>
<ol class="lettre">
	<li>Liberté d'utilisation du logiciel, pour tous usages, sans restriction ;</li>
	<li>Liberté d'étude du logiciel et liberté de le modifier pour l'adapter à vos besoins ; </li>
	<li>Liberté de copie et de diffusion du logiciel d'origine sans limitation de nombre ni de bénéficiaires ;</li>
	<li>Liberté de rediffusion des modifications ;</li>
</ol>
<p>Pour les libertés b et d, la disponibilité du code source est une condition nécessaire, mais pas suffisante.</p>
<p>Un logiciel non-libre est encore appelé logiciel propriétaire.</p>
<p>On parle de <strong>licence libre</strong> pour toute licence  attachée à un logiciel et garantissant les 4 énoncés ci-dessus. 
Il existe un grand nombre de licences considérées comme libres, mais un petit nombre qui soient largement utilisées, parmi 
lesquelles la General Public License (GPL)<a href="#_ftn2" name="_ftnref2" title="" id="_ftnref2"><sup>2</sup></a>, la Berkeley Software 
Distribution (BSD)<a href="#_ftn3" name="_ftnref3" title="" id="_ftnref3"><sup>3</sup></a>, certaines licences Creative Commons
<a href="#_ftn4" name="_ftnref4" title="" id="_ftnref4"><sup>4</sup></a> et la Licence française de logiciels libres (CECILL)
<a href="#_ftn5" name="_ftnref5" title="" id="_ftnref5"><sup>5</sup></a>. La plus célèbre et la plus utilisée est sans conteste la GPL, 
issue de la FSF et associée par exemple au système d'exploitation Linux.</p>
<p>Le <strong>copyleft</strong>, notion complémentaire et optionnelle, stipule qu'un logiciel libre qui est modifié et diffusé 
(liberté 4) doit l'être sous la même licence que celle du logiciel d'origine. Cette notion divise l'ensemble des licences 
libres en deux grandes catégories, avec comme « chefs de file » la GPL pour les licences copyleft et la BSD pour les autres. 
Cette distinction a une conséquence économique importante : un logiciel libre sous licence non-copyleft peut être modifié 
et rendu propriétaire. Par exemple, le système propriétaire Mac OS X d'Apple est issu du système libre FreeBSD, sous licence BSD.</p>
<p>Dans <strong>le cas d'Esprit</strong>, nous avons choisi la GPL v.2, qui représente le mieux l'esprit du copyleft, et 
qui est internationalement reconnue</p>
<h3>1.2. L'accès au code source</h3>
<h4>1.2.1. Sur SourceSup</h4>
<p>Une des premières étapes du passage au libre a consisté à initier un mode de développement collaboratif de la plate-forme. 
Nous utilisons une plate-forme de développement ouverte qui intègre de nombreux outils à travers une interface web unifiée : 
la plate-forme <strong>SourceSup</strong><a href="#_ftn6" name="_ftnref6" title="" id="_ftnref6"><sup>6</sup></a>. C'est une instance 
de Gforge administrée par la Comité Réseau des Universités (CRU de Rennes, France). Elle fournit les fonctionnalités suivantes :</p>
<ol class="lettre">
	<li>outil de suivi de version (subversion) pour l'arborescence du projet</li>
	<li>système de suivi de bogues et de demandes de fonctionnalités nouvelles</li>
	<li>outil de gestion de correctifs et modifications soumis par la communauté (patches)</li>
	<li>outil de gestion de tâches, pour la répartition du travail entre développeurs</li>
	<li>listes et forums web de discussion pour la communauté.</li>
	<li>hébergement de pages web liées au projet.</li>
</ol>
<p><strong>La page d'accueil d'Esprit sur Sourcesup</strong> est : <a href="https://sourcesup.cru.fr/projects/esprit/" target="_blank">https://sourcesup.cru.fr/projects/esprit/</a></p>
<p>Vous pouvez télécharger l'archive source d'Esprit sur cette page.</p>
<p><strong>Le fichier à télécharger</strong> est esprit-2.0.tgz (ou version plus récente).</p>
<p>Si vous voulez avoir accès aux derniers développements effectués, vous pouvez le faire à travers un client Subversion. 
Voir les indications à l'adresse : <a href="https://sourcesup.cru.fr/scm/?group_id=204" target="_blank">https://sourcesup.cru.fr/scm/?group_id=204</a></p>
<h4>1.2.2. Prévenez-nous</h4>
<p>Vous souhaitez récupérer les sources et installer la plate-forme dans votre institution, nous en sommes ravis et nous 
vous remercions de l'intérêt que vous manifestez pour Esprit. Afin de montrer que la communauté des utilisateurs s'agrandit 
et que des usages nouveaux sont initiés nous aimerions savoir qui vous êtes et l'usage que vous souhaitez faire du logiciel. 
Nous vous remercions par avance du temps que vous allez passer à remplir ce petit questionnaire.</p>
<a href="#_frm1r" name="_frm1r" title="" id="_frm1r"> </a>
[BLOCK_FORMULAIRE+]
<form action="gpl.php#_frm1r" method="post">
<label for="idOrg">Nom de l'organisme*:</label><input type="text" size="70" name="organisme" id="idOrg" value="[ORG]" />
<label for="idRai">Raison sociale :</label><input type="text" size="70" name="raison" id="idRai" value="[RAISON]" />
<label for="idEma">Contact (mail du responsable du projet) :</label><input type="text" size="70" name="email" id="idEma" value="[EMAIL]" />
<label for="idUsa">Usage prévu de la PF*:</label><textarea cols="55" rows="5" id="idUsa" name="usage">[USAGE]</textarea>
<label for="idEvo">Évolution envisagée :</label><textarea cols="55" rows="5" id="idEvo" name="evolution">[EVO]</textarea>
<div id="champs">*champs obligatoires</div>
<input type="submit" id="envoyer" value="envoyer" />
</form>
[BLOCK_FORMULAIRE-]
[BLOCK_MERCI+]
<div id="merci">
Merci d'avoir rempli ce questionnaire
</div>
[BLOCK_MERCI-]
<h2>2. En quoi cela concerne les utilisateurs d'Esprit ?</h2>
<h3>2.1. Les ressources pédagogiques ne sont pas concernées</h3>
<p>Si vous êtes un utilisateur d'Esprit, il n'y a aucune inquiétude à avoir. Vos ressources sont indépendantes du code 
de la plate-forme. Même si celle-ci et un logiciel libre vos ressources gardent le statut juridique que vous leur avait 
attribué (pour information vous n'avez rien à faire pour quelles soient protégées par droit d'auteur).</p>
<h3>2.2. Le texte à inclure dans les publications</h3>
<p>Lorsque vous êtes amenés à communiquer sur votre pratique pédagogique et votre utilisation de la plate-forme Esprit dans 
ce cadre, nous vous demandons de bien vouloir inclure le texte suivant dans votre publication.</p>
<p>« La plateforme Esprit (<a href="http://ute2.umh.ac.be/esprit" target="_blank">http://ute2.umh.ac.be/esprit</a>) a été développée par 
l'Unité de Technologie de l'Education de l'Université de Mons-Hainaut (Belgique) avec la collaboration de Grenoble Universités 
pour l'adaptation aux besoins des formations en langues dans le cadre du projet FLODI (<a href="http://www.grenoble-universites.fr/flodi/" target="_blank">http://www.grenoble-universites.fr/flodi/</a>) »</p>
<ul id="explication">
	<li>1. <a href="#_ftnref1" name="_ftn1" title="" id="_ftn1"> </a>au  sens de la Fondation  pour le logiciel libre, FSF pour <em>Free Software Foundation</em></li>
	<li>2. <a href="#_ftnref2" name="_ftn2" title="" id="_ftn2"> </a><a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">http://www.gnu.org/copyleft/gpl.html</a></li>
	<li>3. <a href="#_ftnref3" name="_ftn3" title="" id="_ftn3"> </a><a href="http://www.opensource.org/licenses/bsd-license.php" target="_blank">http://www.opensource.org/licenses/bsd-license.php</a></li>
	<li>4. <a href="#_ftnref4" name="_ftn4" title="" id="_ftn4"> </a><a href="http://www.creativecommons.org" target="_blank">http://www.creativecommons.org</a>  Attention : toutes les licences CC ne sont  pas libres.</li>
	<li>5. <a href="#_ftnref5" name="_ftn5" title="" id="_ftn5"> </a><a href="http://www.cecill.info/index.fr.html" target="_blank">http://www.cecill.info/index.fr.html</a></li>
	<li>6. <a href="#_ftnref6" name="_ftn6" title="" id="_ftn6"> </a><a href="http://sourcesup.cru.fr" target="_blank">http://sourcesup.cru.fr</a></li>
</ul>
</div>
<div id="piedpage">
<div id="fermer">
<a href="javascript: self.close();">Fermer</a>
</div>
</div>
</body>
</html>
