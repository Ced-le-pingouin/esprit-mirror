<!--
  -- Ne pas oublier de vérifier le lien du script javascript:
  -- <br><script type="text/javascript" language="javascript" src="/esprit/login-formations.php"></script><br>
  -->

[BLOCK_INFOS_PLATEFORME+]
<table border='0' cellspacing='0' cellpadding='5' width='100%'>
  <tr>
    <td><p>Vous &ecirc;tes d&eacute;j&agrave; inscrit&nbsp;? Introduisez votre
        pseudo et mot de passe dans la zone situ&eacute;e &agrave; gauche de
        cet &eacute;cran (Si cette zone n'appara&icirc;t pas compl&egrave;tement,
        appuyez alors sur la touche F11 pour passer en mode plein &eacute;cran).</p>
      [BLOCK_LISTE_FORMATIONS+]
      <p>Vous n'&ecirc;tes pas inscrit&nbsp;? Vous pouvez n&eacute;anmoins vous
        faire une id&eacute;e des possibilit&eacute;s d'Esprit en s&eacute;lectionnant
        une des formations propos&eacute;es ci-dessous  &agrave; titre d'exemple.
        Vous aurez la possibilit&eacute;, en parcourant l'une ou l'autre de ces
        formations, de d&eacute;couvrir la mani&egrave;re dont est structur&eacute;e
        un cours sur Esprit.</p></td>
  </tr>
  <tr>
    <td><ul>
        [BLOCK_FORMATION+]
        <li>{formation->url}</li>
        [BLOCK_FORMATION-]
      </ul>
      [BLOCK_LISTE_FORMATIONS-]</td>
  </tr>
</table>
[BLOCK_INFOS_PLATEFORME-] 