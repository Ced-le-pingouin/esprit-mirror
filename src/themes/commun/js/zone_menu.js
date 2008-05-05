function positionner_cours() {
	if (top.sauver_position)
		self.scrollTo(0,top.sauver_position);
}
function restorer_position_cours() { top.sauver_position = 0; }
function sauver_position_cours() {
	if (document.body)
		top.sauver_position = document.body.scrollTop;
	else if (document.documentElement)
		top.sauver_position = document.documentElement.scrollTop;
	else if (window.pageYOffset)
		top.sauver_position = self.pageYOffset;
	else
		top.sauver_position = 0;
}