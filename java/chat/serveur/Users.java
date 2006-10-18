import java.util.*;
import java.io.IOException;

public class Users {
	
	private Hashtable users;
	private Connected connected;
	
	public Users() {
		users = new Hashtable();
		connected = new Connected();
	}
	
	public int size()
	{
		return users.size();
	}
	
	public synchronized boolean exists(User user) {
		User user_ = null;
		
		try {
			user_ = (User)users.get(user.getUserKey());
		} catch (NullPointerException npe) {
		}
		
		return (user_ != null);
	}
	
	public boolean add(User user) {
		if (exists(user))
			return false;
		
		users.put(user.getUserKey(), user);
		
		// Ajouter le connecté dans la liste
		connected.add(user);
		
		try {
			sendWelcome(user);
		} catch (IOException ioe) {
		}
		
		return true;
	}
	
	public boolean remove(User user) {
		if (user != null && users != null) {
			if ((users.remove(user.getUserKey())) != null) {
				
				// Supprimer le connecté de la liste
				connected.remove(user);
				
				try {
					sendGoodbye(user);
				} catch (IOException ioe) {
				}
				
				return true;
			}
		}
		
		return false;
	}
	
	public User getUser(String userKey) {
		User user_ = null;
		
		synchronized (users) {
			try {
				user_ = (User)users.get(userKey);
			} catch (NullPointerException npe){
			}
		}
		
		return user_;
	}
	
	public Vector getGroup(String groupKey) {
		Vector group = new Vector();
		
		synchronized (users) {
			for (Enumeration e=users.elements(); e.hasMoreElements(); )
			{
				User user_ = (User)e.nextElement();
				
				if (user_.getGroupKey().equals(groupKey))
					group.addElement(user_);
			}
		}
		
		return group;
	}
	
	public void sendMessage(User user, String message)
		throws IOException {
		
		if (message.startsWith("<MSGPRIVATE:")) {
			sendMessagePrivate(user, message);
		} else if (message.startsWith("<SPIRIT_TO_WRITE>")) {
			sendMsgSpiritToWrite(getGroup(user.getGroupKey()), message);
		} else {
			sendMessageGroup(getGroup(user.getGroupKey()), message);
		}
	}
	
	public synchronized void sendMessageGroup(Vector group, String message)
		throws IOException {
		
		synchronized (group) {
			for (Enumeration e = group.elements(); e.hasMoreElements(); )
				((User)e.nextElement()).sendMessage(message);
		}
	}
	
	public synchronized void sendWelcome(User user)
		throws IOException {
		
		try {
			user.sendMessage("<WELCOME>" + user.getNick());
			sendListConnected(getGroup(user.getGroupKey()));
		} catch (IOException ioe) {
			System.out.println("ChatServer::sendWelcome@error");
		}
	}
	
	public synchronized void sendGoodbye(User user)
		throws IOException {
		
		Vector group = getGroup(user.getGroupKey());
		String message = "<GOODBYE>" + user.getNick();
		
		if (group.size() > 0)
			try {
				sendMessageGroup(group, message);
				sendListConnected(group);
			} catch (IOException ioe) {
				System.out.println("ChatServer::sendWelcome@error");
			}
	}
	
	private synchronized void sendMessagePrivate(User user, String message)
		throws IOException {
		
		int posMessage = message.indexOf(">");
		String userKey = user.getGroupKey()
			+ ":"
			+ message.substring("<MSGPRIVATE:".length(),posMessage++); // Pseudo
		
		User recipient = getUser(userKey);
		
		if (recipient != null)
			recipient.sendMessage("<MSGPRIVATE>" + message.substring(posMessage));
	}
	
	private synchronized void sendMsgSpiritToWrite(Vector group, String message)
		throws IOException {
		
		if (message.substring("<SPIRIT_TO_WRITE>".length()).lastIndexOf("+") > -1) {
			sendMessageGroup(group,message.replace('+',' ').trim());
		} else {
			sendMessageGroup(group,message);
		}
	}
	
	protected synchronized void sendListConnected(Vector group)
		throws IOException {
		
		String message = new String("<CLEARLIST>");
		
		for (Enumeration e = group.elements(); e.hasMoreElements(); )
			message += "<MEMBER>" + ((User)e.nextElement()).getNick();
		
		sendMessageGroup(group,message);
	}
}
