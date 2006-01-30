var g_oElementSelect = null;

function charger_tableau_bord() {
	document.forms[0].submit();
}

function init() {
	document.getElementById("select_liste").onchange = function() {
		charger_tableau_bord();
		this.blur();
		return false;
	};
	
	document.getElementById("select_modalite").onchange = function() {
		charger_tableau_bord();
		this.blur();
	};
	
	document.getElementById("select_type").onchange = function() {
		charger_tableau_bord();
		this.blur();
		return false;
	};
	
	charger_tableau_bord();
}

window.onload = init;
