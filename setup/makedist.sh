###
# Auteur: Cédric FLOQUET <cedric.floquet@umh.ac.be>
# Dernière mise à jour: 19/12/2005
#
# Unité de Technologie de l'Education
# 18, Place du Parc
# 7000 MONS
#

REPERT_A_SAUVER=".."

FICHIER_EXCLUSIONS="fichiers_exclus.txt"

FICHIER_ARCHIVE="esprit"
EXT_ARCHIVE="tar.gz"

#########################
# Répertoires à exclure
#########################
# éviter les répertoires de contenu des formations
find .. -mindepth 2 -maxdepth 2 -path "$REPERT_A_SAUVER/src/formation/f*" -a -type d -printf './%P\n' > $FICHIER_EXCLUSIONS

# éviter les thèmes non-officiels d'Esprit
find .. -mindepth 2 -maxdepth 2 -path "$REPERT_A_SAUVER/src/themes/*" -a -type d -a ! \( -name 'commun' -o -name 'esprit' \) -printf './%P\n' >> $FICHIER_EXCLUSIONS

# éviter les fichiers SQL non-officiels d'Esprit
find .. -mindepth 3 -maxdepth 3 -path "$REPERT_A_SAUVER/install/sql/*" -a -type d -a ! \( -name 'commun' -o -name 'esprit' \) -printf './%P\n' >> $FICHIER_EXCLUSIONS

# éviter le répertoire tmp, qui contient les mots de passe décryptés
echo './src/tmp/*' >> $FICHIER_EXCLUSIONS


#########################
# Fichiers à exclure
#########################
# éviter les config.inc* qui sont propres à l'installation courante (mots de passe DB !), sauf le .dist qui doit être fourni
find .. -path "$REPERT_A_SAUVER/src/include/config.inc*" -a ! -name 'config.inc.dist' -printf './%P\n' >> $FICHIER_EXCLUSIONS

# éviter les fichiers temporaires d'inscription (dans install/inscriptions)
find .. -path "$REPERT_A_SAUVER/install/inscriptions/*" -a \( -name '*.sql' -o -name '*.csv' \) -printf './%P\n' >> $FICHIER_EXCLUSIONS

# éviter le fichier tar lui-même, puisqu'il sera construit en même temps
echo "./install/$FICHIER_ARCHIVE.$EXT_ARCHIVE" >> $FICHIER_EXCLUSIONS

# mais aussi le fichier temporaire de ce script, qui contient les chemins à exclure
echo "./install/$FICHIER_EXCLUSIONS" >> $FICHIER_EXCLUSIONS

# éviter tous les répertoires Subversion
echo '*/.svn' >> $FICHIER_EXCLUSIONS

# perso : éviter tous les fichiers locaux de Cedric
echo '*-CEDRIC' >> $FICHIER_EXCLUSIONS

# - enlever le unzip intégré ?


# Archiver la plate-forme en exlcuant les fichiers qui ne doivent pas faire partie de la distribution
if tar -cpvz -X $FICHIER_EXCLUSIONS -f ./$FICHIER_ARCHIVE.$EXT_ARCHIVE -C $REPERT_A_SAUVER .; then
	echo "OK"
	rm -f $FICHIER_EXCLUSIONS
	exit 0
else
	echo "ERREUR"
	echo -e -n "\n***** !!!!! ATTENTION !!!!! IL Y A EU DES ERREURS LORS DE LA COMMANDE 'tar' *****\n\n"
	rm -f $FICHIER_EXCLUSIONS
	exit 1
fi
