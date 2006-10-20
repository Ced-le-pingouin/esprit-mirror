/**
 * @author PORCO Filippo (filippo.porco@umh.ac.be)
 * @created (28/04/2003)
 * @modifies (30/04/2003)
 *
 * Unité de Technologie de l'Education
 * Place du Parc, 18
 * 7000 MONS
 *
**/

import java.io.*;
import java.lang.*;
import java.util.*;

public class ClientFileNameLog {
	
	private String fileName;
	private String fileExtension = "txt";
	private String clientFileNameLog;
	
	private String dirLog = "chatlog";
	private ResourceBundle prop = ResourceBundle.getBundle("deltachat");
	
	public ClientFileNameLog() {
	}
	
	public ClientFileNameLog(String nameLog) {
		setName(nameLog);
	}
	
	public ClientFileNameLog(String dirLog,String nameLog) {
		setDirectory(dirLog);
		setName(nameLog);
	}
	
	public void setName(String nameLog) {
		this.fileName = nameLog;
		clientFileNameLog = getUniqueName();
	}
	
	public void setFileExtension(String fileExtension) {
		this.fileExtension = fileExtension;
		clientFileNameLog = getUniqueName();
	}
	
	public String getName() {
		return clientFileNameLog;
	}
	
	public String getDirname() {
		return prop.getString("DOCUMENT_ROOT");
	}
	
	public void setDirectory(String dirLog) {
		this.dirLog = dirLog;
	}
	
	public String getDirectory() {
		return getDirname() 
			+ (dirLog.length() > 0 ? dirLog : System.getProperty("file.separator"));
	}
	
	private String getUniqueName() {
		// Exemple: chatlog/chat-02042001_124506.txt
		DateChat dc = new DateChat();
		return getDirectory()
			+ this.fileName
			+ dc.getDateFormat("ddMMyyyy") + "_" + dc.getDateFormat("HHmmss")
			+ "." + this.fileExtension;
	}
}
