var timerID=null;

function oPrincipal()
{
	return top.frames['Principal'];
}

function oFrmPersonne()
{
	return oPrincipal().frames['FRM_PERSONNE'];
}

function oFrmInscrit()
{
	return oPrincipal().frames['FRM_INSCRIT'];
}

function oFrmCours()
{
	return oPrincipal().frames['FRM_COURS'];
}

function envoyerPersonnes()
{
	if (typeof(oFrmPersonne().document.forms) != "undefined")
		oFrmPersonne().document.forms[0].submit();
}

function changerFiltre(v_iFiltre,v_iStatut,v_iIdForm,v_iIdMod,v_bCetteFormation)
{
	oFrmPersonne().document.location = 'liste_personnes.php'
		+ '?FILTRE=' + v_iFiltre
		+ '&STATUT_PERS=' + v_iStatut
		+ '&idform=' + v_iIdForm
		+ '&ID_MOD=' + v_iIdMod
		+ '&FORMATION=' + (v_bCetteFormation ? 1 : 0);
}

function changerStatut(v_iStatut,v_iIdForm)
{
	defStatutPers(v_iStatut);

	oFrmInscrit().document.location = 'liste_inscrits.php'
		+ '?idform=' + v_iIdForm
		+ '&STATUT_PERS=' + v_iStatut;
}

function majInscrits()
{
	if (typeof(oFrmInscrit().document.forms) != "undefined")
		oFrmInscrit().document.forms[0].submit();
}

function enleverPersonneInscrit()
{
	if (typeof(oFrmInscrit().document.forms) != "undefined")
		oFrmInscrit().document.forms[0].submit();
}

function mettre_a_jour(v_sRecharger)
{
	oFrmCours().document.location = v_sRecharger;
	clearInterval(timerID);
	timerID = null;
}

function majListeCours(v_sRecharger)
{
	if (typeof(oFrmCours().document.forms) != "undefined" && oFrmCours().document.forms.length > 0)
	{
		oFrmCours().document.forms[0].submit();
		
		if (typeof(v_sRecharger) != "undefined")
			timerID = setInterval("mettre_a_jour('" + v_sRecharger + "')",1000);
	}
}

function majListeConcepteursReels(v_iIdMod)
{
	defIdModule(v_iIdMod);

	oFrmInscrit().document.location = 'liste_inscrits.php'
		+ '?STATUT_PERS=' + retStatutPers()
		+ '&ID_MOD=' + v_iIdMod;
}

function retIdModule()
{
	return oFrmPersonne().document.forms[0].ID_MOD.value;
}

function defIdModule(v_iIdMod)
{
	oFrmPersonne().document.forms[0].ID_MOD.value = v_iIdMod;
}

function retFiltrePers()
{
	return oFrmPersonne().document.forms[0].FILTRE.value;
}

function defStatutPers(v_iStatut)
{
	oFrmPersonne().document.forms[0].STATUT_PERS.value = v_iStatut;
}

function retStatutPers()
{
	return oFrmPersonne().document.forms[0].STATUT_PERS.value;
}

function fermerFenetre()
{
	self.close();
}
