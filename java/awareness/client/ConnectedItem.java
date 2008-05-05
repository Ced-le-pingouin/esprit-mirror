import java.awt.*;
import java.awt.event.*;

public class ConnectedItem
	extends Component {
	
	public final static int DEFAULT_WIDTH = 250;
	public final static int DEFAULT_HEIGHT = 55;
	
	public MouseListener mouseListener = null;
	
	public String nickname;
	public Image imageSex;
	public Image imageBusy;
	public String team;
	public String location;
	public int statut;
	
	public Tooltip tooltip;
	
	public Font nicknameFont;
	public Font teamFont;
	public Font locationFont;
	
	public ConnectedItem() {
		// Attribuer les polices de caractères
		nicknameFont = new Font("Dialog",Font.BOLD,12);
		teamFont = new Font("Dialog",Font.ITALIC,10);
		locationFont = new Font("Dialog",Font.PLAIN,12);
		
		tooltip = null;
	}
	
	public ConnectedItem(String nickname,Image imageSex,Image imageBusy,String team,String location,int statut) {
		this();
		
		setName(nickname);
		
		/*addMouseMotionListener(new MouseMotionAdapter() {
			public void mouseMoved(MouseEvent e) {
				if (tooltip != null) {
					tooltip.move(new Point(e.getX(),e.getY()));
				}
			}	
		});*/
		
		this.nickname = nickname;
		this.imageSex = imageSex;
		this.imageBusy = imageBusy;
		this.team = team;
		this.location = location;
		this.statut = statut;
	}
	
	public void setStatut(int i) { statut = i;	}
	public int getStatut() { return statut;	}
	
	public void setTeam(String s) { team = s; }
	public void setTeamFont(Font font) { teamFont = font; }
	
	public void setUserLocation(String s) { location = s; }
	public void setLocationFont(Font font) { locationFont = font; }
	
	public String getUserLocation() { return location; }
	
	public String getNickname() { return nickname; }
	
	public void setNicknameFont(Font font) {
		nicknameFont = font;
	}
	
	public void addTooltip(Tooltip tooltip) {
		this.tooltip = tooltip;
	}
	
	public synchronized void addMouseListener(MouseListener l) {
	   mouseListener = AWTEventMulticaster.add(mouseListener,l);
	   enableEvents(AWTEvent.MOUSE_EVENT_MASK);
	   setCursor(new Cursor(Cursor.HAND_CURSOR));
	}
	
	public synchronized void removeActionListener(MouseListener l) {
		mouseListener = AWTEventMulticaster.remove(mouseListener,l);
		disableEvents(AWTEvent.MOUSE_EVENT_MASK);
		setCursor(new Cursor(Cursor.DEFAULT_CURSOR));
    }
	
	public void processMouseEvent(MouseEvent me) {
		if (mouseListener != null) {
			switch (me.getID()) {
				case MouseEvent.MOUSE_CLICKED:
					mouseListener.mouseClicked(me);
					break;
				case MouseEvent.MOUSE_ENTERED:
					
					if (tooltip != null)
						tooltip.show(new Point(50,(int)(getBounds().y+getBounds().height)));
					
					mouseListener.mouseEntered(me);
					
					break;
				case MouseEvent.MOUSE_EXITED:
					if (tooltip != null)
						tooltip.hide();
					
					mouseListener.mouseExited(me);
					
					break;
				case MouseEvent.MOUSE_PRESSED:
					mouseListener.mousePressed(me);
					break;
				case MouseEvent.MOUSE_RELEASED:
					mouseListener.mouseReleased(me);
					break;
			}
		}
	}
	
	public void update(Graphics g) {
		paint(g);
	}
	
	public void paint(Graphics g) {
		
		String tmp;
		Color color = (statut == AwarenessProtocol.USER_STATUT_AVAILABLE ? Color.black : new Color(177,193,203));
		int y = getLocation().y;
		
		Dimension dimension = getPreferredSize();
		
		setSize(dimension);
		
		// Afficher une image
		g.drawImage(imageSex,5,y+10,this);
		
		if (statut != AwarenessProtocol.USER_STATUT_AVAILABLE)
			g.drawImage(imageBusy,imageSex.getWidth(this)-5,y+imageSex.getHeight(this),this);
		
		// Afficher le pseudo
		g.setFont(nicknameFont);
		g.setColor(color);
		y = y + (g.getFontMetrics()).getHeight();
		g.drawString(AwarenessURLDecoder.decode(nickname),30,y);
		
		// Afficher le nom de l'équipe
		y += 15;
		if (!"NULL".equals(team)) {
			g.setFont(teamFont);
			g.drawString(team,30,y);
		}
		
		// Afficher la position du connecté dans la plate-forme
		if (location.startsWith("TXT_"))
			tmp = AwarenessClient.i18n.getString(location);
		else
			tmp = location;
		
		y += 15;
		if (!"NULL".equals(tmp)) {
			g.setFont(locationFont);
			g.drawString(AwarenessURLDecoder.decode(tmp),30,y);
		}
		
		// Dessiner une ligne droite horizontale
		g.setColor(new Color(177,193,203));
		y = (getLocation().y + DEFAULT_HEIGHT) - 2;
		g.drawLine(10,y,((int)dimension.width-10),y);
	}
	
	public Dimension getMinimumSize() {
		return new Dimension(DEFAULT_WIDTH,DEFAULT_HEIGHT);
	}
	
	public Dimension getPreferredSize() {
		Container container = getParent();
		
		while (container != null) {
			if (container instanceof ScrollPane) {
				ScrollPane scrollPane = (ScrollPane)container;
				int width = (int)scrollPane.getViewportSize().width;
				return new Dimension(width,DEFAULT_HEIGHT);
			}
			
			container = container.getParent();
		}
		
		return getMinimumSize();
	}
}
