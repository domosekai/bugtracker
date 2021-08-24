document.getElementById('signup-form').addEventListener("submit", AddTimezone);
function AddTimezone() {
	var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
	var field = document.getElementById('timezone-field');
	field.value = tz;
}