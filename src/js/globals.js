function table_ligne_surbrillance(v_oElement,v_bSurbrillance)
{
	if (v_oElement.cells && v_oElement.cells.item)
	{
		if (v_bSurbrillance)
			g_sSurbrillance = v_oElement.cells.item(0).className;
		
		for (var i=0; i<v_oElement.cells.length; i++)
			v_oElement.cells.item(i).className = (v_bSurbrillance ? 'cellule_surbrillante' : g_sSurbrillance);
	}
}

/**
 * Cette fonction permet de tout sélectionner/désélectionner les checkbox
 *
 * Exemple
 * <input type="checkbox" name="IDPERS" onclick="select_deselect_checkbox(this)">
 */
function select_deselect_checkbox(v_oObj)
{
	if (typeof(v_oObj) == "undefined")
		return;
	
	var elements = document.getElementsByName(v_oObj.name + "[]");
	
	for (var i=0; i<elements.length; i++)
		elements.item(i).checked = v_oObj.checked;
}

/**
 * Cette fonction permet séléctionner/désélectionner le checkbox principal
 *
 * Exemple
 * <input type="checkbox" name="IDPERS[]" onclick="verif_checkbox_principal(this)">
 */
function verif_checkbox_principal(v_oObj)
{ //v1.2
	if (typeof(v_oObj) == "undefined" || v_oObj.type != "checkbox")
		return;
	
	var compteur = 0;
	var elements = document.getElementsByName(v_oObj.name);
	var total = elements.length;
	
	for (var i=0; i<total; i++)
		if (elements.item(i).checked)
			compteur++;
	
	document.getElementsByName(v_oObj.name.substring(0,(v_oObj.name.length - 2))).item(0).checked = (total == compteur);
}

/**
 * Permet de rechercher une personne dans une liste
 * 
 */
function rechPersonne(v_sRech,v_oObj,v_sElementsByName)
{ // DOM
	var obj = v_oObj.document;
	
	if (!obj.getElementsByTagName || !obj.getElementsByName(v_sRech))
		return;

	if (v_sRech.length == 0)
		v_oObj.g_asListeRech == null;
	else if (v_sRech.length == 1)
	{// on prend la premi�re lettre entree et on cr�e un tableau
		v_oObj.g_asListeRech = obj.getElementsByName(v_sRech);
		if (v_oObj.g_asListeRech.item(0))
			sPremiereLettre = v_oObj.g_asListeRech.item(0).firstChild.nodeValue.substring(0,1).toLowerCase();
	}

	if (v_oObj.g_sRech!=null)
		v_oObj.g_sRech.style.fontWeight = "normal";

	if (self.document.getElementsByName('nomPersonneRech').item(0)!=null)
		self.document.getElementsByName('nomPersonneRech').item(0).style.color = "";

	if (v_sRech.length > 0)
	{
		v_sRech = v_sRech.toUpperCase();
		for (var i=0; i<v_oObj.g_asListeRech.length; i++)
		{
			v_oObj.g_sRech = v_oObj.g_asListeRech.item(i);
			sRech = v_oObj.g_asListeRech.item(i).firstChild.nodeValue;

			if (sRech != null && sRech.indexOf(v_sRech) == 0)
			{// la recherche est trouv�e dans la liste
				v_oObj.g_asListeRech.item(i).style.fontWeight = "bold";
				
				if (v_sRech.length < 2)
					v_oObj.location.hash = sPremiereLettre;
				else
				{
					g_iPosDernierePosition = v_oObj.location.hash = "pos" + sPremiereLettre + i;
				}

				//g_iPosDernierePosition = i;
				g_iPosDernierelettre = v_sRech.length-1;
				//alert('\nrecherche : '+v_sRech + '\nliste : ' + sRech+'\ni : '+i);
				return;
			}
		}
		v_oObj.location.hash = g_iPosDernierePosition;
		self.document.getElementsByName('nomPersonneRech').item(0).style.color = "red";
		//alert('derniere position : '+ v_oObj.location.hash);
	}
	else v_oObj.location.hash = "top";
	
	g_iPosDernierePosition = g_iPosDernierelettre = 0;
}

function view_dom(v_oObj)
{
	var obj;
	var win = open("","WinDOM","");
	win.document.open();
	win.document.writeln("<table border='1' cellspacing='0' cellpadding='0' width='100%'>");
	for (obj in v_oObj)
	{
		win.document.writeln("<tr>");
		win.document.writeln("<td>" + obj + "</td>");
		win.document.writeln("<td>" + eval("v_oObj." + obj) + "</td>");
		win.document.writeln("</tr>");
	}
	win.document.writeln("</table>");
	win.document.close();
	
}

function sePlacerPersonne(v_sRech,v_oObj)
{
	if (!v_oObj.document.getElementsByTagName)
		return;
	
	var obj = v_oObj.document;
	var sRech = new String;
	var iPos = 1;
	var elem;
	var i;
	
	if (v_sRech.length > 0)
	{
		var elems = obj.getElementsByTagName("a");
		for (i=0; i<elems.length; i++)
		{
			if (elems[i].name.indexOf("pos") != -1)
			{
				elem = obj.getElementById('nom_' + iPos);
				if (elem != null)
				{
					sRech = elem.innerHTML;
					if (sRech.indexOf(v_sRech.toUpperCase()) == 0)
					{
						v_oObj.location.hash = "pos" + iPos;
						return "pos" + iPos;
					}
					iPos++; // Passer au nom suivant
				}
			}
		}
	}
	v_oObj.location.hash = "top";
}

function select_deselect_radio(v_oElems,v_iIdxSelectionne)
{
	var iIdxRadio = 0;
	
	if (typeof(v_oElems.length) == "number")
	{
		for (iIdxRadio=0; iIdxRadio<v_oElems.length; iIdxRadio++)
		{
			if (v_oElems[iIdxRadio].checked)
			{
				if (iIdxRadio == v_iIdxSelectionne) { v_oElems[iIdxRadio].checked = false; v_iIdxSelectionne = null; }
				else { v_iIdxSelectionne = iIdxRadio; }
				break;
			}
		}
	}
	else if (iIdxRadio == v_iIdxSelectionne)
	{
		v_oElems.checked = false; v_iIdxSelectionne = null;
	}
	else
	{
		v_iIdxSelectionne = 0;
	}
	
	return v_iIdxSelectionne;
}

// {{{ Object String
String.prototype.trim = function() {
	return this.replace(/(^\s*)|(\s*$)/g,'');
};
// }}}
