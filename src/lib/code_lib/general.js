var CSS_TOP = 0;
var CSS_RIGHT = 1;
var CSS_BOTTOM = 2;
var CSS_LEFT = 3;

var sCheminBiblio = "/code_lib/new/";
var ns4 = false, ie4 = false, ns6 = false;

var _contextMenuState = true;

var drag_canDrag = new Array();
var drag_diffX = 0, drag_diffY = 0;
var drag_obj = null;

var evt_callBack = new Array();

/**************************************************************************
** D�finitions de fonctions g�n�riques
**************************************************************************/

function falseFct() { return false; }

/**************************************************************************
** D�tection du navigateur et initialisations en cons�quence
**************************************************************************/

if (!document.all && document.getElementById)
{
	ns6 = true;
	document.write('<script language="javascript" src="' + sCheminBiblio + 'ns6.js"></script>');
	document.addEventListener("mouseover",evt_mouseOver,true);
	document.addEventListener("mouseout",evt_mouseOut,true);
}
else if (document.all)
{
	ie4 = true;
	document.ondragstart = document.onselectstart = falseFct;
	document.write('<script language="javascript" src="' + sCheminBiblio + 'ie4.js"></script>');
	
	document.onmousedown = evt_mouseDown;
	document.onmousemove = evt_mouseMove;
	document.onmouseup = evt_mouseUp;
	document.onmouseover = evt_mouseOver;
	document.onmouseout = evt_mouseOut;
}
else if (document.layers)
{
	ns4 = true;
	document.captureEvents(Event.MOUSEDOWN | Event.MOUSEMOVE | Event.MOUSEUP | Event.MOUSEOVER | Event.MOUSEOUT);
	document.write('<script language="javascript" src="' + sCheminBiblio + 'ns4.js"></script>');
	
	document.onmousedown = evt_mouseDown;
	document.onmousemove = evt_mouseMove;
	document.onmouseup = evt_mouseUp;
	document.onmouseover = evt_mouseOver;
	document.onmouseout = evt_mouseOut;
}


/**************************************************************************
** D�finitions des fonctions d'�v�nements de souris
**************************************************************************/
// *** Ev�nement "bouton de souris appuy�" ***
function evt_mouseDown(e)
{
	var ret = true;
	
	if (isLeftButton(e))
	{
		drag_obj = drag_canDrag[getTarget(e).name];
		if (drag_obj)
		{
			drag_diffX = getMouseX(e) - getObjX(drag_obj);
			drag_diffY = getMouseY(e) - getObjY(drag_obj);
			ret = false;
		}
	}
	else if (isRightButton(e) && !getContextMenuState())
		ret = false;

	if (evt_callBack[getEventType(e)])
		evt_callBack[getEventType(e)](e);

	return ret;	
}

// *** Ev�nement "d�placement de la souris" ***
function evt_mouseMove(e)
{
	if (evt_callBack[getEventType(e)])
		evt_callBack[getEventType(e)](e);

	if (drag_obj)
		setObjXY(drag_obj, getMouseX(e) - drag_diffX, getMouseY(e) - drag_diffY);
}

// *** Ev�nement "bouton de souris rel�ch�" ***
function evt_mouseUp(e)
{
	var ret = true;
	
	if (evt_callBack[getEventType(e)])
		evt_callBack[getEventType(e)](e);
	
	if (drag_obj)
	{
		drag_obj = null;
		drag_diffX = 0; drag_diffY = 0;
		ret = false;
	}
	else if (isRightButton(e) && !getContextMenuState())
		ret = false;

	return ret;
}

// *** Ev�nement "le souris entre dans un objet" ***
function evt_mouseOver(e)
{
	if (evt_callBack[getEventType(e)])
		evt_callBack[getEventType(e)](e);
}

// *** Ev�nement "la souris sort d'un objet" ***
function evt_mouseOut(e)
{
	if (evt_callBack[getEventType(e)])
		evt_callBack[getEventType(e)](e);
}