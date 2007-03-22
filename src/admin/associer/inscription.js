function visible(v_elem) {
	for (var i=0; i<v_elem.childNodes.length; i++)
	{
		if ( v_elem.childNodes.item(i).nodeName == "DT"
			|| v_elem.childNodes.item(i).nodeName == "DD") {
			if (v_elem.childNodes.item(i).className == "replier")
				v_elem.childNodes.item(i).className = "deplier";
			else
				v_elem.childNodes.item(i).className = "replier";
		}
		
		if (v_elem.childNodes.item(i).nodeName == "DD")
			break;
	}
}

function rechargerListeInscrits(v_iNouveauStatut) {
	with (document.getElementById("recharger_liste_inscrits")) {
		elements["idstatut"].value = v_iNouveauStatut;
		submit();
	}
}

function changerTaille(v_element, v_nouvelleTaille) {
	if (v_nouvelleTaille < 50)
		v_nouvelleTaille = 50;
	v_element.style.height = v_nouvelleTaille + "px";
}

function verifierTailles() {
	var heightIFrame;
	var height = window.innerHeight;
	var heightRows = document.getElementsByTagName('tr').item(0).offsetHeight
		+ document.getElementsByTagName('tr').item(2).offsetHeight;

	if (document.body.clientHeight)
		height = document.body.clientHeight;

	height = height - heightRows - 20;

	heightIFrame = parseInt(height - document.getElementById('liste_personnes').offsetTop);
	changerTaille(document.getElementById('liste_personnes'), heightIFrame);

	heightIFrame = parseInt(height - document.getElementById('liste_inscrits').offsetTop);
	changerTaille(document.getElementById('liste_inscrits'), heightIFrame);
}

function init() {
	verifierTailles();
}

window.onload = init;
window.onresize = verifierTailles;