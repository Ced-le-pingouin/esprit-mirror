/* ''' Frame principale */
function oPrincipale() { return top.frames["Principale"]; }

function envoyer() {
	var elems = document.getElementsByName("idPers[]");
	
	for (var i=0; i<elems.length; i++)
		elems.item(i).checked = !elems.item(i).checked;
	
	document.forms[0].submit();
}

function grise(value) {
	var id = "pers_" + value;
	document.getElementById(id).className = "nom_grise";
}

function normal(value) {
	var id = "pers_" + value;
	document.getElementById(id).className = "";
}

function init_principale() {
	var elems = document.getElementsByName("idPers[]");
	for (var i=0; i<elems.length; i++) {
		elems.item(i).onclick = function() {
			if (this.checked) normal(this.value); else grise(this.value);
		};
		
		if (!elems.item(i).checked) 
			grise(elems.item(i).value);
	}
}
/* Frame principale ''' */

function init() {
	if ("Principale" == window.name)
		init_principale();
}

window.onload = init;
