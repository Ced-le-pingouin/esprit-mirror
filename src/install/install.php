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
// Copyright:
// (C) 2001-2006 Unite de Technologie de l'Education, 
//               Universite de Mons-Hainaut, Belgium. 
// (C) 2006 Grenoble UniversitÃ©s

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Installation d'Esprit</title>
		<link rel="stylesheet" type="text/css" href="install.css" />
	</head>
	<body>
		<h1>Installation d'Esprit</h1>
<?php
/*
	Installateur d'Esprit (en cours, trÃ¨s partiellement testÃ©)
	Reste Ã  faire :
		- tester
		- modifier /INSTALL et /setup (en distinguant installation manuelle et automatique)
		- Ã©crire une fonction javascript qui vÃ©rifie les donnÃ©es du formulaire (pb de SÃCURITÃ en particulier)
*/

$step=1;
if (isset($_GET['step']) || isset($_POST['step'])) {
	$step=($_GET['step']?$_GET['step']:$_POST['step']);
} else {
	if (file_exists('../include/config.inc')) {
		echo "<P>Configuration interrompue : Esprit semble dÃ©jÃ  configurÃ©.</P>";
		echo '</body></html>';
		exit;
	}
}
?>
		<h2>Ãtape <?= $step ?></h2>

<?php
switch ($step) {
// ******************* Getting database info *******************
case 1:
?>
<p>Avant de commencer l'installation, vous devez avoir crÃ©Ã© une base de donnÃ©e pour Esprit et un utilisateur MySQL associÃ©.
</p>

<p>
Par exemple, les commandes suivantes sous Linux crÃ©ent une table <em>esprit</em> administrÃ©e par <em>esprit-admin</em>&nbsp;:
<pre>
# mysql -u root -p
&gt; CREATE DATABASE esprit;
&gt; GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,INDEX,ALTER ON esprit.* TO 'esprit-admin'@localhost IDENTIFIED BY 'motdepasse';
&gt; quit
# mysqladmin -u root -p reload
</pre>
Vous pouvez Ã©galement utiliser phpmyadmin s'il est installÃ©.
</p>

<h3>Saisie</h3>

<p>Veuillez saisir ci-dessous le nom de la base crÃ©Ã©e et de son utilisateur MySQL.
</p>
<form action="install.php" method="post">
<p>
	Base de donnÃ©e : <INPUT type="text" name="base" /><br />
	Serveur BdD : <INPUT type="text" name="host" value="localhost" /><br />
	Login de l'utilisateur : <INPUT type="text" name="user" /><br />
	Mot de passe de l'utilisateur : <INPUT type="password" name="password" /><br /><br />
	<INPUT name="step" value="<?= $step+1 ?>" type="hidden" />
	<INPUT type="submit" value="Ãtape suivante" />
</p>
</form>

<?php
	break;
// ******************* Filling up the database *******************
case 2:
	if (!isset($_POST['base']) || !isset($_POST['user']) || !isset($_POST['password'])) {
		echo '<p>Informations incomplÃ¨tes, retournez Ã  <a href="install.php?step=1">l\'Ã©tape prÃ©cÃ©dente</a>.</p>';
		echo '</body></html>';
		exit;
	}
	$link = mysql_connect($_POST['host'],$_POST['user'],$_POST['password']);
	if (! $link) {
		echo "<P>Erreur de connexion Ã  la base de donnÃ©es.</P>";
		echo '<p>Retournez Ã  <a href="install.php?step=1">l\'Ã©tape prÃ©cÃ©dente</a>.</p>';
		echo '</body></html>';
		exit;
	}
	if (! mysql_selectdb($_POST['base'])) {
		echo "<P>Connexion rÃ©ussie, mais erreur d'accÃ¨s Ã  la base de donnÃ©es.</P>";
		echo '<p>Retournez Ã  <a href="install.php?step=1">l\'Ã©tape prÃ©cÃ©dente</a>.</p>';
		echo '</body></html>';
		exit;
	}
	// filling the database
	$files = glob("sql/*.sql");
	sort($files);
	foreach ($files as $filename) {
		if (!load_mysql_dump($filename)) {
			echo "<P>Connexion rÃ©ussie Ã  la base de donnÃ©e, mais erreur lors de l'importation.</P>";
			echo "<code>", mysql_error(), "</code>";
			echo '</body></html>';
			exit;
		}
	}
?>
<p>La base de donnÃ©e a Ã©tÃ© remplie avec succÃ¨s.
</p>
<?php
	show_next_step();
	break;

// ******************* creating config file *******************
case 3:
	$buffer = "<?php\n// Fichier de configuration gÃ©nÃ©rÃ© automatiquement par install.php\n\n"
	          . "// {{{ Base de donnÃ©es\n" 
	          . '$g_sNomBdd = \''. $_POST['base'] ."'; // Nom de la base de donnÃ©es\n"
	          . '$g_sNomServeur = \''. $_POST['host'] ."'; // Nom du serveur\n"
	          . '$g_sNomProprietaire = \''. $_POST['user'] ."'; // Nom du propriÃ©taire de la base de donnÃ©es MySQL\n"
	          . '$g_sMotDePasse = \''. $_POST['password'] ."'; // Mot de passe de la base de donnÃ©es MySQL\n"
	          . "// }}}\n\n";

	$buffer .= "// {{{ Informations BdD nÃ©cessaires au transfert de formations entre deux plateformes\n"
	           . '$g_sNomServeurTransfert = \''. $_POST['host'] ."';\n"
	           . '$g_sNomProprietaireTransfert = "root";' ."\n"
	           . '$g_sMotDePasseTransfert = "mot_de_passe_root";' ."\n"
	           . "// }}}\n\n";

	$buffer .= "// {{{ Cookie\n"
	           . '$g_sNomCookie = "{'.$g_sNomProprietaire.'}_{'.$g_sNomBdd.'}";	// Nom du cookie'."\n"
	           . "// }}}\n\n";

	$buffer .= '// {{{ Adresse courrielle
//     Cette adresse courriel sert, dans le cas d\'un problÃ¨me ou autre
//     (forum), Ã  envoyer un message aux administrateurs de la plate-forme
define("GLOBAL_ESPRIT_ADRESSE_COURRIEL_ADMIN","ute@umh.ac.be");
// }}}'
	           ."\n\n";

	$buffer .= '// {{{ ThÃ¨mes
define("THEME","esprit");
// }}}
?>';

	if (file_put_contents('../include/config.inc',$buffer) != strlen($buffer)) {
		echo '<p>Erreur lors de l\'Ã©criture du fichier de configuration.</p>';
		echo '<p>VÃ©rifiez que les accÃ¨s en Ã©criture sont autorisÃ©s dans le rÃ©pertoire <em>include</em>.</p>';
		redo_step();
		echo '</body></html>';
		exit;
	}
?>
<p>Le fichier de configuration a Ã©tÃ© Ã©crit avec succÃ¨s.
</p>
<?php
	show_next_step();
	break;

// ******************* directories and permissions *******************
case 4:
	if (!is_dir('../tmp')) {
		mkdir('../tmp');
	}
	if (!file_exists('../tmp/mdpcnte')) {
		touch('../tmp/mdpcnte');
	}
	if (!is_writable('../tmp/mdpcnte')) {
		echo '<p>Erreur : le fichier tmp/mdpcnte n\'est pas accessible en Ã©criture.<p>';
		echo '<p>VÃ©rifiez que le serveur web a bien des droits d\'Ã©criture sur ce fichier et le rÃ©pertoire tmp/</p>';
		redo_step();
		echo '</body></html>';
		exit;
	}
?>
<p>Les permissions d'accÃ¨s aux fichiers ont Ã©tÃ© contrÃ´lÃ©es avec succÃ¨s.
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

<p>L'installation est terminÃ©e. Pour des <strong>raisons de sÃ©curitÃ©</strong>, il est maintenant recommandÃ© d'effacer le rÃ©pertoire <em>install/</em> de votre serveur web.
</p>

<p>Vous pouvez dÃ©sormais vous rendre sur votre <a href="<?= $DirUp ?>">nouvelle interface d'Esprit</a>. Le login par dÃ©faut est <em>admin</em>, et le mot de passe <em>mdp</em>.
</p>

<p>Une fois connectÃ© en tant qu'<em>admin</em>, vous pouvez crÃ©er une formation de test, en cliquant sur <em>Outils</em> qui se trouve dans la barre infÃ©rieure du site, puis en vous choisissant l'outil de conception de cours eConcept.
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
	<INPUT name="base" value="<?= $_POST['base'] ?>" type="hidden" />
	<INPUT name="host" value="<?= $_POST['host'] ?>" type="hidden" />
	<INPUT name="user" value="<?= $_POST['user'] ?>" type="hidden" />
	<INPUT name="password" value="<?= $_POST['password'] ?>" type="hidden" />
	<INPUT name="step" value="<?= $step+1 ?>" type="hidden" />
	<INPUT type="submit" value="Ãtape suivante" />
</p>
</form>
<?php
}

function redo_step() {
	global $step;
?>
<form action="install.php" method="post">
<p>
	<INPUT name="base" value="<?= $_POST['base'] ?>" type="hidden" />
	<INPUT name="host" value="<?= $_POST['host'] ?>" type="hidden" />
	<INPUT name="user" value="<?= $_POST['user'] ?>" type="hidden" />
	<INPUT name="password" value="<?= $_POST['password'] ?>" type="hidden" />
	<INPUT name="step" value="<?= $step ?>" type="hidden" />
	<INPUT type="submit" value="Refaire cette Ã©tape" />
</p>
</form>
<?php
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

// This one already exists in PHP5, but not in PHP4
function file_put_contents($filename, $content) {
	if (file_exists($filename)) {
		echo "Le fichier ($filename) existe dÃ©jÃ ";
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
		echo "Impossible d'Ã©crire dans le fichier ($filename)";
		return FALSE;
	}
	return $written;
}
?>
