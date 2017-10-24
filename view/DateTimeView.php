<?php
namespace view;

class DateTimeView {
	private static $timezone = 'DateTimeView::TimeZone';
	private static $timeselect = 'timeselect';
	private static $timepick = 'DateTimeView::TimePick';
	private $selectedTimeZone = 'stockholm';

	private static $changeTimeZone = "changetimezone";

	public function __construct() {
		if ($this->wantsToChangeTimeZone()) {
			$this->selectedTimeZone = $this->getSelectedTimeZone();
		}
	}

	public function showDateAndTime() {
		switch ($this->selectedTimeZone) {
			case "stockholm":
				error_log("timezone stockholm", 3, "errors.log");
				date_default_timezone_set('Europe/Stockholm');
				break;
			case "london":
				error_log("timezone london", 3, "errors.log");
			 	date_default_timezone_set('Europe/London');
				break;
			case "tallinn":
				error_log("timezone tallinn", 3, "errors.log");
				date_default_timezone_set('Europe/Tallinn');
				break;
			case "moscow":
				error_log("timezone moscow", 3, "errors.log");
				date_default_timezone_set('Europe/Moscow');
				break;
			case "paris":
				error_log("timezone paris", 3, "errors.log");
				date_default_timezone_set('Europe/Paris');
				break;
			default:
				error_log("timezone default", 3, "errors.log");
				date_default_timezone_set('Europe/Stockholm');
		}

		$timeString = date('l, \t\h\e jS \o\f F Y, \T\h\e \t\i\m\e \i\s H:i:s', time());

		return '<p>' . $timeString . '</p>';
	}

	public function showChangeTimeZoneForm() {
		$ret = '
			<form action="?'. self::$changeTimeZone .'" method="post" enctype="multipart/form-data">
					<label for="' . self::$timezone . '">Pick timezone :</label>
					<select name="' . self::$timeselect . '">
  					<option value="stockholm">Europe/Stockholm</option>
  					<option value="london">Europe/London</option>
  					<option value="tallinn">Europe/Tallinn</option>
						<option value="moscow">Europe/Moscow</option>
						<option value="paris">Europe/Paris</option>
					</select>
					<input type="submit" name="' . self::$timepick . '" value="Change"/>
			</form>
		';

		return $ret;
	}

	public function wantsToChangeTimeZone() : bool {
		if (isset($_GET[self::$changeTimeZone])) {
			return true;
		}
		return false;
	}

	public function getSelectedTimeZone() {
		return $_POST[self::$timeselect];
	}

}
