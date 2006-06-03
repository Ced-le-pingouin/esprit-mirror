function oPersonnes() { return top.frames["personnes"]; }
function oListe() { return top.frames["liste"]; }

function trier(v_sTrier,v_sOrdre_tri)
{
	with (document.forms[0])
	{
		elements["TRI"].value = v_sTrier;
		elements["ORDRE_TRI"].value = v_sOrdre_tri;
		submit();
	}
}

function ajouter_personnes_liste()
{
	var aiPersonnes = oPersonnes().document.forms[0].elements["IDPERS[]"];
	var element = document.forms[0].elements["LISTE_IDPERS"];
	var i, j;
	
	if (typeof(aiPersonnes.length) == "undefined")
		aiPersonnes = new Array(aiPersonnes);
	
	var x = element.value.split(",");
	
	for (i=0; i<aiPersonnes.length; i++)
		if (aiPersonnes[i].checked)
		{
			for (j=0; j<x.length; j++)
				if (x[j] == aiPersonnes[i].value)
					break;
			
			if (j == x.length)
				// Element non trouvé dans la liste
				element.value += aiPersonnes[i].value + ",";
			else
				x.slice(j)
		}
	
	document.forms[0].submit();
}

function exporter_liste_personnes()
{
	var winExport = window.open("","winExport",centrerFenetre(380,500) + ",menubar=0,resizable=0");
	
	// Envoyer la liste des personnes à cette fenêtre
	document.forms[0].action = "exporter-dialog-index.php";
	document.forms[0].target = "winExport";
	document.forms[0].method = "post";
	
	winExport.onLoad = document.forms[0].submit();
	
	// Remettre les anciennes valeurs
	document.forms[0].action = "exporter-liste.php";
	document.forms[0].target = "liste";
	
	winExport.focus();
}

function vider_liste_personnes()
{
	document.forms[0].elements["LISTE_IDPERS"].value = "";
	document.forms[0].submit();
}

function retirer_personnes_liste()
{
	var i;
	var aiPersonnes = document.forms[0].elements["IDPERS[]"];
	var element = document.forms[0].elements["LISTE_IDPERS"];
	
	// Attention, lorsqu'on retrouve 1 seul checkbox sur la page html,
	// celui-ci n'est pas considéré comme un tableau de checkbox
	if (typeof(aiPersonnes.length) == "undefined")
		aiPersonnes = new Array(aiPersonnes);
	
	for (i=0; i<aiPersonnes.length; i++)
	{
		z1 = element.value.indexOf(aiPersonnes[i].value + ",");
		
		if (aiPersonnes[i].checked && z1 != -1)
		{
			z2 = element.value.indexOf(",",z1) + 1;
			
			element.value = element.value.substring(0,z1)
				+ element.value.substring(z2);
		}
	}
	
	document.forms[0].submit();
}

function verif_checkbox(evt)
{
	if (!evt)
		var evt = window.event;
	
	if (evt)
	{
		var element = (evt.srcElement ? evt.srcElement : evt.target);
		
		if (evt.ctrlKey)
		{
			var sNom = element.name;
			var aoObject = document.getElementsByName(sNom);
			var rech = element.id.substring(0,8);
			var bSelect = element.checked;
			var i;
			
			for (i=0; i<aoObject.length; i++)
				if (aoObject[i].id.indexOf(rech) != -1)
					aoObject[i].checked = bSelect;
		}
		
		verif_checkbox_principal(element);
	}
}

