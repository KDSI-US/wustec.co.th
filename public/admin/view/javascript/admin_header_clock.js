function showTime() {
	let dateObj = new Date();
	const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
	let tzAbbr = dateObj
		.toLocaleTimeString("en-us", {
			timeZone: userTimezone,
			timeZoneName: "short",
		})
		.split(" ")[2];
	let localTime = dateObj.toLocaleString();

	let time = localTime + " " + tzAbbr;

	document.getElementById("currentoctime").innerText = time;
	document.getElementById("currentoctime").textContent = time;

	setTimeout(showTime, 1000);
}
showTime();
