/**************************************************************************
** Définitions de fonctions qui utilisent des méthodes spécifiques à
** chaque navigateur. Elles DOIVENT donc avoir le même nom et le même
** prototype pour tous les fichiers .JS qui représentent un navigateur
** particulier (pour l'instant IE4.JS, NS4.JS, NS6.JS)
**************************************************************************/
var browserID = "Netscape 4";

function getEventType(e) { return e.type; }
function getTarget(e) { return e.target; }

function getMouseX(e) { return e.pageX; }
function getMouseY(e) { return e.pageY; }
function isLeftButton(e) { return (e.which == 1); }
function isRightButton(e) { return (e.which == 3); }
function isMiddleButton(e) { return false; }
function setObjEventZoneWidth(objID, v_iWidth) { setObjClipWidth(objID, v_iWidth); }
function setObjEventZoneHeight(objID, v_iHeight) { setObjClipHeight(objID, v_iHeight); }
function setObjEventZoneSize(objID, v_iWidth, v_iHeight)
{
	setObjEventZoneWidth(objID, v_iWidth);
	setObjEventZoneHeight(objID, v_iHeight);
}

function getObjX(objID) { return document.layers[objID].left; }
function getObjY(objID) { return document.layers[objID].top; }
function setObjX(objID, x) { document.layers[objID].left = x; }
function setObjY(objID, y) { document.layers[objID].top = y; }
function setObjXY(objID, x, y)
 { document.layers[objID].left = x; document.layers[objID].top = y; }
function getObjWidth(objID) { return document.layers[objID].document.width; }
function getObjHeight(objID) { return document.layers[objID].document.height; }

function getObjClip(objID)
{
	o = document.layers[objID].clip;
	r_aiClip = new Array(o.top, o.right, o.bottom, o.left);
	return r_aiClip;
}
function setObjClip(objID, aiClip)
{
	o = document.layers[objID].clip;
	o.top = aiClip[CSS_TOP];
	o.right = aiClip[CSS_RIGHT];
	o.bottom = aiClip[CSS_BOTTOM];
	o.left = aiClip[CSS_LEFT];
}
function getObjClipWidth(objID) { return document.layers[objID].clip.width; }
function getObjClipHeight(objID) { return document.layers[objID].clip.height; }
function setObjClipWidth(objID, v_iWidth)
	{ document.layers[objID].clip.width = v_iWidth; }
function setObjClipHeight(objID, v_iHeight)
	{ document.layers[objID].clip.height = v_iHeight; }
function setObjClipSize(objID, v_iWidth, v_iHeight)
	{ setObjClipWidth(objID, v_iWidth); setObjClipHeight(objID, v_iHeight); }

function setObjVis(objID, vis)
{
	o = document.layers[objID];

	switch(vis)
	{
	case "switch":
	case -1:
		o.visibility = ((o.visibility == "visible")?"hidden":"visible");
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
}
function getContextMenuState()
{
	return _contextMenuState;
}