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
	
  	/** d�marre le serveur avec �coute sur le port sp�cifi� */
  	public synchronized void startServer(int port) throws IOException {
		if (runner == null) {
	       	// cr�e une socket serveur sur le port sp�cifi�
	       	server = new ServerSocket(port);
	       
	       	// cr�e le thread qui va �couter sur le port
	       	runner = new Thread(this);
	       
	       	// lance le thread
	       	runner.start();
			
			// Lancement du serveur log
			startServerLog();
	    }
	}
	
    /**  arr�te le serveur */
    public synchronized void stopServer() throws IOException {
      if (server != null) {
        // arr�te le thread d'�coute
        runner.stop();
        							
        runner = null;
        
        // referme la socket serveur
        server.close();
		stopServerLog();
      }
    }
    
    /** d�marrage du thread (via Runnable) */
	public void run() {
    	// si d�marrage du thread d'�coute)
      	if (server != null) {
        	while (true) {
          		try {
            		// cr�e une socket client lors du retour de accept
					Socket socketClient = server.accept();
					
            		// se clone soi-m�me
            		TCPServer newSocket = (TCPServer)clone();
					
		            // initialise server � null, afin que le thread que l'on va lancer ensuite
		            // soit aiguill� vers la conversation (et non l'�coute)
		            newSocket.server = null;
		            
		            // m�morise la socket de conversation
		            newSocket.data = socketClient;
		            
		            // cr�e le thread de conversation
		            newSocket.runner = new Thread(newSocket);
		            
		            // et d�marre celui-ci
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
			+ (connected ? " Connexion" : " D�connexion");
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