# Makefile
# 
# AwarenessServer
#
# Unité de Technologie de l'Education (UTE)
#

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

clean:
	rm -f *.class
