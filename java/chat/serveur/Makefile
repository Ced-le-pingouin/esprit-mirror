# Makefile
#
# Auteur: PORCO Filippo
# Compilateur: Java
#

CC="`which javac`" -target 1.1 -source 1.2

all: Communs ChatLog DeltaUsers
	$(CC) TCPServer.java ChatServer.java DeltaChat.java

Communs:
	@echo -e "Compilation du serveur chat\n"
	$(CC) DateChat.java User.java

DeltaUsers:
	$(CC) Users.java Connected.java

ChatLog:
	$(CC) WriterLog.java ServerLog.java ClientFileNameLog.java ClientLog.java

clean-log:
	if [ `ls chatlog/*.txt 2>/dev/null` ] ; then rm chatlog/*.txt ; fi

clean:
	rm -f *.class