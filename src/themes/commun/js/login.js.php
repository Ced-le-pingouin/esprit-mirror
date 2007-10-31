<?php require_once("../../../globals.inc.php"); ?>

function mdp_oublier()
{
	var sUrl = "<?php echo dir_admin('personne','mdp_oublier-index.php')?>";
	var iLargeurFenetre = 600;
	var iHauteurFenetre = 550;
	var sOptions = ',menubar=0,scrollbars=0,statusbar=0,resizable=1';
	var oWinMdpOublier = PopupCenter(sUrl,"winMdpOublier",iLargeurFenetre,iHauteurFenetre,sOptions);
	oWinMdpOublier.focus();
	return false;
}

