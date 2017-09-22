<?php
namespace view;

class DateTimeView {


	public function showDateAndTime() {
		date_default_timezone_set('Europe/Stockholm');
		//Todays date is shown as: [Day of week], the [day of month numeric]th of
		//[Month as text] [year 4 digits]. The time is [Hour]:[minutes]:[Seconds].
		//Example: "Monday, the 8th of July 2015, The time is 10:59:21".
		$timeString = date('l, \t\h\e jS \o\f F Y, \T\h\e \t\i\m\e \i\s H:i:s', time());

		return '<p>' . $timeString . '</p>';
	}


}
