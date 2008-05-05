import java.io.*;
import java.net.*;
import java.util.*;

public final class AwarenessAccount
	extends Thread
	implements Cloneable {
	
	public volatile boolean isConnected;
	
	public AwarenessServerSocket awarenessServerSocket;
	
	public Socket clientSocket = null;
	public DataInputStream dis = null;
	public DataOutputStream dos = null;
	
	private String nickname;
	private String username;
	private String sex;
	private String language;
	private String session;
	private String location;
	private String team;
	private int statut;
	
	public AwarenessAccount(AwarenessServerSocket parent,Socket _clientSocket) {
		try {
			awarenessServerSocket = parent;
			clientSocket = _clientSocket;
			
			dis = new DataInputStream(clientSocket.getInputStream());
			dos = new DataOutputStream(clientSocket.getOutputStream());
			
			isConnected = true;
			
			start();
			
		} catch (IllegalThreadStateException itse) {
		} catch (IOException ioe) {
		}
	}
	
	public Object clone() {
		Object o = null;
		
		try {
			o = super.clone();
		} catch (CloneNotSupportedException cnse) {
		}
		
		return o;
	}
	
	public void run() {
		
		String message = null;
		
		try {
			while (isConnected) {
				message = dis.readUTF();
				
				if (message == null) {
					throw new Exception(AwarenessProtocol.AWARENESS_ERROR);
					
				} else if (message.startsWith(AwarenessProtocol.ADD_USER_LIST)) {
					// Récupérer les informations du message
					AwarenessMessage user = new AwarenessMessage(message);
					
					nickname = user.getNickname();
					session = user.getSession();
					username = user.getUsername();
					sex = user.getSex();
					language = user.getLanguage();
					team = user.getTeam();
					location = user.getLocation();
					statut = new Integer(user.getStatut()).intValue();
					
					user = null;
					
					// Enregistrer cet utilisateur dans la table des connectés
					awarenessServerSocket.addAccount(this);
					
					// Confirmer à l'utilisateur qu'il a bien été enregistré
					message = AwarenessProtocol.AWARENESS_HELLO
						+ " " + nickname
						+ " " + session;
					awarenessServerSocket.addMessage(message);
					
					// Demande au serveur d'envoyer la liste complète
					// des connectés au groupe de cette session
					message = AwarenessProtocol.SEND_USERS_LIST
						+ " " + nickname
						+ " " + session;
					awarenessServerSocket.addMessage(message);
					
					// Donner un nom au thread
					setName("AwarenessAccount-" + nickname);
					
				} else {
					// Ajouter ce nouveau message dans la table des messages
					awarenessServerSocket.addMessage(message);
				}
			}
			
		} catch (Exception e) {
			// Retirer cet utilisateur de la table des connectés
			awarenessServerSocket.removeAccount(this);
		}
		
		disconnect();
	}
	
	public void disconnect() {
		isConnected = false;
		
		if (dis != null) {
			try {
				dis.close();
			} catch (IOException ioe) {
			} finally {
				dis = null;
			}
		}
			
		if (dos != null) {
			try {
				dos.close();
			} catch (IOException ioe) {
			} finally {
				dos = null;
			}
		}
		
		if (clientSocket != null) {
			try {
				clientSocket.close();
			} catch (IOException ioe) {
			} finally {
				clientSocket = null;
			}
		}
	}
	
	public String getNickname() { return nickname; }
	public String getUsername() { return username; }
	public String getSex() { return sex; }
	public String getLanguage() { return language; }
	public String getSession() { return session; }
	public void setSession(String s) { session = s; }
	public void setLocation(String s) { location = s; }
	public String getLocation() { return location; }
	public void setTeam(String s) { team = s; }
	public String getTeam() { return team; }
	public void setStatut(int i) { statut = i; }
	public int getStatut() { return statut; }
	
	/**
	 * Envoyer un message
	 */
	
	public void sendTo(String _message) 
		throws IOException {
		dos.writeUTF(_message);
		dos.flush();
	}
}
