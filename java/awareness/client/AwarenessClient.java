import java.applet.*;
import java.awt.*;
import java.awt.event.*;
import java.io.*;
import java.util.*;
import java.net.*;

public class AwarenessClient
	extends Applet
	implements Runnable, MouseListener {
	
	public static DataOutputStream ac_dos;
	
	public static Resource i18n;
	
	public static String ac_nickname;
	public static String ac_sex;
	public static String ac_language;
	public static String ac_session;
	public static String ac_team;
	public static String ac_location;
	public static int ac_statut;
	
	public static boolean ac_isConnected;
	
	public static AwarenessListConnected awarenessListConnected;
	
	public static Vector awarenessChatClient;
	
	public static Image[] images;
	
	public String hostname = null;
	public int port = 0;
	
	public Socket socket;
	public DataInputStream ac_dis;
	
	public Thread thread;
	public AwarenessMessagesThread messagesThread = null;
	public ConnectionListener connectionListener = null;
	
	public String mode;
	
	public String ac_username;
	
	public MediaTracker tracker;
	
	public void init() {
		ac_isConnected = false;
		
		mode = getParameter("mode").trim();
		
		if (mode == null || mode.length() < 1)
			mode = "applet";
		
		// Gestionnaire d'images
		tracker = new MediaTracker(this);
		
		images = new Image[5];
		images[0] = getImage(getCodeBase(),"images/non_oeil.jpg");
		images[1] = getImage(getCodeBase(),"images/oeil.jpg");
		images[2] = getImage(getCodeBase(),"images/boy.gif");
		images[3] = getImage(getCodeBase(),"images/girl.gif");
		images[4] = getImage(getCodeBase(),"images/interdit.gif");
		
		tracker.addImage(images[0],0);
		tracker.addImage(images[1],1);
		tracker.addImage(images[2],2);
		tracker.addImage(images[3],2);
		tracker.addImage(images[4],2);
		
		try {
			tracker.waitForID(0);
			
		} catch (InterruptedException ie) {
		}
		
		// Charger le fichier de traduction
		loadTranslations(new String(getParameter("language")).trim(),getCodeBase());
	}
	
	public AwarenessChatClient findNicknameFrame(String _nickname) {
		// D'abord vérifiez qu'on n'a pas déjà ouvert une fenêtre chat
		// pour cet utilisateur
		for (Enumeration e=awarenessChatClient.elements(); e.hasMoreElements(); ) {
			AwarenessChatClient acc =(AwarenessChatClient)e.nextElement();
			
			if (acc.getRecipient().equals(_nickname))
				return acc;
		}
		
		// Ajouter une nouvelle fenêtre chat pour ce nouveau connecté
		AwarenessChatClient acc = new AwarenessChatClient(this,_nickname);
		awarenessChatClient.addElement(acc);
		
		return acc;
	}
	
	/**
	 * Charger le/les fichiers de traduction.
	 *
	 * Exemple:
	 *
	 *   <code><param name="language" value="Fra|Galanet"></code>
	 */
	
	public static void loadTranslations(String _language,URL url) {
		
		if (_language == null || _language.length() < 1)
			return;
		
		ac_language = _language;
		
		i18n = new Resource("AwarenessClient_",url,ac_language);
	}
	
	public AwarenessChatClient showChatFrame(String _nickname) {
		AwarenessChatClient acc = findNicknameFrame(_nickname);
		acc.setVisible(true);
		acc.toFront();
		
		return acc;
	}
	
	public static void setSession(String session) {
		
		if (session == null || session.equals(ac_session) || ac_nickname == null)
			return;
		
		try {
			sendTo(AwarenessProtocol.CHANGE_SESSION_PLATEFORM
				+ " " + ac_nickname
				+ " " + ac_session
				+ " " + session
			);
			
		} catch (IOException ioe) {
		}
	}
	
	public static void setTitleListConnected(String s) {
		if (s == null || s.length() < 1)
			return;
		
		while (true) {
			try {
				Class.forName("AwarenessURLDecoder");
				awarenessListConnected.setTitle(AwarenessURLDecoder.decode(s));
				break;
			} catch (Exception e) {
			}
			
			try {
				Thread.sleep(100);
			} catch (InterruptedException ie) {
			}
		}
	}
	
	public void addMessageChatFrame(String nicknameFrame,String nicknameWriter,String _message) {
		AwarenessChatClient acc = showChatFrame(nicknameFrame);
		acc.addMessage(nicknameWriter,nicknameFrame,_message);
	}
	
	public void mouseClicked(MouseEvent me) {
		String namePanel = ((Component)me.getComponent()).getName();
		
		if (this.getName().equals(namePanel)) {
			showListConnected();
		} else {
			showChatFrame(namePanel);
		}
	}
	
	public void mouseEntered(MouseEvent e) {
	}
	
	public void mouseExited(MouseEvent e) {
	}
	
	public void mousePressed(MouseEvent e) {
	}
	
	public void mouseReleased(MouseEvent e) {
	}
	
	public static void refreshLabels() {
		if (awarenessListConnected != null)
			awarenessListConnected.refreshLabels();
		
		// Actualiser/Réactuliser les textes des fenêtres chat
		if (awarenessChatClient != null)
			for (Enumeration e=awarenessChatClient.elements(); e.hasMoreElements(); ) {
				((AwarenessChatClient)e.nextElement()).refreshLabels();
			}
	}
	
	public void openListConnected() {
		showListConnected();
	}
	
	public static void showListConnected() {
		if (awarenessListConnected != null)
			awarenessListConnected.setVisible(true);
	}
	
	public void connect()
		throws Exception {
		
		// Tentative de connection
		socket = new Socket(hostname,port);
		ac_dis = new DataInputStream(socket.getInputStream());
		ac_dos = new DataOutputStream(socket.getOutputStream());
		
		// Inscription de l'utilisateur
		registerUser();
		
		// Attendre la confirmation du serveur qu'on a bien été enregistré
		String verify = AwarenessProtocol.AWARENESS_HELLO
			+ " " + ac_nickname
			+ " " + ac_session;
		
		String message = ac_dis.readUTF();
		
		if (!verify.equals(message))
			throw new Exception("User not registed");
		
		verify = null;
		
		// Faut-il envoyer la table des traductions
		awarenessListConnected = new AwarenessListConnected(this);
		awarenessChatClient = new Vector();
		
		// Initialiser le thread de l'applet
		if (thread == null)
			thread = new Thread(this);
		
		// Lancer l'applet
		ac_isConnected = true;
		
		if (thread != null)
			thread.start();
		
		addMouseListener(this);
		setCursor(new Cursor(Cursor.HAND_CURSOR));
	}
	
	public void start() {
		setName("AwarenessClient");
		
		try {
			// Tentative de connexion
			hostname = getParameter("hostname");
			port     = Integer.parseInt(getParameter("port"));
			
			// Vérifier tous les x temps que nous sommes toujours connecté
			// avec le serveur
			connectionListener = new ConnectionListener() {
				public void isConnected()
					throws NullPointerException, IOException {
					
					if (ac_nickname == null || ac_session == null)
						throw new IOException();
					
					sendTo(AwarenessProtocol.KEEP_ALIVE_CLIENT
						+ " " + ac_nickname
						+ " " + ac_session);
				}
				
				public void connectionSuccessful() {
					ac_isConnected = true;
					repaint();
				}
				
				public void connectionLost() {
					
					try {
						if (ac_isConnected)
							disconnect();
						
						// Essayons de nous reconnecter
						connect();
						
					} catch (Exception e) {
					}
					
					repaint();
				}
			};
			
			if (connectionListener == null)
				throw new NullPointerException("ConnectionListener: NullPointerException");
			
			connectionListener.start();
			
		} catch (Exception e) {
		}
	}
	
	public void run() {
		String message;
		
		try {
			// Lancer la boucle des messages
			messagesThread = new AwarenessMessagesThread() {
				public void dispatchMessage(String message) {
					AwarenessMessage awarenessMessage = new AwarenessMessage(message);
					String command = awarenessMessage.getCommand();
					
					if (command == null) {
						return;
						
					} else if (AwarenessProtocol.CLIENT_MESSAGE_PRIVATE.equals(command)) {
						if (ac_nickname.equals(awarenessMessage.getNickname())) {
							addMessageChatFrame(awarenessMessage.getRecipient(),ac_nickname,awarenessMessage.getMessage());
						} else {
							addMessageChatFrame(awarenessMessage.getNickname(),awarenessMessage.getNickname(),awarenessMessage.getMessage());
						}
						
					} else if (AwarenessProtocol.CHANGE_USER_LOCATION.equals(command) ||
						AwarenessProtocol.CHANGE_USER_STATUT.equals(command) ||
						AwarenessProtocol.CHANGE_USER_TEAM.equals(command)) {
						awarenessListConnected.UpdateUser(awarenessMessage);
						
					} else if (AwarenessProtocol.UPDATE_USER_LIST.equals(command)) {
						// Si l'utilisateur a changé de session alors il faudra
						// supprimer complétement la liste des connectés
						if (!awarenessMessage.getSession().equals(ac_session)) {
							ac_session = awarenessMessage.getSession();
							awarenessListConnected.ClearListConnected();
						}
						
						awarenessListConnected.UpdateUserList(awarenessMessage);
						
					} else if (AwarenessProtocol.REMOVE_USER_LIST.equals(command)) {
						awarenessListConnected.RemoveUserList(awarenessMessage.getNickname());
					}
				}
			};
			
			if (messagesThread == null)
				throw new Exception("AwarenessMessagesThread: NullPointerException");
				
			messagesThread.start();
			
			// Boucle des messages
			while (ac_isConnected) {
				message = ac_dis.readUTF();
				
				if (message == null)
					throw new Exception("connection interrupted");
				
				messagesThread.addMessage(message);
			}
		
		} catch (Exception e) {
		}
	}
	
	public void update(Graphics g) {
		paint(g);
	}
	
	public void paint(Graphics g) {
		if (!mode.equals("applet"))
			return;
		
		try {
			g.drawImage((ac_isConnected ? getEyeImage() : getNoEyeImage()),0,0,this);
		} catch (NullPointerException npe) {
		}
	}
	
	public static Image getNoEyeImage() { return images[0]; }
	public static Image getEyeImage() { return images[1]; }
	public Image getBoyImage() { return images[2]; }
	public Image getGirlImage() { return images[3]; }
	public Image getBusyImage() { return images[4]; }
	
	public static synchronized void sendNewTeamUser(String _team) {
		
		if (_team.equals(ac_team) ||
			ac_nickname == null ||
			ac_session == null)
			return;
		
		try {
			sendTo(AwarenessProtocol.CHANGE_USER_TEAM
				+ " " + ac_nickname
				+ " " + ac_session
				+ " " + _team
			);
			
		} catch (IOException ioe) {
		}
	}
	
	public static synchronized void sendNewUserPosition(String _location) {
		
		if (_location.equals(ac_location) ||
			ac_nickname == null ||
			ac_session == null)
			return;
		
		try {
			sendTo(AwarenessProtocol.CHANGE_USER_LOCATION
				+ " " + ac_nickname
				+ " " + ac_session
				+ " " + _location
			);
			
		} catch (IOException ioe) {
		}
	}
	
	public static synchronized void sendNewUserStatut(int _statut) {
		
		if (ac_nickname == null || ac_session == null)
			return;
		
		try {
			sendTo(AwarenessProtocol.CHANGE_USER_STATUT
				+ " " + ac_nickname
				+ " " + ac_session
				+ " " + _statut
			);
			
		} catch (IOException ioe) {
		}
	}
	
	public static synchronized void sendTo(String _message)
		throws IOException {
		
		if (ac_dos == null)
			throw new IOException("DataOutputStream is null");
		
		ac_dos.writeUTF(_message + "\n");
		ac_dos.flush();
	}
	
	public void registerUser() 
		throws NullPointerException, IOException {
		
		ac_nickname = getParameter("nickname");
		ac_session = getParameter("session");
		ac_username = getParameter("username");
		
		ac_sex = getParameter("sex");
		if (ac_sex == null || ac_sex.length() < 1)
			ac_sex = "M";
		
		ac_language = getParameter("language");
		if (ac_language == null || ac_language.length() < 1)
			ac_language = "Fra";
		
		ac_team = getParameter("team");
		if (ac_team == null || ac_team.length() < 1)
			ac_team = "NULL";
		
		ac_location = getParameter("location");
		if (ac_location == null || ac_location.length() < 1)
			ac_location = "NULL";
		
		ac_statut = AwarenessProtocol.USER_STATUT_AVAILABLE;
		
		String message = AwarenessProtocol.ADD_USER_LIST
			+ " " + ac_nickname
			+ " " + ac_session
			+ " " + ac_username
			+ " " + ac_sex
			+ " " + ac_language
			+ " " + ac_team
			+ " " + ac_location
			+ " " + ac_statut;
		
		sendTo(message);
	}
	
	public void disconnect() {
		
		// Nous sommes déconnecté
		ac_isConnected = false;
		
		removeMouseListener(this);
		setCursor(new Cursor(Cursor.DEFAULT_CURSOR));
		
		// Fermer toutes les fenêtres chat
		if (awarenessChatClient != null) {
			for (Enumeration e= awarenessChatClient.elements(); e.hasMoreElements(); ) {
				AwarenessChatClient f = (AwarenessChatClient)e.nextElement();
				f.setVisible(false);
				f.dispose();
			}
			
			awarenessChatClient = null;
		}
		
		// Fermer la fenêtre de la liste des connectés
		if (awarenessListConnected != null) {
			awarenessListConnected.setVisible(false);
			awarenessListConnected.dispose();
			awarenessListConnected = null;
		}
		
		// Arrêter la boucle des messages
		if (messagesThread != null) {
			messagesThread.stopMessages();
			messagesThread = null;
			System.out.println("-- Arrêter la boucle des messages");
		}
		
		// Fermer le flux d'écoute
		if (ac_dis != null) {
			try {
				ac_dis.close();
			} catch (IOException ioe) {
			} finally {
				ac_dis = null;
			}
			
			System.out.println("-- Fermer le flux d'écoute");
		}
		
		// Fermer le flux d'écriture
		if (ac_dos != null) {
			try {
				ac_dos.close();
			} catch (IOException ioe) {
			} finally {
				ac_dos = null;
			}
			
			System.out.println("-- Fermer le flux d'écriture");
		}
		
		// Fermer la socket
		if (socket != null) {
			try {
				socket.close();
			} catch (IOException ioe) {
			} finally {
				socket = null;
			}
			
			System.out.println("-- Fermer la socket");
		}
		
		// Arrêter le thread de l'applet
		if (thread != null) {
			try {
				thread.join();
			} catch (InterruptedException ie) {
			}
			
			thread = null;
			
			System.out.println("-- Arrêter le thread de l'applet");
		}
	}
	
	public void stop() {
		i18n = null;
		
		//Déconnexion
		disconnect();
		
		// Arrêter l'écoute de la connection
		if (connectionListener != null) {
			connectionListener.stopListener();
			connectionListener = null;
			System.out.println("-- Arrêter l'écoute de la connection");
		}
		
		// Nettoyage
		hostname = null;
		port = 0;
		
		tracker = null;
		images = null;
		
		ac_nickname = null;
		ac_session = null;
		ac_username = null;
		ac_sex = null;
		ac_language = null;
		ac_team = null;
		ac_location = null;
	}
}
