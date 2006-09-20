function selectionobj(idobj,idformulaire) 
{
	parent.FORMFRAMEMODIFMENU.location.replace("formulaire_modif_menu.php?idobj="+idobj+"&idformulaire="+idformulaire);
	parent.FORMFRAMEMODIF.location.replace("formulaire_modif.php?idobj="+idobj+"&idformulaire="+idformulaire);
}

function rechargerliste(idobj,idformulaire) 
{
	parent.FORMFRAMELISTE.location.replace("formulaire_liste.php?idobj="+idobj+"&idformulaire="+idformulaire+"&pos="+idobj); 
	//permet de rafraichir la frame liste[dessus] avec le formulaire dont on envoie le numéro
}

function rechargermenugauche()
{
	//alert ("je suis dans rechargermenugauche");
	parent.FORMFRAMEMENU.location.replace("formulaire_menu.php"); 
}

function rechargerlistepopup(idobj,idformulaire) 
{
	opener.parent.FORMFRAMELISTE.location.replace("formulaire_liste.php?idobj="+idobj+"&idformulaire="+idformulaire+"&pos="+idobj); 
	//permet de rafraichir la frame liste[dessus] avec le formulaire dont on envoie le numéro depuis une popup
}

function majmodifmenu(idobj,idformulaire)
{
	parent.FORMFRAMEMODIFMENU.location.replace("formulaire_modif_menu.php?idobj="+idobj+"&idformulaire="+idformulaire);
}

function popupajout(idobj,idformulaire) 
{
	opener.parent.FORMFRAMEMODIFMENU.location.replace("formulaire_modif_menu.php?idobj="+idobj+"&idformulaire="+idformulaire);
	opener.parent.FORMFRAMELISTE.location.replace("formulaire_liste.php?idobj="+idobj+"&idformulaire="+idformulaire+"&pos="+idobj);
	opener.parent.FORMFRAMEMODIF.location.replace("formulaire_modif.php?idobj="+idobj+"&idformulaire="+idformulaire);
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
		sMessage += "Ce formulaire est actuellement utilisé ("+v_iNbUtilisations+" fois) dans le cadre des cours sur la plate-forme.\n";
		sMessage += "Si vous le modifiez, il perdra peut-être son sens dans le contexte où il est en cours d'utilisation.\n";
		sMessage += "\n\n";
	}

	if (v_iNbRemplis > 0)
	{
		if (sMessage != "")
			sMessage += "De plus, toujours dans le cadre des cours, ";
		else
			sMessage += "Ce formulaire a déjà été utilisé dans le cadre des cours sur la plate-forme, et";
		
		sMessage += " il a déjà été complété par des étudiants ("+v_iNbRemplis+" fois).\n";
		sMessage += "Si vous le modifiez, il se peut que les réponses déjà données par ces étudiants soient perdues.\n";
		sMessage += "\n\n";
	}
	
	if (sMessage != "")
		alert("ATTENTION\n\n" + sMessage + "C'est pourquoi il est vivement conseillé de faire une copie de ce formulaire, et de modifier cette copie.\n\n");
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

