/**
 * Afficher une aide en ligne dans le collecticiel actuel.
 * @param v_oObj object L'objet sélectionné ;
 * @param v_iIdCollecticiel integer numéro d'identifiant du collecticiel ;
 * @param v_sTexte string
 */
function aide_en_ligne(v_oObj,v_iIdCollecticiel,v_sTexte)
{
	var sIdAideEnLigne = "id_aide_en_ligne_" + v_iIdCollecticiel;
	
	if (document.getElementById &&
		document.getElementById(sIdAideEnLigne))
	{
		var sTexte = "&nbsp;";
		
		if (typeof(asTextesBarreIcones[v_sTexte]) != "undefined")
			sTexte += asTextesBarreIcones[v_sTexte];
		
		document.getElementById(sIdAideEnLigne).innerHTML = sTexte;
	}
}

function liste_equipes(v_iIdEquipe)
{
	PopupCenter(GLOBALS["racine"] + "admin/equipe/liste_equipes-index.php?idEquipes=" + v_iIdEquipe + "&affBlocVide=non&typeCourriel=courriel-unite","wListeEquipes",550,380,",toolbar=0,scrollbars=0,resizable=0");
	return false;
}

function ressource_evaluation(v_iIdResSA)
{
	PopupCenter("ressource_evaluation-index.php?idResSA=" + v_iIdResSA,"wRessourceEvaluation",750,540,",toolbar=0,scrollbars=0,resizable=1");
	return false;
}

function ressource_vote(v_iIdCollecticiel)
{
	var elems = document.getElementsByName("idResSA" + v_iIdCollecticiel + "[]");
	var sParamsUrl = "";
	
	for (var i=0; i<elems.length; i++)
		if (elems[i].checked)
			sParamsUrl += (sParamsUrl.length > 0 ? "&" : "?")
				+ "idResSAVotes=" + elems[i].value;
	PopupCenter("ressource_vote-index.php" + sParamsUrl,"wRessourceVote",550,380,",toolbar=0,scrollbars=0,resizable=0");
	return false;
}

function ressource_votants(v_iIdResSA,v_iIdEquipe)
{
	var sParamsUrl = "?idResSA=" + v_iIdResSA
		+ (typeof(v_iIdEquipe) == "undefined" ? "" : "&idEquipe=" + v_iIdEquipe);
	PopupCenter("ressource_votants-index.php" + sParamsUrl,"wRessourceVotant",640,480,",toolbar=0,scrollbars=0,resizable=1");
	return false;
}

function ressource_deposer() { return PopupCenter("ressource_deposer-index.php","wAjouterRessource",550,380,",toolbar=0,scrollbars=0,resizable=0"); }

function ressource_transfert() {
	var sParamsUrl = "";
	
	if (arguments.length > 0)
		sParamsUrl = "?" + arguments[0];
	
	return PopupCenter("ressource_transfert-index.php" + sParamsUrl,"wRessourceTransfert",790,595,",toolbar=0,scrollbars=0,resizable=0");
}

function ressource_description(v_iIdResSA) { PopupCenter("ressource_description-index.php?idResSA=" + v_iIdResSA,"wDocumentNote",550,380,",toolbar=0,scrollbars=0,resizable=0"); return false; }

function ressource_supprimer(v_iIdCollecticiel)
{
	var wRessourceSupprimer;
	wRessourceSupprimer = PopupCenter("","wRessourceSupprimer",300,150,",toolbar=no,scrollbars=no,resizable=no");
	wRessourceSupprimer.focus();
	var oForm = document.forms["formCollecticiel" + v_iIdCollecticiel];
	oForm.action = "ressource_supprimer-index.php";
	oForm.method = "get";
	oForm.target = "wRessourceSupprimer";
	oForm.submit();
}

/* ''' Formulaire "formRecharger" */
function oFrameFiltres()
{
	if (parent && parent.frames["filtres"])
		return parent.frames["filtres"].document.forms[0];
	else
		return null;
}
function recharger() { oFrameFiltres().submit(); return false; }
function inverser_type_tri(v_sTri,v_iTypeTri)
{
	oFrameFiltres().elements["tri"].value = v_sTri;
	oFrameFiltres().elements["typeTri"].value = v_iTypeTri;
	return recharger();
}
/* ''' */

