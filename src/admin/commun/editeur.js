function insertAtCursor(v_sNomChamp,v_sBaliseDepart,v_sBaliseFin)
{
	if (document.selection)
	{
		// IE support
		v_sNomChamp.focus();
		selection = document.selection.createRange();
		selection.text = v_sBaliseDepart + selection.text + v_sBaliseFin;
	}
	else if (v_sNomChamp.selectionStart || v_sNomChamp.selectionStart == '0')
	{
		// MOZILLA/NETSCAPE support
		var startPos = v_sNomChamp.selectionStart;
		var endPos = v_sNomChamp.selectionEnd;
		v_sNomChamp.value = v_sNomChamp.value.substring(0, startPos)
			+ v_sBaliseDepart
			+ v_sNomChamp.value.substring(startPos,endPos)
			+ v_sBaliseFin
			+ v_sNomChamp.value.substring(endPos,v_sNomChamp.value.length);
	}
	else
	{
		v_sNomChamp.value += v_sBaliseDepart + v_sBaliseFin;
	}
}

function tableau_de_bord(v_sModalite) {
	if (v_sModalite && v_sModalite == "/e")
		insererBalise("[tableaudebord /e]","");
	else
		insererBalise("[tableaudebord /i]","");
}
