<?php
/**
 * phx-modifier: date_mod.phx.php
 * Description: Modifier for timestamp
 * @author
 * Bruno Perner
 * @return timestamp
 */

if (!class_exists('date_mod')) {

    class date_mod {

        function day($timestamp, $useadodbtime, $dateoptions) {
            $timeshift = 0;
            $shiftoption = '';
            if (strlen($dateoptions[1]) > 0) {
                $shiftarr = explode('_', $dateoptions[1]);
                $timeshift = $shiftarr[0];
                $shiftoption = $shiftarr[1];
            }
            $adddays = ($shiftoption == 'day') ? $timeshift : 0;
            $day = ($useadodbtime == '1') ? xetadodb_date("d", $timestamp) : date("d", $timestamp);
            $day = $day + $adddays;
            $month = ($useadodbtime == '1') ? xetadodb_date("m", $timestamp) : date("m", $timestamp);
            $year = ($useadodbtime == '1') ? xetadodb_date("Y", $timestamp) : date("Y", $timestamp);
            $timestamp = ($useadodbtime == '1') ? xetadodb_mktime(0, 0, 0, $month, $day, $year) : mktime(0, 0, 0, $month, $day, $year);
            return ($useadodbtime == '1') ? xetadodb_date("d", $timestamp) : date("d", $timestamp);
        }
        function month($timestamp, $useadodbtime, $dateoptions) {
            $timeshift = 0;
            $shiftoption = '';
            if (isset($dateoptions[1])) {
                $shiftarr = explode('_', $dateoptions[1]);
                $timeshift = $shiftarr[0];
                $shiftoption = $shiftarr[1];
            }
            $adddays = ($shiftoption == 'day') ? $timeshift : 0;
            $day = ($useadodbtime == '1') ? xetadodb_date("d", $timestamp) : date("d", $timestamp);
            $day = ($shiftoption == 'day') ? $day + $adddays : '01';
            $addmonths = ($shiftoption == 'month') ? $timeshift : 0;
            $month = ($useadodbtime == '1') ? xetadodb_date("m", $timestamp) : date("m", $timestamp);
            $month = $month + $addmonths;
            $year = ($useadodbtime == '1') ? xetadodb_date("Y", $timestamp) : date("Y", $timestamp);
            $timestamp = ($useadodbtime == '1') ? xetadodb_mktime(0, 0, 0, $month, $day, $year) : mktime(0, 0, 0, $month, 01, $year);
            return ($useadodbtime == '1') ? xetadodb_date("m", $timestamp) : date("m", $timestamp);
        }
        function year($timestamp, $useadodbtime, $dateoptions) {

            $timeshift = 0;
            $shiftoption = '';
            if (isset($dateoptions[1])) {
                $shiftarr = explode('_', $dateoptions[1]);
                $timeshift = $shiftarr[0];
                $shiftoption = $shiftarr[1];
            }
            $adddays = ($shiftoption == 'day') ? $timeshift : 0;
            $day = ($useadodbtime == '1') ? xetadodb_date("d", $timestamp) : date("d", $timestamp);
            $day = ($shiftoption == 'day') ? $day + $adddays : '01';
            $addmonths = ($shiftoption == 'month') ? $timeshift : 0;
            $month = ($useadodbtime == '1') ? xetadodb_date("m", $timestamp) : date("m", $timestamp);
            $month = $month + $addmonths;
            $addyears = ($shiftoption == 'year') ? $timeshift : 0;
            $year = ($useadodbtime == '1') ? xetadodb_date("Y", $timestamp) : date("Y", $timestamp);
            $year = $year + $addyears;
            $timestamp = ($useadodbtime == '1') ? xetadodb_mktime(0, 0, 0, $month, $day, $year) : mktime(0, 0, 0, $month, 01, $year);

            return ($useadodbtime == '1') ? xetadodb_date("Y", $timestamp) : date("Y", $timestamp);
        }
        function hour($timestamp, $useadodbtime) {
            return ($useadodbtime == '1') ? xetadodb_date("H", $timestamp) : date("H", $timestamp);
        }
        function minute($timestamp, $useadodbtime) {
            return ($useadodbtime == '1') ? xetadodb_date("i", $timestamp) : date("i", $timestamp);
        }
        function second($timestamp, $useadodbtime) {
            return ($useadodbtime == '1') ? xetadodb_date("s", $timestamp) : date("s", $timestamp);
        }
        function dow($timestamp, $useadodbtime) {
            return ($useadodbtime == '1') ? (($w = xetadodb_date("w", $timestamp)) ? $w : 7) : (($w = xetadodb_date("w", $timestamp)) ? $w : 7);
        }

        function getISOkw($timestamp, $useadodbtime, $dateoptions) {

            $timeshift = 0;
            $shiftoption = '';
            if (strlen($dateoptions[1]) > 0) {
                $shiftarr = explode('_', $dateoptions[1]);
                $timeshift = $shiftarr[0];
                $shiftoption = $shiftarr[1];
            }
            $adddays = ($shiftoption == 'day') ? $timeshift : 0;
            $timestamp = $timestamp + $adddays * 86400;
            $tsweekThu = $this->timestampweekstart($timestamp, $useadodbtime) + 3 * 86400; //Donnerstag=Montag+3 Tage
            $kwyear = ($useadodbtime == '1') ? xetadodb_date("Y", $tsweekThu) : date("Y", $tsweekThu);
            $ts4Jan = xetadodb_mktime(0, 0, 0, 01, 04, $kwyear);
            $ts4Jan = ($useadodbtime == '1') ? xetadodb_mktime(0, 0, 0, 01, 04, $kwyear) : mktime(0, 0, 0, 01, 04, $kwyear);
            $tsfirstDo = $this->timestampweekstart($ts4Jan, $useadodbtime) + 3 * 86400;
            $isokw = ceil(($tsweekThu - $tsfirstDo) / 86400 / 7 + 1);
            return ($dateoptions[0] == 'ISOcw_year') ? $kwyear : $isokw;
        }

        function timestampweekstart($timestamp, $useadodbtime) {
            $dow = $this->dow($timestamp, $useadodbtime);
            $day = ($useadodbtime == '1') ? xetadodb_date("d", $timestamp) : date("d", $timestamp);
            $month = $this->month($timestamp, $useadodbtime, $dateoptions);
            $year = $this->year($timestamp, $useadodbtime, $dateoptions);
            return ($useadodbtime == '1') ? xetadodb_mktime(0, 0, 0, $month, $day - $dow + 1, $year) : mktime(0, 0, 0, $month, $day - $dow + 1, $year);
        }

        function timestampweekend($timestamp, $useadodbtime) {
            $dow = $this->dow($timestamp, $useadodbtime);
            $day = ($useadodbtime == '1') ? xetadodb_date("d", $timestamp) : date("d", $timestamp);
            $month = $this->month($timestamp, $useadodbtime, $dateoptions);
            $year = $this->year($timestamp, $useadodbtime, $dateoptions);
            return ($useadodbtime == '1') ? xetadodb_mktime(23, 59, 59, $month, $day + 7 - $dow, $year) : mktime(23, 59, 59, $month, $day + 7 - $dow, $year);
        }

        function tsday($timestamp, $useadodbtime, $dateoptions) {
            $timeshift = (isset($dateoptions[1])) ? $dateoptions[1] : '-0';
            $day = ($useadodbtime == '1') ? xetadodb_date("d", $timestamp) : date("d", $timestamp);
            $day = $day + $timeshift;
            $month = $this->month($timestamp, $useadodbtime, $dateoptions);
            $year = $this->year($timestamp, $useadodbtime, $dateoptions);
            if (substr($timeshift, 0, 1) == '-')
                return ($useadodbtime == '1') ? xetadodb_mktime(0, 0, 0, $month, $day, $year) : mktime(0, 0, 0, $month, $day, $year);
            else
                return ($useadodbtime == '1') ? xetadodb_mktime(23, 59, 59, $month, $day, $year) : mktime(23, 59, 59, $month, $day, $year);
        }

        function tsmonth($timestamp, $useadodbtime, $dateoptions) {
            $timeshift = (isset($dateoptions[1])) ? $dateoptions[1] : '-0';
            $day = ($useadodbtime == '1') ? xetadodb_date("d", $timestamp) : date("d", $timestamp);
            $month = $this->month($timestamp, $useadodbtime, $dateoptions) + $timeshift;
            $year = $this->year($timestamp, $useadodbtime, $dateoptions);
            if (substr($timeshift, 0, 1) == '-')
                return ($useadodbtime == '1') ? xetadodb_mktime(0, 0, 0, $month, $day, $year) : mktime(0, 0, 0, $month, $day, $year);
            else
                return ($useadodbtime == '1') ? xetadodb_mktime(23, 59, 59, $month, $day, $year) : mktime(23, 59, 59, $month, $day, $year);
        }

        function monthname($timestamp, $useadodbtime, $dateoptions, $monthnames) {
            $monthnames = (isset($dateoptions[2])) ? explode(',', $dateoptions[2]) : $monthnames;
            $month = abs($this->month($timestamp, $useadodbtime, $dateoptions));
            return (isset($dateoptions[3])) ? substr($monthnames[$month], 0, $dateoptions[3]) : $monthnames[$month];
        }
        function dayname($timestamp, $useadodbtime, $dateoptions, $daynames) {
            $daynames = (isset($dateoptions[2])) ? explode(',', $dateoptions[2]) : $daynames;
            $dow = abs($this->dow($timestamp, $useadodbtime));
            return (isset($dateoptions[3])) ? substr($daynames[$dow], 0, $dateoptions[3]) : $daynames[$dow];
        }
    }

}


$daynames = array();
/*
$loc_de = setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'de', 'ge');
echo "Preferred locale for german on this system is '$loc_de'";
*/
setlocale(LC_TIME, 'de_DE@euro');
for ($i = 1; $i <= 7; $i++) {
    $timestamp = mktime(0, 0, 0, 05, $i, 2000);
    $daynames[$i] = strftime("%A", $timestamp);
}
/*
$monthnames=array();
for ($i=1;$i<=12;$i++){
$timestamp=mktime(0, 0, 0, $i, 01, 2000);
$monthnames[$i]=strftime("%B",$timestamp);
}
*/
$str_monthnames = ',Januar,Februar,März,April,Mai,Juni,Juli,August,September,Oktober,November,Dezember';
$str_daynames = ',Montag,Dienstag,Mittwoch,Donnerstag,Freitag,Samstag,Sonntag';

$monthnames = isset($GLOBALS['monthnames']) ? $GLOBALS['monthnames'] : explode(',', $str_monthnames);
$daynames = isset($GLOBALS['daynames']) ? $GLOBALS['daynames'] : explode(',', $str_daynames);
$options = explode('||', $options);
//path where adodb-time.inc.php is installed.
$dateoptions = (isset($options[0])) ? $options[0] : '';
if ($dateoptions == '') {
    return $input;
}
$dateoptions = explode(':', $dateoptions);
$adodbpath = (isset($options[1])) ? $options[1] : "assets/snippets/blox/inc/";
$adodbFile = $modx->config['base_path'] . $adodbpath . 'adodb-time.inc.php';
$useadodbtime = '0';
if (file_exists($adodbFile)) {
    include_once ($adodbFile);
    $useadodbtime = '1';
}
//get timestamp from placeholder
$date = (strlen($input) > 0) ? $input : time();
$dateday = strftime("%d", $date);
$datemonth = strftime("%m", $date);
$dateyear = strftime("%Y", $date);
//$dateday = $dateday + $timeshift;

$datemod = new date_mod();

switch ($dateoptions[0]) {
    case 'day':
        return $datemod->day($input, $useadodbtime, $dateoptions);
        break;
    case 'month':
        return $datemod->month($input, $useadodbtime, $dateoptions);
        break;
    case 'year':
        return $datemod->year($input, $useadodbtime, $dateoptions);
        break;
    case 'hour':
        return $datemod->hour($input, $useadodbtime);
        break;
    case 'minute':
        return $datemod->minute($input, $useadodbtime);
        break;
    case 'second':
        return $datemod->second($input, $useadodbtime);
        break;
    case 'ISOcalweek':
        return $datemod->getISOkw($input, $useadodbtime, $dateoptions);
        break;
    case 'ISOcw_year':
        return $datemod->getISOkw($input, $useadodbtime, $dateoptions);
        break;
    case 'dow':
        return $datemod->dow($input, $useadodbtime);
        break;
    case 'monthname':
        return $datemod->monthname($input, $useadodbtime, $dateoptions, $monthnames);
        break;
    case 'dayname':
        return $datemod->dayname($input, $useadodbtime, $dateoptions, $daynames);
        break;
    case 'tsday':
        return $datemod->tsday($input, $useadodbtime, $dateoptions);
        break;
    case 'tsmonth':
        return $datemod->tsmonth($input, $useadodbtime, $dateoptions);
        break;

}
return $input;