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

function fonth1() { insererBalise("[h1]","[/h1]"); }
function fonth2() { insererBalise("[h2]","[/h2]"); }
function fonth3() { insererBalise("[h3]","[/h3]"); }
function fonth4() { insererBalise("[h4]","[/h4]"); }
function fonth5() { insererBalise("[h5]","[/h5]"); }
function fonth6() { insererBalise("[h6]","[/h6]"); }

function bold() { insererBalise("[b]","[/b]"); }
function italic() { insererBalise("[i]","[/i]"); }
function underline() { insererBalise("[u]","[/u]"); }

function left_alignment() { insererBalise("[l]","[/l]"); }
function center_alignment() { insererBalise("[c]","[/c]"); }
function right_alignment() { insererBalise("[r]","[/r]"); }
function justify_alignment() { insererBalise("[j]","[/j]"); }

function list_ul() { insererBalise("[liste \"*\"]\n","\n[/liste]"); }
function list() { insererBalise("[liste \"1\"]\n","\n[/liste]"); }
function list_ol() { insererBalise("[liste \"A\"]\n","\n[/liste]"); }

function increase_indent() { insererBalise("[blockquote]","[/blockquote]\n"); }

function email() { insererBalise("[mailto:","]"); }
function site() { insererBalise("[http://","]"); }

function hrule() { insererBalise("[hr]",""); }

function tableau_de_bord(v_sModalite) {
	if (v_sModalite && v_sModalite == "/e")
		insererBalise("[tableaudebord /e]","");
	else
		insererBalise("[tableaudebord /i]","");
}
