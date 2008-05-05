public class ChatCliUser {
	
	public String plateform;
	public String room;
	public String group;
	public String user;
	public String nickname;
	public char sex;
	public String language;
	
	public ChatCliUser(String plateform_, String room_, String group_, String user_, String nickname_, char sex_, String language_) {
		plateform = plateform_;
		room = room_;
		group = group_;
		user = user_;
		nickname = nickname_;
		sex = sex_;
		language = language_;
	}
	
	public String getPlateform() { return plateform; }
	public String getRoom() { return room; }
	public String getGroup() { return group; }
	public String getUser() { return user; }
	public String getNickname() { return nickname; }
	public char getSex() { return sex; }
	public String getLanguage() { return language; }
}
