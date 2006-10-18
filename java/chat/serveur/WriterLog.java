import java.io.*;

public abstract class WriterLog {
	
	private String fileNameLog;
	private FileOutputStream writerLog;
	
	public static final String CRLF = "\r\n";
	
	public WriterLog(String fileNameLog) {
		this.fileNameLog = fileNameLog;
	}
	
	public void open() throws IOException {
		writerLog = new FileOutputStream(fileNameLog,true);
	}
	
	public void write(String s) throws IOException {
		open();
		
		if (writerLog != null && s.length() > 0) {
			s += CRLF;
			writerLog.write(s.getBytes());
		}
		
		close();
	}
	
	public abstract void writeFooter(String footer) throws IOException;
	
	public void close() throws IOException {
		if (writerLog != null) {
			writerLog.flush();
			writerLog.close();
			writerLog = null;
		}
	}
	
	public void close(String footer) throws IOException {
		if (writerLog != null) {
			writeFooter(footer);
			close();
		}
	}
	
	public String getFileNameLog() {
		return fileNameLog;
	}
	
	public boolean isOpen() {
		return (writerLog != null);
	}
}
