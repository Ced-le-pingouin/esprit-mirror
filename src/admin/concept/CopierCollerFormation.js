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
	copier.appendChild(document.createTextNode("Copier de"));
	
	var coller = document.createElement("a");
	coller.setAttribute("href", "#");
	coller.onclick = afficherCadreColler;
	coller.appendChild(document.createTextNode("Coller vers"));
	
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

function enleverBoutonsChoisir()
{
	// les boutons Choisir disparaissent
	document.getElementsByName("changerFormationSrc")[0].style.display = "none";
	document.getElementsByName("changerFormationDest")[0].style.display = "none";
	// tout changement dans leur liste associée est automatiquement soumis
	document.getElementById("idFormationSrcId").onchange = function()
	 { document.getElementsByName("changerFormationSrc")[0].click(); };
	document.getElementById("idFormationDestId").onchange = function()
	 { document.getElementsByName("changerFormationDest")[0].click(); };
}

function initPage()
{
	ajouterOngletsCopierColler();
	restaurerOngletCourant();
	enleverBoutonsChoisir();
}

var copierCollerForm_ancienOnLoad = window.onload || new Function();
window.onload = function() { copierCollerForm_ancienOnLoad(); initPage(); };