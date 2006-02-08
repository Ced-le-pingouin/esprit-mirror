<?php

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
