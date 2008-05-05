import java.io.*;
import java.net.*;

public class AutoSendMessage extends Thread {
	
	private int nbCaracteres;
	private long millis;
	private int nbTimes;
	private DataOutputStream dos;
	private String message;
	private String nickname;
	
	public AutoSendMessage() {
		nbCaracteres = 5; // 5 x 64 cararctères
		millis = (long)1000;
		nbTimes = 10;
	}
	
	public AutoSendMessage(DataOutputStream dataoutputstream, String nick)
	{
		this();
		dos = dataoutputstream;
		nickname = nick;
	}
	
	private void composeMessage() {
		String text = new String("abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ1234567890");
		
		message = "[" + nickname + "]";
		
		for (int i=0; i<nbTimes; i++)
			message += text;
		
		message += "\r\n";
	}
	
	private void sendMessage(String msg) throws IOException {
		dos.writeUTF(msg);
		dos.flush();
	}
	
	public void run() {
		int nbTimesActuel = 0;
		
		composeMessage();
		
		try {
			while (nbTimes > nbTimesActuel) {
				nbTimesActuel++;
				
				sendMessage(message);
				
				if (nbTimes == nbTimesActuel) {
					sendMessage("[" + nickname + "]" 
						+ nbTimes + " messages à bien été envoyé au serveur chat\n");
				}
				
				Thread.sleep(millis);
			}
		} catch (InterruptedException ie) {
		} catch (IOException ioe) {
		}
	}
}
