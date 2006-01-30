<?php
require_once("globals.inc.php");
$sLoginUrl = "login.php"
	.(empty($HTTP_GET_VARS["codeEtat"])
		? NULL
		: "?codeEtat=".$HTTP_GET_VARS["codeEtat"]);
$oTpl = new Template(dir_theme("login/login-index.tpl",FALSE,TRUE));
$oTpl->remplacer("{login->url}",$sLoginUrl);
$oTpl->afficher();
?>
