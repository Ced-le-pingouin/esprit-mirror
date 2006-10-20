import java.applet.*;
import java.awt.*;
import java.awt.event.*;
import java.io.*;
import java.util.*;
import java.net.*;

public class AwarenessAppletClient
	extends Applet 
	implements Runnable {
	
	public Thread thread;
	
	public MediaTracker tracker;
	public Image[] images;
	
	public Cursor defaultCursor;
	public Cursor handCursor;
	
	public boolean isRunnable;
	public boolean isTranslated;
	
	private static String spy_location;
	
	public void start() {
		setName("AwarenessAppletClient");
		
		// Gestionnaire d'images
		tracker = new MediaTracker(this);
		
		images = new Image[2];
		images[0] = getImage(getCodeBase(),"images/non_oeil.jpg");
		images[1] = getImage(getCodeBase(),"images/oeil.jpg");
		
		tracker.addImage(images[0],0);
		tracker.addImage(images[1],0);
		
		try {
			tracker.waitForID(0);
			
		} catch (InterruptedException ie) {
		}
		
		repaint();
		
		isRunnable = true;
		
		// Définir les différents curseurs de la souris
		defaultCursor = new Cursor(Cursor.DEFAULT_CURSOR);
		handCursor = new Cursor(Cursor.HAND_CURSOR);
		
		addMouseListener(new MouseAdapter() {
			public void mouseClicked(MouseEvent e) {
				if (AwarenessClient.ac_isConnected && isTranslated) {
					try {
						AwarenessClient.showListConnected();
					} catch (NoClassDefFoundError ncdfe) {
					}
				}
			}
		});
		
		// Lancer l'applet
		thread = new Thread(this);
		
		if (thread != null)
			thread.start();
	}
	
	public void run() {
		while (isRunnable) {
			repaint();
			
			if (!isTranslated)
				loadTranslations();
			
			try {
				thread.sleep(2000);
			} catch (InterruptedException ie) {
			}
		}
	}
	
	public void loadTranslations() {
		
		String language = new String(getParameter("language")).trim();
		spy_location = getParameter("location");
		
		try {
			AwarenessClient.loadTranslations(language,getCodeBase());
			
			if (language != null && language.length() > 0) {
				for (Enumeration e=AwarenessClient.i18n.keys(); e.hasMoreElements(); ) {
					String key = (String)e.nextElement();
					
					if (key != null && key.startsWith("TXT_")) {
						String value = getParameter(key);
						
						if (value != null)
							AwarenessClient.i18n.add(key,value);
					}
				}
				
				isTranslated = (AwarenessClient.i18n.size() > 0);
			}
			
		} catch(Exception e) {
			isTranslated = false;
			return;
		}
			
		// Réactualiser la traduction
		AwarenessClient.refreshLabels();
	}
	
	public void sendChangeOfPlace(String _location) {
		try {
			AwarenessClient.sendNewUserPosition(_location);
		} catch (NoClassDefFoundError ncdfe) {
		}
	}
	
	public void update(Graphics g) {
		paint(g);
	}
	
	public void paint(Graphics g) {
		
		boolean isConnected = false;
		
		try {
			isConnected = AwarenessClient.ac_isConnected & isTranslated;
		} catch (NoClassDefFoundError ncdfe) {
		}
		
		if (isConnected) {
			setCursor(handCursor);
			g.drawImage(images[1],0,0,this);
		} else {
			setCursor(defaultCursor);
			g.drawImage(images[0],0,0,this);
		}
	}
	
	public void stop() {
		isRunnable = false;
		
		if (thread != null) {
			try {
				thread.join();
			} catch (InterruptedException ie) {
			}
			
			thread = null;
		}
		
		defaultCursor = null;
		handCursor = null;
		
		thread = null;
		tracker = null;
		images = null;
	}
}
