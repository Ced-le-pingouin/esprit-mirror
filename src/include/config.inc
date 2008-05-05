<?php
// Fichier de configuration généré automatiquement par install.php

// {{{ Chemins des fichiers
$g_sCheminRacine = 'C:/wamp/www/espritCSS/'; // Chemin réel des fichiers d'Esprit
$g_sCheminRacineWeb = 'http://localhost/espritCSS/'; // Adresse des fichiers sur le serveur web
// }}}

// {{{ Base de données
$g_sNomBdd = 'espritcss'; // Nom de la base de données
$g_sNomServeur = 'localhost'; // Nom du serveur
$g_sNomProprietaire = 'root'; // Nom du propriétaire de la base de données MySQL
$g_sMotDePasse = ''; // Mot de passe de la base de données MySQL
// }}}

// {{{ Informations BdD nécessaires au transfert de formations entre deux plateformes
$g_sNomServeurTransfert = 'localhost';
$g_sNomProprietaireTransfert = "root";
$g_sMotDePasseTransfert = "mot_de_passe_root";
// }}}

// {{{ Cookie
$g_sNomCookie = "{root}_{espritcss}";	// Nom du cookie
// }}}

// V�rifier l'unicit� du couple nom+pr�nom ?
define('UNICITE_NOM_PRENOM',TRUE);
// {{{ Adresse courrielle
//     Cette adresse courriel sert, dans le cas d'un problème ou autre
//     (forum), à envoyer un message aux administrateurs de la plate-forme
define("GLOBAL_ESPRIT_ADRESSE_COURRIEL_ADMIN","ute@umh.ac.be");
// }}}

// {{{ Thèmes
define("THEME","esprit");
// }}}
?>