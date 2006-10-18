import java.io.*;
import java.net.*;

public abstract class ConnectionListener
	extends Thread {
	
	public long delay;
	public boolean listener;
	
	public ConnectionListener() {
		this((long)5000);
		setName("ConnectionListenerThread");
	}
	
	public ConnectionListener(long delay) {
		this.delay = delay;
	}
	
	public void run() {
		
		listener = true;
		
		while (listener) {
			try {
				// Tentative de connexion
				isConnected();
				connectionSuccessful();
				
			} catch (Exception e) {
				connectionLost();
			}
			
			try {
				sleep(delay);
			} catch (InterruptedException ie) {
			}
		}
	}
	
	public void stopListener() {
		listener = false;
		delay = (long)100;
	}
	
	public abstract void isConnected()
		throws NullPointerException, IOException;
	
	public abstract void connectionSuccessful();
	public abstract void connectionLost();
}

