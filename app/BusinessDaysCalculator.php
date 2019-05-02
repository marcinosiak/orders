<?php

namespace App;
use DateTime;

class BusinessDaysCalculator
{
  const BUSINESS_START = 8 * 3600; // 8:00
  const BUSINESS_END = 16 * 3600; // 16:00


    /**
     * Funkcja sprawdza czy podana data jest dniem pracującym (TRUE) lub święto/sobota/niedziele (FALSE)
     *
     * @param string $date Data w formacie Y-m-d (np. 2015-08-26)
     * @return boolean
     */
     public static function isThatDateWorkingDay($date)
     {
        $time = strtotime($date);
        $dayOfWeek = (int)date('w',$time);
        $year = (int)date('Y',$time);

        #sprawdzenie czy to nie weekend
        if( $dayOfWeek==6 || $dayOfWeek==0 ) {
            return false;
        }

        #lista swiat stalych
        $holiday=array('01-01', '01-06','05-01','05-03','08-15','11-01','11-11','12-25','12-26');

        #dodanie listy swiat ruchomych
        #wialkanoc
        $easter = date('m-d', easter_date( $year ));
        #poniedzialek wielkanocny
        $easterSec = date('m-d', strtotime('+1 day', strtotime( $year . '-' . $easter) ));
        #boze cialo
        $cc = date('m-d', strtotime('+60 days', strtotime( $year . '-' . $easter) ));
        #Zesłanie Ducha Świętego
        $p = date('m-d', strtotime('+49 days', strtotime( $year . '-' . $easter) ));

        $holiday[] = $easter;
        $holiday[] = $easterSec;
        $holiday[] = $cc;
        $holiday[] = $p;

        $md = date('m-d',strtotime($date));
        if(in_array($md, $holiday)) return false;

        return true;
    }

    /**
     * Get the total working hours in seconds between 2 dates..
     * @param DateTime $start Start Date and Time
     * @param DateTime $end Finish Date and Time
     * @param array $working_hours office hours for each weekday (0 Monday, 6 Sunday), Each day must be an array containing a start/finish time in seconds since midnight.
     * @return integer
     * @link https://github.com/RCrowt/working-hours-calculator
     */
    // public static function getWorkingHoursInSeconds(DateTime $start, DateTime $end, array $working_hours)
    public static function getWorkingHoursInSeconds(DateTime $start, DateTime $end)
    {
      $working_hours = [
          null, // Niedz
          [self::BUSINESS_START, self::BUSINESS_END], // Pn
          [self::BUSINESS_START, self::BUSINESS_END], // Wt
          [self::BUSINESS_START, self::BUSINESS_END], // Śr
          [self::BUSINESS_START, self::BUSINESS_END], // Czw
          [self::BUSINESS_START, self::BUSINESS_END], // Pt
          null //Sob
      ];
      // var_dump($start->format('Y-m-d'));
      // dd($start);
      // dd($end);
    	$seconds = 0; // Total working seconds
    	// Calculate the Start Date (Midnight) and Time (Seconds into day) as Integers.
    	$start_date = clone $start;
    	$start_date = $start_date->setTime(0, 0, 0)->getTimestamp();
    	$start_time = $start->getTimestamp() - $start_date;
    	// Calculate the Finish Date (Midnight) and Time (Seconds into day) as Integers.
    	$end_date = clone $end;
    	$end_date = $end_date->setTime(0, 0, 0)->getTimestamp();
    	$end_time = $end->getTimestamp() - $end_date;
    	// For each Day
    	for ($today = $start_date; $today <= $end_date; $today += 86400) {
    		// Get the current Weekday.
    		$today_weekday = date('w', $today);
    		// Skip to next day if no hours set for weekday.
    		// if (!self::isThatDateWorkingDay($start->format('Y-m-d')) || !self::isThatDateWorkingDay($end->format('Y-m-d'))) continue;
    		// if (!isset($working_hours[$today_weekday][0]) || !isset($working_hours[$today_weekday][1])) continue;
    		if (!isset($working_hours[$today_weekday][0]) || !isset($working_hours[$today_weekday][1])) continue;
    		// Set the office hours start/finish.
    		$today_start = $working_hours[$today_weekday][0];
    		$today_end = $working_hours[$today_weekday][1];
    		// Adjust Start/Finish times on Start/Finish Day.
    		if ($today === $start_date) $today_start = min($today_end, max($today_start, $start_time));
    		if ($today === $end_date) $today_end = max($today_start, min($today_end, $end_time));
    		// Add to total seconds.
    		$seconds += $today_end - $today_start;
    	}

      if ($seconds < 60)
    	  return round($seconds/60, 1);
      else
        return round($seconds/60);

    }


}
