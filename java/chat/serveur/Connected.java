import java.io.*;
import java.util.*;

public class Connected {
	
	private final static String CRLF = "\r\n";
	private ResourceBundle prop = ResourceBundle.getBundle("deltachat");
	
	public Connected() {
	}
	
	public Enumeration getConnected(User user) {
		FileInputStream fis = null;
		Vector v = new Vector();
		
		try {
			fis = new FileInputStream(getAbsoluteFile(user));
		} catch (FileNotFoundException fnfe) {
		}
		
		if (fis != null) {
			try {
				int b;
				StringBuffer userKey = new StringBuffer("");
				
				while ((b = fis.read()) != -1) {
					if ((char)b == '\r') {
						continue;
					} else if ((char)b == '\n') {
						v.addElement(userKey.toString());
						userKey = new StringBuffer("");
					} else {
						userKey.append((char)b);
					}
				}
			} catch (IOException ioe1) {
			}
			
			try {
				fis.close();
			} catch (IOException ioe2) {
			}
		}
		
		return v.elements();
	}
	
	private String getAbsoluteFile(User user) {
		return prop.getString("DOCUMENT_ROOT") + user.getDirChatLog() + user.getID();
	}
	
	public boolean exists(User user) {
		Enumeration e = getConnected(user);
		String userKey = user.getUserKey();
		
		while (e.hasMoreElements()) {
			String userKey_ = (String)e.nextElement();
			if (userKey.startsWith(userKey_))
				return true;
		}
		
		return false;
	}
	
	public synchronized void add(User user) {
		if (!exists(user)) {
			FileOutputStream fos = null;
			String text = user.getUserKey() + CRLF;
			
			try {
				fos = new FileOutputStream(getAbsoluteFile(user),true);
			} catch (FileNotFoundException fnfe){
			} catch (IOException ioe1){
			}
			
			if (fos != null) {
				try {
					fos.write(text.getBytes());
					fos.flush();
				} catch (IOException ioe2) {
				}
				
				try {
					fos.close();
				} catch (IOException ioe3) {
				}
			}
		}
	}
	
	public synchronized void remove(User user) {
		FileOutputStream fos = null;
		Enumeration e = getConnected(user);
		String userKey = user.getUserKey();
		String text = new String("");
		
		while (e.hasMoreElements()) {
			String userKey_ = (String)e.nextElement();
			if (!userKey.startsWith(userKey_))
				text += userKey_ + CRLF;
		}
		
		// Effacer complètement le fichier
		new File(getAbsoluteFile(user)).delete();
		
		if (text.length() > 0) {
			try {
				fos = new FileOutputStream(getAbsoluteFile(user),true);
			} catch (FileNotFoundException fnfe){
			} catch (IOException ioe1){
			}
			
			if (fos != null) {
				try {
					fos.write(text.getBytes());
					fos.flush();
				} catch (IOException ioe2) {
				}
				
				try {
					fos.close();
				} catch (IOException ioe3) {
				}
			}
		}
	}
}
