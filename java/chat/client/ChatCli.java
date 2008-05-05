import java.applet.Applet;
import java.awt.*;
import java.awt.event.*;
import java.io.*;
import java.net.*;
import java.util.*;

public class ChatCli extends Applet
    implements Runnable, FocusListener, ActionListener {
	
	public static final String CRLF = "\r\n";
	
    public StatusBar status;
    public Button BtnSendPublicMessage;
    public Button BtnSendPrivateMessage;
    public TextArea TaPublicMessage;
    public TextArea TaPrivateMessage;
    public java.awt.List lstConnected;
    public TextArea TaEditPublicMessage;
    public TextArea TaEditPrivateMessage;
    public Choice ChcConnected;
    public String Title;
    public Label lbl_private;
	
    protected Color colorRoom;
    protected boolean usePrivateRoom;
    public boolean isConnected;
    
	public String host;
	public int port;
	public Socket socket;
    public DataInputStream dis;
	public DataOutputStream dos;
    public Resource i18n;
    public Thread listen;
	public ClientSpy clientSpy;
    public int Index;
	public boolean alreadyInformed;
	public StringBuffer restoreConversation;
	public ChatCliUser user;
	public Connect connect;
	public int width, height;
	
    public ChatCli() {
        Title = null;
        socket = null;
        listen = null;
        alreadyInformed = false;
		Index = -1;
		clientSpy = null;
		restoreConversation = null;
    }
	
    public Color getColor(String s) {
        int r = 236;
        int g = 233;
        int b = 216;
		
		if (s != null)
			try {
				StringTokenizer stringtokenizer = new StringTokenizer(s, ",");
				
				r = Integer.valueOf((String)stringtokenizer.nextElement()).intValue();
				g = Integer.valueOf((String)stringtokenizer.nextElement()).intValue();
				b = Integer.valueOf((String)stringtokenizer.nextElement()).intValue();
				
			} catch(NullPointerException npe) {
				return new Color(236, 233, 216);
			} catch(NoSuchElementException nsee) {
				return new Color(236, 233, 216);
			}
		
        return new Color(r, g, b);
    }
	
    public void send(String message) {
        try {
            dos.writeUTF(message);
            dos.flush();
			
        } catch(IOException ioe) {
			isConnected = false;
            System.out.println("Message non envoy\351: " + message);
        }
    }
	
    protected void sendMessagePublic(String message)
    {
        message = "[" + user.getNickname() + "] " + message;
		
		send(message);
		
        TaEditPublicMessage.setText("");
		TaEditPublicMessage.requestFocus();
    }
	
    protected void sendMessagePrivate(String message)
    {
		if (!usePrivateRoom)
			return;
		
		// Rajouter des informations au message privé
		message = "[de " + user.getNickname()
			+ " \340 " + ChcConnected.getSelectedItem()
			+ "] "
			+ message;
		
		// Envoyer le message privé au serveur
		send("<MSGPRIVATE:" + ChcConnected.getSelectedItem() + ">" + message);
		
		// Ajouter le nouveau message dans la zone de messages
		TaPrivateMessage.append(message + "\n");
		
		// Effacer la zone d'écriture du message
		TaEditPrivateMessage.setText("");
		TaEditPrivateMessage.requestFocus();
    }
	
    protected void sendMessage() {
		String message = (Index < 0 ? TaEditPublicMessage : TaEditPrivateMessage).getText().trim();
		
		if (isConnected && message.length() > 0)
			if (message.equals("/auto-send-message/"))
				sendAutoMessage();
			else if (Index >= 0)
				sendMessagePrivate(message);
			else
				sendMessagePublic(message);
    }
	
	public void sendKeepAliveReply() {
        send("<KEEP_ALIVE_REPLY>");
    }
	
	private void showSpiritToWrite(String s) {
        if (s.indexOf(user.getNickname()) == -1)
            if (s.lastIndexOf("-") > -1)
                status.setText("");
            else
                status.setText(
					s.substring("<SPIRIT_TO_WRITE>".length())
						+ " "
						+ getI18N("message_to_compose")
				);
    }
	
	public void sendSpiritToWrite() {
        int i = TaEditPublicMessage.getText().trim().length();
		
        send("<SPIRIT_TO_WRITE>" + user.getNickname() + (i < 1 ? "-" : "+"));
    }
	
	public void actionPerformed(ActionEvent actionevent) {
        Index = -1;
		
        if (actionevent.getSource() == BtnSendPublicMessage ||
			actionevent.getSource() == BtnSendPrivateMessage) {
			
            if (actionevent.getSource() == BtnSendPrivateMessage)
                Index = ChcConnected.getSelectedIndex();
			
            sendMessage();
            sendSpiritToWrite();
        }
    }
	
	protected void connectedNow() {
		try {
			if (listen == null) {
				host = getParameter("hostname");
				port = Integer.parseInt(getParameter("port"));
				
				int port_spy = Integer.parseInt(getParameter("port_spy"));
				
				if (port_spy > 1024)
				{
					clientSpy = new ClientSpy(host, port_spy);
					
					clientSpy.put("id_session", getParameter("id_session"));
					clientSpy.put("nickname", user.getNickname());
					clientSpy.put("location", getParameter("location"));
				}
				
				// Tentative de connexion
				connect = new Connect();
				connect.start();
				
			} else {
				throw new Exception("Fin");
			}
			
        } catch(Exception e) {
            System.out.println("Deconnexion en cours...");
			
            isConnected = false;
            lstConnected.removeAll();
			
			if (e.getMessage() != null && e.getMessage().equals("Fin"))
				TaPublicMessage.setText(getI18N("error_fatal"));
			else
                TaPublicMessage.setText(
					getI18N("error_connection_failed")
				);
        }
    }
	
	private void sendAutoMessage() {
		new AutoSendMessage(dos, user.getNickname()).start();
		TaEditPublicMessage.setText("");
		TaEditPublicMessage.requestFocus();
	}
	
    private Enumeration split(String s, String s1) {
        int i;
        Vector vector = new Vector();
		
        while ((i = s.indexOf(s1)) != -1) 
        {
            s = s.substring(i + s1.length());
            if ((i = s.indexOf(s1)) != -1)
                vector.addElement(s.substring(0, i));
            else
                vector.addElement(s);
        }
        return vector.elements();
    }
	
    private synchronized void updateListUsers(String s) {
        if (s.startsWith("<CLEARLIST>")) {
            lstConnected.removeAll();
			
			if (usePrivateRoom)
				ChcConnected.removeAll();
			
            s = s.substring("<CLEARLIST>".length());
        }
		
        if (s.startsWith("<MEMBER>")) {
            for(Enumeration e = split(s, "<MEMBER>"); e.hasMoreElements();) {
				String nickname = (String)e.nextElement();
				
				lstConnected.add(nickname);
				
				if (usePrivateRoom && !nickname.equals(user.getNickname()))
					ChcConnected.add(nickname);
            }
        }
		
		// Activer/Desactiver l'utilisation du salon privé
		if (usePrivateRoom) {
			boolean bEnabled = (ChcConnected.getItemCount() > 0);
			
			TaEditPrivateMessage.setEnabled(bEnabled);
			BtnSendPrivateMessage.setEnabled(bEnabled);
			ChcConnected.setEnabled(bEnabled);
		}
    }
	
	public void notifyServer() {
		if (clientSpy != null) {
			try {
				AwarenessClient.sendNewUserPosition((String)getParameter("location"));
				alreadyInformed = true;
			} catch (Exception e) {
			}
		}
	}
	
	public void focusGained(FocusEvent fe) {
		notifyServer();
	}
	
	public void focusLost(FocusEvent fe) {
		alreadyInformed = false;
	}
	
	public String getI18N(String key) {
		String s = i18n.getString(key);
		int pos = s.indexOf("%nickname%");
		
		if (pos == -1)
			return s;
		else
			return s.substring(0,pos)
				+ user.getNickname()
				+ s.substring(pos + "%nickname%".length());
	}
	
	public void run() {
		String message;
		
		componentsPublicEnabled(true);
		
		try {
			TaEditPublicMessage.requestFocus();
			
			while (isConnected) {
				message = dis.readUTF();
				
				if (message.startsWith("<WELCOME>")) {
					if (TaPublicMessage.getText().length() == 0)
						TaPublicMessage.append(
							getI18N("message_welcome")
							+ CRLF
						);
				} else if (message.startsWith("<GOODBYE>")) {
					TaPublicMessage.append(
						message.substring("<GOODBYE>".length())
							+ " "
							+ getI18N("message_user_disconnected")
							+ CRLF
					);
				} else if (message.startsWith("<CLEARLIST>") ||
					message.startsWith("<MEMBER>")) {
					updateListUsers(message);
				} else if (usePrivateRoom && message.startsWith("<MSGPRIVATE")) {
					addMessagePrivate(message);
				} else if (message.startsWith("<KEEP_ALIVE_REQUEST>")) {
					sendKeepAliveReply();
				} else if (message.startsWith("<SPIRIT_TO_WRITE>")) {
					showSpiritToWrite(message);
				} else {
					addMessagePublic(message);
				}
			}
			
		} catch (Exception e) {
		}
		
		if (isConnected) {
			connect = new Connect();
			connect.start();
		}
    }
	
	private void addMessagePublic(String message) {
		TaPublicMessage.append(message + CRLF);
	}
	
	private void addMessagePrivate(String message) {
		TaPrivateMessage.append(message.substring("<MSGPRIVATE>".length()) + CRLF);
	}
	
	public void launchChat() {
		
		// Lancer la discussion
		try {
			dos.writeUTF(getParameter("ID"));
			dos.writeUTF(user.getPlateform());
			dos.writeUTF(user.getRoom());
			dos.writeUTF(user.getGroup());
			dos.writeUTF(user.getNickname());
			dos.writeUTF(user.getUser());
			dos.writeChar(user.getSex());
			dos.writeUTF(getParameter("dir_client_log"));
			dos.writeBoolean(Boolean.valueOf(getParameter("to_file_conversation")).booleanValue());
			dos.writeUTF(getParameter("command"));
			dos.flush();
			
		} catch (IOException ioe) {
		}
		
		listen = new Thread(this);
		listen.start();
	}
	
	private void componentsPublicEnabled(boolean enabled) {
		
		TaEditPublicMessage.setEnabled(enabled);
		BtnSendPublicMessage.setEnabled(enabled);
		
		if (enabled) {
			if (restoreConversation != null &&
				restoreConversation.length() > 0) {
				// Restaurer la conversation
				TaPublicMessage.setText(restoreConversation.toString());
				restoreConversation = null;
			} else {
				TaPublicMessage.setText("");
			}
			
		} else {
			if (TaPublicMessage.getText().length() > 0) {
				// Sauvegarder la conversation dans un buffer
				restoreConversation = new StringBuffer(TaPublicMessage.getText());
			}
			
			TaPublicMessage.setText(getI18N("error_connection_interrupted"));
			TaEditPublicMessage.setText("");
			lstConnected.removeAll();
		}
	}
	
	private void componentsPrivateEnabled(boolean enabled) {
		
		if (!usePrivateRoom)
			return;
		
		TaEditPrivateMessage.setEnabled(enabled);
		TaEditPrivateMessage.setText("");
		
		BtnSendPrivateMessage.setEnabled(enabled);
		
		ChcConnected.setEnabled(enabled);
		
		if (enabled) {
			TaEditPrivateMessage.requestFocus();
		} else {
			ChcConnected.removeAll();
		}
	}
	
	class Connect extends Thread {
		
		public void run() {
			isConnected = false;
			
			componentsPublicEnabled(false);
			componentsPrivateEnabled(false);
			
			while (!isConnected) {
				TaPublicMessage.append(".");
				
				try {
					socket = new Socket(host, port);
					dis    = new DataInputStream(new BufferedInputStream(socket.getInputStream()));
					dos    = new DataOutputStream(new BufferedOutputStream(socket.getOutputStream()));
					
					isConnected = true;
					
					continue;
					
				} catch (UnknownHostException uhe) {
				} catch (IOException ioe) {
				} catch (SecurityException se) {
				}
				
				try {
					sleep(1000);
					System.out.println("Tentative de reconnexion pour " + user.getNickname());
				} catch (InterruptedException ie) {
				}
			}
			
			launchChat();
		}
	}
	
    private void streamsClean() {
		
		isConnected = false;
		
		if (dis != null)
			try {
				dis.close();
			} catch(IOException ioe1) {
			}
		
		dis = null;
		
		if (dos != null)
			try {
				dos.close();
			} catch(IOException ioe2) {
			}
		
		dos = null;
		
		if (socket != null)
			try {
				socket.close();
			} catch(IOException ioe3) {
			}
		
		socket = null;
		
		connect = null;
		
		if (listen != null)
			for(; listen.isAlive(); Thread.yield());
		
		listen = null;
	}
	
	public synchronized void stop() {
		streamsClean();
    }
	
    private Panel getPublicRoom() {
		
        TaPublicMessage = new TextArea("", 0, 0, 1);
        TaPublicMessage.setBounds(10, 12, 450, 210);
        TaPublicMessage.setEditable(false);
        TaPublicMessage.setBackground(Color.white);
        TaPublicMessage.addFocusListener(this);
		
		lstConnected = new java.awt.List(0, false);
        lstConnected.setBounds(465, 12, 125, 210);
        lstConnected.setBackground(Color.white);
        lstConnected.addFocusListener(this);
		
		TaEditPublicMessage = new TextArea("", 0, 0, 1);
        TaEditPublicMessage.setBounds(10, 230, 500, 52);
        TaEditPublicMessage.setBackground(Color.white);
		TaEditPublicMessage.setEnabled(false);
        TaEditPublicMessage.addKeyListener(new KeyAdapter() {
			
			public void keyPressed(KeyEvent ke) {
				if (!ke.isShiftDown() && ke.getKeyChar() == KeyEvent.VK_ENTER)
					ke.consume();
			}
			
			public void keyReleased(KeyEvent ke) {
				if (!ke.isShiftDown() && ke.getKeyChar() == KeyEvent.VK_ENTER) {
					TaEditMessage_keyReleased(ke, false);
				}
			}
        });
		TaEditPublicMessage.addFocusListener(this);
		
		BtnSendPublicMessage = new Button(getI18N("button_send_message"));
        BtnSendPublicMessage.setBounds(515, 230, 75, 24);
        BtnSendPublicMessage.setVisible(true);
		BtnSendPublicMessage.setEnabled(false);
        BtnSendPublicMessage.addActionListener(this);
        BtnSendPublicMessage.addFocusListener(this);
		
        Panel panel = new Panel() {
			public Dimension minimumSize() {
				return preferredSize();
			}
			
			public Dimension preferredSize() {
				return new Dimension(640,290);
			}
		};
        panel.setLayout(null);
		panel.add(TaPublicMessage);
		panel.add(lstConnected);
        panel.add(TaEditPublicMessage);
		panel.add(BtnSendPublicMessage);
        //panel.setBounds(0, 0, 0, 290);
		panel.addFocusListener(this);
		
        return panel;
    }
	
    private Panel getPrivateRoom() {
		status = new StatusBar();
        status.setSize(new Dimension(600,22));
		status.setBackground(colorRoom.darker());
		
        Panel panel = new Panel() {
			public Dimension minimumSize() {
				return preferredSize();
			}
			
			public Dimension maximumSize() {
				return preferredSize();
			}
			
			public Dimension preferredSize() {
				return new Dimension(640,180);
			}
		};
		
        panel.setLayout(null);
		
		if (usePrivateRoom) {
			lbl_private = new Label(getI18N("label_private_conversation"));
			lbl_private.setBounds(10, 0, 580, 20);
			
			TaPrivateMessage = new TextArea("", 0, 0, 1);
			TaPrivateMessage.setBounds(10, 20, 580, 80);
			TaPrivateMessage.setEditable(false);
			TaPrivateMessage.setBackground(Color.white);
			TaPrivateMessage.addFocusListener(this);
			
			TaEditPrivateMessage = new TextArea("", 0, 0, 1);
			TaEditPrivateMessage.setBounds(10, 110, 340, 24);
			TaEditPrivateMessage.setBackground(Color.white);
			TaEditPrivateMessage.addFocusListener(this);
			TaEditPrivateMessage.setEnabled(false);
			TaEditPrivateMessage.addKeyListener(new KeyAdapter() {
				public void keyReleased(KeyEvent keyevent)
				{
					TaEditMessage_keyReleased(keyevent, true);
				}
			});
			
			BtnSendPrivateMessage = new Button(getI18N("button_send_message_private"));
			BtnSendPrivateMessage.setBounds(358, 110, 75, 24);
			BtnSendPrivateMessage.setVisible(true);
			BtnSendPrivateMessage.setEnabled(false);
			BtnSendPrivateMessage.addActionListener(this);
			BtnSendPrivateMessage.addFocusListener(this);
			
			ChcConnected = new Choice();
			ChcConnected.setBounds(440, 110, 150, 28);
			ChcConnected.addFocusListener(this);
			ChcConnected.setEnabled(false);
			
			panel.setSize(new Dimension(width,170));
			panel.add(lbl_private);
			panel.add(TaPrivateMessage);
			panel.add(TaEditPrivateMessage);
			panel.add(BtnSendPrivateMessage);
			panel.add(ChcConnected);
			
			status.setBounds(0,(int)panel.getSize().height-22,width,22);
			
		} else {
			panel.setSize(new Dimension(width,22));
			status.setBounds(0,0,width,22);
		}
		
		panel.add(status);
		
        return panel;
    }
	
    public void loadResources(String lang) {
        i18n = new Resource("DeltaChatClient_", getCodeBase(), lang);
    }
	
    /*public void init() {
    }*/
	
    public void start() {
		usePrivateRoom = Boolean.valueOf(getParameter("use_private_room")).booleanValue();
		
		// Récupérer la couleur de fond
		colorRoom = getColor(getParameter("color_room"));
		
        user = new ChatCliUser(
			getParameter("plateform"),
			getParameter("room"),
			getParameter("group"),
			getParameter("user"),
			getParameter("nickname"),
			getParameter("sex").charAt(0),
			getParameter("language")
		);
		
		loadResources(user.getLanguage());
		
        width  = 600;
		height = Integer.parseInt(getParameter("height_room"));
        
		resize(width, height);
        setLayout(new BorderLayout(0,0));
        setBackground(colorRoom);
        addFocusListener(this);
		
		add(getPublicRoom(), BorderLayout.NORTH);
		add(getPrivateRoom(), BorderLayout.CENTER);
		
		// Tentative de connexion
		connectedNow();
    }
	
    public void TaEditMessage_keyReleased(KeyEvent keyevent, boolean flag) {
        if (!keyevent.isShiftDown()) {
            KeyEvent _tmp = keyevent;
			
            if (keyevent.getKeyCode() == 10) {
                Index = flag ? ChcConnected.getSelectedIndex() : -1;
                sendMessage();
            }
        }
		
        sendSpiritToWrite();
    }
}
