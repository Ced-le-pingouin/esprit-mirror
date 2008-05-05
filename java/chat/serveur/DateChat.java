/**
 * @author PORCO Filippo (filippo.porco@umh.ac.be)
 * @created (28/04/2003)
 * @modifies (29/04/2003)
 *
**/

import java.lang.*;
import java.text.SimpleDateFormat;
import java.util.*;

public class DateChat {
	
	Calendar date;
	
	public DateChat() {
		date = Calendar.getInstance();
	}
	
	public String get(int field) {
		return Integer.toString(field);
	}
	
	public String getDate() {
		return getDateFormat("dd/MM/yyyy");
	}
	
	public String getDateFormat(String pattern) {
		SimpleDateFormat sdf = new SimpleDateFormat(pattern);
		sdf.setTimeZone(TimeZone.getDefault());
		return sdf.format(date.getTime());
	}
	
	public String getYear() {
		return get(Calendar.YEAR);
	}
	
	public String getMonth() {
		return get(Calendar.MONTH);
	}
	
	public String getDay() {
		return get(Calendar.DAY_OF_MONTH);
	}
	
	public String getTime() {
		return getDateFormat("HH:mm:ss");
	}
	
	public String getSecond() {
		return get(Calendar.SECOND);
	}
	
	public String getMinute() {
		return get(Calendar.MINUTE);
	}
	
	public String getHour() {
		return get(Calendar.HOUR_OF_DAY);
	}
	
	public void getGMT() {
	}
}
