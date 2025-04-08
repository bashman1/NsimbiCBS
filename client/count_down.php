<script src="./vendor/global/global.min.js"></script>

<script>
	// Set the date we're counting down to
	// var countDownDate = new Date("Dec 19, 2023 11:36:10").getTime();
	var countDownDate = new Date("<?= $_SESSION['working_hours_end_at'] ?>").getTime();

	// Update the count down every 1 second
	var x = setInterval(function() {

		// Get today's date and time
		var now = new Date().getTime();

		// Find the distance between now and the count down date
		var distance = countDownDate - now;

		// Time calculations for days, hours, minutes and seconds
		var days = Math.floor(distance / (1000 * 60 * 60 * 24));
		var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		var seconds = Math.floor((distance % (1000 * 60)) / 1000);

		let countdownHours = document.getElementById("hours");
		let countdownMinutes = document.getElementById("minutes");
		let countdownSeconds = document.getElementById("seconds");

		hours = hours || 0;
		minutes = minutes || 0;
		seconds = seconds || 0;
		countdownHours.innerHTML = hours;
		countdownMinutes.innerHTML = minutes;
		countdownSeconds.innerHTML = seconds;
		// If the count down is finished, reset to zero

		if (minutes == 5 && seconds == 59) {
			swal({
				title: "You have 5 minutes left to ",
				text: message,
				type: "info",
			});
		}

		if (distance < 0) {
			clearInterval(x);
			countdownDays.innerHTML = '00';
			countdownHours.innerHTML = '00';
			countdownMinutes.innerHTML = '00';
			countdownSeconds.innerHTML = '00';
		}
		//location.reload();
	}, 1000);
</script>