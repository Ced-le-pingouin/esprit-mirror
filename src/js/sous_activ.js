function forum(v_sParamsUrl,v_sNomFenetre)
{
	var sForum = GLOBALS["sousactiv"] + "forum2/forum-index.php";
	var iLargeurFenetre = screen.availWidth-80;
	var iHauteurFenetre = screen.availHeight-80;
	var sOptions = ',menubar=0,scrollbars=0,statusbar=0,resizable=1';
	var oWinForum = PopupCenter(sForum + v_sParamsUrl,v_sNomFenetre,iLargeurFenetre,iHauteurFenetre,sOptions);
	oWinForum.focus();
	return false;
}

function formulaire(v_sParamsUrl,v_sNomFenetre)
{
	var sFormulaire = GLOBALS["sousactiv"] + "formulaire/modifier_formulaire.php";
	var iLargeurFenetre = screen.availWidth-80;
	var iHauteurFenetre = screen.availHeight-80;
	var sOptions = ',menubar=0,scrollbars=1,statusbar=0,resizable=1';
	var oWinFormulaire = PopupCenter(sFormulaire + v_sParamsUrl,v_sNomFenetre,iLargeurFenetre,iHauteurFenetre,sOptions);
	oWinFormulaire.focus();
	return false;
}

function tableau_de_bord(v_oObj) {
	var sUrl = v_oObj.href;
	var iLargeurFen = screen.width-30;
	var iHauteurFen = screen.height-60;
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=no";
	var oWin = PopupCenter(sUrl,"winTableauDeBord",iLargeurFen,iHauteurFen,sOptionsFenetre,top.frames["AWARENESS"]);
	oWin.focus();
	return false;
}

function autorisation_action(v_oObj) {
	var sUrl = v_oObj.href;
	var iLargeurFen = 640;
	var iHauteurFen = 480;
	var sOptionsFenetre = ",status=no,resizable=yes,scrollbars=no";
	var oWin = PopupCenter(sUrl,"winAutorisationAction",iLargeurFen,iHauteurFen,sOptionsFenetre);
	oWin.focus();
	return false;
}
