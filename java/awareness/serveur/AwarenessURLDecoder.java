public class AwarenessURLDecoder {
	
	public static String decode(String s) {
		String urlDecode = new String();
		int a;
		char ch;
		
		for (int i=0; i<s.length(); i++) {
			ch = s.charAt(i);
			
			if (ch == '+') {
				ch = ' ';
			} else if (ch == '%') {
				ch = s.charAt(++i);
				
				if (ch >= 'A' && ch <= 'F')
					a = ((ch - 'A') + 10) * 16;
				else
					a = ((ch - '0') * 16);
				
				ch = s.charAt(++i);
				
				if (ch >= 'A' && ch <= 'F')
					a += ((ch - 'A') + 10);
				else
					a += (ch - '0');
				
				ch = (char)a;
			}
			
			urlDecode += ch;
		}
		
		return urlDecode;
	}
}
