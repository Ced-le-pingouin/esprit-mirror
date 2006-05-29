function propos(v_sRepTheme)
{
	var sUrl = v_sRepTheme + "propos/propos.htm";
	var sNom = "WinPropos";
	var iLargeurFenetre = 640;
	var iHauteurFenetre = 430;
	var iPositionGauche = (screen.width-iLargeurFenetre)/2;
	var iPositionHaut = (screen.height-iHauteurFenetre)/2;
	var sCaracteristiques = "left=" + iPositionGauche
		+ ",top=" + iPositionHaut
		+ ",width=" + iLargeurFenetre
		+ ",height=" + iHauteurFenetre;
	var w =	window.open(sUrl,sNom,sCaracteristiques);
	w.focus();
}

