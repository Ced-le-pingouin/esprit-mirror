import java.applet.*;
import java.io.*;

public class AwarenessSpyEsprit
	extends AwarenessSpyClient {
	
	public void start() {
		
		String[] s = { getParameter("location")
				, getParameter("title_list_connected")
				, getParameter("session")
				, getParameter("statut_utilisateur")
			};
		
		// Modifier l'endroit dans la plate-forme
		if (s[0] != null)
			sendChangeOfPlace(s[0].trim());
		
		// Changer le titre de la fenêtre contenant la liste des connectés ?
		if (s[1] != null) {
			try {
				AwarenessClient.setTitleListConnected(s[1].trim());
			} catch (NoClassDefFoundError ncdfe) {
			}
		}
		
		// Ne pas changer l'ordre d'envoi !!!!!!!!!!!!!!!!!
		if (s[3] != null) {
			try {
				AwarenessClient.sendNewTeamUser(s[3].trim());
			} catch (NoClassDefFoundError ncdfe) {
			}
		}
		
		// L'utilisateur a changé de session
		if (s[2] != null) {
			try {
				AwarenessClient.setSession(s[2].trim());
			} catch (NoClassDefFoundError ncdfe) {
			}
		}
		
	}
}
