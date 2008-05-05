function getRealLeft(imgElem) {
	xPos = eval(imgElem).offsetLeft;
	tempEl = eval(imgElem).offsetParent;
	while (tempEl != null) {
		xPos += tempEl.offsetLeft;
		tempEl = tempEl.offsetParent;
	}
	
	if (navigator.userAgent.indexOf("Mac") != -1 && 
        typeof(document.body.leftMargin != "undefined"))
        xPos += document.body.leftMargin;
	
	return xPos;
}

function getRealTop(imgElem) {
	yPos = eval(imgElem).offsetTop;
	tempEl = eval(imgElem).offsetParent;
	while (tempEl != null) {
		yPos += tempEl.offsetTop;
		tempEl = tempEl.offsetParent;
	}
	
	if (navigator.userAgent.indexOf("Mac") != -1 && 
        typeof(document.body.topMargin != "undefined"))
        yPos += document.body.topMargin;
		
	return yPos;
}

function masquerIndice(v_sIndice)
{
	document.getElementById(v_sIndice).style.visibility = "hidden";
}

function afficherIndice(v_sIndice)
{
	document.getElementById(v_sIndice).style.visibility = "visible";
}

function deplacerIndice(v_sIndice,v_iX,v_iY,v_iOffsetX,v_iOffsetY)
{
	if (typeof(v_iOffsetX) != "undefined")
		v_iX += v_iOffsetX;
	
	if (typeof(v_iOffsetY) != "undefined")
		v_iY += v_iOffsetY;
	
	document.getElementById(v_sIndice).style.visibility = "visible";
	document.getElementById(v_sIndice).style.left = v_iX;
	document.getElementById(v_sIndice).style.top = v_iY;
}

