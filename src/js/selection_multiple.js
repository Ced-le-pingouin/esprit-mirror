var g_sMenu = "";

function selectionner(v_oThis,v_sParent,v_iId,v_bBoucle,v_bRafraichir)
{
	var bChecked = v_oThis.checked;
	var oElems = v_oThis.form.elements;
	var i, j;
	
	v_iId = parseInt(v_iId);
	
	for (i=0; i<oElems.length; i++)
	{
		if (v_oThis == oElems[i])
		{
			if (v_bBoucle)
			{
				for (j=i+1; j<oElems.length; j++)
				{
					if ((oElems[j].name.indexOf(v_sParent) > -1 && v_iId == 0) ||
						(oElems[j].name.indexOf(v_sParent) == -1 &&	oElems[j].value < 1))
						break;
					
					if (!oElems[j].disabled)
						oElems[j].checked = bChecked;
				}
				
				if (i>0) selectionner(oElems[i],v_sParent,oElems[i].value,false);
			}
			else
			{
				var max = 0;
				var count = 0;
				
				if (v_iId < 1)
				{
					// Trouver le dernier de la liste
					for (j=i+1; j<oElems.length; j++)
					{
						if (v_iId >= parseInt(oElems[j].value)) break;
						if (!oElems[j].disabled) { max++; if (oElems[j].checked) count++; }
					}
					
					bChecked = (max == count);
				}
				
				if (!oElems[i].disabled)
					oElems[i].checked = bChecked;
				
				// Rechercher son parent
				for (j=i-1; j>=0; j--)
				{
					if (oElems[j].type == "checkbox" &&
						parseInt(oElems[j].value) < 1 &&
						parseInt(oElems[j].value) < v_iId)
					{
						selectionner(oElems[j],v_sParent,oElems[j].value,false);
						break;
					}
				}
			}
			
			break;
		}
	}
if ((v_bRafraichir) || (v_bRafraichir == undefined)) changerMenu();
}

function changerMenu()
{
	var sMenu = "";
	var oElems = document.forms[0].elements;
	
	for (var i=0; i<oElems.length; i++)
		if (oElems[i].name == "idPers[]" && oElems[i].checked)
		{
			if (g_sMenu != "" && g_sMenu == sMenu) return;
			sMenu = "?menu=1";
			break;
		}
	
	top.rafraichir_menu(sMenu);
	
	g_sMenu = sMenu;
}

function verifier_selectionner()
{
	var elems = document.getElementsByName("idPers[]");
	var s1 = null;
	var s2 = null;
	
	for (var i=0; i<elems.length; i++)
	{
		if (elems.item(i).checked)
		{
			s2 = elems.item(i).onclick.toString();
			
			if (s1 == s2)
				continue;
			
			s1 = s2;
			
			elems.item(i).checked = !elems.item(i).checked;
			elems.item(i).click();
		}
	}
}

function init()
{
	verifier_selectionner();
	changerMenu();
}