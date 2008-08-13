function flotterFenetreErreurs()
{
	var fenetreErreurs = document.getElementById("erreurs");
	if (fenetreErreurs != null && fenetreErreurs.hasChildNodes())
	{
		fenetreErreurs.className = "erreursFenetre";
			
		var barre = document.createElement("div");
		barre.className = "barreBas2";
		fenetreErreurs.appendChild(barre);
		
		var fermer = document.createElement("a");
		fermer.setAttribute("href", "javascript:document.getElementById('erreurs').style.visibility = 'hidden'; void(0);");
		fermer.appendChild(document.createTextNode("Fermer"));
		barre.appendChild(fermer);
	}
}

var fenErreurs_ancienOnLoad = window.onload || new Function();
window.onload = function() { fenErreurs_ancienOnLoad(); flotterFenetreErreurs(); }