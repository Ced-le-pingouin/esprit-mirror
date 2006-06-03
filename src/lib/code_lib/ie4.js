/**************************************************************************
** Définitions de fonctions qui utilisent des méthodes spécifiques à
** chaque navigateur. Elles DOIVENT donc avoir le même nom et le même
** prototype pour tous les fichiers .JS qui représentent un navigateur
** particulier (pour l'instant IE4.JS, NS4.JS, NS6.JS)
**************************************************************************/

var browserID = "Internet Explorer 4-5";

function getEventInput(e)
{
	return window.event.button;
}

function getEventType(e)
{
	return window.event.type;
}

function getTarget(e)
{
	return window.event.srcElement;
}

function getMouseX(e)
{
	return window.event.x;
}

function getMouseY(e)
{
	return window.event.y;
}

function isLeftButton(e)
{
	return (window.event.button == 1);
}

function isRightButton(e)
{
	return (window.event.button == 2);
}

function isMiddleButton(e)
{
	return (window.event.button == 4);
}

function setObjEventZoneWidth(objID, v_iWidth)
{
	document.all[objID].style.width = v_iWidth;
}

function setObjEventZoneHeight(objID, v_iHeight)
{
	document.all[objID].style.height = v_iHeight;
}

function setObjEventZoneSize(objID, v_iWidth, v_iHeight)
{
	setObjEventZoneWidth(objID, v_iWidth);
	setObjEventZoneHeight(objID, v_iHeight);
}

function getObjX(objID)
{
	return document.all[objID].offsetLeft;
}

function getObjY(objID)
{
	return document.all[objID].offsetTop;
}

function setObjX(objID, x)
{
	document.all[objID].style.pixelLeft = x;
}

function setObjY(objID, y)
{
	document.all[objID].style.pixelTop = y;
}

function setObjXY(objID, x, y)
{
	document.all[objID].style.pixelLeft = x;
	document.all[objID].style.pixelTop = y;
}

function getObjWidth(objID)
{
	return document.all[objID].offsetWidth;
}

function getObjHeight(objID)
{
	return document.all[objID].offsetHeight;
}

function getObjClip(objID)
{
	o = document.all[objID].currentStyle;
	
	t = (o.clipTop != "auto")?parseInt(o.clipTop):0;
	r = (o.clipRight != "auto")?parseInt(o.clipRight):getObjWidth(objID);
	b = (o.clipBottom != "auto")?parseInt(o.clipBottom):getObjHeight(objID);
	l = (o.clipLeft != "auto")?parseInt(o.clipLeft):0;
	
	r_aiClip = new Array(t, r, b, l);
	
	return r_aiClip;
}

function setObjClip(objID, aiClip)
{
	document.all[objID].style.clip = 
		"rect(" + aiClip[CSS_TOP] + " " + aiClip[CSS_RIGHT] + 
		" " + aiClip[CSS_BOTTOM] + " " + aiClip[CSS_LEFT] + ")";
}

function getObjClipWidth(objID)
{
	aiClip = getObjClip(objID);
	return (aiClip[CSS_RIGHT] - aiClip[CSS_LEFT]);
}

function getObjClipHeight(objID)
{
	aiClip = getObjClip(objID);
	return (aiClip[CSS_BOTTOM] - aiClip[CSS_TOP]);
}

function setObjClipWidth(objID, v_iWidth)
{
	aiClip = getObjClip(objID);
	aiClip[CSS_RIGHT] = aiClip[CSS_LEFT] + v_iWidth;
	setObjClip(objID, aiClip);
}

function setObjClipHeight(objID, v_iHeight)
{
	aiClip = getObjClip(objID);
	aiClip[CSS_BOTTOM] = aiClip[CSS_TOP] + v_iHeight;
	setObjClip(objID, aiClip);
}

function setObjClipSize(objID, v_iWidth, v_iHeight)
	{ setObjClipWidth(objID, v_iWidth); setObjClipHeight(objID, v_iHeight); }

/*
** Fonction .....: setObjVis
** Description ..: Afficher/cacher un élément.
**
*/

function setObjVis(objID, vis)
{
	o = document.all[objID].style;

	switch(vis)
	{
		case "switch":
		case -1:
			o.visibility = ((o.visibility == "visible") ? "hidden" : "visible");
			break;

		case "hide":
		case false:
			o.visibility = "hidden";
			break;

		case "show":
		case true:
			o.visibility = "visible";
	}
}

function setContextMenuState(bool)
{
	_contextMenuState = bool;
	
	if (!bool)
		document.oncontextmenu = falseFct;
}

function getContextMenuState()
{
	return _contextMenuState;
}