import java.awt.*;

public class StatusBar extends Canvas {
	
	private String message;
	private Font font;
	private int vOffset;
	
	public StatusBar() {
		super();
		
		message = new String();
		font = new Font("TimesRoman",Font.PLAIN,12);
		FontMetrics fm = getFontMetrics(font);
		vOffset = fm.getDescent() + 4;
		
		disable();
	}
	
	public Dimension minimumSize() {
		return preferredSize();
	}
	
	public Dimension preferredSize() {
		return new Dimension(640,22);
	}
	
	public void paint(Graphics g) {
		g.draw3DRect(1,1,size().width-3,size().height-3,false);
		
		g.setFont(font);
		
		// Afficher le texte
		g.setColor(Color.white);
		g.drawString(message,6,size().height-vOffset);
	}
	
	public void clearStatusBar() {
		message = "";
		repaint();
	}
	
	public void setText(String message) {
		this.message = message;
		repaint();
	}
	
	public String getText() {
		return message;
	}
}

