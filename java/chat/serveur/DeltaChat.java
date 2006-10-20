import java.io.*;
import java.net.*;

public class DeltaChat {
	
	public static void main(String args[]) throws IOException {
		System.out.println("Serveur de discussion (chat)");

		if (args.length != 1) {
			System.out.println("Attend le numéro de port comme argument!");
			return;
		}
		
		Integer noPort = Integer.valueOf(args[0]);
		
		System.out.println("Ecoute sur port " + noPort.toString());
		
		// crée le serveur chat
		ChatServer serv = new ChatServer();
		
		// démarre le serveur sur le port spécifié
		serv.startServer(noPort.intValue());
	}
}