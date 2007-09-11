function afficherCadreCopier()
{
	document.getElementById("cadreColler").style.display = "none";
	document.getElementById("cadreCopier").style.display = "";
//!!! setAttribute("class", "...") ne prend pas la classe en compte dans IE6/7
	//document.getElementById("ongletCopier").setAttribute("class", 
	//                                                     "onglet actif");
	//document.getElementById("ongletColler").setAttribute("class", "onglet");
	document.getElementById("ongletCopier").className = "onglet actif";
	document.getElementById("ongletColler").className = "onglet";
	document.getElementById("ongletCourant").value = "copier";
	return false;
}

function afficherCadreColler()
{
	document.getElementById("cadreCopier").style.display = "none";
	document.getElementById("cadreColler").style.display = "";
//!!! setAttribute("class", "...") ne prend pas la classe en compte dans IE6/7	
	//document.getElementById("ongletCopier").setAttribute("class", "onglet");
	//document.getElementById("ongletColler").setAttribute("class",
	//                                                     "onglet actif");
	document.getElementById("ongletCopier").className = "onglet";
	document.getElementById("ongletColler").className = "onglet actif";
	document.getElementById("ongletCourant").value = "coller";
	return false;
}

function restaurerOngletCourant()
{
	var ongletCourant = document.getElementById("ongletCourant").value;
	if (ongletCourant == "coller")
		afficherCadreColler();
	else //if (ongletCourant == "copier")
		afficherCadreCopier();
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
	liCopier.setAttribute("id", "ongletCopier");
	liCopier.appendChild(copier);
	var liColler = document.createElement("li");
	liColler.setAttribute("id", "ongletColler");
	liColler.appendChild(coller);
	
	var ul = document.createElement("ul");
//!!! setAttribute("class", "...") ne prend pas la classe en compte dans IE6/7
	//ul.setAttribute("class", "onglets");
	ul.className = "onglets"; // comme ceci, ok
	ul.appendChild(liCopier);
	ul.appendChild(liColler);
	
	var cadreOnglets = document.getElementById("cadreOnglets");
	cadreOnglets.parentNode.insertBefore(ul, cadreOnglets);	
}

function initPage()
{
	ajouterOngletsCopierColler();
	restaurerOngletCourant();
}

var copierCollerForm_ancienOnLoad = window.onload || new Function();
window.onload = function() { copierCollerForm_ancienOnLoad(); initPage(); };