/* ------------------- */
/* La liste des sujets */
/* ------------------- */
function ret_nom_equipe_selectionner()
{
	var i;
	var oSelect = top.oFrmSujets().document.forms[0].elements['selectIdEquipe'];
	for (i=0; i<oSelect.options.length; i++)
		if (oSelect.options[i].selected) return oSelect.options[i].text;
	return "inconnu";
}

function popup_nouveau_sujet(v_iIdForum,v_iIdEquipeUtilisateur,v_bSujetPourTous)
{
	if (v_iIdEquipeUtilisateur > 0 && v_iIdEquipeUtilisateur != top.g_iIdEquipe)
	{
		alert("Vous ne pouvez pas déposer de sujet dans cette partie du forum,\ncar vous ne faites pas partie de l'équipe '" + ret_nom_equipe_selectionner() + "'.");
		return;
	}
	
	if (v_iIdEquipeUtilisateur < 1)
		v_iIdEquipeUtilisateur = top.g_iIdEquipe;
	
	var sUrl = "modifier_sujet-index.php"
		+ "?modaliteFenetre=ajouter"
		+ "&idEquipe=" + (v_bSujetPourTous ? "0" : v_iIdEquipeUtilisateur)
		+ "&idForum=" + v_iIdForum
		+ "&idSujet=0"
		+ "&idNiveau=" + top.g_iIdNiveau
		+ "&typeNiveau=" + top.g_iTypeNiveau;
	var oWinNouveauSujet = PopupCenter(sUrl,"winModifierSujet",600,180,"");
	oWinNouveauSujet.focus();
}

function popup_modifier_sujet()
{
	var aoObjs = top.oFrmListeSujets().ret_liste_ids_sujets();
	var iIdSujet = ret_element_selectionner(aoObjs);
	
	if (iIdSujet == 0)
	{
		alert("Vous devez sélectionner un titre de sujet avant de modifier");
		return;
	}
	
	top.oFrmListeSujets().def_id_sujet(iIdSujet);
	
	var sUrl = "modifier_sujet-index.php"
		+ "?modaliteFenetre=modifier"
		+ "&idSujet=" + iIdSujet;
	var oWinModifierSujet = PopupCenter(sUrl,"winModifierSujet",600,180,"");
	oWinModifierSujet.focus();
}

function popup_supprimer_sujet(v_bSujetEquipes)
{
	var aoObjs = top.oFrmListeSujets().ret_liste_ids_sujets();
	var iIdForum = top.oFrmListeSujets().ret_id_forum();
	var iIdSujet = ret_element_selectionner(aoObjs);
	
	if (iIdSujet == 0)
	{
		alert("Vous devez sélectionner un titre de sujet avant de supprimer");
		return;
	}
	
	var sUrl = "modifier_sujet-index.php"
		+ "?modaliteFenetre=supprimer"
		+ "&idForum=" + iIdForum
		+ "&idSujet=" + iIdSujet
		+ "&idNiveau=" + top.g_iIdNiveau
		+ "&typeNiveau=" + top.g_iTypeNiveau
		+ "&idEquipe=" + (v_bSujetEquipes ? top.g_iIdEquipe : 0);
	var oWinSupprimerSujet = PopupCenter(sUrl,"winModifierSujet",420,210,"");
	oWinSupprimerSujet.focus();
}

function rafraichir_liste_sujets(v_iIdSujet,sSituation)
{
	top.oFrmSujets().def_id_sujet(v_iIdSujet);
	top.oFrmListeSujets().def_id_sujet(v_iIdSujet);
	
	if (typeof(sSituation) != "undefined")
	{
		if (v_iIdSujet > 0)
		{
			if (sSituation.indexOf("modifier") == -1)
			{
				top.oFrmSujets().document.forms[0].submit();
				return false;
			}
		}
	}
	
	top.oFrmListeSujets().rafraichir();
	
	return false;
}

/* Les informations à propos du sujet */
function afficher_infos_sujet(v_iIdSujet)
{
	var oDOMWindow = new DOMWindow(self);
	top.g_iPageYOffsetSujets = oDOMWindow.pageYOffset();
	top.oFrmInfosSujet().location.replace("sujet-infos.php?idSujet=" + v_iIdSujet);
	top.oFrmSujets().def_id_sujet(v_iIdSujet);
	top.oFrmListeSujets().def_id_sujet(v_iIdSujet);
	top.oFrmListeSujets().afficher_indice(v_iIdSujet);
	return false;
}

/* Gestion des forums */
function ajouter_forum(v_iIdForumParent)
{
	var sUrl = "modifier_forum-index.php"
		+ "?modaliteFenetre=ajouter"
		+ "&idForum=0"
		+ "&idForumParent=" + v_iIdForumParent;
	PopupCenter(sUrl,"WinAjouterForum",600,200,"");
}

function modifier_forum() { top.frames["FORUM"].document.forms[0].submit(); }

/* Gestion des sujets */
function ret_element_selectionner(v_aoObjs)
{
	if (typeof(v_aoObjs) == "undefined")
		return 0;
	
	if (typeof(v_aoObjs.length) != "undefined")
		for (i=0; i<v_aoObjs.length; i++) { if (v_aoObjs[i].checked) return v_aoObjs[i].value; }
	else if (v_aoObjs.checked)
		return v_aoObjs.value;
	
	return 0;
}

/* Gestion des messages */
function ouvrir_modifier_message(v_iIdSujet,v_iIdMessage,v_sModaliteFenetre,v_bMessageEquipes)
{
	var sUrl = "modifier_message-index.php"
		+ "?modaliteFenetre=" + v_sModaliteFenetre
		+ "&idSujet=" + v_iIdSujet
		+ "&idMessage=" + v_iIdMessage
		+ "&idNiveau=" + top.g_iIdNiveau
		+ "&typeNiveau=" + top.g_iTypeNiveau
		+ "&idEquipe=" + (v_bMessageEquipes ? "0" : top.g_iIdEquipe);
	var oWinMessageSujet = PopupCenter(sUrl,"winMessageSujet",750,560,"");
	oWinMessageSujet.focus();
	return false;
}

function popup_nouveau_message(v_iIdEquipeUtilisateur,v_bMessageEquipes)
{
	if (v_iIdEquipeUtilisateur > 0 && v_iIdEquipeUtilisateur != top.g_iIdEquipe)
	{
		alert("Vous ne pouvez pas déposer de message dans cette partie du forum,\ncar vous ne faites pas partie de l'équipe '" + ret_nom_equipe_selectionner() + "'.");
		return;
	}
	
	if (v_iIdEquipeUtilisateur < 1)
		v_iIdEquipeUtilisateur = top.g_iIdEquipe;
	
	var iIdSujet = top.oFrmMessages().ret_id_sujet();
	top.page_message = new Array(iIdSujet,0,0);
	/*return ouvrir_modifier_message(iIdSujet,0,"ajouter",v_bMessageEquipes);*/
	var sUrl = "modifier_message-index.php"
		+ "?modaliteFenetre=ajouter"
		+ "&idSujet=" + iIdSujet
		+ "&idMessage=0"
		+ "&idNiveau=" + top.g_iIdNiveau
		+ "&typeNiveau=" + top.g_iTypeNiveau
		+ "&idEquipe=" + (v_bMessageEquipes ? "0" : v_iIdEquipeUtilisateur);
	var oWinMessageSujet = PopupCenter(sUrl,"winMessageSujet",750,560,"");
	oWinMessageSujet.focus();
	return false;
}

function popup_modifier_message()
{
	var oForm = top.oFrmMessages().document.forms[0];
	var iIdSujet = oForm.elements["idSujet"].value;
	var aoObjs = oForm.elements["idMessage"];
	var iIdMessage = ret_element_selectionner(aoObjs);
	if (iIdMessage == 0) {
		alert("Vous devez sélectionner un message avant de modifier");
		return;
	}
	var oMyDomWin = new DOMWindow(top.oFrmMessages());
	top.page_message = new Array(iIdSujet,0,oMyDomWin.pageYOffset());
	return ouvrir_modifier_message(iIdSujet,iIdMessage,"modifier",false);
}

function popup_supprimer_message()
{
	var oForm = top.oFrmMessages().document.forms[0];
	var iIdSujet = oForm.elements["idSujet"].value;
	var aoObjs = oForm.elements["idMessage"];
	var iIdMessage = ret_element_selectionner(aoObjs);
	
	if (iIdMessage == 0)
	{
		alert("Vous devez sélectionner un message avant de supprimer");
		return;
	}
	
	var sUrl = "modifier_message-index.php"
		+ "?modaliteFenetre=supprimer"
		+ "&idSujet=" + iIdSujet
		+ "&idMessage=" + iIdMessage
		+ "&idNiveau=" + top.g_iIdNiveau
		+ "&typeNiveau=" + top.g_iTypeNiveau;
	var oWinSupprimerMessage = PopupCenter(sUrl,"winSupprimerMessage",420,180,"");
	oWinSupprimerMessage.focus();
	
	return false;
}

function rafraichir_liste_messages() { oFrmMessages().document.forms[0].submit(); }

