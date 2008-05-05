function DOMElement(v_sElem) {
	// Properties
	this.name = v_sElem;
	this.element = document.getElementById(this.name);
	this.offsetParent = 0;
	this.offsetLeft = 0;
	this.offsetTop = 0;
	this.height = 0;
	
	// Initialiser cet élément
	this.DOMElementInit();
}

DOMElement.prototype.DOMElementInit = function() {
	if (this.element) {
		if (document.defaultView) {
			this.offsetTop = document.defaultView.getComputedStyle(this.element, "").getPropertyValue("top");
			this.height = document.defaultView.getComputedStyle(this.element,"").getPropertyValue("height");
		} else if (this.element.offsetHeight) {
			this.height = this.element.offsetHeight;
		} else {
			this.height = this.element.currentStyle.height;
		}
	}
};

DOMElement.prototype.getOffsetTop = function() {
	return this.element.offsetTop;
};

DOMElement.prototype.getHeight = function() {
	return this.height;
};

DOMElement.prototype.setHeight = function(v_iHeight) {
	this.height = (v_iHeight < 22 ? 22 : v_iHeight);
	this.element.style.height = (typeof(this.height) == "string" && this.height.indexOf("px") == -1 ? this.height + "px" : this.height);
};
