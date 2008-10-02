function init() {
	var obj = null;
	var elems = document.getElementsByTagName("input");
	
	for (var i=0; i<elems.length; i++) {
		elems.item(i).onclick = function() {
			if (parseInt(this.value) > 0)
				verif_checkbox_principal(this);
			else
				select_deselect_checkbox(this);
			
			document.getElementById("liste_ressources").style.borderColor = "red";
			
			this.onfocus = this.blur();
		};
		
		if (obj == null && elems.item(i).checked)
			obj = elems.item(i);
	}
	
	if (obj != null)
		verif_checkbox_principal(obj);
}

function sauvegarder() {
	document.forms[0].elements["action"].value = "sauvegarder";
	document.forms[0].submit();
	top.opener.recharger();
	top.close();
}

window.onload = init;

