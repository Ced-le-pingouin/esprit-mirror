var g_sIdBoutonEvaluer = null;

function surbrillance(v_sIdElem) {
	if (g_sIdBoutonEvaluer != v_sIdElem && document.getElementById) {
		if (g_sIdBoutonEvaluer != null)
			document.getElementById(g_sIdBoutonEvaluer).className = 'soumettre_passif';
		
		document.getElementById(v_sIdElem).className = 'soumettre';
		g_sIdBoutonEvaluer = v_sIdElem;
	}
}

function sauverPosYPage() {
	var oWindow = new DOMWindow(self);
	top.g_iPosYPagePrincipale = oWindow.pageYOffset();
}

function init() {
	if (typeof(top.g_iPosYPagePrincipale) == "undefined")
		top.g_iPosYPagePrincipale = 0;
	
	var oWindow = new DOMWindow(self);
	oWindow.scrollTo(0,top.g_iPosYPagePrincipale);
}
// utilisation d'ancre pour défiler vers le formulaire inline -> désactivation de la position de page
//window.onload = init;

