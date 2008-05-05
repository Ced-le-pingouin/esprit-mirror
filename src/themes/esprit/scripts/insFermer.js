function insererBoutonFermer()
{
	var barre = document.getElementById("barreBas");
	if (barre == null)
	{
		barre = document.createElement("div");
		barre.setAttribute("id", "barreBas");
		var page = document.getElementsByTagName("body")[0];
		page.appendChild(barre);
	}
	var fermer = document.createElement("a");
	fermer.setAttribute("href", "javascript:window.close();");
	fermer.appendChild(document.createTextNode("Fermer"));
	barre.appendChild(fermer);
}

var insFermer_ancienOnLoad = window.onload || new Function();
window.onload = function() { insFermer_ancienOnLoad(); insererBoutonFermer(); }