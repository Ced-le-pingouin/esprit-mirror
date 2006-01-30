var g_iIdFormulaire = 0;

function selectionFormulaire(v_sTitre)
{
	var oSelect = document.forms['listeformulaire'].idformulaire;
	var iIdFormulaireSel = oSelect.options[oSelect.selectedIndex].value;
	//alert (iIdFormulaireSel);
	
	// recharger l'écran principal avec le bon formulaire
	parent.FORMFRAMELISTE.location.replace('formulaire_liste.php?idformulaire='+iIdFormulaireSel+'&idobj=0');
	top.frames['menu'].location.replace('formulaire_bas.php?idformulaire='+iIdFormulaireSel);
	
	document.forms['listeformulaire'].action='formulaire_menu.php';
	document.forms['listeformulaire'].target='_self';
	document.forms['listeformulaire'].submit();
}

function suppression(TypeAct)
{
	if (document.listeformulaire.idformulaire.selectedIndex == -1)
	{
		alert('Veuillez sélectionner un formulaire dans la liste');
	}
	else
	{
		if(confirm('Voulez-vous supprimer le formulaire sélectionné ?'))
		{
			document.forms['listeformulaire'].typeaction.value=TypeAct;
			document.forms['listeformulaire'].action='formulaire_menu.php';
			document.forms['listeformulaire'].target='_self';
			document.forms['listeformulaire'].submit();
			parent.FORMFRAMELISTE.location.replace('formulaire_liste.php');
			
			parent.FORMFRAMELISTE.location.replace('formulaire_liste.php');
			parent.FORMFRAMEMODIF.location.replace('formulaire_modif.php');
			parent.FORMFRAMEMODIFMENU.location.replace('formulaire_modif_menu.php');
		}
	}
}

function copie(TypeAct)
{
	if (document.listeformulaire.idformulaire.selectedIndex == -1)
	{
		alert('Veuillez sélectionner un formulaire dans la liste');
	}
	else
	{
		if(confirm('Voulez-vous copier le formulaire sélectionné ?'))
		{
			document.forms['listeformulaire'].typeaction.value=TypeAct;
			document.forms['listeformulaire'].action='formulaire_menu.php';
			document.forms['listeformulaire'].target='_self';
			document.forms['listeformulaire'].submit();
			parent.FORMFRAMEMODIF.location.replace('formulaire_modif.php');
			parent.FORMFRAMEMODIFMENU.location.replace('formulaire_modif_menu.php')
			parent.FORMFRAMELISTE.location.replace('formulaire_liste.php');
			//-> de preference pointer vers la copie
		}
	}
}
