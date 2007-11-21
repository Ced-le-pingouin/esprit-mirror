function centrerFenetre(v_iLargeur,v_iHauteur)
{
	var iTop = ((screen.height-v_iHauteur)/2)-30;
	if (iTop < 10) iTop = 10;
	return "left=" + ((screen.width-v_iLargeur)/2)
		+ ",top=" + iTop
		+ ",width=" + v_iLargeur
		+ ",height=" + v_iHauteur
		+ ",resizable=yes";
}

function PopupCenter(v_sUrl,v_sNom,v_iLargeur,v_iHauteur,v_sCaracteristiques,v_oWin)
{
	var wPopupCenter;
	var sCaracteristiques = centrerFenetre(v_iLargeur,v_iHauteur)
		+ v_sCaracteristiques;
	
	if (typeof(v_oWin) == "undefined")
		v_oWin = window;
	wPopupCenter = v_oWin.open(v_sUrl,v_sNom,sCaracteristiques);
	wPopupCenter.focus();
	return wPopupCenter;
}

function PopupCenterOffset(url,nom,largeur,hauteur,options,offsetX,offsetY)
{
	var top=(screen.height-hauteur)/2 + offsetY;
	var left=(screen.width-largeur)/2 + offsetX;
	return window.open(url,nom,"top="+top+",left="+left+",width="+largeur+",height="+hauteur+",resizable=yes,"+options);
}
