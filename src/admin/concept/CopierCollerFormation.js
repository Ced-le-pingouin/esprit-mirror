function afficherCadreCopier()
{
	document.getElementById("cadreColler").style.display = "none";
	document.getElementById("cadreCopier").style.display = "";
	return false;
}

function afficherCadreColler()
{
	document.getElementById("cadreCopier").style.display = "none";
	document.getElementById("cadreColler").style.display = "";
	return false;
}

function ajouterOngletsCopierColler()
{
	var copier = document.createElement("a");
	copier.setAttribute("href", "#");
	copier.onclick = afficherCadreCopier;
	copier.appendChild(document.createTextNode("Copier"));
	
	var coller = document.createElement("a");
	coller.setAttribute("href", "#");
	coller.onclick = afficherCadreColler;
	coller.appendChild(document.createTextNode("Coller"));
	
	var liCopier = document.createElement("li");
	var liColler = document.createElement("li");
	liCopier.appendChild(copier);
	liColler.appendChild(coller);
	
	var ul = document.createElement("ul");
	ul.appendChild(liCopier);
	ul.appendChild(liColler);
	
	var contenu = document.getElementById("contenuPrincipal");
	contenu.insertBefore(ul, contenu.firstChild);
}

function initPage()
{
	ajouterOngletsCopierColler();
	afficherCadreCopier();
}

var copierCollerForm_ancienOnLoad = window.onload || new Function();
window.onload = function() { copierCollerForm_ancienOnLoad(); initPage(); };