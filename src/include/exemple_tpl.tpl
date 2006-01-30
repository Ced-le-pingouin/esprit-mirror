<html>
<head>
<title>Galanet</title>
<style type="text/css">
<!--
BODY, DIV, TR, TD, H1, H2, H3, H4, H5, H6, P, OL, LI, UL
{
	font-family: tahoma,Verdana,Arial,Times;
	font-size: 10pt; 
	color: rgb(0,50,100); 
}

BODY
{
	background: rgb(251,236,202); 
}

A:link
{
	color: rgb(2,63,127); 
	font-family: Tahoma,Verdana,Arial,Times; 
	font-size: 10pt;
	text-decoration:none;
	font-weight: bold;	
}

A:visited
{
	color: rgb(2,63,127); 
	font-family: Tahoma,Verdana,Arial,Times; 
	font-size: 10pt;
	text-decoration:none;
	font-weight: bold;	
}

A:hover
{
	color: rgb(22,83,147);
	text-decoration: underline;
	font-weight: bold;	
}
//-->
</style>
<script type="text/javascript" language="javascript">
<!--
function PopupCenter(url,largeur,hauteur,options,name)
{
  var top=(screen.height-hauteur)/2;
  var left=(screen.width-largeur)/2;
  fenetre=window.open(url,name,"top="+top+",left="+left+",width="+largeur+",height="+hauteur+","+options);
  fenetre.focus();
}
//-->
</script>
</head>
<body>

[SET_FORUM+]
<a href="javascript:PopupCenter('../forum',500,300,'menubar=no,scrollbars=yes,status=no,resizable=yes','forum');">Forum : [IFORUM] ->[TJRSMEME]</a><br>
[SET_FORUM-]

[SET_PROFIL+]
<a href="javascript:PopupCenter('../profil',500,300,'menubar=no,scrollbars=yes,status=no,resizable=yes','profil');">Profil</a><br>
[SET_PROFIL-]

[AFFICHE_LISTE_CATEG+]
<form action="liste_ajout_structure_ressource.php" method="get" name="formulaire">
<table border="0" cellpadding="5" cellspacing="0">
<tr>
	<td>
		&nbsp;
	</td>
	<td class="Titre">
		&nbsp;
	</td>
	<td align="right">
		--&gt;
	</td>
	<td align="left">
		<select name="LgCib" onchange="this.form.submit();">[OPTIONSLGCIB]</select>
	</td>
</tr>
[CATEG_LISTE+]
<tr>
	<td>
		<b>[ORDRE_CATEG].</b>
	</td>
	<td>
		<a href="liste_ajout_structure_ressource.php?IdCategRess=[IDCATEGRESS]&LgSrc=[LGSRC]&LgCib=[LGCIB]">[NOM_CATEG]</a>
	</td>
	<td align="right">
		--&gt;
	</td>
	<td align="left">
		[LANGUE_CIBLE]
	</td>
</tr>
[CATEG_LISTE-]
</table>
</form>
[AFFICHE_LISTE_CATEG-]

[AFFICHE_STRUCTURE+]
<table border="0" cellpadding="5" cellspacing="0">
<tr>
	<td>
		<b>[ORDRE_CATEG].</b>
	</td>
	<td>
		<b>[NOM_CATEG]</b>&nbsp;&nbsp;&nbsp;--&gt;&nbsp;[LANGUE_CIBLE]
	</td>
</tr>
[STRUCTURE_LISTE+]
<tr>
	<td>
		&nbsp;
	</td>
	<td>
		[NOM_BRANCHE]
	</td>
</tr>
[STRUCTURE_LISTE-]
</table>
[AFFICHE_STRUCTURE-]


[BLOCK_TEST+]

[BLOCK_TEST-]

</body>
</html>
