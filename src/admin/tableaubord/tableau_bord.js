var g_asLigneEnEvidence = null;

function ret_entetes(v_sId) {
	return v_sId.match(/u(.+)l(.+)c(.+)/);
}

function mettre_en_evidence_ligne(v_sLigneTable,v_sClasseCSS) {
	var tbody = document.getElementById("tb" + v_sLigneTable);
	
	if (tbody && tbody.rows.length == 1) {
		var td = tbody.rows.item(0).childNodes;
		
		for (var i=5; i<td.length; i++)
			if (td.item(i).nodeName == "TD")
				td.item(i).className = v_sClasseCSS;
	}
}

function mettre_en_evidence(v_asLigne) {
	var l  = "u" + v_asLigne[1] + "l" + v_asLigne[2];						// Ligne contenant toutes les données de l'étudiant
	var th = "u" + v_asLigne[1] + "c" + v_asLigne[3];						// Titre de la colonne
	var lc = "u" + v_asLigne[1] + "l" + v_asLigne[2] + "c" + v_asLigne[3];	// Colonne contenant la valeur sous le pointeur de la souris
	
	mettre_en_evidence_ligne(l,"cellule_valeur_surbrillance");
	
	document.getElementById(l).className  = "ligne_en_evidence";
	document.getElementById(th).className = "surbrillance";
	document.getElementById(lc).className = "cellule_valeur_en_evidence";
	
	var title = top.oMenu().document.getElementById("id_title");
	
	if (title)
		title.innerHTML = document.getElementById(th).innerHTML
			+ " ("
			+ document.getElementById(l).childNodes.item(0).innerHTML.replace("&nbsp;"," ")
			+ ")";
	
	g_asLigneEnEvidence = [l,th,lc];
}

function retirer_en_evidence() {
	var l  = g_asLigneEnEvidence[0];
	var th = g_asLigneEnEvidence[1];
	var lc = g_asLigneEnEvidence[2];
	
	mettre_en_evidence_ligne(l,"cellule_valeur");
	
	document.getElementById(l).className  = "cellule_etudiant";
	document.getElementById(th).className = "";
	document.getElementById(lc).className = "cellule_valeur";
	
	var title = top.oMenu().document.getElementById("id_title");
	
	if (title)
		title.innerHTML = "";
}

function zone_de_cours(v_sParamsUrl) {
	if (top.opener)
		top.opener.top.frames["INDEX"].location = GLOBALS["racine"]
			+ "zone_cours-index.php"
			+ v_sParamsUrl;
	return false;
}

window.onload = function() {
	var elems = document.getElementsByTagName("td");
	var s = /u[0-9]{1,}l[0-9]{1,}c[0-9]{1,}/;
	
	for (var i=0; i<elems.length; i++) {
		if (elems.item(i).getAttribute("id") != null) {
			if (elems.item(i).getAttribute("id").search(s) != -1) {
				elems.item(i).className = "cellule_valeur";
				elems.item(i).onmouseover = function() {
					mettre_en_evidence(ret_entetes(this.id));
					return false;
				};
				
				elems.item(i).onmouseout = function() {
					retirer_en_evidence();
					return false;
				};
			}
		}
	}
};
