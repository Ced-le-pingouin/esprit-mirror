import java.awt.*;
import java.awt.event.*;
import java.io.*;
import java.net.*;

public class AwarenessChatClient
	extends Frame 
	implements WindowListener {
	
	public TextArea messages;
	public TextArea message;
	public Button send;
	
	public AwarenessClient awarenessClient;
	public String recipient;
	
	public AwarenessChatClient(AwarenessClient parent,String recipient) {
		
		awarenessClient = parent;
		this.recipient = recipient;
		
		setSize(480,210);
		setLayout(new BorderLayout());
		setVisible(false);
		
		// Zone d'affichage de tous les messages
		messages = new TextArea("",1,1,TextArea.SCROLLBARS_VERTICAL_ONLY);
		messages.setBackground(Color.white);
		messages.setEditable(false);
		
		// Zone d'edition
		message = new TextArea("",1,1,TextArea.SCROLLBARS_VERTICAL_ONLY);
		message.setBackground(Color.white);
		message.setEditable(true);
		message.addKeyListener(new KeyAdapter() {
			public void keyPressed(KeyEvent ke) {
				if (!ke.isShiftDown() && ke.getKeyChar() == KeyEvent.VK_ENTER)
					ke.consume();
			}
			
			public void keyReleased(KeyEvent ke) {
				if (!ke.isShiftDown() && ke.getKeyChar() == KeyEvent.VK_ENTER) {
					sendTo();
				}
			}
		});
		
		// Bouton envoyer
		send = new Button("Envoyer");
		send.addActionListener(new ActionListener() {
			public void actionPerformed(ActionEvent ae) {
				sendTo();
			}
		});
		
		Panel panel = new Panel();
		panel.setLayout(new BorderLayout());
		panel.add(send,BorderLayout.EAST);
		panel.add(message,BorderLayout.CENTER);
		
		add(panel,BorderLayout.SOUTH);
		add(messages,BorderLayout.CENTER);
		
		refreshLabels();
		
		addWindowListener(this);
	}
	
	public void refreshLabels() {
		if (awarenessClient.i18n == null || awarenessClient.i18n.size() < 1)
			return;
		
		// Titre principal de la fenêtre
		String title = awarenessClient.i18n.getString("titre_chat");
		int pos = title.indexOf("%nickname%");
		
		setTitle(title.substring(0,pos)
			+ AwarenessURLDecoder.decode(recipient)
			+ title.substring(pos + "%nickname%".length())
		);
		
		// Bouton <ENVOYER>
		send.setLabel(awarenessClient.i18n.getString("bouton_envoyer"));
	}
	
	public void windowActivated(WindowEvent we) {
		message.requestFocus();
	}
	
	public void windowClosed(WindowEvent we) {
	}
	
	public void windowClosing(WindowEvent we) {
		setVisible(false);
	}
	
	public void windowDeactivated(WindowEvent we) {
	}
	
	public void windowDeiconified(WindowEvent we) {
	}
	
	public void windowIconified(WindowEvent we) {
	}
	
	public void windowOpened(WindowEvent we) {
		message.requestFocus();
	}
	
	public String getRecipient() {
		return recipient;
	}
	
	public String parseMessage(String nicknameDst,String _message) {
		if (_message.equals("texte_personne_non_contactable") ||
			_message.equals("texte_personne_deconnecter")) {
			_message = awarenessClient.i18n.getString(_message);
			
			int beginIndex = _message.indexOf("%nickname%");
			
			if (beginIndex != -1) {
				_message = _message.substring(0,beginIndex)
					+ nicknameDst
					+ _message.substring(beginIndex + "%nickname%".length(),_message.length());
			}
		}
		
		return (AwarenessURLDecoder.decode(_message) + "\n");
	}
	
	public void addMessage(String nicknameSrc,String nicknameDst,String _message) {
		messages.append(AwarenessURLDecoder.decode(nicknameSrc) + "> " + parseMessage(nicknameDst,_message));
		message.requestFocus();
	}
	
	public void sendTo() {
		String _message = message.getText().trim();
		
		if (_message.length() > 0) {
			_message = AwarenessProtocol.CLIENT_MESSAGE_PRIVATE
				+ " " + awarenessClient.ac_nickname
				+ " " + awarenessClient.ac_session
				+ " " + recipient
				+ " " + URLEncoder.encode(_message);
			
			try {
				awarenessClient.sendTo(_message);
			} catch (IOException ioe) {
			}
			
			message.setText("");
		}
		
		message.requestFocus();
	}
}
