function selectionobj(idobj, idformulaire, v_sTitre)
{
	// barre horizontale centrale
	parent.FORMFRAMEMODIFMENU.location.replace("formulaire_modif_menu.php?idobj="+idobj+"&idformulaire="+idformulaire);
	// �cran inf�rieur (modif options de l'�l�ment)
	parent.FORMFRAMEMODIF.location.replace("formulaire_modif.php?idobj="+idobj+"&idformulaire="+idformulaire);
	// menu gauche
	rechargermenugauche(idobj, idformulaire);
}

function rechargerliste(idobj,idformulaire) 
{
	parent.FORMFRAMELISTE.location.replace("formulaire_liste.php?idobj="+idobj+"&idformulaire="+idformulaire+"&pos="+idobj); 
	//permet de rafraichir la frame liste[dessus] avec le formulaire dont on envoie le num�ro
}

function rechargermenugauche(idobj, idformulaire)
{
	var bMesForms = (parent.FORMFRAMEMENU.document.forms['listeformulaire'].cbMesForms.checked?1:0);
	
	//alert ("je suis dans rechargermenugauche: "+bMesForms);
	if (!arguments.length)
		parent.FORMFRAMEMENU.location.replace("formulaire_menu.php?cbMesForms="+bMesForms);
	else
		parent.FORMFRAMEMENU.location.replace("formulaire_menu.php?idobj="+idobj+"&idformulaire="+idformulaire+"&cbMesForms="+bMesForms);
}

function rechargerlistepopup(idobj,idformulaire) 
{
	opener.parent.FORMFRAMELISTE.location.replace("formulaire_liste.php?idobj="+idobj+"&idformulaire="+idformulaire+"&pos="+idobj); 
	//permet de rafraichir la frame liste[dessus] avec le formulaire dont on envoie le num�ro depuis une popup
}

function majmodifmenu(idobj,idformulaire)
{
	parent.FORMFRAMEMODIFMENU.location.replace("formulaire_modif_menu.php?idobj="+idobj+"&idformulaire="+idformulaire);
}


function modifaxeform(idformulaire) 
{ 
	fenetre = window.open("formulaire_axe.php?idformulaire="+idformulaire,"PopUP","width=450,height=300,location=no,status=no,toolbar=no,scrollbars=yes,left=' + ((screen.width - 450)/2) + ',top=' + ((screen.height - 300)/2)");
}

function popupajout(idobj,idformulaire) 
{
	opener.parent.FORMFRAMEMODIFMENU.location.replace("formulaire_modif_menu.php?idobj="+idobj+"&idformulaire="+idformulaire);
	opener.parent.FORMFRAMELISTE.location.replace("formulaire_liste.php?idobj="+idobj+"&idformulaire="+idformulaire+"&pos="+idobj);
	opener.parent.FORMFRAMEMODIF.location.replace("formulaire_modif.php?idobj="+idobj+"&idformulaire="+idformulaire);
}

//Permet de v�rifier si un champ est bien num�rique
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
		sMessage += "Ce formulaire est actuellement utilis� ("+v_iNbUtilisations+" fois) dans le cadre des cours sur la plate-forme.\n";
		sMessage += "Si vous le modifiez, il perdra peut-�tre son sens dans le contexte o� il est en cours d'utilisation.\n";
		sMessage += "\n\n";
	}

	if (v_iNbRemplis > 0)
	{
		if (sMessage != "")
			sMessage += "De plus, toujours dans le cadre des cours, ";
		else
			sMessage += "Ce formulaire a d�j� �t� utilis� dans le cadre des cours sur la plate-forme, et";
		
		sMessage += " il a d�j� �t� compl�t� par des �tudiants ("+v_iNbRemplis+" fois).\n";
		sMessage += "Si vous le modifiez, il se peut que les r�ponses d�j� donn�es par ces �tudiants soient perdues.\n";
		sMessage += "\n\n";
	}
	
	if (sMessage != "")
		alert("ATTENTION\n\n" + sMessage + "C'est pourquoi il est vivement conseill� de faire une copie de ce formulaire, et de modifier cette copie.\n\n");
}
