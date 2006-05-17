function ret_formulaire_complete_selectionne()
{
	var o = document.forms[0].elements["idFCSousActiv"];
	if (typeof(o.length) != "undefined") { for (var i=0; i<o.length; i++) if (o[i].checked) return o[i].value; }
	else if (typeof(o.value)) { return o.value; }
	return 0;
}

function formulaire_eval(v_sParamsUrl,v_sNomFenetre)
{
	var iIdFCSousActiv = ret_formulaire_complete_selectionne();
	if (parseInt(iIdFCSousActiv) < 1) { alert("Vous n'avez pas s�lectionn� de formulaire"); return false; }
	var sUrl = "formulaire_eval-index.php";
	v_sParamsUrl = "?idFCSousActiv=" + iIdFCSousActiv;
	var iLargeurFenetre = 640;
	var iHauteurFenetre = 480;
	var sOptions = ',menubar=0,scrollbars=0,statusbar=0,resizable=0';
	var oWinFormulaireEval = PopupCenter(sUrl + v_sParamsUrl,v_sNomFenetre,iLargeurFenetre,iHauteurFenetre,sOptions);
	oWinFormulaireEval.focus();
	return false;
}

function verifNbQcocher(NbRepMaxQC,MessMaxQC) { ; }

// !!! la fonction trim() est d�finie dans /code_lib/general.js, qui doit donc �tre inclus avant ce fichier !!!
function validerFormulaire(v_bRemplirTout)
{
	var oForm = document.forms['questionnaire'];
	var i;
	var oElementIncorrect = null;
	var sNomDernierElement = '';
	
	if (v_bRemplirTout)
	{
		for (i = 0; i < oForm.elements.length && oElementIncorrect == null; i++)
		{
			if (oForm.elements[i].name != sNomDernierElement)
			{
				switch(oForm.elements[i].type)
				{
					case 'text':
						if (trim(oForm.elements[i].value) == '')
							oElementIncorrect = oForm.elements[i];
						break;
					
					case 'textarea':
						if (trim(oForm.elements[i].value) == '')
							oElementIncorrect = oForm.elements[i];
						break;
					
					case 'radio':
						aoElements = document.getElementsByName(oForm.elements[i].name);
						for (iIndexEl = 0, bCoche = false; iIndexEl < aoElements.length && bCoche == false; iIndexEl++)
						{
							if (aoElements[iIndexEl].checked)
								bCoche = true;
						}
						if (!bCoche)
							oElementIncorrect = oForm.elements[i];
						break;
					
					case 'checkbox':
						aoElements = document.getElementsByName(oForm.elements[i].name);
						for (iIndexEl = 0, bCoche = false; iIndexEl < aoElements.length && bCoche == false; iIndexEl++)
						{
							if (aoElements[iIndexEl].checked)
								bCoche = true;
						}
						if (!bCoche)
							oElementIncorrect = oForm.elements[i];
						break;
						
					case 'select-one':
						if (oForm.elements[i].length > 1 && oForm.elements[i].selectedIndex <= 0)
							oElementIncorrect = oForm.elements[i];
						break;
						
					default:
						break;
				}
			}
			
			sNomDernierElement = oForm.elements[i].name;
		}
	}
	
	if (oElementIncorrect != null)
	{
		alert("Le formulaire n'a pas �t� compl�tement rempli. Veuillez compl�ter les r�ponses manquantes.");
		//alert('#ancre' + oElementIncorrect.name);
		document.location = '#ancre' + oElementIncorrect.name;
	}
	else
	{
		oForm.submit();
	}
}

function validerQNombre(v_oCase)
{
	// il ne faut faire la validation des champs QNombre que lors du remplissage par
	// un utilisateur, pas dans le concepteur de formulaire (les FORM n'ont pas le m�me nom)
	if (v_oCase.form.name != 'questionnaire')
		return;
	
	var bErreur = false;
	var fValeurCase = parseFloat(v_oCase.value);
	
	// l'Id d'une case QNombre est structur� come ceci: <idobjet>_<nbmin>_<nbmax>
	var asParties = v_oCase.id.split('_');
		
	var fNbMin = parseFloat(asParties[1]);
	var fNbMax = parseFloat(asParties[2]);
		
	if (isNaN(fValeurCase) || fValeurCase < fNbMin)
	{
		v_oCase.value = fNbMin;
		bErreur = true;
	}
	else if (fValeurCase > fNbMax)
	{
		v_oCase.value = fNbMax;
		bErreur = true;
	}
	
	if (bErreur)
	{
		alert
		(
			"Attention! Cette case doit contenir un nombre compris entre " + fNbMin + " et " + fNbMax + ".\n\n"
			+ "Sa valeur a �t� ramen�e dans l'intervalle autoris�."
		);
	}
}