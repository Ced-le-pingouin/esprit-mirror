# Makefile
#
# AwarenessClient
#
# Unité de Technologie de l'Education (UTE)
#

JAVAC="`which javac`"
CC=$(JAVAC) -target 1.1 -source 1.2
TARGET_ESPRIT=../../../src/admin/awareness/client
AWARENESS_SERVER_SOURCE_DIR=../serveur

all: clean
	cp $(AWARENESS_SERVER_SOURCE_DIR)/AwarenessURLDecoder.class .
	cp $(AWARENESS_SERVER_SOURCE_DIR)/ConnectionListener.class .
	cp $(AWARENESS_SERVER_SOURCE_DIR)/AwarenessProtocol.class .
	cp $(AWARENESS_SERVER_SOURCE_DIR)/AwarenessMessage.class .
	cp $(AWARENESS_SERVER_SOURCE_DIR)/AwarenessMessagesThread.class .
	$(CC) Resource.java \
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
