###
# Auteur: C�dric FLOQUET <cedric.floquet@umh.ac.be>
# Derni�re mise � jour: 19/12/2005
#
# Unit� de Technologie de l'Education
# 18, Place du Parc
# 7000 MONS
#

REPERT_A_SAUVER=".."

FICHIER_EXCLUSIONS="fichiers_exclus.txt"

FICHIER_ARCHIVE="esprit"
EXT_ARCHIVE="tar.gz"

#########################
# R�pertoires � exclure
#########################
# �viter les r�pertoires de contenu des formations
find .. -mindepth 2 -maxdepth 2 -path "$REPERT_A_SAUVER/src/formation/f*" -a -type d -printf './%P\n' > $FICHIER_EXCLUSIONS

# �viter les th�mes non-officiels d'Esprit
find .. -mindepth 2 -maxdepth 2 -path "$REPERT_A_SAUVER/src/themes/*" -a -type d -a ! \( -name 'commun' -o -name 'esprit' \) -printf './%P\n' >> $FICHIER_EXCLUSIONS

# �viter les fichiers SQL non-officiels d'Esprit
find .. -mindepth 3 -maxdepth 3 -path "$REPERT_A_SAUVER/install/sql/*" -a -type d -a ! \( -name 'commun' -o -name 'esprit' \) -printf './%P\n' >> $FICHIER_EXCLUSIONS

# �viter le r�pertoire tmp, qui contient les mots de passe d�crypt�s
echo './src/tmp/*' >> $FICHIER_EXCLUSIONS


#########################
# Fichiers � exclure
#########################
# �viter les config.inc* qui sont propres � l'installation courante (mots de passe DB !), sauf le .dist qui doit �tre fourni
find .. -path "$REPERT_A_SAUVER/src/include/config.inc*" -a ! -name 'config.inc.dist' -printf './%P\n' >> $FICHIER_EXCLUSIONS

# �viter les fichiers temporaires d'inscription (dans install/inscriptions)
find .. -path "$REPERT_A_SAUVER/install/inscriptions/*" -a \( -name '*.sql' -o -name '*.csv' \) -printf './%P\n' >> $FICHIER_EXCLUSIONS

# �viter le fichier tar lui-m�me, puisqu'il sera construit en m�me temps
echo "./install/$FICHIER_ARCHIVE.$EXT_ARCHIVE" >> $FICHIER_EXCLUSIONS

# mais aussi le fichier temporaire de ce script, qui contient les chemins � exclure
echo "./install/$FICHIER_EXCLUSIONS" >> $FICHIER_EXCLUSIONS

# �viter tous les r�pertoires Subversion
echo '*/.svn' >> $FICHIER_EXCLUSIONS

# perso : �viter tous les fichiers locaux de Cedric
echo '*-CEDRIC' >> $FICHIER_EXCLUSIONS

# - enlever le unzip int�gr� ?


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
