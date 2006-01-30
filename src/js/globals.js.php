<?php header("Content-type: text/javascript"); require_once("../globals.inc.php"); ?>

var GLOBALS = new Array();

/* ''' Répertoires */
GLOBALS["racine"] = "<?=dir_root_plateform(NULL,FALSE)?>";
GLOBALS["theme"] = "<?=dir_theme()?>";
GLOBALS["theme_commun"] = "<?=dir_theme_commun()?>";
GLOBALS["admin"] = "<?=dir_admin()?>";
GLOBALS["sousactiv"] = "<?=dir_sousactiv()?>";
/* ''' */
