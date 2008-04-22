[SET_PERSONNE_INFOS+]
<table border="0" cellspacing="0" cellpadding="1" width="100%">
<tr>
<td rowspan="2" valign="top" id="{personne_infos.id}">{personne_infos.sexe}</td>
<td width="99%"><span style="font-size: 8pt; font-weight: bold;">&nbsp;{personne_infos.nom_complet}&nbsp;</span></td>
<td nowrap="nowrap">&nbsp;{personne_infos.email}&nbsp;</td>
</tr>
<tr>
<td colspan="2"><div style="font-size: 8pt; border: rgb(174,165,138) none 1px; border-top-style: dashed; width: 100%;">&nbsp;{personne_infos.pseudo}&nbsp;</div><img src="commun://espacer.gif" width="1" height="8" border="0"></td>
</tr>
</table>
[SET_PERSONNE_INFOS-]
[SET_ICONE_FAVORI+]&nbsp;<img src="theme://icones/etoile.gif" width="13" height="13" border="0">[SET_ICONE_FAVORI-]
[SET_URL+]<a href="javascript: void(0);" onclick="{a->href}" onfocus="blur()">{a->label}</a>[SET_URL-]

<!-- [[ Envoi courriel -->
[SET_ENVOI_COURRIEL_ICONE+]<img src="commun://icones/mail.gif" width="16" height="16" border="0">[SET_ENVOI_COURRIEL_ICONE-]
[SET_ENVOI_COURRIEL_ICONE_PASSIVE+]<img src="commun://icones/pas_mail.gif" width="16" height="16" border="0">[SET_ENVOI_COURRIEL_ICONE_PASSIVE-]
[SET_ENVOI_COURRIEL_MULTIPLE_ICONE+]<img src="commun://icones/24x24/courriel_envoye.gif" width="24" height="24" alt="Envoi courriel" border="0">[SET_ENVOI_COURRIEL_MULTIPLE_ICONE-]
[SET_ENVOI_COURRIEL_TEXTE+]&nbsp;Envoi courriel[SET_ENVOI_COURRIEL_TEXTE-]
[SET_MAIL_ACTIF+]<a href="javascript: void(0);" onclick="choix_courriel('{personne->email}'); return false;" title="Envoyer un mail" onfocus="blur()"><img src="commun://icones/mail.gif" width="16" height="16" border="0"></a>[SET_MAIL_ACTIF-]
[SET_MAIL_PASSIF+]<img src="commun://icones/pas_mail.gif" width="16" height="16" border="0">[SET_MAIL_PASSIF-]
[SET_ENVOI_COURRIEL+]<a href="javascript: void(0);" onclick="choix_courriel('{envoi_courriel.params}'); return false;" onfocus="blur()" title="Envoyer un courriel">{envoi_courriel.icone}{envoi_courriel.texte}</a>[SET_ENVOI_COURRIEL-]
[SET_ENVOI_COURRIEL_PASSIF+]{envoi_courriel.icone}{envoi_courriel.texte}[SET_ENVOI_COURRIEL_PASSIF-]
<!-- Envoi courriel]] -->

[SET_INPUT_RADIO+]<input type="radio" name="{input->name}" value="{input->value}" onfocus="blur()">[SET_INPUT_RADIO-]
[SET_EQUIPE+]<img src="commun://icones/16x16/equipe.gif" width="16" height="16" border="0">[SET_EQUIPE-]
[SET_SEXE_MASCULIN+]<img src="commun://icones/boy.gif" width="15" height="26" border="0">[SET_SEXE_MASCULIN-]
[SET_SEXE_FEMININ+]<img src="commun://icones/girl.gif" width="15" height="26" border="0">[SET_SEXE_FEMININ-]

