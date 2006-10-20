# Makefile
# 
# DeltaChatClient
#

CC="`which javac`" -target 1.1 -source 1.2 -classpath ../../awareness/client
JAR="`which jar`" -cvf 

TAR=tar czvf

TARGET=../../../src/sousactiv/chat/client

all: clean jar

clean:
	rm -f *.class *.jar

class:
	$(CC) AutoSendMessage.java \
		Resource.java \
		ChatCliUser.java \
		ClientSpy.java \
		StatusBar.java \
		ChatCli.java

jar: class
	rm -f DeltaChatClient.jar
	$(JAR) DeltaChatClient.jar *.class

install:
	cp -f *.jar *.properties $(TARGET)
	chmod -R ugo+r $(TARGET)

clean-install:
	rm $(TARGET)/DeltaChatClient.jar
	rm $(TARGET)/*.properties

