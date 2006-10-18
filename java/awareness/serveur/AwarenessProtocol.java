/**
 * Protocol de l'Awareness :
 *
 *    HEL: <nickname> <session>
 *    AOK: <nickname> <session>
 *
 *    AUL: <nickname> <session> <username> <sex: [M|F]> <language: [Fra|Eng|Ita|Esp|Por]> <team> <location> <statut: [AVAILABLE|BUSY|HIDDEN]>
 *    UUL: <nickname> <session> <sex:> <team> <location> <statut>
 *    CUL: <nickname> <session> <location>
 *    CUS: <nickname> <session> <statut:>
 *    CUT: <nickname> <session> <team>
 *    CSP: <nickname> <session> <nouvelle_session>
 *    RUL: <nickname> <session>
 *
 *    OFL: <nickname> <session> <boolean: [true|false]>
 *
 *    CCM: <nickname> <session> <compose: [true|false]>
 *    CMP: <nickname> <session> <recipient> <message>
 *
 * Paramètres :
 *
 *    nickname  pseudo de l'utilisateur unique pour chaque plate-forme
 *    username  nom complet de l'utilisateur
 *    sex       sexe de l'utilisateur
 *    team      nom de l'équipe
 *    language  langue
 *    location  position de l'utilisateur dans la plate-forme
 *    session   nom unique de la plate-forme
 *
 * Remarques :
 *
 *    - Toutes les balises doivent doivent être remplies.
 *
 * Attention :
 *
 *    - Remplacer les balises vides par le mot-clè NULL.
 *
 */

public class AwarenessProtocol {
	
	// Global
	public static final String AWARENESS_HELLO = "HEL:";
	public static final String AWARENESS_OK = "AOK:";
	public static final String AWARENESS_ERROR = "AER:";
	
	// Utilisateur
	public static final String ADD_USER_LIST = "AUL:";
	public static final String SEND_USERS_LIST = "SUL:";
	public static final String UPDATE_USER_LIST = "UUL:";
	public static final String CHANGE_USER_LOCATION = "CUL:";
	public static final String CHANGE_USER_STATUT = "CUS:";
	public static final String CHANGE_SESSION_PLATEFORM = "CSP:";
	public static final String CHANGE_USER_TEAM = "CUT:";
	public static final String KEEP_ALIVE_CLIENT = "KAC:";
	public static final String REMOVE_USER_LIST = "RUL:";
	
	// Les différents statuts de l'utilisateur
	public static final int USER_STATUT_AVAILABLE = 0;
	public static final int USER_STATUT_BUSY = 1;
	public static final int USER_STATUT_HIDDEN = 2;
	
	// Message public/privé
	public static final String CLIENT_MESSAGE_ALL = "CMA:";
	public static final String CLIENT_MESSAGE_PRIVATE = "CMP:";
	
	public static final String OPEN_FRAME_LIST = "OFL:";
}
