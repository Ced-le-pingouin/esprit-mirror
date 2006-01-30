function ajoutobj(idformulaire)
{
	PopupCenter('formulaire_modif_ajout.php?idformulaire='+idformulaire,'WinAjoutObjForm',450,150,'location=no,status=no,toolbar=no,scrollbars=no');
}

function supobj(idobj, idformulaire)
{
	if (confirm('Voulez-vous supprimer l\'objet sélectionné ?'))
	{
		parent.FORMFRAMEMODIF.location.replace("formulaire_modif_sup.php?idobj="+idobj+"&idformulaire="+idformulaire);
	}
}
	
function modifposobj(idobj, idformulaire)
{
	PopupCenter('position_objet.php?idobj='+idobj+'&idformulaire='+idformulaire,'WinModifPosObjForm',300,150,'location=no,status=no,toolbar=no,scrollbars=no');
}

function copieobj(idobj, idformulaire) 
{
	if (confirm('Voulez-vous copier l\'objet sélectionné ?'))
	{
		parent.FORMFRAMEMODIF.location.replace("formulaire_modif_copie.php?idobj="+idobj+"&idformulaire="+idformulaire);
	}
}
