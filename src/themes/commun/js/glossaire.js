function envoyer() {
	with (document.forms[0]) {
		action = "glossaire_composer.php";
		target = "Principale";
		method = "post";
		submit();
	}
}

function ajouter_glossaire() {
	ouvrir_glossaire(0);
}

function modifier_glossaire() {
	var iIdGlossaire = 0;
	
	if (typeof(document.forms[0].elements["ID_GLOSSAIRE"])) {
		var oObj = document.forms[0].elements["ID_GLOSSAIRE"];
		
		if (typeof(oObj.length) != "undefined")
		{
			var iNbElems = oObj.length;
			
			for (i=0; i<iNbElems; i++) {
				if (oObj[i].checked) {
					iIdGlossaire = oObj[i].value;
					break;
				}
			}
		}
		else
		{
			if (oObj.checked)
				iIdGlossaire = oObj.value;
		}
	}
	
	if (iIdGlossaire > 0)
		ouvrir_glossaire(iIdGlossaire);
}

function ouvrir_glossaire(v_iIdGlossaire) {
	var sUrl = "glossaire_titre-index.php"
		+ "?idGlossaire=" + v_iIdGlossaire;
	var wModifierTitreGlossaire = PopupCenter(sUrl,"wModifierTitreGlossaire",380,240,",resizable=no");
	wModifierTitreGlossaire.focus();
}