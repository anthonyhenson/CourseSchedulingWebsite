
<?php

class LinearProgramming
{
    private static $DAY_SIZE = 51;
    
    //section fits roomsize and is proper room type
    public function happinessRoomVal($oRoom, $oCourse, $oSection) {
        if ($oRoom->getSeatingCapacity() >= $oSection->getSectionSize() && 
                strcmp($oRoom->getRoomType(), $oCourse->getCourseType()) == 0 && 
                strcmp($oCourse->getCoursetype, "standard") !== 0) {
            return 1.5; //happy with room and section type
        } 
        else if ($oRoom->getSeatingCapacity() >= $oSection->getSectionSize() && 
                strcmp($oCourse->getCourseType(), "standard") == 0){
            return 1; //no preference for room type
        } 
        else {
            return 0; //room did not fit section type
        }
    }
    
   private function getDayIndex($sDay) {
        switch ($sDay) {
            case "M":  //before "Monday"
                $Index = 0;
                break;
            case "T":
                $Index = 1;
                break;
            case "W":
                $Index = 2;
                break;
            case "R":
                $Index = 3;
                break;
            case "F":
                $Index = 4;
                break;
            default:
                $Index = 0;
        }
        return $Index;
    }

    private function helperCheckHour($iStart, $iCount, $iIncrement, $iMin, $iMax, $iTimeIndex, $lstDays) {
        for ($i = $iStart; $i <= $iCount; $i += $iIncrement) {
            for ($k = $iMin; $k <= $iMax; $k++) {
                if ($lstDays[$i]->getTimeLengths()[$iTimeIndex + $k]->isTimeFilled()) {
                    return false; 
                }
            }
        }
        return true;
     }
    
    private function helperCheckHourOneDayR($iMin, $iMax, $iIndex, $iTimeIndex, $lstDays) {
        for ($i = $iMin; $i <= $iMax; $i++) {
            if ($lstDays[$iIndex]->getTimeLengths()[$iTimeIndex + $i]->isTimeFilled()) {
                return false; 
            }
        }
        return true;
    }

    private function checkHourOneDayR($iIndex, $iTimeIndex, $lstDays) {
        if ($iTimeIndex == 0) { //beginning day check: !15before + 15after
            return (self::helperCheckHourOneDayR(0, 4, $iIndex, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex + 3 == self::$DAY_SIZE) { //end day check: 15before + !15after
            return (self::helperCheckHourOneDayR(-1, 3, $iIndex, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex == self::$DAY_SIZE - 2 || $iTimeIndex == self::$DAY_SIZE - 1 || $iTimeIndex == self::$DAY_SIZE) { //not enough time for section
            return false;
        }
        else { //all other cases
            return (self::helperCheckHourOneDayR(-1, 4, $iIndex, $iTimeIndex, $lstDays));
        }
    }
    
    private function checkHourThreeDayR($iTimeIndex, $lstDays) {
        if ($iTimeIndex == 0) { //beginning day check: !15before + 15after
            return (self::helperCheckHour(0, 4, 2, 0, 4, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex + 3 == self::$DAY_SIZE) { //end day check: 15before + !15after
            return (self::helperCheckHour(0, 4, 2, -1, 3, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex == (self::$DAY_SIZE - 2) || $iTimeIndex == (self::$DAY_SIZE - 1) || $iTimeIndex == self::$DAY_SIZE) { //not enough time for section
            return false;
        }
        else { //all other cases
            return (self::helperCheckHour(0, 4, 2, -1, 4, $iTimeIndex, $lstDays));
        }
    }
    
    private function checkHourHalfTwoDayR($iTimeIndex, $lstDays) {
        if ($iTimeIndex == 0) { //beginning day check: !15before + 15after
            return (self::helperCheckHour(1, 3, 2, 0, 6, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex + 5 == self::$DAY_SIZE) { //end day check: 15before + !15after
            return (self::helperCheckHour(1, 3, 2, 0, 5, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex == self::$DAY_SIZE - 4 || $iTimeIndex == self::$DAY_SIZE - 3 || $iTimeIndex == self::$DAY_SIZE - 2 || $iTimeIndex == self::$DAY_SIZE - 1 || $iTimeIndex == self::$DAY_SIZE) { //not enough $iTime for section
            return false;
        }
        else { //all other cases
            return (self::helperCheckHour(1, 3, 2, -1, 6, $iTimeIndex, $lstDays));
        }
    }

    private function checkHourOneDayP($iTimeIndex, $lstDays) {
        if ($iTimeIndex == 0) { //beginning day check: !15before + 15after
            return (self::helperCheckHour(0, 0, 1, 0, 4, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex + 3 == self::$DAY_SIZE) { //end day check: 15before + !15after
            return (self::helperCheckHour(0, 0, 1, -1, 3, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex == self::$DAY_SIZE - 2 || $iTimeIndex == self::$DAY_SIZE - 1 || $iTimeIndex == self::$DAY_SIZE) { //not enough $iTime for section
            return false;
        }
        else { //all other cases
            return (self::helperCheckHour(0, 0, 1, -1, 4, $iTimeIndex, $lstDays));
        }
    }
    
    private function checkHourThreeDayP($iTimeIndex, $lstDays) {
        if ($iTimeIndex == 0) { //beginning day check: !15before + 15after
            return (self::helperCheckHour(0, 2, 1, 0, 4, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex + 3 == self::$DAY_SIZE) { //end day check: 15before + !15after
            return (self::helperCheckHour(0, 2, 1, -1, 3, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex == self::$DAY_SIZE - 2 || $iTimeIndex == self::$DAY_SIZE - 1 || $iTimeIndex == self::$DAY_SIZE) {
            return false;
        }
        else { //all other cases
            return (self::helperCheckHour(0, 2, 1, -1, 4, $iTimeIndex, $lstDays));
        }
    }
    
    private function checkHourHalfTwoDayP($iTimeIndex, $lstDays) {
        if ($iTimeIndex == 0) { //beginning day check: !15before + 15after
            return (self::helperCheckHour(0, 1, 1, 0, 6, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex + 5 == self::$DAY_SIZE) { //end day check: 15before + !15after
            return (self::helperCheckHour(0, 1, 1, -1, 5, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex == self::$DAY_SIZE - 4 || $iTimeIndex == self::$DAY_SIZE - 3 || $iTimeIndex == self::$DAY_SIZE - 2 || $iTimeIndex == self::$DAY_SIZE - 1 || $iTimeIndex == self::$DAY_SIZE) { 
            return false;
        }
        else { //all other cases
            return (self::helperCheckHour(0, 1, 1, -1, 6, $iTimeIndex, $lstDays));
        }
    }
    
    public function isProfessorTimesAvailable($iTimeIndex, $lstDays, $iCredits) {
        
        if (sizeof($lstDays) == 1 && $iCredits == 1) { 
                return self::checkHourOneDayP($iTimeIndex, $lstDays);
        }
        else if (sizeof($lstDays) == 3 && $iCredits == 3) {
            //first time isn't start of day
            return self::checkHourThreeDayP($iTimeIndex, $lstDays);
        }
        else if (sizeof($lstDays) == 2 && $iCredits == 3) {
            return self::checkHourHalfTwoDayP($iTimeIndex, $lstDays);
        }
        else {
            return false;
        }
    }
    
    public function isRoomTimesAvailable($iTimeIndex, $lstDays, $iCredits, $sDay) {
        $iIndex = self::getDayIndex($sDay);
        
        //one credit on any day
        if ($iCredits == 1) {
            return self::checkHourOneDayR($iIndex, $iTimeIndex, $lstDays);
        }
        else if ($iCredits == 3 && $sDay == "M") {
            //0, 2, 4 day indexes for one hour(4)
            return self::checkHourThreeDayR($iTimeIndex, $lstDays);
        }
        else if ($iCredits == 3 && $sDay == "T") {
            // if tuesday check 2 days TR with 1, 3 day indexes for 1.5 hours(6)
            return self::checkHourHalfTwoDayR($iTimeIndex, $lstDays);
        }
        else {
            return false;
        }
    }
    
    //returns List of either MWF or TR with all times cleared
    public function clearAllTimeSlots($lstBestDays) {
        foreach ($lstBestDays as $oD) {
            foreach ($oD->getTimeLengths() as $oT) {
                $oT->setTimeFilled(false);
            }
        }
        return $lstBestDays;
    }
            
    //assigns time slots to either MWF or TR daytimes list
    function assignTimeSlots($lstBestDays, $iTimeIndex, $iCredits, $sDay) {
        $iIndex = 0;
        switch ($sDay) {
            case "M": //before case "Monday"
                $iIndex = 0;
                break;
            case "T":
                $iIndex = 0;
                break;
            case "W":
                $iIndex = 1;
                break;
            case "R":
                $iIndex = 1;
                break;
            case "F":
                $iIndex = 2;
                break;
            default:
                $iIndex = 0;
        }
        
        if (sizeof($lstBestDays) == 2 && $iCredits == 1) {
            //check tuesday or thursday in day string
            //assign off index of day in bestdays for tuesday or thursday for one hour
            for ($i = 0; $i < 4; $i++ ) {
                $lstBestDays[$iIndex]->getTimeLengths()[$iTimeIndex+$i]->setTimeFilled(true);
            }
        }
        else if (sizeof($lstBestDays) == 2 && $iCredits == 3) {
            //assign for tuesday and thursday for 1.5 hour on both time slots
            //no need to check day string
            for ($i = 0; $i < 2; $i++) {
                for ($j = 0; $j < 6; $j++ ) {
                    $lstBestDays[$i]->getTimeLengths()[$iTimeIndex+$j]->setTimeFilled(true);
                }
            }
        }
        else if (sizeof($lstBestDays) == 3 && $iCredits == 1) {
            //check monday or wednesday or friday in day string
            //assign off index of day in bestdays for monday or wednesday or friday for one hour
            for ($i = 0; $i < 4; $i++ ) {
                $lstBestDays[$iIndex]->getTimeLengths()[$iTimeIndex+$i]->setTimeFilled(true);
            }
        }
        else if (sizeof($lstBestDays) == 3 && $iCredits == 3) {
            //assign for Monday and Wednesday and Friday for 1 hour on all time slots
            //no need to check day string
            for ($i = 0; $i < 3; $i++) {
                for ($j = 0; $j < 4; $j++ ) {
                    $lstBestDays[$i]->getTimeLengths()[$iTimeIndex+$j]->setTimeFilled(true);
                } 
            }
        }
        return $lstBestDays;
    }
    
}
?>



