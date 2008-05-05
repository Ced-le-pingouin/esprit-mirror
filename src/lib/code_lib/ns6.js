/*
** Fichier ................: ns6.js
** Description ............: Définitions de fonctions qui utilisent des méthodes spécifiques à
**                           chaque navigateur. Elles DOIVENT donc avoir le même nom et le même
**                           prototype pour tous les fichiers .JS qui représentent un navigateur
**                           particulier (pour l'instant IE4.JS, NS4.JS, NS6.JS).
**
** Date de création .......: 01-06-2001
** Dernière modification ..: 26-02-2002 (Fili//0: Porco, ute@umh.ac.be)
** Auteur .................: Cédric Floquet
** Email ..................: ute@umh.ac.be
**
** Copyright (c) 2001-2002 UTE. All rights reserved.
**
*/

var browserID = "Netscape 6";

function getEventInput(e)
{
	return e.button;
}

function getEventType(e)
{
	return e.type;
}

function getTarget(e) 
{
	return e.target.parentNode;
}

function getMouseX(e)
{
	return e.pageX;
}

function getMouseY(e)
{
	return e.pageY;
}

function isLeftButton(e)
{
	return (e.button == 1);
}

function isRightButton(e)
{
	return (e.button == 3);
}

function isMiddleButton(e)
{
	return false;
}

function getObjX(objID)
{
	return document.getElementById(objID).offsetLeft;
}

function getObjY(objID)
{
	return document.getElementById(objID).offsetTop;
}

function setObjX(objID, x)
{
	document.getElementById(objID).style.left = x;
}

function setObjY(objID, y)
{
	document.getElementById(objID).style.top = y;
}

function setObjXY(objID, x, y)
{
	document.getElementById(objID).style.left = x;
 	document.getElementById(objID).style.top = y;
}

function getObjWidth(objID)
{
	return document.getElementById(objID).offsetWidth;
}

function setObjClipWidth(objID, v_iWidth)
{
	aiClip = getObjClip(objID);
	aiClip[CSS_RIGHT] = aiClip[CSS_LEFT] + v_iWidth;
	setObjClip(objID, aiClip);
}

function setObjClip(objID, aiClip)
{
	document.getElementById(objID).style.clip = 
		"rect(" + aiClip[CSS_TOP] + " " + aiClip[CSS_RIGHT] + 
		" " + aiClip[CSS_BOTTOM] + " " + aiClip[CSS_LEFT] + ")";
}

function getObjHeight(objID)
{
	return document.getElementById(objID).offsetHeight;
}

function getObjClipHeight(objID)
{
	aiClip = getObjClip(objID);
	return (aiClip[CSS_BOTTOM] - aiClip[CSS_TOP]);
}

function getObjHeight(objID)
{
	return document.getElementById(objID).offsetHeight;
}

function getObjClipWidth(objID)
{
	aiClip = getObjClip(objID);
	
	return (aiClip[CSS_RIGHT] - aiClip[CSS_LEFT]);
}

function getObjClip(objID)
{
	var clip = document.getElementById(objID).style.clip;

	if (clip)
	{
		var clipVals = clip.split("rect(")[1].split(" ");

		for (var i=0; i<clipVals.length; i++)
			clipVals[i] = parseInt(clipVals[i]);

		r_aiClip = new Array(0,clipVals[1],clipVals[2],0);
	}
	else r_aiClip = new Array(0,0,0,0);
		
	return r_aiClip;
}

function setObjVis(objID,vis)
{
	o = document.getElementById(objID).style;
	
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
