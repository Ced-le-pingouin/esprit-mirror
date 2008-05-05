<?php header("Content-type: text/javascript"); require_once("../globals.inc.php"); ?>

var GLOBALS = new Array();

/* ''' Répertoires */
GLOBALS["racine"] = "<?php echo dir_root_plateform(NULL,FALSE)?>";
GLOBALS["theme"] = "<?php echo dir_theme()?>";
GLOBALS["theme_commun"] = "<?php echo dir_theme_commun()?>";
GLOBALS["admin"] = "<?php echo dir_admin()?>";
GLOBALS["sousactiv"] = "<?php echo dir_sousactiv()?>";
/* ''' */
