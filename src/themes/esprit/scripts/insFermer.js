function insererBoutonFermer(fctAAppeler)
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
	fermer.setAttribute('href', '#');
	fermer.onclick = function()
	{
		if (fctAAppeler)
			fctAAppeler();

		window.close();
		return false;
	}
	fermer.appendChild(document.createTextNode("Fermer"));
	
	barre.appendChild(fermer);
}