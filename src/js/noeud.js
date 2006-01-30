function Noeud(v_oNoeud) {
	this.noeud = v_oNoeud;
	this.ret_largeur = ret_largeur_noeud;
	this.ret_hauteur = ret_hauteur_noeud;
	this.ret_nom_balise = ret_nom_balise_noeud;
	this.ret_parent = ret_parent_noeud;
	this.ret_position_gauche = ret_position_gauche_noeud;
	this.ret_position_haut = ret_position_haut_noeud;
	this.modifier_texte = modifier_texte_noeud;
	this.def_zone_affichage = def_zone_affichage_noeud;
}

function ret_position_gauche_noeud() { return this.noeud.offsetLeft; }
function ret_position_haut_noeud() { return this.noeud.offsetTop; }
function ret_largeur_noeud() { return this.noeud.offsetWidth; }
function ret_hauteur_noeud(v_oNoeud) { return this.noeud.offsetHeight; }
function ret_nom_balise_noeud(v_oNoeud) { return this.noeud.tagName; }
function ret_parent_noeud() {
	var obj = this.noeud.parentNode;
	
	if (arguments[0] != null)
		while (obj.tagName != arguments[0])
			obj = obj.parentNode;
	
	return new Noeud(obj);
}

function modifier_texte_noeud(v_sTexte) { this.noeud.innerHTML = v_sTexte; }

function def_zone_affichage_noeud(v_oNoeud) {
	var iLargeur = parseInt(v_oNoeud.ret_largeur() - this.ret_position_gauche());
	
	if (iLargeur < parseInt(this.ret_largeur())) {
		this.noeud.style.clip = 'rect('
			+ '0px '
			+ parseInt(iLargeur - 25) + 'px '
			+ this.ret_hauteur() + 'px '
			+ '0px'
			+ ')';
	} else {
		this.noeud.style.clip = null;
	}
}
