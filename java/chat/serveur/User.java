import java.lang.*;
import java.io.*;
import java.net.*;

public class User {
	
	private String ID;
	private String Plateform;
	private String Room;
	private String Group;
	private String Nick;
	private String CompleteName;
	private char Sex;
	private String DirChatLog;
	private boolean SaveConversation;
	
	protected DataOutputStream os;
	
	public User(DataInputStream is,DataOutputStream os) throws IOException {
		URLDecoder url = new URLDecoder();
		
		this.ID = is.readUTF(); 		// Nom unique
		
		this.Plateform = url.decode(is.readUTF());
		this.Room = url.decode(is.readUTF());
		this.Group = url.decode(is.readUTF());
		
		this.Nick = is.readUTF();
		this.CompleteName = is.readUTF();
		this.Sex = (char)is.readChar();
		
		this.DirChatLog = is.readUTF();
		this.SaveConversation = is.readBoolean();
		
		this.os = os;
	}
	
	public String getID() {
		return ID;
	}
	
	public String getUserKey() {
		return getGroupKey() + ":" + Nick;
	}
	
	public String getGroupKey() {
		return Plateform + ":" + ID + ":" + Group;
	}
	
	public String getPlateform() {
		return Plateform;
	}
	
	public String getRoom() {
		return Room;
	}
	
	public String getGroup() {
		return Group;
	}
	
	public String getNick() {
		return Nick;
	}
	
	public String getCompleteName() {
		return CompleteName;
	}
	
	public char getSex() {
		return Sex;
	}
	
	public String getDirChatLog() {
		return DirChatLog;
	}
	
	public boolean getSaveConversation() {
		return SaveConversation;
	}
	
	public synchronized boolean sendMessage(String message) {
		try {
			os.writeUTF(message);
			os.flush();
			
		} catch (IOException ioe) {
			System.out.println("Message non envoyé: "
				+ message
				+ " à " + CompleteName);
			
			return false;
		}
		
		return true;
	}
	
	public void clear() {
		if (os != null) {
			try {
				os.close();
			} catch (IOException ioe) {
			}
		}
		
		ID = null;
		Plateform = null;
		Room = null;
		Group = null;
		Nick = null;
		CompleteName = null;
		DirChatLog = null;
		os = null;
	}
}
