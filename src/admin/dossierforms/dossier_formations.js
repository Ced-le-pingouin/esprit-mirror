function ret_id_dossiers_forms() {
	return document.getElementsByName("idDossierForms").item(0).value;
}

function def_id_dossiers_forms(v_iIdDossierForms) {
	document.getElementsByName("idDossierForms").item(0).value = v_iIdDossierForms;
}

function reclasser_formations(v_oSelect) {
	
	var iOrdre = v_oSelect.selectedIndex;
	var iOrdreAncien = 0;
	var iIdxOrdre = 0;
	var oSelects = document.getElementsByTagName("select");
	var iNbSelects = oSelects.length;
	
	for (var i=0; i<v_oSelect.options.length; i++) {
		if (v_oSelect.options[i].defaultSelected)
			iOrdreAncien = i;
		v_oSelect.options[i].defaultSelected = false;
	}
	
	v_oSelect.options[v_oSelect.selectedIndex].defaultSelected = true;
	
	if (iOrdreAncien < iOrdre) {
		for (var i=0; i<iNbSelects; i++) {
			iIdxOrdre = oSelects.item(i).selectedIndex;
			
			if (v_oSelect.formIndex == i || iIdxOrdre > iOrdre || iIdxOrdre < iOrdreAncien)
				continue;
			
			if (iIdxOrdre > 0)
				iIdxOrdre--;
			
			oSelects.item(i).selectedIndex = iIdxOrdre;
			
			for (var j=0; j<oSelects.item(i).options.length; j++)
				oSelects.item(i).options[j].defaultSelected = (j == iIdxOrdre);
		}
	} else {
		for (var i=0; i<iNbSelects; i++) {
			iIdxOrdre = oSelects.item(i).selectedIndex;
			
			if (v_oSelect.formIndex == i || iIdxOrdre < iOrdre || iIdxOrdre > iOrdreAncien)
				continue;
			
			if (iIdxOrdre < iNbSelects)
				iIdxOrdre++;
			
			oSelects.item(i).selectedIndex = iIdxOrdre;
			
			for (var j=0; j<oSelects.item(i).options.length; j++)
				oSelects.item(i).options[j].defaultSelected = (j == iIdxOrdre);
		}
	}
}

function sauver() { document.forms[0].submit(); }

window.onload = function() {
	
	top.g_bModifier = false;
	
	var checkbox = document.getElementsByName("idForms[]");
	var select = document.getElementsByTagName("select");
	
	for (var i=0; i<checkbox.length; i++) {
		checkbox.item(i).onclick = function() {
			top.g_bModifier = true;
			document.getElementById("liste_formations").className = "liste_formations_sauver";
		};
		
		select.item(i).formIndex = i;
		select.item(i).onchange = function() {
			reclasser_formations(this);
			document.getElementById("liste_formations").className = "liste_formations_sauver";
		};
	}
};

