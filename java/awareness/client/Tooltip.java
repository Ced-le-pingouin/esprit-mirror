import java.awt.*;
import java.awt.event.*;

public class Tooltip
	extends Container {
	
	public Window win;
	public boolean isVisible;
	
	public Tooltip(Frame parent,String textTooltip) {
		win = new Window(parent);
		win.setBackground(new Color(255,255,210));
		win.add(new Label(textTooltip));
		win.pack();
		
		isVisible = false;
	}
	
	public Tooltip(String textTooltip) {
		win = new Window(new Frame());
		win.setBackground(new Color(255,255,210));
		win.setBounds(10,10,250,50);
		win.add(new Label(textTooltip));
	}
	
	public synchronized Point getLocationOfComponent(Component component) {
		Component next = component;
		Point point = new Point(0,0);
		
		while (next != null) {
			if (next instanceof Frame) {
				Point location = next.getLocation();
				point.translate(location.x,location.y);
				
				return point;
			}
			
			next = next.getParent();
		}
		
		return null;
	}
	
	public synchronized void move(Point point) {
		if (win != null) {
			Point location = getLocationOfComponent(win);
			point.translate(location.x,location.y);
			win.setLocation(point);
		}
	}
	
	public void show(Point point) {
		if (win != null) {
			Point location = getLocationOfComponent(win);
			point.translate(location.x,location.y);
			show(point.x,point.y);
			isVisible = true;
		}
	}
	
	public void show(int x, int y) {
		win.setLocation(x,y);
		win.show();
	}
	
	public void hide() {
		win.hide();
		isVisible = false;
	}
}