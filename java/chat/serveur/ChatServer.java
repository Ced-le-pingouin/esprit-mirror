import java.io.*;
import java.net.*;
import java.util.*;

public class ChatServer extends TCPServer {
	
	public static Object nbConnectLock = new Object();
	
	private final static int nbMaxConnect = 500;
	
	private static Users users;
	private static Hashtable clientsLog;
	
	private User user;
	private	DataInputStream dis;
	private DataOutputStream dos;
	private boolean bSaveConversation;
	private int errorAliveReply;
	
	public ChatServer() {
		if (users == null)
			users = new Users();
		
		if (clientsLog == null)
			clientsLog = new Hashtable();
		
		initErrorAlive();
		
		dis = null;
		dos = null;
		
		user = null;
	}
	
	public void run(Socket socket) {
		PingClient pingClient;
		
		try {
			synchronized(nbConnectLock) {
				if (users.size() >= nbMaxConnect)
					throw (new Exception("Nombre maximum de connexions dépassées"));
				
				// Un nouvel utilisateur vient de se connecter
				dis  = new DataInputStream(new BufferedInputStream(socket.getInputStream()));
				dos  = new DataOutputStream(new BufferedOutputStream(socket.getOutputStream()));
				user = new User(dis,dos);
				
				if (users.exists(user))
					users.remove(user);
				
				// Ajouter un nouvel utilisateur à la liste
				users.add(user);
				bSaveConversation = user.getSaveConversation();
			}
			
			// boucle de traitement
			String message;
			
			pingClient = new PingClient();
			pingClient.start();
			
			while(user != null) {
				message = dis.readUTF();
				Thread.yield();
				
				if (message == null)
					throw (new Exception("shutdown"));
				
				if (message.equals("<KEEP_ALIVE_REPLY>")) {
					initErrorAlive();
				} else if (message.startsWith("<SPIRIT_TO_WRITE>")
					|| message.startsWith("<MSGPRIVATE")
					) {
					users.sendMessage(user, message);
				} else if (message.length() > 0) {
					saveConversation(message);
					users.sendMessage(user, message);
				}
			}
			
		} catch(Exception e) {
			clear();
			
			if (socket != null) {
				try {
					socket.close();
				} catch (IOException ioe) {
				}
				
				socket = null;
			}
			
			pingClient = null;
		}
	}
	
	private synchronized void initErrorAlive() {
		errorAliveReply = 0;
	}
	
	public synchronized boolean getKeepAlive() {
		return (user == null || ++errorAliveReply > 3 ? false : user.sendMessage("<KEEP_ALIVE_REQUEST>"));
	}
	
	class PingClient extends Thread {
		
		private boolean isConnected;
		
		public PingClient() {
			isConnected = true;
		}
		
		public void run() {
			while (isConnected) {
				
				isConnected = getKeepAlive();
				
				if (isConnected) {
					try {
						sleep(5000);
					} catch (InterruptedException ie) {
						//isConnected = false;
					}
				}
			}
			
			clear();
		}
	}
	
	private synchronized void saveConversation(String message) {
		
		if (!bSaveConversation)
			return;
		
		synchronized (clientsLog) {
			ClientLog clientLog = (ClientLog)clientsLog.get(user.getGroupKey());
			
			try {
				if (clientLog == null) {
					clientLog = new ClientLog("ChatLog-", user.getDirChatLog());
					clientLog.writeFooter(user);
					clientsLog.put(user.getGroupKey(), clientLog);
				}
				
				clientLog.writeMessage(message,user);
				
			} catch (NullPointerException npe) {
			} catch (IOException ioe) {
			}
			
			clientLog = null;
		}
	}
	
	private void clear() {
		
		if (dis!= null) {
			try {
				dis.close();
			} catch (IOException ioe) {
			}
			
			dis = null;
		}
		
		if (dos != null) {
			try {
				dos.close();
			} catch (IOException ioe) {
			}
			
			dos = null;
		}
		
		if (user != null) {
			synchronized (nbConnectLock) {
				if (users != null) {
					users.remove(user);
					
					if (bSaveConversation &&
						(users.getGroup(user.getGroupKey())).size() == 0)
						// Retirer de la liste le fichier des conversations
						clientsLog.remove(user.getGroupKey());
				}
			}
			
			user = null;
		}
	}
}