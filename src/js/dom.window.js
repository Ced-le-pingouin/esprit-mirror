/**
 * DOMWindow
 *
 * Auteur: Filippo PORCO <filippo.porco@umh.ac.be>
 * Dernière modification: 16/12/2004
 *
 * Unité de Technologie de l'Education
 * 18, Place du Parc
 * 7000 MONS
 */

function DOMWindow(v_oWindow) {
	this.window = (v_oWindow ? v_oWindow : window);
}

DOMWindow.prototype.scrollTo = function(v_xCoord,v_yCoord) {
	if (window.scrollTo) {
		this.window.scrollTo(v_xCoord,v_yCoord);
	} else if (document.body) {
		this.window.document.body.scrollLeft = v_xCoord;
		this.window.document.body.scrollTop = v_yCoord;
	}
};

DOMWindow.prototype.pageXOffset = function() {
	if (window.pageXOffset)
		return this.window.pageXOffset;
	else if (document.body)
		return this.window.document.body.scrollLeft;
	else
		return 0;
};

DOMWindow.prototype.pageYOffset = function() {
	if (window.pageYOffset)
		return this.window.pageYOffset;
	else if (document.body)
		return this.window.document.body.scrollTop;
	else
		return 0;
};

DOMWindow.prototype.innerWidth = function() {
	if (window.innerHeight)
		return this.window.innerWidth;
	else if (document.body)
		return this.window.document.body.clientWidth;
};

DOMWindow.prototype.innerHeight = function() {
	if (window.innerHeight)
		return this.window.innerHeight;
	else if (document.body)
		return this.window.document.body.clientHeight;
};

