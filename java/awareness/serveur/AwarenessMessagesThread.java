import java.io.*;
import java.util.*;

public abstract class AwarenessMessagesThread
	extends Thread {
	
	public Vector messages = null;
	
	public AwarenessMessagesThread() {
		super("MessagesThread");
		messages = new Vector();
	}
	
	public void run() {
		String message;
		
		while (messages != null) {
			
			// Si le tableau des messages est vide, il faudra attendre
			if (messages.isEmpty()) {
				synchronized (this) {
					try {
						wait(0);
					} catch (IllegalArgumentException iae) {
						System.out.println("AwarenessMessagesThread@IllegalArgumentException");
					} catch (IllegalMonitorStateException imse) {
						System.out.println("AwarenessMessagesThread@IllegalMonitorStateException");
					} catch (InterruptedException ie) {
						System.out.println("AwarenessMessagesThread@InterruptedException");
					}
				}
			}
			
			if (messages == null)
				break;
			
			try {
				// Récupérer le premier message du tableau
				message = (String)messages.firstElement();
				
				// Retirer ce premier message du tableau
				messages.removeElementAt(0);
				
				// Réagir au message
				dispatchMessage(message);
				
			} catch (Exception e) {
			}
			
			try {
				sleep((long)100);
			} catch (InterruptedException ie) {
			}
		}
	}
	
	public synchronized void addMessage(String message) {
		messages.addElement(message);
		notify();
	}
	
	public abstract void dispatchMessage(String message);
	
	public synchronized void stopMessages() {
		messages = null;
		notify();
	}
}
