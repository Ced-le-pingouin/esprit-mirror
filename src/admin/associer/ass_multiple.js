function oFramePersonnes() { return top.frames["centre"].frames["personnes"]; }
function oFrameInscrits() { return top.frames["centre"].frames["inscrits"]; }

function oFormPersonnes() { return oFramePersonnes().document.forms[0]; }
function oFormInscrits() { return oFrameInscrits().document.forms[0]; }

function ajouterPersonnes()
{
	majListeCours();
	if (oFormPersonnes().elements["IDS_ACTION"].value.length > 0)
		oFormPersonnes().submit();
	else
		alert("Vous devez sélectionner, au moins, un cours"
			+ "\ndans la liste des cours.");
}

function retirerPersonnes()
{
	var elems = oFormInscrits().elements;
	var aiIdPers = new Array();
	var s = "";
	
	for (i=0; i<elems.length; i++)
		if (elems[i].name.indexOf("ID_MOD[]") != -1)
		{
			if (s.substring(s.length-1) == ",")
				s = s.substring(0,s.length-1);
			
			if (s.indexOf(":") != -1)
				s += ";";
			
			s += elems[i].value + ":";
		}
		else if (elems[i].name.indexOf("ID_MOD_") != -1 && elems[i].checked)
			s += elems[i].value + ",";
	
	if (s.substring(s.length-1) == ",")
		s = s.substring(0,s.length-1);
	
	oFormPersonnes().elements["IDS_ACTION"].value = s;
	oFormPersonnes().elements["ACTION"].value = "retirer";
	oFormPersonnes().submit();
}

function chargerPageInscrits(v_iIdForm,v_iIdStatut)
{
	oFrameInscrits().location = "ass_multiple-inscrits.php"
		+ "?ID_FORM=" + v_iIdForm
		+ "&STATUT_PERS=" + v_iIdStatut;
}

function majListeCours()
{
	var sValeurs = "";
	var elems = oFrameInscrits().document.getElementsByName("ID_MOD[]");
	
	for (i=0; i<elems.length; i++)
		if (elems[i].checked)
			sValeurs += (sValeurs != "" ? "," : "") + elems[i].value;
	
	oFormPersonnes().elements["IDS_ACTION"].value = sValeurs;
	oFormPersonnes().elements["ACTION"].value = "ajouter";
}

function verif_cours_non_select()
{
	if (!document.getElementsByName)
		return;
	
	var elems = document.getElementsByName("ID_MOD[]");
	
	for (i=0; i<elems.length; i++)
		elems[i].checked = false;
}

function verif_inscrits_non_select()
{
	var elems = document.forms[0].elements;
	
	for (i=0; i<elems.length; i++)
		if (elems[i].name.indexOf("ID_MOD_") != -1)
			elems[i].checked = false;
}

function trier()
{
	oFormPersonnes().elements["IDS_ACTION"].value = "";
	oFormPersonnes().elements["ACTION"].value = "tri";
	oFormPersonnes().submit();
}
