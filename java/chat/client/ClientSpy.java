import java.util.Hashtable;
import java.io.*;
import java.net.*;

public class ClientSpy
	extends Hashtable {
	
	private String host;
	private int port;
	
	public ClientSpy(String _host, int _port) {
		host = _host;
		port = _port;
	}
	
	/**
	 * Cette fonction va, d'abord, se connecter à un serveur,
	 * puis dès que la connexion est établie elle envoie les
	 * informations.
	 *
	*/
	
	public void send() {
		Socket socket = null;
		DataOutputStream dos = null;
		
		try {
			// Initialiser les paramètres
			String id_session = "id_session:" + get("id_session");
			String nickname   = "nickname:" + get("nickname");
			String location   = "location:" + get("location");
			
			// Etablir une connexion avec le serveur
			socket = new Socket(host,port);
			
			// Création d'un flux de communication
			dos = new DataOutputStream(socket.getOutputStream());
			
			// Envoyer les informations au serveur
			dos.writeUTF("command:AWARENESSSPY");
			dos.writeUTF(id_session);
			dos.writeUTF(nickname);
			dos.writeUTF(location);
			dos.writeUTF("<EOI>");
			dos.flush();
			
			System.out.println(id_session + ":" + nickname + ":" + location);
			
		} catch (NullPointerException npe) {
		} catch (UnknownHostException ukhe) {
		} catch (IOException ioe) {
		} catch (SecurityException se) {
		}
		
		if (socket != null) {
			if (dos != null) {
				try {
					dos.close();
				} catch (IOException ioe) {
				}
				
				dos = null;
			}
			
			try {
				socket.close();
			} catch (IOException ioe) {
			}
			
			socket = null;
		}
	}
}
