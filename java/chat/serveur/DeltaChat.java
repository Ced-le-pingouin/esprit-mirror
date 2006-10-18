import java.io.*;
import java.net.*;

public class DeltaChat {
	
	public static void main(String args[]) throws IOException {
		System.out.println("Serveur de discussion (chat)");

		if (args.length != 1) {
			System.out.println("Attend le num�ro de port comme argument!");
			return;
		}
		
		Integer noPort = Integer.valueOf(args[0]);
		
		System.out.println("Ecoute sur port " + noPort.toString());
		
		// cr�e le serveur chat
		ChatServer serv = new ChatServer();
		
		// d�marre le serveur sur le port sp�cifi�
		serv.startServer(noPort.intValue());
	}
}