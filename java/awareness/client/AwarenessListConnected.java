import java.awt.*;
import java.awt.event.*;
import java.util.*;

public class AwarenessListConnected
	extends Frame {
	
	public AwarenessClient awarenessClient;
	
	public ListConnected listConnected;
	
	public ScrollPane scrollPane;
	public Panel panelNotDisturb;
	public Checkbox checkboxNotDisturb;
	
	public AwarenessListConnected(AwarenessClient parent) {
		awarenessClient = parent;
		
		setName("AwarenessListConnected");
		setLayout(new BorderLayout());
		setSize(getMinimumSize());
		setVisible(false);
		
		listConnected = new ListConnected(awarenessClient);
		
		scrollPane = new ScrollPane(ScrollPane.SCROLLBARS_AS_NEEDED);
		scrollPane.add(listConnected);
		
		checkboxNotDisturb = new Checkbox("",false);
		checkboxNotDisturb.setFont(new Font("Dialog",Font.PLAIN,10));
		checkboxNotDisturb.addItemListener(new ItemListener() {
			public void itemStateChanged(ItemEvent ie) {
				awarenessClient.sendNewUserStatut((ie.getStateChange() == ItemEvent.SELECTED ? AwarenessProtocol.USER_STATUT_BUSY : AwarenessProtocol.USER_STATUT_AVAILABLE));
			}
		});
		
		// Ne pas déranger
		panelNotDisturb = new Panel() {
			
			public Dimension getPreferredSize() {
				return getMinimumSize();
			}
			
			public Dimension getMinimumSize() {
				return new Dimension(0,22);
			}
			
			public Insets getInsets() {
				return new Insets(0,5,0,0);
			}
		};
		panelNotDisturb.setBackground(new Color(238,238,230));
		panelNotDisturb.setLayout(new BorderLayout());
		panelNotDisturb.add(checkboxNotDisturb,BorderLayout.CENTER);
		
		add("Center",scrollPane);
		add("South",panelNotDisturb);
		
		refreshLabels();
		
		addWindowListener(new WindowAdapter() {
			public void windowClosing(WindowEvent e) {
				setVisible(false);
			}
		});
	}
	
	public void ClearListConnected() {
		listConnected.clearItems();
	}
	
	public void refreshLabels() {
		if (awarenessClient.i18n == null || awarenessClient.i18n.size() < 1)
			return;
		
		setTitle(awarenessClient.i18n.getString("titre_liste_connectes"));
		listConnected.repaint();
		checkboxNotDisturb.setLabel(awarenessClient.i18n.getString("texte_ne_pas_deranger"));
	}
	
	public Dimension getMinimumSize() {
		return new Dimension(180,360);
	}
	
	public synchronized void revalidate() {
		listConnected.invalidate();
		listConnected.validate();
		scrollPane.invalidate();
		scrollPane.validate();
		invalidate();
		validate();
		repaint();
	} 
	
	/**
	 * Ajoute/modifie un utilisateur connecté
	 *
	 */
	
	public void UpdateUserList(AwarenessMessage awarenessMessage) {
		
		String nickname = awarenessMessage.getNickname();
		
		if (nickname == null)
			return;
		
		String sex      = awarenessMessage.getSex();
		String location = AwarenessURLDecoder.decode(awarenessMessage.getLocation());
		String team     = awarenessMessage.getTeam();
		int statut      = Integer.parseInt(awarenessMessage.getStatut());
		
		ConnectedItem connected = new ConnectedItem(
			nickname,
			("M".equals(sex) ? awarenessClient.getBoyImage() : awarenessClient.getGirlImage()),
			awarenessClient.getBusyImage(),
			AwarenessURLDecoder.decode(team),
			AwarenessURLDecoder.decode(location),
			statut
		);
		
		connected.addMouseListener(awarenessClient);
		
		// connected.addTooltip(new Tooltip(this,nickname));
		
		// Réactualiser la liste des connectés
		listConnected.addItem(connected);
		
		revalidate();
	}
	
	public void UpdateUser(AwarenessMessage awarenessMessage) {
		
		String command  = awarenessMessage.getCommand();
		String nickname = awarenessMessage.getNickname();
		
		if (nickname == null)
			return;
		
		int index               = listConnected.indexOfItem(nickname);
		ConnectedItem connected = listConnected.getItem(index);
		
		if (connected == null)
			return;
		
		if (AwarenessProtocol.CHANGE_USER_LOCATION.equals(command)) {
			awarenessClient.ac_location = awarenessMessage.getLocation();
			connected.setUserLocation(AwarenessURLDecoder.decode(awarenessClient.ac_location));
			
		} else if (AwarenessProtocol.CHANGE_USER_STATUT.equals(command)) {
			awarenessClient.ac_statut = Integer.parseInt(awarenessMessage.getStatut());
			connected.setStatut(awarenessClient.ac_statut);
			
		} else if (AwarenessProtocol.CHANGE_USER_TEAM.equals(command)) {
			awarenessClient.ac_team = awarenessMessage.getTeam();
			connected.setTeam(AwarenessURLDecoder.decode(awarenessClient.ac_team));
		}
		
		listConnected.updateItem(index);
	}
	
	public void RemoveUserList(String nickname) {
		listConnected.removeItem(nickname);
		revalidate();
	}
}
