public class AwarenessServer {
	
	public static void main(String[] args) {
		
		if (args.length != 1) {
			System.out.println("Usage: java AwarenessServer port");
			System.exit(1);
		}
		
		System.out.println("Ecoute de la AwarenessServer sur le port " + args[0]);
		
		new AwarenessServerSocket().startServer(Integer.parseInt(args[0]));
	}
}
