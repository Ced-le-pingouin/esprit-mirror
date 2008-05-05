function envoyer() { document.forms[0].submit(); }

function ret_valeur_option(v_sNomSelect) {
	if (document.getElementsByName) {
		var options = document.getElementsByName(v_sNomSelect).item(0);
		
		for (var i=0; i<options.length; i++)
			if (options.item(i).selected) break;
		
		return options.item(i).value;
	}
	
	return 0;
}

function voir_collecticiels_vides(v_bVoir) {
	document.getElementsByName("cbBlocsVides").item(0).disabled = v_bVoir;
}

function changer_personne() {
	voir_collecticiels_vides((ret_valeur_option("sltPersEquipe") != 0));
	envoyer();
}

function init() { changer_personne(); }

