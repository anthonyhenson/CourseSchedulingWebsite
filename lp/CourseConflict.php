<?php

class CourseConflict
{
    public $sCourseID;
    public $iStartTimeHour;
    public $iStartTimeMinute;
    public $iEndTimeHour;
    public $iEndTimeMinute;
    public $bCourseOverlap;
    
    function CourseConflict($sID, $iSTH, $iSTM, $iETH, $iETM) {
        $this->sCourseID = $sID;
        $this->iStartTimeHour = $iSTH;
        $this->iStartTimeMinute = $iSTM;
        $this->iEndTimeHour = $iETH;
        $this->iEndTimeMinute = $iETM;
    }
    
    function doesCourseOverlap(CourseConflict $oAddedCourse) {
        $iCourse1 = $this->sCourseID[3];
        $iCourse2 = $oAddedCourse->sCourseID[3];
        if (($iCourse1 == 50 && $iCourse2 == 50) //No sophomore overlap
                || ($iCourse1 == 51 && $iCourse2 == 51) //No junior overlap
                || ($iCourse1 == 52 && $iCourse2 == 52) //No senior overlap     
                || ($iCourse1 == 50 && $iCourse2 == 51) || ($iCourse1 == 51 && $iCourse2 == 50) //No sophomore/junior overlap
                || ($iCourse1 == 50 && $iCourse2 == 52) || ($iCourse1 == 52 && $iCourse2 == 50) //No sophomore/senior overlap
                || ($iCourse1 == 51 && $iCourse2 == 52) || ($iCourse1 == 52 && $iCourse2 == 51) //No junior/senior overlap
                ) {
            if ($this->iStartTimeHour >= $oAddedCourse->iStartTimeHour && $this->iStartTimeHour <= $oAddedCourse->iEndTimeHour) {
                if ($this->iStartTimeMinute >= $oAddedCourse->iStartTimeMinute && $this->iStartTimeMinute <= $oAddedCourse->iEndTimeMinute)
                    $bCourseOverlap = true;
            }
            if ($this->iEndTimeHour >= $oAddedCourse->iEndTimeHour && $this->iEndTimeHour <= $oAddedCourse->iEndTimeHour) {
                if ($this->iEndTimeMinute >= $oAddedCourse->iEndTimeMinute && $this->iEndTimeMinute <= $oAddedCourse->iEndTimeMinute)
                    $bCourseOverlap = true;
            } else {
                $bCourseOverlap = false;
            }
        } else {
            $bCourseOverlap = false;
        }
        return $bCourseOverlap;
    }
}