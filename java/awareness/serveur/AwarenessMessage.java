import java.util.*;

public class AwarenessMessage
	extends Hashtable {
	
	private String message;
	
	public AwarenessMessage(String _message) {
		
		message = _message;
		
		if (message.startsWith(AwarenessProtocol.CLIENT_MESSAGE_PRIVATE))
			ClientMessagePrivate(AwarenessProtocol.CLIENT_MESSAGE_PRIVATE);
			
		else if (message.startsWith(AwarenessProtocol.UPDATE_USER_LIST))
			UpdateUserList(AwarenessProtocol.UPDATE_USER_LIST);
			
		else if (message.startsWith(AwarenessProtocol.CHANGE_USER_LOCATION))
			UpdateUserLocation(AwarenessProtocol.CHANGE_USER_LOCATION);
			
		else if (message.startsWith(AwarenessProtocol.CHANGE_USER_STATUT))
			UpdateUserStatut(AwarenessProtocol.CHANGE_USER_STATUT);
			
		else if (message.startsWith(AwarenessProtocol.CHANGE_SESSION_PLATEFORM))
			UpdateUserSession(AwarenessProtocol.CHANGE_SESSION_PLATEFORM);
			
		else if (message.startsWith(AwarenessProtocol.CHANGE_USER_TEAM))
			ChangeUserTeam(AwarenessProtocol.CHANGE_USER_TEAM);
		
		else if (message.startsWith(AwarenessProtocol.ADD_USER_LIST))
			AddUserList(AwarenessProtocol.ADD_USER_LIST);
			
		else if (message.startsWith(AwarenessProtocol.SEND_USERS_LIST))
			AwarenessSimpleCommand(AwarenessProtocol.SEND_USERS_LIST);
			
		else if (message.startsWith(AwarenessProtocol.REMOVE_USER_LIST))
			AwarenessSimpleCommand(AwarenessProtocol.REMOVE_USER_LIST);
			
		else if (message.startsWith(AwarenessProtocol.AWARENESS_OK))
			AwarenessSimpleCommand(AwarenessProtocol.AWARENESS_OK);
			
		else if (message.startsWith(AwarenessProtocol.AWARENESS_HELLO))
			AwarenessSimpleCommand(AwarenessProtocol.AWARENESS_HELLO);
	}
	
	public StringTokenizer getValues(String command) {
		return new StringTokenizer(message.substring(command.length()).trim()," ");
	}
	
	public void AwarenessSimpleCommand(String command) {
		StringTokenizer values = getValues(command);
		
		try {
			put("command",command);
			put("nickname",values.nextToken());
			put("session",values.nextToken());
		
		} catch (NoSuchElementException nsee) {
			clear();
		}
	}
	
	/**
	 * AddUserList
	 *
	 * Ajouter un nouvel utilisateur dans la liste
	 *
	 */
	
	public void AddUserList(String command) {
		StringTokenizer values = getValues(command);
		
		try {
			put("command",command);
			put("nickname",values.nextToken());
			put("session",values.nextToken());
			put("username",values.nextToken());
			put("sex",values.nextToken());
			put("language",values.nextToken());
			put("team",values.nextToken());
			put("location",values.nextToken());
			put("statut",values.nextToken());
			
		} catch (NoSuchElementException nsee) {
			clear();
		}
	}
	
	public void ClientMessagePrivate(String command) {
		StringTokenizer values = getValues(command);
		
		try {
			put("command",command);
			put("nickname",values.nextToken());
			put("session",values.nextToken());
			put("recipient",values.nextToken());
			put("message",values.nextToken());
			
		} catch (NoSuchElementException nsee) {
			clear();
		}
	}
	
	public void UpdateUserList(String command) {
		
		StringTokenizer values = getValues(command);
		
		try {
			put("command",command);
			put("nickname",values.nextToken());
			put("session",values.nextToken());
			put("sex",values.nextToken());
			put("team",values.nextToken());
			put("location",values.nextToken());
			put("statut",values.nextToken());
			
		} catch (NoSuchElementException nsee) {
			clear();
		}
	}
	
	public void UpdateUserLocation(String command) {
		StringTokenizer values = getValues(command);
		
		try {
			put("command",command);
			put("nickname",values.nextToken());
			put("session",values.nextToken());
			put("location",values.nextToken());
			
		} catch (NoSuchElementException nsee) {
			clear();
		}
	}
	
	/**
	 * UpdateUserStatut
	 *
	 * Mettre à jour le statut de l'utilisateur
	 *
	 */
	
	public void UpdateUserStatut(String command) {
		StringTokenizer values = getValues(command);
		
		try {
			put("command",command);
			put("nickname",values.nextToken());
			put("session",values.nextToken());
			put("statut",values.nextToken());
			
		} catch (NoSuchElementException nsee) {
			clear();
		}
	}
	
	public void UpdateUserSession(String command) {
		StringTokenizer values = getValues(command);
		
		try {
			put("command",command);
			put("nickname",values.nextToken());
			put("session",values.nextToken());
			put("new_session",values.nextToken());
			
		} catch (NoSuchElementException nsee) {
			clear();
		}
	}
	
	public void ChangeUserTeam(String command) {
		StringTokenizer values = getValues(command);
		
		try {
			put("command",command);
			put("nickname",values.nextToken());
			put("session",values.nextToken());
			put("team",values.nextToken());
			
		} catch (NoSuchElementException nsee) {
			clear();
		}
	}
	
	public String toString() { return message; }
	
	// Commande
	public String getCommand() { return (String)get("command"); }
	
	// Utilisateur
	public String getNickname() { return (String)get("nickname"); }
	public String getUsername() { return (String)get("username"); }
	public String getSex() { return (String)get("sex"); }
	public String getLanguage() { return (String)get("language"); }
	public String getTeam() { return (String)get("team"); }
	public String getStatut() { return (String)get("statut"); }
	
	// Session
	public String getSession() { return (String)get("session"); }
	public void setSession(String s) { put("session",s); }
	public String getNewSession() { return (String)get("new_session"); }
	public String getLocation() { return (String)get("location"); }
	
	// Message
	public String getRecipient() { return (String)get("recipient"); }
	public boolean getComposeMessage() { return true; }
	public String getMessage() { return (String)get("message"); }
}
