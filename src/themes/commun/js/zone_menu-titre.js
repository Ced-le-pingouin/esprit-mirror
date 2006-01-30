function changerTitres(v_sTitre,v_sSousTitre) {
	if (document.getElementById) {
		if (v_sTitre != null && document.getElementById("titre"))
			document.getElementById("titre").innerHTML = unescape(v_sTitre);
		
		if (v_sSousTitre != null && document.getElementById("sous_titre"))
			document.getElementById("sous_titre").innerHTML = unescape(v_sSousTitre);
		else
			setTimeout("changerTitres('" + v_sTitre + "','" + v_sSousTitre + "')",1000);
	}
}

