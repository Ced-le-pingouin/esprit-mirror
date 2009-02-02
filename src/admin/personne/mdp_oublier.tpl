[BLOCK_ENTRER_INFORMATIONS+]
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<style type="text/css">
<!--
input { width: 100%; }
//-->
</style>
<script type="text/javascript" language="javascript">
<!--
function init() { top.document.getElementsByTagName("frame").item(2).setAttribute("src","mdp_oublier-menu.php?menu=valider"); }
function valider()
{
	// Vérifier que le nom est entré
	if (document.forms[0].elements["nomPers"].value.length < 1)
	{
		alert("Vous avez oublié d'entrer votre nom.");
		document.forms[0].elements["nomPers"].focus();
		return;
	}
	
	// Vérifier que le prénom est entré
	if (document.forms[0].elements["prenomPers"].value.length < 1)
	{
		alert("Vous avez oublié d'entrer votre prénom.");
		document.forms[0].elements["prenomPers"].focus();
		return;
	}
	
	// Vérifier l'adresse courriel
	if (document.forms[0].elements["emailPers"] &&
		document.forms[0].elements["emailPers"].value.length < 1)
	{
		alert("Vous avez oublié d'entrer votre adresse courriel.");
		document.forms[0].elements["emailPers"].focus();
		return;
	}
	
	document.forms[0].submit();
}
//-->
</script>
</head>
<body onload="init()">
<p style="font-weight: bold;">Vous avez oubli&eacute; votre pseudo et/ou votre mot de passe&nbsp;?</p>
<p>Entrez votre nom et votre pr&eacute;nom, puis cliquez sur "Valider".</p>
<p>Vous recevrez par la suite un courriel contenant votre pseudo ainsi que votre mot de passe.</p>
<form action="mdp_oublier.php" method="get" target="_self">
<table border="0" cellspacing="1" cellpadding="0" width="100%">
<tr><td><img src="commun://espacer.gif" width="20" height="1" alt="" border="0"></td><td style="text-align: right;"><div class="intitule">Nom&nbsp;:</div></td><td>&nbsp;&nbsp;</td><td style="width: 99%;"><input type="text" name="nomPers" size="40" value="{personne->nom}"></td></tr>
<tr><td>&nbsp;</td><td style="text-align: right;"><div class="intitule">Pr&eacute;nom&nbsp;:</div></td><td>&nbsp;&nbsp;</td><td style="width: 99%;"><input type="text" name="prenomPers" size="40" value="{personne->prenom}"></td></tr>
[BLOCK_ADRESSE_COURRIEL+]
<tr><td>&nbsp;</td><td style="text-align: right;"><div class="intitule">Adresse&nbsp;courriel&nbsp;:</div></td><td>&nbsp;&nbsp;</td><td style="width: 99%;"><input type="text" name="emailPers" size="40"></td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;&nbsp;</td><td style="width: 99%;"><span style="color: rgb(136,37,37); font-size: 7pt; ">*Veuillez &eacute;galement introduire votre adresse courriel.</span></td></tr>
[BLOCK_ADRESSE_COURRIEL-]
<tr><td colspan="3"><img src="commun://espacer.gif" width="150" height="1" alt="" border="0"></td><td style="width: 99%;">&nbsp;</td></tr>
</table>
</form>
<p><strong>Attention</strong></p>

<p>Si votre messagerie est dotée d'un filtre destiné à contrer les courriels indésirables, il se peut que le courriel qui vous est transmis atterrisse 
dans un dossier intitulé  "Spam" (Gmail, Yahoo) , "Courrier indésirable" (Hotmail) ou directement versé dans la "corbeille" (Thunderbird). Il vous suffit dans 
ce cas de vous rendre dans ce dossier pour lire le message.</p>

<p>Si le message ne se trouve dans aucun de vos dossiers, ajoutez l'adresse courriel suivante à votre liste des expéditeurs autorisés: <em>{esprit->email}</em>
et... recommencez la procédure (nom et prénom puis valider).</p>

</body>
</html>
[BLOCK_ENTRER_INFORMATIONS-]

[BLOCK_COURRIEL_ENVOYER+]
[VAR_OK+]<tr><td><img src="commun://icones/64x64/courriel_envoye.gif" width="64" height="64" alt="0" border="0"></td><td><p class="bold_center">Votre pseudo ainsi que votre mot de passe vous ont été envoyés à l'adresse suivante&nbsp;: &laquo;&nbsp;<span style="color: rgb(64,64,153);">{personne->email}</span>&nbsp;&raquo;.</p></td></tr>[VAR_OK-]
[VAR_NOM_PRENOM_INCORRECT+]
<tr>
<td valign="top"><img src="commun://icones/64x64/courriel_pas_envoye.gif" width="64" height="64" alt="0" border="0"></td>
<td>
<p style="font-weight: bold;">Le nom/prénom que vous avez indiqué n'a pas été reconnu par la plateforme.</p>
<p>Deux causes possibles&nbsp;:
<ul><li>vous n'êtes pas inscrit à une formation hébergée par {plateforme.nom}, dans ce cas contactez votre enseignant.<br><br></li><li>vous avez mal introduit votre nom ou prénom. Essayez à nouveau.</li></ul></p>
</td>
</tr>
[VAR_NOM_PRENOM_INCORRECT-]
[VAR_AUCUNE_ADRESSE+]
<tr>
<td><img src="commun://icones/64x64/courriel_pas_envoye.gif" width="64" height="64" alt="0" border="0"></td>
<td>
<p class="bold_center">Aucune adresse courrielle n'est associée à votre nom. Il nous est donc impossible de vous envoyer votre pseudo et mot de passe. Veuillez contacter votre enseignant.</p>
</td>
</tr>
[VAR_AUCUNE_ADRESSE-]
[VAR_ERREUR_ENVOI_COURRIEL+]
<tr>
<td><img src="commun://icones/64x64/courriel_pas_envoye.gif" width="64" height="64" alt="0" border="0"></td>
<td>
<p class="bold_center">Désolé notre système d'envoi de courriel a échoué. Essayez à nouveau.</p>
</td>
</tr>
[VAR_ERREUR_ENVOI_COURRIEL-]
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link type="text/css" rel="stylesheet" href="css://commun/globals.css">
<script type="text/javascript" language="javascript">
<!--
function init() { top.document.getElementsByTagName("frame").item(2).setAttribute("src","mdp_oublier-menu.php"); }
//-->
</script>
</head>
<body onload="init()">
<table border="0" cellspacing="0" cellpadding="3" width="100%">
<tr><td colspan="2"><img src="commun://espacer.gif" width="1" height="15" alt="" border="0"></td></tr>
{erreur}
</table>
</body>
</html>
[BLOCK_COURRIEL_ENVOYER-]
