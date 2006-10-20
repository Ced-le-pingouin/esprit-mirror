/**
 * @author PORCO Filippo (filippo.porco@umh.ac.be)
 * @created (28/04/2003)
 * @modifies (05/05/2003)
 *
 * Unité de Technologie de l'Education
 * Place du Parc, 18
 * 7000 MONS
 *
**/

import java.io.*;
import java.util.*;

public class ClientLog extends WriterLog {
	
	public ClientLog() {
		super(new ClientFileNameLog("ChatLog-").getName());
	}
	
	public ClientLog(String nameLog) {
		super(new ClientFileNameLog(nameLog).getName());
	}
	
	public ClientLog(String nameLog, String dirLog) {
		super(new ClientFileNameLog(dirLog,nameLog).getName());
	}
	
	public void writeFooter(User user)
		throws IOException {
		
		String footer = user.getPlateform()
			+ ":" + user.getRoom()
			+ ":" + user.getGroup()
			+ ":" + user.getID();
		writeFooter(footer);
	}
	
	public synchronized void writeFooter(String footer)
		throws IOException {
		
		DateChat dc = new DateChat();
		String lineFooter = (footer.length() > 0 ? "[" + footer + "]" : "")
			+ "[" + dc.getDate() + "]"
			+ "[" + dc.getTime() + "]";
		write(lineFooter);
	}
	
	public synchronized void writeMessage(String message,User user)
		throws IOException {
		
		DateChat dc = new DateChat();
		write("[" + dc.getTime() + "]"
			+ "[" + java.net.URLDecoder.decode(user.getCompleteName()) + "]"
			+ message);
	}
}

