# Makefile
#
# AwarenessClient
#
# Unité de Technologie de l'Education (UTE)
#

AWARENESS_SERVEUR_DIR=../serveur
JAVAC="`which javac`"
CC=$(JAVAC) -target 1.1 -source 1.2 -classpath $(AWARENESS_SERVEUR_DIR)
TARGET_ESPRIT=../../../src/admin/awareness/client

all: clean
	$(CC) Resource.java \
		Tooltip.java \
		ConnectedItem.java \
		ListConnected.java \
		AwarenessListConnected.java \
		AwarenessClient.java \
		AwarenessChatClient.java \
		AwarenessAppletClient.java \
		AwarenessSpyClient.java \
		AwarenessSpyEsprit.java

clean:
	rm -rf *.class

install:
	rm -f $(TARGET_ESPRIT)/*.class
	cp *.class $(TARGET_ESPRIT)
	cp AwarenessClient_Fra.properties $(TARGET_ESPRIT)
	if [ ! -d $(TARGET_ESPRIT)/images ]; then \
		mkdir $(TARGET_ESPRIT)/images ; \
	fi
	cp images/boy.gif \
		images/girl.gif \
		images/interdit.gif \
		$(TARGET_ESPRIT)/images
	chmod -R ugo+r $(TARGET_ESPRIT)
