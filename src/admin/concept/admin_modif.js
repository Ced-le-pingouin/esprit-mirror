var asConfirm = new Array("cette formation","ce cours","cette rubrique","cette unité","ce block","cet élément actif");

function Ajouter()
{
	with(document.forms[0])
	{
		act.value="ajouter";
		submit();
	}
}

function Couper()
{
	top.document.copier = null;
	//top.document.couper = new Array(<?php echo "'$g_iType','$g_sParams'"; ?>);
}

function Copier()
{
	top.document.couper = null;
	//top.document.copier = new Array(<?php echo "'$g_iType','$g_sParams'"; ?>);
}

function Coller()
{
	top.frames[0].document.width = "15px";
}

function Effacer(v_iNum)
{
	if (confirm("Etes-vous certain de vouloir supprimer " + asConfirm[v_iNum] + " ?"))
	{
		with(document.forms[0])
		{
			act.value="supprimer";
			submit();
		}
	}
}

function afficherElementType(v_sIdDiv,v_iType,v_iNbTotalDivs) {
	i=0;
	
	do {
		obj = document.getElementById(v_sIdDiv + i);
		
		if (obj) {
			obj.style.visibility = (v_iType != i ? 'hidden' : 'visible');
			obj.style.display = (v_iType != i ? 'none' : 'block');
		}
		
		i++;
	} while (i <= v_iNbTotalDivs)
}

function choisirType(v_aoType,v_iType)
{
	var iType = parseInt(v_iType)-1;
	for (var i=0; i<v_aoType.length; i++)
		if (v_aoType[i])
			v_aoType[i].className = "Cacher";
	
	if (v_aoType[iType])
		v_aoType[iType].className = "Afficher";
	
	window.setTimeout("afficher_zone_description(" + v_iType + ")",50);
}

function MontrerCacher(v_sNomDiv,v_bAfficher)
{
	if (document.getElementById)
		document.getElementById(v_sNomDiv).className = (v_bAfficher == 2 || v_bAfficher == 4 ? "Afficher" : "Cacher");
}

function afficher_zone_description(x)
{
	var obj = document.forms[0].elements["MODALITE_AFFICHAGE[" + x + "]"];
	var y   = 0;
	
	if (obj)
	{
		for (i=0; i<obj.options.length; i++)
			if (obj.options[i].selected)
				y = obj.options[i].value;
	}
	
	MontrerCacher('div_description',y);
}

function ouvrir_dico_intitules(v_iTypeIntitule)
{
	var sUrl = "intitules/intitule-index.php?TYPE_INTITULE=" + v_iTypeIntitule;
	var wDicoIntitules = PopupCenter(sUrl,"wDicoIntitules",250,350,",menubar=0,statuts=0");
	wDicoIntitules.focus();
}

function reinitIntitules(v_amIntitule,v_sNomIntituleSelect)
{
	var options = document.forms[0].elements[g_sNomHtmlSelectIntitules].options;
	
	for (i=options.length-1; i>=0; i--)
	{
		if (v_sNomIntituleSelect == null && options[i].selected)
			v_sNomIntituleSelect = options[i].value;
			
		options[i] = null;
	}
	
	options[0] = new Option("Pas d'intitulé","",false,false);
	
	for (i=0; i<v_amIntitule.length; i++)
	{
		options[i+1] = new Option(v_amIntitule[i],v_amIntitule[i],false,false);
		
		if (v_sNomIntituleSelect == v_amIntitule[i])
			options[i+1].selected = true;
	}
}

function ajouterFormation(v_iIdForm)
{
	var wForm;
	wForm = window.open("index_nouv_form.php?ID_FORM=" + v_iIdForm,"wNouvForm",centrerFenetre(640,400) + ",location=0,menubar=0,scrollbars=1,resizable=0,status=0,toolbar=0");
	wForm.focus();
}

function composer_chats(v_iIdNiveau,v_iTypeNiveau)
{
	var wComposerChats;
	var sParamsUrl = "?idNiveau=" + v_iIdNiveau
		+ "&typeNiveau=" + v_iTypeNiveau;
	wComposerChats = window.open("chat/chat-index.php" + sParamsUrl,"wComposerChats",centrerFenetre(640,480) + ",location=0,menubar=0,scrollbars=1,resizable=0,status=0,toolbar=0");
	wComposerChats.focus();
}

function modalite_affichage(v_sType,v_iIdType) {
	switch (v_sType) {
		case 'tableau_de_bord':
			var HtmlSelectElement = document.getElementsByName('MODALITE_AFFICHAGE[' + v_iIdType + ']').item(0);
			var HtmlTextAreaElement = document.getElementsByName('DESCRIPTION[' + v_iIdType + ']').item(0);
			
			if (HtmlSelectElement.options[HtmlSelectElement.selectedIndex].value != 1)
				HtmlTextAreaElement.disabled = true;
			else
				HtmlTextAreaElement.disabled = false;
			
			break;
	}
}
