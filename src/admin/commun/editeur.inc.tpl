<!--
  -- Editeur
  --
  -- Dernière modification: 05/04/2005
  -- Auteur: Filippo PORCO <filippo.porco@umh.ac.be>
  --
  -- Unité de Technologie de l'Education
  -- 18, Place du Parc
  -- 7000 MONS
  --
  -- MODE D'EMPLOI
  --
  -- <script type="text/javascript" language="javascript" src="editeur://editeur.js"></script>
  -- ...
  -- function insererBalise(v_sBaliseDepart,v_sBaliseFin) { insertAtCursor(document.forms[0].elements["{editeur->nom}"],v_sBaliseDepart,v_sBaliseFin); }
  -->
[SET_EDITEUR+]
<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td>
<a href="javascript: void(0);" onclick="fonth1()" onfocus="blur()" title="Titre 1"><img src="icones://fonth1.gif" width="22" height="22" border="0" hspace="0"></a>
<a href="javascript: void(0);" onclick="fonth2()" onfocus="blur()" title="Titre 2"><img src="icones://fonth2.gif" width="22" height="22" border="0" hspace="0"></a>
<a href="javascript: void(0);" onclick="fonth3()" onfocus="blur()" title="Titre 3"><img src="icones://fonth3.gif" width="22" height="22" border="0" hspace="0"></a>
<a href="javascript: void(0);" onclick="fonth4()" onfocus="blur()" title="Titre 4"><img src="icones://fonth4.gif" width="22" height="22" border="0" hspace="0"></a>
<a href="javascript: void(0);" onclick="fonth5()" onfocus="blur()" title="Titre 5"><img src="icones://fonth5.gif" width="22" height="22" border="0" hspace="0"></a>
<a href="javascript: void(0);" onclick="fonth6()" onfocus="blur()" title="Titre 6"><img src="icones://fonth6.gif" width="22" height="22" border="0" hspace="0"></a>
</td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td>
<a href="javascript: void(0);" onclick="bold()" onfocus="blur()" title="Gras"><img src="icones://bold.gif" width="22" height="21" border="0" hspace="0"></a>
<a href="javascript: void(0);" onclick="italic()" onfocus="blur()" title="Italique"><img src="icones://italic.gif" width="22" height="21" border="0" hspace="0"></a>
<a href="javascript: void(0);" onclick="underline()" onfocus="blur()" title="Soulign&eacute;"><img src="icones://underline.gif" width="22" height="21" border="0" hspace="0"></a>
</td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td>
<a href="javascript: void(0);" onclick="left_alignment()" onfocus="blur()" title="Align&eacute; &agrave; gauche"><img src="icones://left_alignment.gif" width="22" height="21" border="0" hspace="0"></a>
<a href="javascript: void(0);" onclick="center_alignment()" onfocus="blur()" title="Centr&eacute;"><img src="icones://center_alignment.gif" width="22" height="21" border="0" hspace="0"></a>
<a href="javascript: void(0);" onclick="right_alignment()" onfocus="blur()" title="Align&eacute; &agrave; droite"><img src="icones://right_alignment.gif" width="22" height="21" border="0" hspace="0"></a>
<a href="javascript: void(0);" onclick="justify_alignment()" onfocus="blur()" title="Justifi&eacute;"><img src="icones://justify_alignment.gif" width="22" height="21" border="0" hspace="0"></a>
</td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td>
<a href="javascript: void(0);" onclick="list_ul()" onfocus="blur()" title="Liste à puce"><img src="icones://list_ul.gif" width="22" height="22" border="0" hspace="0"></a>
<a href="javascript: void(0);" onclick="list()" onfocus="blur()" title="Liste numérotée"><img src="icones://list.gif" width="22" height="22" border="0" hspace="0"></a>
<a href="javascript: void(0);" onclick="list_ol()" onfocus="blur()" title="Liste"><img src="icones://list_ol.gif" width="22" height="22" border="0" hspace="0"></a>
</td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td>
<a href="javascript: void(0);" onclick="increase_indent()" onfocus="blur()" title="Augment&eacute; le retrait"><img src="icones://increase_indent.gif" width="22" height="21" border="0" hspace="0"></a>
</td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td>
<a href="javascript: void(0);" onclick="hrule()" onfocus="blur()" title="Ligne horizontale"><img src="icones://hrule.gif" width="22" height="22" border="0" hspace="0"></a>
</td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td>
<a href="javascript: void(0);" onclick="email()" onfocus="blur()" title="Email"><img src="icones://email.gif" width="22" height="22" border="0" hspace="0"></a>
</td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td>
<a href="javascript: void(0);" onclick="site()" onfocus="blur()" title="Lien vers un site Internet"><img src="icones://site.gif" width="22" height="22" border="0" hspace="0"></a>
</td>
[BLOCK_TABLEAU_DE_BORD+]<td>&nbsp;&nbsp;&nbsp;</td>
<td><a href="javascript: tableau_de_bord('/i')" onfocus="blur()" title="Lien vers le tableau de bord individuel"><img src="commun://icones/24x24/tableaubord.gif"></a></td>
<td><a href="javascript: tableau_de_bord('/e')" onfocus="blur()" title="Lien vers le tableau de bord par équipe"><img src="commun://icones/24x24/tableaubord.gif"></a></td>[BLOCK_TABLEAU_DE_BORD-]
</tr>
</table>
<table id="id_table_editeur_texte" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><td><textarea id="id_{editeur->nom}" name="{editeur->nom}" cols="80" rows="26" class="editeur_texte"></textarea></td></tr>
</table>
[SET_EDITEUR-]
