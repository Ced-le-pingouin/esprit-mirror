# Makefile
# 
# AwarenessServer
#
# Unité de Technologie de l'Education (UTE)
#

TARGET_ESPRIT=..

CC="`which javac`" -g -target 1.1 -source 1.2
TAR=tar czvf

all: clean
	$(CC) AwarenessURLDecoder.java \
		ConnectionListener.java \
		AwarenessProtocol.java \
		AwarenessMessage.java \
		AwarenessAccount.java \
		AwarenessMessagesThread.java \
		AwarenessServerSocket.java \
		AwarenessServer.java

install:
	rm -f $(TARGET_ESPRIT)/*.class
	cp -f *.class AwarenessServer $(TARGET_ESPRIT)

clean:
	rm -f *.class
