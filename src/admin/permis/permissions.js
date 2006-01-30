function oBarreOutils() { return top.frames["titre"]; }
function oPermission() { return top.frames["Principale"]; }
function appliquer() { oPermission().document.forms[0].submit(); }
function retablir() { oPermission().document.forms[0].reset(); }
function oui()
{
	var aoObjets = oPermission().document.forms[0].elements;
	
	for (i=0; i<aoObjets.length; i++)
		if (aoObjets[i].name.indexOf("idPermis") != -1)
			aoObjets[i++].checked = true;
}
function non()
{
	var aoObjets = oPermission().document.forms[0].elements;
	
	for (i=0; i<aoObjets.length; i++)
		if (aoObjets[i++].name.indexOf("idPermis") != -1)
			aoObjets[i].checked = true;
}
function inverser()
{
	var aoObjets = oPermission().document.forms[0].elements;
	
	for (i=0; i<aoObjets.length; i++)
		if (aoObjets[i].name.indexOf("idPermis") != -1)
			if (aoObjets[i].checked)
				aoObjets[++i].checked = true;
			else
				aoObjets[i++].checked = true;
}

