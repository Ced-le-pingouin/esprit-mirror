<h2>{formation.retNom}</h2>
<p><strong>Nb total fichiers inutiles: {formation.nbTotalFichiersInutiles}</strong></p>
<p><strong>Taille totale fichiers inutiles: {formation.tailleTotaleFichiersInutilesFormatee}</strong></p>
<p><a href="?action=confirmerEffacement&id={formation.retId}">Voir les fichiers qui seront supprimés</a></p>
    <ul>
    <!--[modules+]-->
    <li>{module.retNom}
        <ul>
        <!--[rubriques+]-->
        <li>{rubrique.retNom}
            <ul>
            <!--[activites+]-->
            <li>{activite.retNom}
                (fichiers inutiles: {activite.nbFichiersInutiles} - 
                 taille: {activite.tailleFichiersInutilesFormatee})
                <ul>
                <!--[sousActivites+]-->
                <li>{sousActivite.retNom}</li>
                <!--[sousActivites-]-->
                </ul>
            </li>
            <!--[activites-]-->
            </ul>
        </li>
        <!--[rubriques-]-->
        </ul>
    </li>
    <!--[modules-]-->
    </ul>