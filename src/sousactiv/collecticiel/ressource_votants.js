function voter() {
	top.voter = false;
	
	var elems = document.getElementsByName("idPers[]");
	
	for (var i=0; i<elems.length; i++)
		if (elems.item(i).checked) {
			top.voter = true;
			break;
		}
	
	if (top.voter)
		document.forms[0].submit();
	else
		alert("Vous avez oublié de sélectionner un membre.");
	
	return false;
}

