<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 
//                          Grenoble Universités

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Installation d'Esprit</title>
<link rel="stylesheet" type="text/css" href="../themes/css/commun/install.css" />
</head>
<body>
<h1>Installation d'Esprit</h1>
<?php
/*
	Installateur d'Esprit
	Reste à faire :
		- tester
		- prévoir une procédure de mise à jour
		- écrire une fonction javascript qui vérifie les données du formulaire (pb de SÉCURITÉ en particulier)
		- A quoi correspondent $g_sNomProprietaire et $g_sNomBdd ? Leur valeur actuelle est-elle correcte ?
*/
// This one already exists in PHP5, but not in PHP4
if (!function_exists("file_put_contents"))
{
	function file_put_contents($filename, $content) {
		if (file_exists($filename)) {
			echo "Le fichier ($filename) existe déjà";
			return FALSE;
		}
		$handle = fopen($filename, 'w');
		if (!$handle) {
			echo "Impossible d'ouvrir le fichier ($filename)";
			return FALSE;
		}
		$written = fwrite($handle, $content);
		fclose($handle);
		if (!$written) {
			echo "Impossible d'écrire dans le fichier ($filename)";
			return FALSE;
		}
		return $written;
	}
}

$nbsteps=5;
$step=1;
if (isset($_REQUEST['step']) && $_REQUEST['step']) {
    $step=$_REQUEST['step'];
} else {
	if (file_exists('../include/config.inc')) {
		/*
		// Mise à jour d'Esprit
		require('../include/config.inc');
		$link = init_db($g_sNomServeur,$g_sNomProprietaire,$g_sMotDePasse,$g_sNomBdd);
		*/
		echo "<p class='erreur'>Configuration interrompue : Esprit semble déjà configuré.</p>";
		echo "</body></html>";
		exit;
	}
}
?>
		<h2>Étape <?php echo $step ?> / <?php echo $nbsteps ?></h2>

<?php
switch ($step) {
// ******************* Getting database info *******************
case 1:
?>
<p><i>En cas de problème d'affichage de caractères accentués sur cette page, veuillez vérifier la configuration du serveur 
web (par exemple pour Apache, commenter la ligne <tt>AddDefaultCharset on</tt> dans <tt>httpd.conf</tt>). 
Normalement, l'encodage devrait être en UTF-8.</i>
</p>
<p>Avant de commencer l'installation, vous devez avoir créé une base de données pour Esprit et un utilisateur MySQL associé.</p>
<p><strong>Vous devez disposer de la version 4.1 ou supérieure de MySQL</strong></p>
<p>Par exemple, les commandes suivantes sous Linux créent une table <em>esprit</em> administrée par <em>esprit-admin</em>&nbsp;:</p>
<pre>
# mysql -u root -p
&gt; CREATE DATABASE esprit DEFAULT CHARACTER SET utf8;
&gt; GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,INDEX,ALTER,LOCK TABLES ON esprit.* TO `esprit-admin`@localhost IDENTIFIED BY 'motdepasse';
&gt; quit
# mysqladmin -u root -p reload
</pre>
<p>Vous pouvez également utiliser phpmyadmin s'il est installé.</p>

<h3>Saisie</h3>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<fieldset>
<legend>Veuillez saisir ci-dessous le nom de la base créée et de son utilisateur MySQL.</legend>
<ul>
	<li><label for="base">Base de données :</label> <input type="text" name="base" id="base" /></li>
	<li><label for="host">Serveur BdD :</label> <input type="text" name="host" id="host" value="localhost" /></li>
	<li><label for="user">Login de l'utilisateur :</label> <input type="text" name="user" id="user" /></li>
	<li><label for="password">Mot de passe de l'utilisateur :</label> <input type="password" name="password" id="password" /></li>
</ul>
<input name="step" value="<?php echo $step+1 ?>" type="hidden" />
<input type="submit" value="Étape suivante" />
</fieldset>
</form>

<?php
	break;
// ******************* Filling up the database *******************
case 2:
	if (!isset($_POST['base']) || !isset($_POST['user']) || !isset($_POST['password'])) {
		echo "<p class='erreur'>Informations incomplètes, retournez à <a href='install.php?step=1'>l'étape précédente</a>.</p>";
		echo "</body></html>";
		exit;
	}
	$link=init_db($_POST['host'],$_POST['user'],$_POST['password'],$_POST['base']);
	if ($link === FALSE) {
		echo "<p>Retournez à <a href='install.php?step=1'>l'étape précédente</a>.</p>";
		echo "</body></html>";
		exit;
	}
	// filling the database
	$files = glob("sql/*.sql");
	sort($files);
	foreach ($files as $filename) {
		if (!load_mysql_dump($filename)) {
			echo "<P>Connexion réussie à la base de donnée, mais erreur lors de l'importation.</P>";
			echo "<code>", mysql_error(), "</code>";
			echo '</body></html>';
			exit;
		}
	}
?>
<p>La base de donnée a été remplie avec succès.
</p>
<?php
	show_next_step();
	break;

// ******************* creating config file *******************
case 3:
	if (empty($_POST['fileroot']) || empty($_POST['webroot'])) {
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<fieldset>
<legend>Veuillez vérifier les chemins d'accès si dessous.</legend>
<ul>
	<li><label for="fileroot">Racine des fichiers d'Esprit :</label> <input type="text" name="fileroot" value="<?php echo get_fileroot(); ?>" size="60" /></li>
	<li><label for="webroot">Racine web d'Esprit :</label> <input type="text" name="webroot" value="<?php echo get_webroot(); ?>" size="60" /></li>
</ul>
	<input name="base" value="<?php echo $_POST['base'] ?>" type="hidden" />
	<input name="host" value="<?php echo $_POST['host'] ?>" type="hidden" />
	<input name="user" value="<?php echo $_POST['user'] ?>" type="hidden" />
	<input name="password" value="<?php echo $_POST['password'] ?>" type="hidden" />
<input name="step" value="<?php echo $step; ?>" type="hidden" />
<input type="submit" value="Étape suivante" />
</fieldset>
</form>
<p>Les chemins proposés peuvent être inadaptés en cas de liens symboliques ou d'alias web (<em>/~login/</em> par exemple).</p>
<?php
	} else {
		$buffer = "<?php\n// Fichier de configuration généré automatiquement par install.php\n\n";

		$buffer .= "// {{{ Chemins des fichiers\n" 
			 . '$g_sCheminRacine = \''. add_ending_slash($_POST['fileroot']) ."'; // Chemin réel des fichiers d'Esprit\n"
			 . '$g_sCheminRacineWeb = \''. add_ending_slash($_POST['webroot']) ."'; // Adresse des fichiers sur le serveur web\n"
			 . "// }}}\n\n";

		$buffer .= "// {{{ Base de données\n" 
			 . '$g_sNomBdd = \''. $_POST['base'] ."'; // Nom de la base de données\n"
			 . '$g_sNomServeur = \''. $_POST['host'] ."'; // Nom du serveur\n"
			 . '$g_sNomProprietaire = \''. $_POST['user'] ."'; // Nom du propriétaire de la base de données MySQL\n"
			 . '$g_sMotDePasse = \''. $_POST['password'] ."'; // Mot de passe de la base de données MySQL\n"
			 . "// }}}\n\n";

		$buffer .= "// {{{ Informations BdD nécessaires au transfert de formations entre deux plateformes\n"
			 . '$g_sNomServeurTransfert = \''. $_POST['host'] ."';\n"
			 . '$g_sNomProprietaireTransfert = "root";' ."\n"
			 . '$g_sMotDePasseTransfert = "mot_de_passe_root";' ."\n"
			 . "// }}}\n\n";

		$g_sNomProprietaire = $_POST['user']; // ???
		$g_sNomBdd = $_POST['base']; // ???
		$buffer .= "// {{{ Cookie\n"
			 . '$g_sNomCookie = "{'.$g_sNomProprietaire.'}_{'.$g_sNomBdd.'}";	// Nom du cookie'."\n"
			 . "// }}}\n\n"
			 . "// Vérifier l'unicité du couple nom+prénom ?\n"
			 . "define('UNICITE_NOM_PRENOM',TRUE);\n";

		$buffer .= '// {{{ Adresse courrielle
//     Cette adresse courriel sert, dans le cas d\'un problème ou autre
//     (forum), à envoyer un message aux administrateurs de la plate-forme
define("GLOBAL_ESPRIT_ADRESSE_COURRIEL_ADMIN","ute@umh.ac.be");
// }}}' ."\n\n";

		$buffer .= '// {{{ Thèmes
define("THEME","esprit");
// }}}
?'.'>';

		if (@file_put_contents('../include/config.inc',$buffer) != strlen($buffer)) {
			echo "<p class='erreur'>Erreur lors de l'écriture du fichier de configuration.</p>";
			echo '<p>Vérifiez que les accès en écriture pour le serveur web sont autorisés dans le répertoire <em>include</em>.</p>';
			redo_step();
			echo '</body></html>';
			exit;
		}
		echo '<p>Le fichier de configuration a été écrit avec succès.</p>';
		show_next_step();
	}

	break;

// ******************* directories and permissions *******************
case 4:
	if (!is_dir('../tmp')) {
		mkdir('../tmp');
	}
	if (!file_exists('../tmp/mdpncpte')) {
		@touch('../tmp/mdpncpte');
	}
	$ok = true;
	if (!is_writable('../tmp/mdpncpte')) {
		echo "<p class='erreur'>Erreur : le fichier tmp/mdpncpte n'est pas accessible en écriture.<p>";
		echo '<p>Vérifiez que le serveur web a bien des droits d\'écriture sur ce fichier et le répertoire tmp/</p>';
		$ok = false;
	}
	if (!is_writable('../formation')) {
		echo "<p class='erreur'>Erreur : le répertoire formation n'est pas accessible en écriture.<p>";
		echo '<p>Vérifiez que le serveur web a bien des droits d\'écriture sur ce répertoire pour pouvoir plus tard y déposer des documents.</p>';
		$ok = false;
	}
	if (!$ok) {
		redo_step();
		echo '</body></html>';
		exit;
	}
?>
<p>Les permissions d'accès aux fichiers ont été contrôlées avec succès.
</p>
<?php
	show_next_step();
	break;

// ******************* this is the end, my little friend, the end *******************
case 5:
	$DocName = "http://" . $_SERVER['SERVER_NAME'] .
	           ($_SERVER['SERVER_PORT']==80?"":":".$_SERVER['SERVER_PORT']) .
	           $_SERVER['PHP_SELF']; 
	$DirUp = dirname(dirname($DocName));
?>

<p>L'installation est terminée. Pour des <strong>raisons de sécurité</strong>, il est maintenant recommandé de bloquer l'accès au répertoire <em>install/</em> de votre serveur web.
</p>

<p>Vous pouvez désormais vous rendre sur votre <a href="<?php echo $DirUp ?>">nouvelle interface d'Esprit</a>. Le login par défaut est <em>admin</em>, et le mot de passe <em>mdp</em>.
</p>

<p>Une fois connecté en tant qu'<em>admin</em>, vous pouvez créer une formation de test, en cliquant sur <em>Outils</em> qui se trouve dans la barre inférieure du site, puis en choisissant l'outil de conception de cours eConcept.
</p>



<?php
} // end of switch
?>

	</body>
</html>

<?php

function show_next_step() {
	global $step;
?>
<form action="install.php" method="post">
<p>
	<input name="base" value="<?php echo $_POST['base'] ?>" type="hidden" />
	<input name="host" value="<?php echo $_POST['host'] ?>" type="hidden" />
	<input name="user" value="<?php echo $_POST['user'] ?>" type="hidden" />
	<input name="password" value="<?php echo $_POST['password'] ?>" type="hidden" />
	<input name="step" value="<?php echo $step+1 ?>" type="hidden" />
	<input type="submit" value="Étape suivante" />
</p>
</form>
<?php
}

function redo_step() {
	global $step;
?>
<form action="install.php" method="post">
<p>
	<input name="base" value="<?php echo $_POST['base'] ?>" type="hidden" />
	<input name="host" value="<?php echo $_POST['host'] ?>" type="hidden" />
	<input name="user" value="<?php echo $_POST['user'] ?>" type="hidden" />
	<input name="password" value="<?php echo $_POST['password'] ?>" type="hidden" />
	<input name="step" value="<?php echo $step ?>" type="hidden" />
	<input type="submit" value="Refaire cette étape" />
</p>
</form>
<?php
}

function init_db( $host, $user, $password, $base ) {
	$link = mysql_connect($host,$user,$password);
	if (! $link) {
		echo "<p class='erreur'>Erreur de connexion au serveur MySQL.</P>";
	}
	mysql_query("SET NAMES 'utf8'");	// configure le charset du client
	if (! mysql_selectdb($base)) {
		echo "<p class='erreur'>Connexion au serveur MySQL réussie, mais erreur d'accès à la base de données.</p>";
	}
	return $link;
}

function load_mysql_dump($path, $ignoreerrors = false) {
	$file_content = file($path);
	$query = "";
	foreach($file_content as $sql_line) {
		$tsl = trim($sql_line);
		if (($sql_line != "") && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != "#")) {
			$query .= $sql_line;
			if (preg_match("/;\s*$/", $sql_line)) { // end of sql command
				$result = mysql_query($query);
				if (!$result && !$ignoreerrors)
					return FALSE;
				$query = "";
			}
		}
	}
	return TRUE;
}

function get_fileroot() {
	$cwd = str_replace("\\","/",realpath(dirname(__FILE__)));
	return substr($cwd,0,strrpos($cwd,'/')).'/';
}

function get_webroot() {
	$root = str_replace( str_replace("\\","/",realpath($_SERVER["DOCUMENT_ROOT"])),
				"/",
				get_fileroot());
	return "http://".$_SERVER["HTTP_HOST"].'/'. $root;
}

function add_ending_slash( $str ) {
	if ($str[strlen($str)-1] === '/') {
		return $str;
	} else {
		return $str.'/';
	}
}

?>
