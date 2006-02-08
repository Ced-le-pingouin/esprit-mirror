<?php

function aFond ($v_sFond)
{
	if (strlen ($v_sFond))
		return "background=\"{$v_sFond}\" ";
}

function aAlignerVerticalement ($v_sAlignerVerticalement)
{
	if (strlen ($v_sAlignerVerticalement))
		return "valign=\"{$v_sAlignerVerticalement}\" ";
}

function aAlignerHorizontalement ($v_sAlignerHorizontalement)
{
	if (strlen ($v_sAlignerHorizontalement))
		return "align=\"{$v_sAlignerHorizontalement}\" ";
}

function aBordsTable ($v_sBordsTable)
{
	if (strlen ($v_sBordsTable))
		return "border=\"{$v_sBordsTable}\" ";
}

function aEspaceEntreCellules ($v_sEspaceEntreCellules)
{
	if (strlen ($v_sEspaceEntreCellules))
		return "cellspacing=\"{$v_sEspaceEntreCellules}\" ";
}

function aEspaceDansCellules ($v_sEspaceDansCellules)
{
	if (strlen ($v_sEspaceDansCellules))
		return "cellpadding=\"{$v_sEspaceDansCellules}\" ";
}

function aEtendreColonne ($v_iEtendreColonne)
{
	if (strlen ($v_iEtendreColonne))
		return "colspan=\"{$v_iEtendreColonne}\" ";
}

function aEtendreLigne ($v_iEtendreLigne)
{
	if (strlen ($v_iEtendreLigne))
		return "rowspan=\"{$v_iEtendreLigne}\" ";
}

function aHauteur ($v_sHauteur)

{
	if (strlen ($v_sHauteur))
		return "height=\"{$v_sHauteur}\" ";
}

function aLargeur ($v_sLargeur)
{
	if (strlen ($v_sLargeur))
		return "width=\"{$v_sLargeur}\" ";
}

function aRetourLigne ($v_bRetourLigne)
{
	if ($v_bRetourLigne === TRUE)
		return "nowrap ";
}

?>
