var g_oDossier = null;

window.onload = function() {
	
	var dossiers = document.getElementsByTagName("div");
	var dossier_selectionner = document.getElementsByName("idDossierForms").item(0);
	
	for (var i=0; i<dossiers.length; i++) {
		
		if (dossiers.item(i).className == "dossier_formations")
		{
			dossiers.item(i).onmouseover = function() { this.className = "dossier_formations_surbrillance"; };
			
			dossiers.item(i).onmouseout = function() {
				this.className = (g_oDossier != null && g_oDossier == this ? "dossier_formations_selectionner" : "dossier_formations");
			};
			
			dossiers.item(i).onclick = function() {
				
				if (g_oDossier != null)
					g_oDossier.className = g_oDossier.className = "dossier_formations";
				
				g_oDossier = this;
				g_oDossier.className = "dossier_formations_selectionner";
				dossier_selectionner.value = g_oDossier.getAttribute("dossier");
			};
			
			if (dossiers.item(i).getAttribute("dossier") == dossier_selectionner.value)
			{
				g_oDossier = dossiers.item(i);
				g_oDossier.className = "dossier_formations_selectionner";
			}
		}
	}
	
};

function valider() {
	document.getElementsByName("event").item(0).value = "valider";
	document.forms[0].submit();
	
	if (top.opener)
		top.opener.top.frames["INDEX"].location = GLOBALS["racine"]
			+ "zone_menu-index.php"
			+ "?idForm=0&idMod=0&idUnite=0&idActiv=0&idSousActiv=0";
	
	top.close();
}

