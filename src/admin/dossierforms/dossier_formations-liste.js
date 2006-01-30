var g_oDossierSelect = null;

function ajouter() {
	var sUrl = "dossier_formations_event-index.php"
		+ "?event=ajout";
	var oWin = PopupCenter(sUrl,"wModifierDossier",500,220,",toolbar=0,scrollbars=0,resizable=0");
}

function modifier() {
	var sUrl = "dossier_formations_event-index.php"
		+ "?event=modif"
		+ "&idDossierForms=" + top.oPrincipale().ret_id_dossiers_forms();
	var oWin = PopupCenter(sUrl,"wModifierDossier",500,220,",toolbar=0,scrollbars=0,resizable=0");
}

function supprimer() {
	var sUrl = "dossier_formations_event-index.php"
		+ "?event=supp"
		+ "&idDossierForms=" + top.oPrincipale().ret_id_dossiers_forms();
	var oWin = PopupCenter(sUrl,"wModifierDossier",500,220,",toolbar=0,scrollbars=0,resizable=0");
}

function recharger() {
	if (arguments.length == 1)
		document.getElementsByName("idDossierForms").item(0).value = parseInt(arguments[0]);
	
	document.forms[0].submit();
}

function ret_params_url() {
	if (g_oDossierSelect == null)
		return null;
	
	return ret_params_url2(g_oDossierSelect);
}

function ret_params_url2(v_oElem) {
	for (var i=0; i<v_oElem.childNodes.length; i++)
		if (v_oElem.childNodes.item(i).nodeName == "A")
			return v_oElem.childNodes.item(i).search.substring(1);
	return null;
}

window.onload = function() {
	var iIdDossierForms =document.getElementsByName("idDossierForms").item(0).value;
	var dossiers = document.getElementsByTagName("div");
	
	for (var i=0; i<dossiers.length; i++)
		if (dossiers.item(i).className == "dossier") {
			var sParamsUrl = ret_params_url2(dossiers.item(i));
			
			if (sParamsUrl == null)
				break;
			
			var ii = sParamsUrl.substring(sParamsUrl.indexOf("=")+1);
			
			if (iIdDossierForms == ii) {
				g_oDossierSelect = dossiers.item(i);
				g_oDossierSelect.className = "dossier_selectionner";
			}
			
			dossiers.item(i).onmouseover = function() { this.className = "dossier_surbrillance"; }
			dossiers.item(i).onmouseout = function() { if (g_oDossierSelect == this) this.className = "dossier_selectionner"; else this.className = "dossier"; }
			dossiers.item(i).onclick = function() {
				if (g_oDossierSelect && g_oDossierSelect != this)
					g_oDossierSelect.className = "dossier";
				
				g_oDossierSelect = this;
				g_oDossierSelect.className = "dossier_selectionner";
				
				var sParamsUrl = ret_params_url2(this);
				var ii = sParamsUrl.substring(sParamsUrl.indexOf("=")+1);
				
				document.forms[0].elements["idDossierForms"].value = ii;
			}
		}
	
	var elem = top.document.getElementsByName("Principale").item(0);
	elem.setAttribute("src","dossier_formations.php?idDossierForms=" + iIdDossierForms);
};

