function rechargerDroite(idformulaire,idobj,bMesForms)
{
	sTypeAction = retParamUrl(window.location,'typeaction');
	bChgtFormul = false;
	if(sTypeAction == 'selection')
	{
		idformcourant = retParamUrl(parent.FORMFRAMELISTE.location,'idformulaire');
		if(idformcourant != idformulaire)
			bChgtFormul = true;
	}
	if(bChgtFormul)
		parent.FORMFRAMELISTE.location.replace("formulaire_liste.php?idobj="+idobj+"&idformulaire="+idformulaire+"&bMesForms="+bMesForms+"&verifUtilisation=1");
	else
		parent.FORMFRAMELISTE.location.replace("formulaire_liste.php?idobj="+idobj+"&idformulaire="+idformulaire+"&bMesForms="+bMesForms);
	parent.FORMFRAMEMODIF.location.replace("formulaire_modif.php?idobj="+idobj+"&idformulaire="+idformulaire+"&bMesForms="+bMesForms);
}
function selectionobj(idformulaire,idobj,bMesForms) 
{
	parent.FORMFRAMEMENU.location.replace("formulaire_menu.php?idformulaire="+idformulaire+"&idobj="+idobj+"&bMesForms="+bMesForms);
	parent.FORMFRAMEMODIF.location.replace("formulaire_modif.php?idformulaire="+idformulaire+"&idobj="+idobj+"&bMesForms="+bMesForms);
}
function rechargerliste(idformulaire,idobj,bMesForms) 
{
	parent.FORMFRAMEMENU.location.replace("formulaire_menu.php?idformulaire="+idformulaire+"&idobj="+idobj+"&bMesForms="+bMesForms);
	parent.FORMFRAMELISTE.location.replace("formulaire_liste.php?idformulaire="+idformulaire+"&idobj="+idobj+"&bMesForms="+bMesForms);
}
function ajoutobj(idformulaire,bMesForms)
{
	PopupCenter('formulaire_modif_ajout.php?idformulaire='+idformulaire+"&bMesForms="+bMesForms,'WinAjoutObjForm',450,150,'location=no,status=no,toolbar=no,scrollbars=no');
}
function supobj(idformulaire,idobj,bMesForms) 
{
	if (confirm('Voulez-vous supprimer l\'élément sélectionné ?'))
	{
		parent.FORMFRAMELISTE.location.replace("formulaire_liste.php?idobj="+idobj+"&idformulaire="+idformulaire+"&action=supprimer"+"&bMesForms="+bMesForms);
	}
}
function modifposobj(idformulaire,idobj)
{
	PopupCenter('position_objet.php?idobj='+idobj+'&idformulaire='+idformulaire,'WinModifPosObjForm',300,150,'location=no,status=no,toolbar=no,scrollbars=no');
}
function copieobj(idformulaire,idobj,bMesForms) 
{
	if (confirm('Voulez-vous copier l\'élément sélectionné ?'))
	{
		parent.FORMFRAMELISTE.location.replace("formulaire_liste.php?idobj="+idobj+"&idformulaire="+idformulaire+"&action=copier"+"&bMesForms="+bMesForms);
	}
}
function modifaxeform(idformulaire)
{
	PopupCenter('formulaire_axe.php?idformulaire='+idformulaire,'WinModifAxesForm',550,400,'location=no,status=no,toolbar=no,scrollbars=yes,resizable=no');
}
function popupajout(idformulaire,idobj,bMesForms) 
{
	opener.parent.FORMFRAMELISTE.location.replace("formulaire_liste.php?idformulaire="+idformulaire+"&idobj="+idobj+"&bMesForms="+bMesForms);
	opener.parent.FORMFRAMEMENU.location.replace("formulaire_menu.php?idformulaire="+idformulaire+"&idobj="+idobj+"&bMesForms="+bMesForms);
	opener.parent.FORMFRAMEMODIF.location.replace("formulaire_modif.php?idformulaire="+idformulaire+"&idobj="+idobj+"&bMesForms="+bMesForms);
}

//Permet de vérifier si un champ est bien numérique
function verifNumeric(Num)
{ 
      if (isNaN(Num.value)) 
	  {
		  alert("Saisie invalide"); 
		  Num.select(); 
		  Num.focus(); 
	  }
} 

function verifNbQcocher(NbRepMaxQC,MessMaxQC)
{ 
	/*
	if (MessMaxQC != '') 
	{ 
		if (document.getElementsByName("192")[0].checked == true) {alert("hello");};
	} 
	*/
} 

function alerteFormulaireUtilise(v_iNbUtilisations, v_iNbRemplis)
{
	sMessage = "";
	
	if (v_iNbUtilisations > 0)
	{
		sMessage += "Cette activité en ligne est actuellement utilisée ("+v_iNbUtilisations+" fois) dans le cadre des cours sur la plate-forme.\n";
		sMessage += "Si vous la modifiez, elle perdra peut-être son sens dans le contexte où elle est en cours d'utilisation.\n";
		sMessage += "\n\n";
	}

	if (v_iNbRemplis > 0)
	{
		if (sMessage != "")
			sMessage += "De plus, toujours dans le cadre des cours,";
		else
			sMessage += "Cette activité en ligne a déjà été utilisée dans le cadre des cours sur la plate-forme, et";
		
		sMessage += " elle a déjà été complétée par des étudiants ("+v_iNbRemplis+" fois).\n";
		sMessage += "Si vous la modifiez, il se peut que les réponses déjà données par ces étudiants soient perdues.\n";
		sMessage += "\n\n";
	}
	
	if (sMessage != "")
		alert("ATTENTION\n\n" + sMessage + "C'est pourquoi il est vivement conseillé de faire une copie de cette activité en ligne, et de modifier cette copie.\n\n");
}

var PARAM_NOM = 0;
var PARAM_VAL = 1;

function retParamUrl(v_sUrl, v_sNomParam)
{
	var asParams = new Array();
	var r_asParams = new Array();
	var i = 0, iPosParams = -1, iPosAncre = -1;

	// il faut transformer le paramètre passé en vraie chaine pour utiliser les fonctions indexOf, etc
	v_sUrl = String(v_sUrl);
	// y a-t-il des paramètres ?
	iPosParams = v_sUrl.indexOf("?");

	// si non, on retourne null
	if (iPosParams == -1)
	{
		return null;
	}
	else
	{
		// on ne retient que les paramètres après le '?'
		v_sUrl = v_sUrl.slice(++iPosParams);

		// si l'URL contient une ancre (#top par ex.), il faut l'enlever avant de récupérer les paramètres
		if ((iPosAncre = v_sUrl.indexOf('#')) != -1)
			v_sUrl = v_sUrl.slice(0,iPosAncre);

		asParams = v_sUrl.split("&");
		
		for (i = 0; i < asParams.length; i++)
		{
			r_asParams[i] = asParams[i].split("=");
			if (v_sNomParam == r_asParams[i][PARAM_NOM])
				return r_asParams[i][PARAM_VAL];
		}
		
		// s'il y avait bien un paramètre à l'appel, comme on ne l'a pas trouvé,
		// on renvoie null
		if (v_sNomParam)
			return null;
		// si l'appel était fait sans paramètre, on renvoie le tableau avec tous les paramètres
		else
			return r_asParams;
	}
}
