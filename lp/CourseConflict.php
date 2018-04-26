<?php

class CourseConflict {
    public $lstCourseConflict; //associative array of section info
 
    public function __construct() {
        $lstCourseConflict = array();
    }
    
    //courseSectionID, credits, dayIndex, timeIndex, isWeek, professorSchedule
    function doesCourseOverlap($section, $day, $time, $isWeek, $professorSchedule) { 
        //if isWeek then day will be -1 
        $isConflict = false; //find conflict with other courses
        
        preg_match_all('!\d+!', $section, $ints); 
        $level = substr($ints[0][0][0], 0, 1);//if EGR102A this will give the 1

        foreach ($this->lstCourseConflict as $sectionCheck) {

            //check level
            preg_match_all('!\d+!', $sectionCheck['sectionCode'], $intsCheck);
            $levelCheck = substr($intsCheck[0][0][0], 0, 1);
            
            if (($levelCheck == 2 && $level == 2)    // No Sophomore overlap
                || ($levelCheck == 3 && $level == 3) // No Junior overlap
                || ($levelCheck == 4 && $level == 4) // No Senior overlap
                || ($levelCheck == 2 && $level == 3) || ($levelCheck == 3 && $level == 2) // No sophomore/junior overlap
                || ($levelCheck == 2 && $level == 4) || ($levelCheck == 4 && $level == 2) // No sophomore/senior overlap
                || ($levelCheck == 3 && $level == 4) || ($levelCheck == 4 && $level == 3) // No junior/senior overlap
                ) { 

                //only same schedule type 
                if (strcmp($professorSchedule, $sectionCheck['schedule']) == 0) {
                    
                    //time range of section to be added
                    ($isWeek && strcmp($professorSchedule, 'TR') == 0) ? $range = 6 : $range = 4;
                    //time range of already added section being checked against
                    ($sectionCheck['isWeek'] && strcmp($sectionCheck['schedule'], 'TR') == 0) ? $rangeCheck = 6 : $rangeCheck = 4;

                    $rangeStart = ($time - 1 == -1) ? 0 : $time - 1;
                    $rangeEnd = ($time - 1 == -1) ? $rangeStart + $range : $rangeStart + $range + 1;
                    $rangeStartCheck = $sectionCheck['timeIndex']; //only check 15 minute gap on one object
                    $rangeEndCheck = $rangeStartCheck + $rangeCheck - 1; //only 15 minute gap on one object
                    
                    if (($isWeek || $sectionCheck['isWeek']) && (($rangeStartCheck >= $rangeStart && $rangeStartCheck <= $rangeEnd) || ($rangeEndCheck >= $rangeStart && $rangeEndCheck <= $rangeEnd))) {
                        //check just times
                        return true;
                    }
                    else if (!$isWeek && !$sectionCheck['isWeek'] && $day == $sectionCheck['dayIndex'] && (($rangeStartCheck >= $rangeStart && $rangeStartCheck <= $rangeEnd) || ($rangeEndCheck >= $rangeStart && $rangeEndCheck <= $rangeEnd))) {
                        //check day index if both days are not weekly otherwise above if will happen
                        //same day if not same day no possible conflict
                        return true;
                    }
                    else {
                        $isConflict = false;
                    }
                }
            }
            else {
                $isConflict = false;
            }
        }
        return $isConflict;
    }
}