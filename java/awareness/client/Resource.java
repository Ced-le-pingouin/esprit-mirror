/**
 **
 **
 */

import java.io.*;
import java.util.*;
import java.net.*;

public class Resource {
	
	private String resourceName;
	public Hashtable strings;
	
	public Resource() {
		strings = new Hashtable();
	}
	
	/**
	 * Exemple:
	 *    <code>Resource res = new Resource("AwarnessClient_",getCodeBase(),"Fra");</code>
	 *    ou
	 *    <code>Resource res = new Resource("AwarnessClient_",getCodeBase(),"Fra|Galanet");</code>
	 *
	 */
	
	public Resource(String baseName_,URL baseURL_,String lang_) {
		this();
		
		StringTokenizer token = new StringTokenizer(lang_,"|");
		
		while (token.hasMoreTokens()) {
			readResource(baseName_,baseURL_,token.nextToken());
		}
	}
	
	public void readResource(String baseName_,URL baseURL_,String lang_) {
		resourceName = baseName_ + lang_ + ".properties";
		
		try {
			readFromFile(
					new URL(
						baseURL_.getProtocol(),
						baseURL_.getHost(),
						baseURL_.getPort(),
						baseURL_.getFile() + resourceName
					)
				);
			
		} catch (MalformedURLException murle) {
		} catch (IOException ioe) {
		}
	}
	
	public void addResource(String baseName_,URL baseURL_,String lang_) {
		readResource(baseName_,baseURL_,lang_);
	}
	
	private void readFromFile(URL urlFile_)
		throws IOException, NoSuchElementException {
		
		BufferedReader in = new BufferedReader(
				new InputStreamReader(urlFile_.openStream(),"ISO-8859-1")
			);
		
		String line,key,value;
		StringTokenizer tokens;
		
		while ((line = in.readLine()) != null) {
			line = line.trim();
			
			if (line.startsWith("#"))
				continue;
			
			tokens = new StringTokenizer(line);
			
			if (tokens.countTokens() < 2)
				continue;
			
			key = (String)tokens.nextElement();
			tokens.nextElement(); // Passer le "="
			value = "";
			
			while (tokens.hasMoreTokens())
				value += (String)tokens.nextToken(" ") + " ";
			
			add(key,value);
		}
		
		in.close();
	}
	
	public void add(String key, String value) {
		strings.put(key,value);
	}
	
	public Enumeration keys() {
		return strings.keys();
	}
	
	public int size() {
		return strings.size();
	}
	
	public String getString(String key) {
		String value = (String)strings.get(key);
		return (value != null ? (value != "null" ? value : key) : "");
	}
}
