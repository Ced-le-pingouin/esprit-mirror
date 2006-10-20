/**
 * @author PORCO Filippo (filippo.porco@umh.ac.be)
 * @created (05/05/2003)
 * @modifies (05/05/2003)
 *
 * Unité de Technologie de l'Education
 * Place du Parc, 18
 * 7000 MONS
 *
**/

import java.io.*;
import java.lang.*;
import java.util.*;

public class ServerLog extends WriterLog {
	
	public ServerLog() {
		super("log/access_log");
	}
	
	public void writeFooter(String message) throws IOException {
	}
}

