<?php
namespace view;

class DateTimeView {
	private static $timezone = 'DateTimeView::TimeZone';
	private static $timeselect = 'timeselect';
	private static $timepick = 'DateTimeView::TimePick';
	private $selectedTimeZone = 'stockholm';
	private static $changeTimeZone = "changetimezone";

	private $timeZones = array(
    0 => array(0 => 'stockholm', 1 => 'Europe/Stockholm'),
    1 => array(0 => 'london', 1 => 'Europe/London'),
		2 => array(0 => 'tallinn', 1 => 'Europe/Tallinn'),
		3 => array(0 => 'moscow', 1 => 'Europe/Moscow'),
		4 => array(0 => 'paris', 1 => 'Europe/Paris'),
	);

	public function __construct() {
		if ($this->wantsToChangeTimeZone()) {
			$this->selectedTimeZone = $this->getSelectedTimeZone();
		}
	}

	public function showDateAndTime() {
		switch ($this->selectedTimeZone) {
			case $this->timeZones[0][0]:
				error_log("timezone stockholm", 3, "errors.log");
				date_default_timezone_set($this->timeZones[0][1]);
				break;
			case $this->timeZones[1][0]:
				error_log("timezone london", 3, "errors.log");
			 	date_default_timezone_set($this->timeZones[1][1]);
				break;
			case $this->timeZones[2][0]:
				error_log("timezone tallinn", 3, "errors.log");
				date_default_timezone_set($this->timeZones[2][1]);
				break;
			case $this->timeZones[3][0]:
				error_log("timezone moscow", 3, "errors.log");
				date_default_timezone_set($this->timeZones[3][1]);
				break;
			case $this->timeZones[4][0]:
				error_log("timezone paris", 3, "errors.log");
				date_default_timezone_set($this->timeZones[4][1]);
				break;
			default:
				error_log("timezone default", 3, "errors.log");
				date_default_timezone_set($this->timeZones[0][1]);
		}

		$timeString = date('l, \t\h\e jS \o\f F Y, \T\h\e \t\i\m\e \i\s H:i:s', time());

		return '<p>' . $timeString . '</p>';
	}

	public function showChangeTimeZoneForm() {
		$ret = '
			<form action="?'. self::$changeTimeZone .'" method="post" enctype="multipart/form-data">
					<label for="' . self::$timezone . '">Pick timezone :</label>
					<select name="' . self::$timeselect . '">';

						foreach ($this->timeZones as list($city, $location)) {
								$ret .= '<option value="'.$city.'"';
								if ($city == $this->selectedTimeZone) {
									$ret .= ' selected';
								}
								$ret .= '>'.$location.'</option>';
						}

						$ret .= '
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
