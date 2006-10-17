/**
 * AwarenessServerSocket
 * 
 * @author Filippo PORCO (filippo.porco@umh.ac.be)
 * 
 * Unité de Technologie de l'Education
 * Place du Parc, 18
 * 7000 MONS
 * 
*/

import java.io.*;
import java.util.*;
import java.net.*;

public class AwarenessServerSocket
	implements Runnable {
	
	public ServerSocket serverSocket;
	public Thread thread;
	
	public Hashtable accounts;			// Table des connectés
	
	public AwarenessMessagesThread messagesThread;
	
	public AwarenessServerSocket() {
		accounts = new Hashtable();
	}
	
	public void startServer(int port) {
		try {
			serverSocket = new ServerSocket(port);
			
			// Lancer le serveur d'écoute
			thread = new Thread(this);
			thread.start();
			
		} catch (NullPointerException npe) {
		} catch (IOException ioe) {
		} catch (IllegalThreadStateException itse) {
		} catch (SecurityException se) {
		}
	}
	
	public void run() {
		Socket clientSocket = null;
		
		try {
			// Lancer la boucle des messages
			messagesThread = new AwarenessMessagesThread() {
				public void dispatchMessage(String message) {
					AwarenessMessage awarenessMessage = new AwarenessMessage(message);
					String command = awarenessMessage.getCommand();
					
					if (command == null) {
						return;
						
					} else if (AwarenessProtocol.CLIENT_MESSAGE_PRIVATE.equals(command)) {
						sendClientMessagePrivate(awarenessMessage);
						
					} else if (AwarenessProtocol.CHANGE_SESSION_PLATEFORM.equals(command)) {
						changeSessionUser(awarenessMessage);
						
					} else if (AwarenessProtocol.CHANGE_USER_LOCATION.equals(command) ||
						AwarenessProtocol.CHANGE_USER_STATUT.equals(command)) {
						
						updateAccount(command,awarenessMessage);
						
					} else if (AwarenessProtocol.SEND_USERS_LIST.equals(command)) {
						sendListUsers(awarenessMessage);
						
					} else if (AwarenessProtocol.CHANGE_USER_TEAM.equals(command)) {
						changeTeamUser(awarenessMessage);
						
					} else if (AwarenessProtocol.REMOVE_USER_LIST.equals(command)) {
						sendTo(message,getAccounts(awarenessMessage.getSession()));
						
					} else if (AwarenessProtocol.AWARENESS_OK.equals(command) ||
						AwarenessProtocol.AWARENESS_HELLO.equals(command)) {
						
						try {
							sendTo(message,getAccount(awarenessMessage.getNickname(),awarenessMessage.getSession()));
						} catch (IOException ioe) {
						}
					}
				}
			};
			
			messagesThread.start();
			
			// Boucle des messages
			while ((clientSocket = serverSocket.accept()) != null) {
				new AwarenessAccount(this,clientSocket);
			}
			
		} catch(Exception e) {
		}
		
		stopServer();
	}
	
	/**
	 * changeSessionUser
	 *
	 * Cette fonction est utilisée lorsqu'un utilisateur a changé de session.
	 * Il doit être retirer de l'ancienne liste et rajouter dans la nouvelle.
	 *
	 */
	
	public void changeSessionUser(AwarenessMessage awarenessMessage) {
		String nickname    = awarenessMessage.getNickname();
		String session     = awarenessMessage.getSession();
		String new_session = awarenessMessage.getNewSession();
		
		if (nickname == null || session == null || new_session == null)
			return;
		
		AwarenessAccount account = getAccount(nickname,session);
		
		if (account == null)
			return;
		
		// Retirer
		removeAccount(account);
		
		// Ajouter
		account.setSession(new_session);
		
		addAccount(account);
		
		// Demande à l'AwarenessServer d'envoyer la nouvelle liste des connectés
		addMessage(AwarenessProtocol.SEND_USERS_LIST
			+ " " + nickname
			+ " " + new_session);
	}
	
	public void changeTeamUser(AwarenessMessage awarenessMessage) {
		String nickname = awarenessMessage.getNickname();
		String session  = awarenessMessage.getSession();
		
		if (nickname == null || session == null)
			return;
		
		Vector accountsSession = getAccounts(session);
		AwarenessAccount account = getAccount(nickname,accountsSession);
		
		if (account == null || accountsSession == null)
			return;
		
		account.setTeam(awarenessMessage.getTeam());
		
		sendTo(awarenessMessage.toString(),accountsSession);
	}
	
	public void updateAccount(String command,AwarenessMessage awarenessMessage) {
		
		String nickname = awarenessMessage.getNickname();
		String session  = awarenessMessage.getSession();
		
		if (nickname == null || session == null)
			return;
		
		Vector accountsSession = getAccounts(session);
		AwarenessAccount account = getAccount(nickname,accountsSession);
		
		if (account == null || accountsSession == null)
			return;
		
		String message = null;
		
		if (command.equals(AwarenessProtocol.CHANGE_USER_STATUT)) {
			message = awarenessMessage.getStatut();
			account.setStatut(new Integer(message).intValue());
			
		} else if (command.equals(AwarenessProtocol.CHANGE_USER_LOCATION)) {
			message = awarenessMessage.getLocation();
			account.setLocation(message);
		}
		
		//accounts.put(session,accountsSession);
		
		if (message != null) {
			
			message = command
				+ " " + nickname
				+ " " + session
				+ " " + message;
			
			sendTo(message.trim(),accountsSession);
		}
	}
	
	/**
	 * Envoyer la liste des connectes
	 *
	 */
	
	public void sendListUsers(AwarenessMessage awarenessMessage) {
		
		String nickname = awarenessMessage.getNickname();
		String session = awarenessMessage.getSession();
		
		if (nickname == null || session == null)
			return;
		
		Vector accountsSession = getAccounts(session);
		AwarenessAccount awarenessAccount = getAccount(nickname,accountsSession);
		
		if (awarenessAccount == null)
			return;
		
		String message = AwarenessProtocol.UPDATE_USER_LIST
			+ " " + nickname
			+ " " + session
			+ " " + awarenessAccount.getSex()
			+ " " + awarenessAccount.getTeam()
			+ " " + awarenessAccount.getLocation()
			+ " " + awarenessAccount.getStatut();
		
		String message2;
		
		// Récupérer le groupe de la session
		for (Enumeration e=accountsSession.elements(); e.hasMoreElements(); ) {
			AwarenessAccount account = (AwarenessAccount)e.nextElement();
			
			if (!account.getNickname().equals(nickname))
			{
				// Envoyer la liste complète à la nouvelle personne connectée
				message2 = AwarenessProtocol.UPDATE_USER_LIST
					+ " " + account.getNickname()
					+ " " + account.getSession()
					+ " " + account.getSex()
					+ " " + account.getTeam()
					+ " " + account.getLocation()
					+ " " + account.getStatut();
				
				try {
					awarenessAccount.sendTo(message2);
				} catch (IOException ioe) {
				}
			}
			
			try {
				// Avertir cet utilisateur qu'une nouvelle personne vient
				// de ce connecter
				account.sendTo(message);
			} catch (IOException ioe) {
			}
		}
	}
	
	public void stopServer() {
		accounts = null;
		
		// Quitter la boucle des messages
		messagesThread.stopMessages();
		
		messagesThread = null;
		
		try {
			serverSocket.close();
		} catch (IOException ioe) {
		} finally {
			serverSocket = null;
		}
		
		try {
			thread.join();
		} catch (InterruptedException ie) {
		} finally {
			thread = null;
		}
	}
	
	/**
	 * Ajoute un message à la fin de la table des messages
	 */
	
	public void addMessage(String message) {
		messagesThread.addMessage(message);
	}
	
	protected void addAccount(AwarenessAccount account) {
		
		String nickname = account.getNickname();
		String session  = account.getSession();
		
		if (nickname == null || session == null)
			return;
		
		Vector v = getAccounts(session);
		
		// Dans le cas où il existe déjà, il faudra le retirer de cette liste
		v = removeAccount(nickname,v);
		
		v.addElement(account);
		
		synchronized (accounts) {
			accounts.put(session,v);
		}
	}
	
	public AwarenessAccount getAccount(String nickname,Vector accountsSession) {
		if (accountsSession != null) {
			for (Enumeration e=accountsSession.elements(); e.hasMoreElements(); ) {
				AwarenessAccount awarenessAccount = (AwarenessAccount)e.nextElement();
				
				if (nickname.equals(awarenessAccount.getNickname()))
					return awarenessAccount;
			}
		}
		
		return null;
	}
	
	public AwarenessAccount getAccount(String nickname,String session) {
		return getAccount(nickname,getAccounts(session));
	}
	
	public Vector getAccounts(Vector nicknames,String session) {
		
		Vector group = getAccounts(session);
		Vector accountsSession = new Vector();
		
		for (Enumeration e1=nicknames.elements(); e1.hasMoreElements(); ) {
			String nickname = (String)e1.nextElement();
			
			for (Enumeration e2=group.elements(); e2.hasMoreElements(); ) {
				AwarenessAccount awarenessAccount = (AwarenessAccount)e2.nextElement();
				
				if (nickname.equals(awarenessAccount.getNickname())) {
					accountsSession.addElement(awarenessAccount);
					break;
				}
			}
		}
		
		return accountsSession;
	}
	
	public Vector getAccounts(String session) {
		return (Vector)accounts.get(session);
	}
	
	protected void removeAccount(AwarenessAccount account) {
		
		if (account == null)
			return;
		
		String nickname = account.getNickname();
		String session  = account.getSession();
		
		if (nickname == null || session == null)
			return;
		
		// Retirer le client de cette session de la table des connectés
		Vector accountsSession = removeAccount(nickname,getAccounts(session));
		
		synchronized (accounts) {
			// Si il n'y a plus de connectés dans cette session alors
			// supprimer cette table
			if (accountsSession.isEmpty()) {
				accounts.remove(session);
			} else {
				accounts.put(session,accountsSession);
				
				// Avertir le restant des connectés de cette session
				// que cet utilisateur vient de quitter la plate-forme
				addMessage(AwarenessProtocol.REMOVE_USER_LIST
					+ " " + nickname
					+ " " + session
				);
			}
		}
	}
	
	protected Vector removeAccount(String nickname,Vector accountsSession) {
		
		if (accountsSession == null)
			return new Vector();
		
		int index = 0;
		
		for (Enumeration e = accountsSession.elements(); e.hasMoreElements(); ) {
			if (((AwarenessAccount)e.nextElement()).getNickname().equals(nickname)) {
				accountsSession.removeElementAt(index);
				accountsSession.trimToSize();
				break;
			}
			
			index++;
		}
		
		return accountsSession;
	}
	
	/**
	 * Envoyer un message
	 */
	
	public void sendClientMessagePrivate(AwarenessMessage awarenessMessage) {
		String session = awarenessMessage.getSession();
		String message = awarenessMessage.toString();
		
		// Envoyé le message au destinataire
		AwarenessAccount awarenessAccount = getAccount(awarenessMessage.getRecipient(),session);
		
		if (awarenessAccount != null) {
			if (awarenessAccount.getStatut() != AwarenessProtocol.USER_STATUT_AVAILABLE) {
				message = AwarenessProtocol.CLIENT_MESSAGE_PRIVATE
					+ " " + awarenessMessage.getNickname()
					+ " " + session
					+ " " + awarenessMessage.getRecipient()
					+ " texte_personne_non_contactable";
			} else {
				try {
					sendTo(message,awarenessAccount);
				} catch (IOException ioe) {
					awarenessAccount = null;
				}
			}
		}
		
		// Prévenir l'expéditeur que le message n'a pas pu été envoyé
		// correctement au destinataire
		if (awarenessAccount == null) {
			message = AwarenessProtocol.CLIENT_MESSAGE_PRIVATE
				+ " " + awarenessMessage.getNickname()
				+ " " + session
				+ " " + awarenessMessage.getRecipient()
				+ " texte_personne_deconnecter";
		}
		
		// Envoyé le message à l'expéditeur
		awarenessAccount = getAccount(awarenessMessage.getNickname(),session);
		
		if (awarenessAccount != null) {
			try {
				sendTo(message,awarenessAccount);
			} catch (IOException ioe) {
			}
		}
	}
	
	public void sendTo(String message,AwarenessAccount awarenessAccount) 
		throws IOException {
		
		awarenessAccount.sendTo(message);
	}
	
	public void sendTo(String message,Vector accountsSession) {
		for (Enumeration e = accountsSession.elements(); e.hasMoreElements(); ) {
			// Recupérer le membre de la session
			AwarenessAccount account = (AwarenessAccount)e.nextElement();
			
			if (account == null)
				continue;
			
			try {
				sendTo(message,account);
			} catch (IOException ioe) {
				// Essayons de fermer correctement les flux de communication
				account.disconnect();
				
				// Retirons cette personne de la liste des connectés
				removeAccount(account);
			}
		}
	}
}
