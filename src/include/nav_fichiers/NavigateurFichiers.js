function confirmerSuppression()
{
	var elems = document.getElementsByName('fichiers[]');
	var auMoinsUnSel = false;
	for (var i = 0; i < elems.length; i++)
	{
		if (elems[i].checked)
		{
			auMoinsUnSel = true;
			break;
		}
	}
	
	if (auMoinsUnSel)
		return confirm('Etes-vous sûr(e) de vouloir supprimer les éléments sélectionnés ?');
}

function basculerPressePapiers()
{
	var pp = document.getElementById('cadrePressePapiers');
	if (pp.style.display == "")
		pp.style.display = "none";
	else
		pp.style.display = "";
}

function ajouterActionPressePapiers()
{
	var voir = document.createElement('input');
	voir.setAttribute('type', 'button');
	voir.setAttribute('name', 'voir');
	voir.setAttribute('value', 'Voir');
	voir.setAttribute('title', 'Afficher/masquer le contenu actuel du presse-papiers');
	voir.onclick = basculerPressePapiers;
	var vider = document.getElementsByName('viderPressePapiers')[0];
	vider.parentNode.insertBefore(voir, vider);
	//basculerPressePapiers(); // masquer au chargement de la page
}

function initPage()
{
	//ajouterActionPressePapiers();
	document.getElementsByName('supprimer')[0].onclick = confirmerSuppression;
}

var nav_ancienOnLoad = window.onload || new Function();
window.onload = function() { nav_ancienOnLoad(); initPage(); };