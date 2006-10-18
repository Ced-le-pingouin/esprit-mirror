import java.applet.*;
import java.io.*;

public class AwarenessSpyClient
	extends Applet {
	
	public void sendChangeOfPlace(String location) {
		try {
			AwarenessClient.sendNewUserPosition(location);
		} catch (NoClassDefFoundError ncdfe) {
		}
	}
}
