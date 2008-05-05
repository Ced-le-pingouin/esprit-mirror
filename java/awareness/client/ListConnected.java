import java.awt.*;
import java.awt.event.*;
import java.util.*;
import javax.accessibility.*;

public class ListConnected
	extends Container {
	
	public AwarenessClient awarenessCient;
	
	public Dimension preferredSize;
	
	public ListConnected() {
		setName("ListConnected");
		setLayout(null);
		setVisible(true);
		setBackground(Color.white);
		
		preferredSize = new Dimension(1,1);
		awarenessCient = null;
	}
	
	public ListConnected(AwarenessClient parent) {
		this();
		awarenessCient = parent;
	}
	
	public Dimension getPreferredSize() {
		return preferredSize();
	}
	
	public void setPreferredSize(Dimension preferredSize) {
		this.preferredSize = preferredSize;
	}
	
	public Dimension preferredSize() {
		return preferredSize;
	}
	
	/**
	 * Ajouter un nouveau connecté
	 */
	
	public synchronized void addItem(ConnectedItem connected) {
		String nickname = connected.getNickname();
		
		if (nickname == null || nickname.length() < 1)
			return;
		
		int index = indexOfItem(nickname);
		
		if (index != -1) {
			// Supprimer de la table
			remove(index);
			
			// Replacer au même endroit
			add(connected,index);
			
			updateItem(index);
			
		} else {
			// Ajouter un nouvel utilisateur
			add(connected);
			repaint();
		}
	}
	
	public void updateItem(int index) {
		// Récupérer la taille/position de l'ancien composant
		Rectangle rect = ((ConnectedItem)getComponent(index)).getBounds();
		
		// Redessiner qu'une portion de la liste
		repaint(rect.x,rect.y,rect.width,rect.height);
	}
	
	public void clearItems() {
		Component[] components = getComponents();
		
		for (int i=components.length-1; i>=0; i--)
			removeItem(((ConnectedItem)components[i]).getNickname());
	}
	
	public synchronized ConnectedItem getItem(String nickname) {
		Component[] components = getComponents();
		
		for (int i=0; i<components.length; i++)
			if (nickname.equals(((ConnectedItem)components[i]).getNickname()))
				return (ConnectedItem)components[i];
		
		return null;
	}
	
	public synchronized ConnectedItem getItem(int index) {
		return (ConnectedItem)getComponent(index);
	}
	
	public synchronized int indexOfItem(String nickname) {
		Component[] components = getComponents();
		
		for (int i=0; i<components.length; i++)
			if (nickname.equals(((ConnectedItem)components[i]).getNickname()))
				return i;
		
		return -1;
	}
	
	/**
	 * Retirer un connecté de la liste
	 */
	
	public synchronized void removeItem(String nickname) {
		Component[] components = getComponents();
		
		for (int i=0; i<components.length; i++) {
			if (nickname.equals(((ConnectedItem)components[i]).getNickname())) {
				remove(components[i]);
				repaint();
				break;
			}
		}
	}
	
	public void update(Graphics g) {
		paint(g);
	}
	
	public void paint(Graphics g) {
		Component[] components = getComponents();
		
		int y = 0;
		int width = 0;
		
		for (int i=0; i<components.length; i++) {
			ConnectedItem connected = (ConnectedItem)components[i];
			
			y += (y > 0 ? ConnectedItem.DEFAULT_HEIGHT : 0) + 5;
			
			if (width == 0)
				width = connected.getSize().width;
			
			connected.setLocation(0,y);
			connected.paint(g);
		}
		
		y += ConnectedItem.DEFAULT_HEIGHT;
		
		if (width == 0)
			width = ConnectedItem.DEFAULT_WIDTH;
		
		setPreferredSize(new Dimension(width-10,y));
	}
}

