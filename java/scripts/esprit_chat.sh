#!/bin/sh
#

# === PARTIE CONFIGURABLE ===

# Chemin o� est install� le service
INSTALLDIR=/home/esprit/java/deltachatd

# Chemin d'acc�s � java
JAVA_JVM=`which java`

# Nom de l'utilisateur qui lancera le service (par exemple : apache ou www-data)
USER=esprit

# === FIN ===

function usage
{
        echo "Usage $0 {start|stop|restart} [port]"
}

# Num�ro du port
if [ -z $2 ] ; then
        PORT=2555
else
        PORT=$2
fi

SSD=`which start-stop-daemon`
APPLICATION=DeltaChat
# Nom du fichier de lock
PIDFILE=/var/run/esprit/${PORT}.pid

case "$1" in
	start)
		if [ -z $PORT ] ; then
			usage
		else
			$SSD --start --background --make-pidfile --chuid $USER --chdir $INSTALLDIR --pidfile $PIDFILE --exec $JAVA_JVM $APPLICATION $PORT
		fi
		;;
	stop)
		echo "Arr�ter le serveur $APPLICATION du port $PORT"
		
		if [ -f $PIDFILE ] ; then
			PID=`cat $PIDFILE`
			kill -TERM $PID
			rm $PIDFILE
		fi
		echo
		;;
	restart)
		echo "Red�marrage du serveur $APPLICATION du port $PORT"
		
		if [ -f $PIDFILE ] ; then
			PID=`cat $PIDFILE`
			kill -TERM $PID
			rm $PIDFILE
		fi
		
		$SSD --start --background --make-pidfile --chuid $USER --chdir $INSTALLDIR --pidfile $PIDFILE --exec $JAVA_JVM $APPLICATION $PORT
		;;
	*)
		usage
esac
