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
  Installateur d'Esprit (en cours, très partiellement testé)
  Reste à faire :
    - tester
    - placer les .sql dans le répertoire
    - modifier /INSTALL et /setup (en distinguant installation manuelle et automatique)
*/

$step=1;
if (isset($_GET['step']) || isset($_POST['step'])) {
  if (file_exists('../include/config.inc')) {
    echo "<P>Configuration interrompue : Esprit semble déjà configuré.</P>";
    echo '</body></html>';
    exit;
  }
  $step=($_GET['step']?$_GET['step']:$_POST['step']);
}
?>
    <h2>Étape <?= $step ?></h2>

<?php
switch ($step) {
// ******************* Getting database info *******************
//! \todo test the form fields with javascript
case 1:
?>
<p>Avant de commencer l'installation, vous devez avoir créé une base de donnée pour Esprit et un utilisateur MySQL associé.
</p>

<p>
Par exemple, les commandes suivantes sous Linux créent une table <em>esprit</em> administrée par <em>esprit-admin</em>&nbsp;:
<pre>
# mysql -u root -p
&gt; CREATE DATABASE esprit;
&gt; GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,INDEX,ALTER ON esprit.* TO 'esprit-admin'@localhost IDENTIFIED BY 'motdepasse';
&gt; quit
# mysqladmin -u root -p reload
</pre>
Vous pouvez également utiliser phpmyadmin s'il est installé.
</p>

<h3>Saisie</h3>

<p>Veuillez saisir ci-dessous le nom de la base créée et de son utilisateur MySQL.
</p>
<form action="install.php" method="post">
<p>
  Base de donnée : <INPUT type="text" name="base" /><br />
  Serveur BdD : <INPUT type="text" name="host" value="localhost" /><br />
  Login de l'utilisateur : <INPUT type="text" name="user" /><br />
  Mot de passe de l'utilisateur : <INPUT type="password" name="password" /><br /><br />
  <INPUT name="step" value="<?= $step+1 ?>" type="hidden" />
  <INPUT type="submit" value="Étape suivante" />
</p>
</form>

<?php
  break;
// ******************* Filling up the database *******************
case 2:
  if (!isset($_POST['base']) || !isset($_POST['user']) || !isset($_POST['password'])) {
    echo '<p>Informations incomplètes, retournez à <a href="install.php?step=1">l\'étape précédente</a>.</p>';
    echo '</body></html>';
    exit;
  }
  $link = mysql_connect($_POST['host'],$_POST['user'],$_POST['password']);
  if (! $link) {
    echo "<P>Erreur de connexion à la base de données.</P>";
    echo '<p>Retournez à <a href="install.php?step=1">l\'étape précédente</a>.</p>';
    echo '</body></html>';
    exit;
  }
  if (! mysql_selectdb($_POST['base'])) {
    echo "<P>Connexion réussie, mais erreur d'accès à la base de données.</P>";
    echo '<p>Retournez à <a href="install.php?step=1">l\'étape précédente</a>.</p>';
    echo '</body></html>';
    exit;
  }
  // filling the database
  $files = glob("*.sql");
  foreach ($files as $filename) {
    if (!load_mysql_dump($filename)) {
      echo "<P>Connexion réussie à la base de donnée, mais erreur lors de l'importation.</P>";
      echo '</body></html>';
      exit;
    }
  }
?>
<p>La base de donnée a été remplie avec succès.
</p>
<form action="install.php" method="post">
<p>
  <INPUT name="base" value="<?= $_POST['base'] ?>" type="hidden" />
  <INPUT name="host" value="<?= $_POST['host'] ?>" type="hidden" />
  <INPUT name="user" value="<?= $_POST['user'] ?>" type="hidden" />
  <INPUT name="password" value="<?= $_POST['password'] ?>" type="hidden" />
  <INPUT name="step" value="<?= $step+1 ?>" type="hidden" />
  <INPUT type="submit" value="Étape suivante" />
</p>
</form>


<?php
  break;
// ******************* creating config file *******************
case 3:
  $buffer = "// Fichier de configuration généré automatiquement par install.php\n\n"
            . "// {{{ Base de données\n" 
            . '$g_sNomBdd = '. $_POST['base'] ."; // Nom de la base de données\n"
            . '$g_sNomServeur = '. $_POST['host'] ."; // Nom du serveur\n"
            . '$g_sNomProprietaire = '. $_POST['user'] ."; // Nom du propriétaire de la base de données MySQL\n"
            . '$g_sMotDePasse = '. $_POST['password'] ."; // Mot de passe de la base de données MySQL\n"
            . "// }}}\n\n";

  $buffer .= "// {{{ Informations BdD nécessaires au transfert de formations entre deux plateformes\n"
             . '$g_sNomServeurTransfert = '. $_POST['host'] .";\n"
             . '$g_sNomProprietaireTransfert = "root";' ."\n"
             . '$g_sMotDePasseTransfert = "mot_de_passe_root";' ."\n"
             . "// }}}\n\n";

  $buffer .= "// {{{ Cookie\n"
             . '$g_sNomCookie = "{'.$g_sNomProprietaire.'}_{'.$g_sNomBdd.'}";	// Nom du cookie'."\n"
             . "// }}}\n\n";

  $buffer .= '// {{{ Adresse courrielle
//     Cette adresse courriel sert, dans le cas d\'un problème ou autre
//     (forum), à envoyer un message aux administrateurs de la plate-forme
define("GLOBAL_ESPRIT_ADRESSE_COURRIEL_ADMIN","'
             . 'ute@umh.ac.be'
             . '");
// }}}' ."\n\n";

  $buffer .= '// {{{ Thèmes
define("THEME","esprit");
// }}}
';

  if (file_put_contents('../include/config.inc',$buffer) != strlen($buffer)) {
    echo '<p>Erreur lors de l\'écriture du fichier de configuration.</p>';
    echo '<p>Vérifiez que les accès en écriture sont autorisés dans le répertoire <em>include</em>.</p>';
    echo '</body></html>';
    exit;
  }
?>
<p>Le fichier de configuration a été écrit avec succès.
</p>

<?php
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
    echo '<p>Erreur : le fichier tmp/mdpcnte n\'est pas accessible en écriture.<p>';
    echo '<p>Vérifiez que le serveur web a bien des droits d\'écriture sur ce fichier et le répertoire tmp/</p>';
    echo '</body></html>';
    exit;
  }
?>
<p>Les permissions d'accès aux fichiers ont été contrôlées avec succès.
</p>


<?php
  break;
// ******************* this is the end, my little friend, the end *******************
case 5:
  $DocName = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']; 
  $DirUp = dirname(dirname($DocName));
?>

<p>L'installation est terminée. Il est maintenant recommandé d'effacer le répertoire <em>install/</em> de votre serveur web.
</p>

<p>Vous pouvez désormais vous rendre sur votre <a href="<?= $DirUp ?>">nouvelle interface d'Esprit</a>. Le login par défaut est <em>admin</em>, et le mot de passe <em>mdp</em>.
</p>

<p>Une fois connecté en tant qu'<em>admin</em>, vous pouvez créer une formation de test, en cliquant sur <em>Outils</em> qui se trouve dans la barre inférieure du site, puis en vous choisissant l'outil de conception de cours eConcept.
</p>



<?php
} // end of switch
?>

  </body>
</html>

<?php
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
          return(mysql_error());
        $query = "";
      }
    }
  }
  return false;
}

// This one already exists in PHP5, but not in PHP4
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
?>