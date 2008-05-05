function changerTitrePrincipal(v_sTitrePrincipal)
{
	if (v_sTitrePrincipal.length < 1) return;
	if (document.getElementById("titre_principal"))
		document.getElementById("titre_principal").innerHTML = unescape(v_sTitrePrincipal);
	else
		setTimeout("changerTitrePrincipal('" + v_sTitrePrincipal + "')",1000);
}

function changerSousTitre(v_sSousTitre)
{
	if (v_sSousTitre.length < 1) return;
	if (document.getElementById("sous_titre"))
		document.getElementById("sous_titre").innerHTML = "&nbsp;&raquo;&nbsp;"
			+ unescape(v_sSousTitre);
	else
		setTimeout("changerSousTitre('" + v_sSousTitre + "')",2000);
}

