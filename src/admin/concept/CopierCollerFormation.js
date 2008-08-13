(function() {

function Onglet(nom, intitule, elemCadreRattache)
{
	this._observateurs = [];
	this.estSelectionne = false;

	this._elemCadreRattache = elemCadreRattache;
	this.nom = nom;
	this.intitule = intitule;

	this.elemOnglet = this._creerHtml();
}

Onglet.prototype._creerHtml = function()
{
	var that = this;
	
	var a = document.createElement('a');
	a.setAttribute('href', '#');
	a.onclick = function(e) { that.selectionner(); return false; };
	a.appendChild(document.createTextNode(this.intitule));

	var li = document.createElement('li');
	li.setAttribute('id', 'onglet' + this.nom);
	li.appendChild(a);
	
	return li;
};

Onglet.prototype.selectionner = function() { this.defSelectionne(true); };
Onglet.prototype.deselectionner = function() { this.defSelectionne(false); };
Onglet.prototype.defSelectionne = function(estSelectionne)
{
	if (estSelectionne)
	{
		if (this.elemOnglet.className.search(/\bactif\b/) === -1)
			this.elemOnglet.className += ' actif';
		
		this._elemCadreRattache.style.display = '';			
	}
	else
	{
		this.elemOnglet.className = this.elemOnglet.className.
		                            replace(/\bactif\b/, '');
		this._elemCadreRattache.style.display = 'none';
	}
	
	this.estSelectionne = estSelectionne;
	
	this._notifier(estSelectionne);
};

Onglet.prototype.ajouterObservateur = function(obs)
{
	this._observateurs.push(obs);
};

Onglet.prototype._notifier = function(message)
{
	for (var i in this._observateurs)
		this._observateurs[i].mettreAJour(this, message);
};


function GroupeOnglets(onglets, elemSuivant, nomOngletAActiver)
{
	this._observateurs = [];
	this._elemSuivant = elemSuivant;
	this._observer = true;
	
	this._onglets = {};
	for (var i in onglets)
	{
		var nom = onglets[i].nom;
		this._onglets[nom] = onglets[i];
		this._onglets[nom].ajouterObservateur(this);
	}
	
	this._creerHtml();
	this.activerOnglet(nomOngletAActiver || onglets[0].nom);
}

GroupeOnglets.prototype._creerHtml = function()
{
	var ul = document.createElement('ul');
	// !!! setAttribute('class',...) non pris en cpt ds IE !!!
	ul.className = 'onglets';
	
	for (var i in this._onglets)
		ul.appendChild(this._onglets[i].elemOnglet);
	
	this._elemSuivant.parentNode.insertBefore(ul, this._elemSuivant);
};	

GroupeOnglets.prototype.mettreAJour = function(sujet, estSelectionne)
{
	// éviter que les actions lancées dans cette méthode provoquent de nouvelles
	// notifications, qui appeleraient à nouveau la méthode, qui provoquerait de
	// nouvelles notifications etc etc ==> "too much recursion" car boucle 
	// infinie
	if (!this._observer)
		return;
	
	this._observer = false;
	
	if (estSelectionne)
		this.activerOnglet(sujet.nom);
		
	this._observer = true;
};

GroupeOnglets.prototype.activerOnglet = function(nomOngletAActiver)
{
	for (var nom in this._onglets)
		if (nom !== nomOngletAActiver)
			this._onglets[nom].deselectionner();

	this._onglets[nomOngletAActiver].selectionner();
	
	this._notifier(nomOngletAActiver);
};

GroupeOnglets.prototype.ajouterObservateur = Onglet.prototype.ajouterObservateur;
GroupeOnglets.prototype._notifier = Onglet.prototype._notifier;


function ListeChoix(nomRadios, miseEnEvidence)
{
	this._radios = document.getElementsByName(nomRadios);
	this._cadreScroll = this._retElemParentParTag(this._radios[0], 'div');
	
	this._ancienChoix  = null;
	this._nouveauChoix = null;
	
	var that = this;
	
	if (miseEnEvidence !== false)
	{
		var gererMiseEnEvidenceSelection = function(e)
		{
			that._nouveauChoix = that._retSelectionRadios();
			
			var elemAncienChoix = that._retElemParentParTag(that._ancienChoix, 'li');
			if (elemAncienChoix)
				elemAncienChoix.className = elemAncienChoix.className
				                            .replace(/\bnivSel\b/, '');
			
			elemNouveauChoix = that._retElemParentParTag(that._nouveauChoix, 'li');
			if (elemNouveauChoix
			     && elemNouveauChoix.className.search(/\bnivSel\b/) === -1)
				elemNouveauChoix.className += ' nivSel';
			
			that._ancienChoix = that._nouveauChoix;
		};
		
		for (var i = 0, l = this._radios.length; i < l; i++)
			this._radios[i].onclick = gererMiseEnEvidenceSelection;
		
		gererMiseEnEvidenceSelection();
	}
	
	this.allerVersSelection();
}

ListeChoix.prototype._retElemParentParTag = function(elem, tag)
{
	if (!elem) return null;
	
	var elemCourant = elem;
	
	while (elemCourant = elemCourant.parentNode) // assignation voulue!
		if (elemCourant.tagName.toLowerCase() == tag)
			return elemCourant;
			
	return null;
}

ListeChoix.prototype._retSelectionRadios = function()
{
	for (var i = 0, l = this._radios.length; i < l; i++)
		if (this._radios[i].checked)
			return this._radios[i];
		
	return null;
};

ListeChoix.prototype.allerVersSelection = function()
{
	var elemSelectionne = this._retSelectionRadios();
	
	if (elemSelectionne)
		this._cadreScroll.scrollTop = (elemSelectionne.offsetTop
		                               - this._cadreScroll.offsetTop);
}

function initOnglets()
{
	grpOnglets = new GroupeOnglets([
		new Onglet('Copier', 'Copier de',
		           document.getElementById('cadreCopier')),
		new Onglet('Coller', 'Coller vers',
		           document.getElementById('cadreColler'))
		],
		document.getElementById('cadreOnglets'),
		document.getElementById('ongletCourant').value
	);
	
	grpOnglets.ajouterObservateur({
		mettreAJour: function(sujet, nomOngletCourant) {
			document.getElementById('ongletCourant').value = nomOngletCourant;
		}
	});
}

function enleverBoutonsChoisir()
{
	// les boutons Choisir disparaissent
	document.getElementsByName('changerFormationSrc')[0].style.display = 'none';
	document.getElementsByName('changerFormationDest')[0].style.display = 'none';
	// tout changement dans leur liste associée est automatiquement soumis
	document.getElementById('idFormationSrcId').onchange = function()
	 { document.getElementsByName('changerFormationSrc')[0].click(); };
	document.getElementById('idFormationDestId').onchange = function()
	 { document.getElementsByName('changerFormationDest')[0].click(); };
}

function associerConfirmationBoutonSupprimer()
{
	document.getElementById('supprimerColler').onclick = function()
	{
		return confirm(
			"Êtes-vous sûr(e) de vouloir supprimer cet élément ?\n"
			+ "ATTENTION!!! Il s'agit d'une suppression définitive!!!"
		);
	}
}

function initPage()
{
	initOnglets();
	enleverBoutonsChoisir();
	associerConfirmationBoutonSupprimer();
	new ListeChoix('brancheSrcSel');
	new ListeChoix('brancheDestSel');
}

var ancienOnLoad = window.onload || function() {};
window.onload = function() { ancienOnLoad(); initPage(); };

})();