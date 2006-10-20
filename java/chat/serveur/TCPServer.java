import java.net.*;
import java.io.*;

public class TCPServer implements Cloneable, Runnable
{
	/** thread de connexion au client */
  	protected Thread runner = null;
	
	/** Socket serveur */
  	protected ServerSocket server = null;
	
	/** socket de connexion client */
  	protected Socket data = null;
	
	protected ServerLog serverLog = new ServerLog();
	
  	/** démarre le serveur avec écoute sur le port spécifié */
  	public synchronized void startServer(int port) throws IOException {
		if (runner == null) {
	       	// crée une socket serveur sur le port spécifié
	       	server = new ServerSocket(port);
	       
	       	// crée le thread qui va écouter sur le port
	       	runner = new Thread(this);
	       
	       	// lance le thread
	       	runner.start();
			
			// Lancement du serveur log
			startServerLog();
	    }
	}
	
    /**  arrête le serveur */
    public synchronized void stopServer() throws IOException {
      if (server != null) {
        // arrête le thread d'écoute
        runner.stop();
        							
        runner = null;
        
        // referme la socket serveur
        server.close();
		stopServerLog();
      }
    }
    
    /** démarrage du thread (via Runnable) */
	public void run() {
    	// si démarrage du thread d'écoute)
      	if (server != null) {
        	while (true) {
          		try {
            		// crée une socket client lors du retour de accept
					Socket socketClient = server.accept();
					
            		// se clone soi-même
            		TCPServer newSocket = (TCPServer)clone();
					
		            // initialise server à null, afin que le thread que l'on va lancer ensuite
		            // soit aiguillé vers la conversation (et non l'écoute)
		            newSocket.server = null;
		            
		            // mémorise la socket de conversation
		            newSocket.data = socketClient;
		            
		            // crée le thread de conversation
		            newSocket.runner = new Thread(newSocket);
		            
		            // et démarre celui-ci
		            newSocket.runner.start();
          		} catch (Exception e) {
				}
        	}
      	} else {
        	// lance la version de run() qui traite la conversation
        	run(data);
      	}
    }
	
    public void run(Socket data) {
    }
	
	public void startServerLog() throws IOException {
		DateChat dc = new DateChat();
		
		try {
			serverLog.write("[" + dc.getDate() + "]"
				+ "[" + dc.getTime() + "]"
				+ "[" + server.getLocalPort() + "]"
				+ " -- Lancement du serveur chat");
		} catch (IOException ioe){
			System.out.println("Impossible de ouvrir le fichier de journalisation");
		}
	}
	
	public void userServerLog(User user,boolean connected) {
		String log;
		DateChat dc = new DateChat();
		log = "[" + dc.getDate() + "]"
			+ "[" + dc.getTime() + "]"
			+ "[" 
			+ user.getCompleteName() + " (" + user.getNick() + ")"
			+ ":" + user.getPlateform()
			+ ":" + user.getRoom()
			+ ":" + user.getGroup()
			+ "]"
			+ (connected ? " Connexion" : " Déconnexion");
		writeServerLog(log);
	}
	
	public void writeServerLog(String s) {
		try {
			serverLog.write(s);
		} catch (IOException ioe) {
			System.out.println("TCPServer::writeServerLog@erreur");
		}
	}
	
	public void stopServerLog() throws IOException {
		DateChat dc = new DateChat();
		
		serverLog.write("[" + dc.getDate() + "]"
			+ "[" + dc.getTime() + "]"
			+ "[" + server.getLocalPort() + "]"
			+ " Fermeture du serveur chat");
	}
}