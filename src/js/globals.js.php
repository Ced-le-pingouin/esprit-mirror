<?php header("Content-type: text/javascript"); require_once("../globals.inc.php"); ?>

var GLOBALS = new Array();

/* ''' Répertoires */
GLOBALS["racine"] = "<?php echo dir_root_plateform(NULL,FALSE)?>";
GLOBALS["theme"] = "<?php echo dir_theme()?>";
GLOBALS["theme_commun"] = "<?php echo dir_theme_commun()?>";
GLOBALS["admin"] = "<?php echo dir_admin()?>";
GLOBALS["sousactiv"] = "<?php echo dir_sousactiv()?>";
/* ''' */

/* ''' plugins tiny_MCE ''' */
GLOBALS["gestionnaire"] = "<?php echo dir_javascript('tiny_mce/plugins/ajaxfilemanager') ?>";
GLOBALS["lecteur"] = "<?php echo dir_root_plateform(NULL,FALSE)."mediaplayer.swf"?>";
GLOBALS["rep_images"] = "<?php echo dir_root_plateform('depot/Image',FALSE) ?>";
GLOBALS["rep_medias"] = "<?php echo dir_root_plateform('depot/Media',FALSE) ?>";
GLOBALS["rep_tiny"] = "<?php echo dir_javascript('tiny_mce') ?>";
/* ''' */