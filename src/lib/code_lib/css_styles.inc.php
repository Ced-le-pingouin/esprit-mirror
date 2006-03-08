<?php

// This file is part of Esprit, a web Learning Management System, developped
// by the Unite de Technologie de l'Education, Universite de Mons, Belgium.
// 
// Esprit is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2, 
// as published by the Free Software Foundation.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, you can get one from the web page
// http://www.gnu.org/licenses/gpl.html
// 
// Copyright (C) 2001-2006  Unite de Technologie de l'Education, 
//                          Universite de Mons-Hainaut, Belgium. 

function Fond ($v_sFond)
{
	if (strlen ($v_sFond))
		return "background: {$v_sFond}; ";
}

function CouleurFond ($v_sCouleurFond)
{
	if (strlen ($v_sCouleurFond))
		return "background-color: {$v_sCouleurFond}; ";
}

function ImageFond ($v_sImageFond)
{
	if (strlen ($v_sImageFond))
		return "background-image: url(\"{$v_sImageFond}\"); ";
}

function ImageRepeter ($v_sImageRepeter)
{
	if (strlen ($v_sImageRepeter))
		return "background-repeat: {$v_sImageRepeter}; ";
}

function ImageDefiler ($v_sImageDefiler)
{
	if (strlen ($v_sImageDefiler))
		return "background-attachment: {$v_sImageDefiler}; ";
}

function ImagePosition ($v_sImagePosition)
{
	if (strlen ($v_sImagePosition))
		return "background-position: {$v_sImagePosition}; ";
}

function CouleurTexte ($v_sTexteCouleur)
{
	if (strlen ($v_sTexteCouleur))
		return "color: {$v_sTexteCouleur}; ";
}

function FamillePolice ($v_sFamillePolice)
{
	if (strlen ($v_sFamillePolice))
		return "font-family: {$v_sFamillePolice}; ";
}

function TaillePolice ($v_sTaillePolice)
{
	if (strlen ($v_sTaillePolice))
		return "font-size: {$v_sTaillePolice}; ";
}

function StylePolice ($v_sStylePolice)
{
	if (strlen ($v_sStylePolice))
		return "font-style: {$v_sStylePolice}; ";
}

function LargeurPolice ($v_sLargeurPolice)
{
	if (strlen ($v_sLargeurPolice))
		return "font-weight: {$v_sLargeurPolice}; ";
}

function AlignementTexte ($v_sAlignement)
{
	if (strlen ($v_sAlignement))
		return "text-align: {$v_sAlignement}; ";
}

function sAlignerHorizontalement ($v_sAlignerHorizontalement)
{
	if (strlen ($v_sAlignerHorizontalement))
		return "text-align: {$v_sAlignerHorizontalement}; ";
}

function sAlignerVerticalement ($v_sAlignerVerticalement)
{
	if (strlen ($v_sAlignerVerticalement))
		return "vertical-align: {$v_sAlignerVerticalement}; ";
}

function EspaceCaracteres ($v_sEspaceCaracteres)
{
	if (strlen ($v_sEspaceCaracteres))
		return "letter-spacing: {$v_sEspaceCaracteres}; ";
}

function sRetourLigne ($v_bRetourLigne)
{
	if ($v_bRetourLigne === TRUE)
		return "white-space: nowrap; ";
}

function sHeight ($v_iHeight)
{
	if (strlen ($v_iHeight))
		return "height: {$v_iHeight}; ";
}

function sWidth ($v_iWidth)
{
	if (strlen ($v_iWidth))
		return "width: {$v_iWidth}; ";
}

function sTexteSouligne ($v_bTexteSouligne)
{
	if ($v_bTexteSouligne === TRUE)
		return "text-decoration: underline; ";
	else
		return "text-decoration: none; ";
}

?>
