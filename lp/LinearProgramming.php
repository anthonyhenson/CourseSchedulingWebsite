<?php
/**
 * Created by PhpStorm
 * User: AD
 * Date: 2/2/2018
 * Time: 12:49 AM
 */

class LinearProgramming
{
    public $iDaySize = 51;
    
    //checked, have observations on it
    //section fits roomsize and is proper room type
    function happinessRoomVal($oRoom, $oCourse, $oSection) {
     if ($oRoom->getSeatingCapacity() >= $oSection->getSectionSize() && $oRoom->getRoomType() == $oCourse->getCourseType() && strcmp($oCourse->getCoursetype,"standard") !== 0) {
         return 1.5; //happy with room and section type
     } elseif ($oRoom->getSeatingCapacity() >= $oSection->getSectionSize() && strcmp($oCourse->getCourseType(), "standard") == 0){
         return 1; //no preference for room type
     } else {
         return 0; //room did not fit section type
     }
    }
    
    //checked
    function getDayIndex($sDay) {
        switch ($sDay) {
            case "Monday":
                $Index = 0;
                break;
            case "Tuesday":
                $Index = 1;
                break;
            case "Wednesday":
                $Index = 2;
                break;
            case "Thursday":
                $Index = 3;
                break;
            case "Friday":
                $Index = 4;
                break;
            default:
                $Index = 0;
        }
        return $Index;
    }
    
    //checked
    function helperCheckHour($iStart, $iCount, $iIncrement, $iMin, $iMax, $iTimeIndex, $lstDays) { //check functions to see which indexes are being checked
        for ($i = $iStart; $i <= $iCount; $i += $iIncrement) {
            for ($k = $iMin; $k <= $iMax; $k++) {
                if ($lstDays[$i]->getDayTimes()->get($TimeIndex + $k)->isTimeFilled())  //this function just loops through time indexes to see if they are filled or not
                    return false;
            }
        }
        return true;
    }
    
    //checked
    function helperCheckHourOneDayR($iMin, $iMax, $iIndex, $iTimeIndex, $lstDays) {
        for ($i = $Min; $i <= $Max; $i++) {
            if ($lstDays->get($iIndex)->getDayTimes()->get($TimeIndex + $i)->isTimeFilled())  //same as helperCheckHour, but for OneDayR, because OneDayR has index passed into it already so it requires one less for loop than the others
                return false;
        }
        return true;
    }
    
    //---------------------- Start Room Times Check -------------------------//
    //The following functions are used by the isRoomTimesAvailable() function below. They are not directly used outside of LinearProgramming.php
    //checked 
    function checkHourOneDayR($iIndex, $iTimeIndex, $lstDays) {
        if ($iTimeIndex == 0) { //beginning day check: !15before + 15after
            return (helperCheckHourOneDayR(0, 4, $iIndex, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex + 3 == $iDaySize) { //end day check: 15before + !15after
            return (helperCheckHourOneDayR(-1, 3, $iIndex, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex == $iDaySize - 2 || $iTimeIndex == $iDaySize - 1 || $iTimeIndex == $iDaySize) { //not enough time for section
            return false;
        }
        else { //all other cases
            return (self::helperCheckHourOneDayR(-1, 4, $iIndex, $iTimeIndex, $lstDays));
        }
    }
    
    
    function checkHourThreeDayR($iTimeIndex, $lstDays) {
        if ($iTimeIndex == 0) { //beginning day check: !15before + 15after
            return (helperCheckHour(0, 4, 2, 0, 4, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex + 3 == $iDaySize) { //end day check: 15before + !15after
            return (helperCheckHour(0, 4, 2, -1, 3, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex == $iDaySize - 2 || $iTimeIndex == $iDaySize - 1 || $iTimeIndex == $iDaySize) { //not enough time for section
            return false;
        }
        else { //all other cases
            return (helperCheckHour(0, 4, 2, -1, 4, $iTimeIndex, $lstDays));
        }
    }
    
    //checked
    function checkHourHalfTwoDayR($iTimeIndex, $lstDays) {
        if ($iTimeIndex == 0) { //beginning day check: !15before + 15after
            return (helperCheckHour(1, 3, 2, 0, 6, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex + 5 == $iDaySize) { //end day check: 15before + !15after
            return (helperCheckHour(1, 3, 2, 0, 5, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex == $iDaySize - 4 || $iTimeIndex == $iDaySize - 3 || $iTimeIndex == $iDaySize - 2 || $iTimeIndex == $iDaySize - 1 || $iTimeIndex == $iDaySize) { //not enough $iTime for section
            return false;
        }
        else { //all other cases
            return (helperCheckHour(1, 3, 2, -1, 6, $iTimeIndex, $lstDays));
        }
    }
    //---------------------- End Room Times Check -------------------------//
    
    //---------------------- Start Professor Times Check -------------------------//
    //The following functions are used by the isProfessorTimesAvailable() function below. They are not directly used outside of LinearProgramming.php
    //checked
    function checkHourOneDayP($iTimeIndex, $lstDays) {
        if ($iTimeIndex == 0) { //beginning day check: !15before + 15after
            return (helperCheckHour(0, 0, 1, 0, 4, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex + 3 == $iDaySize) { //end day check: 15before + !15after
            return (helperCheckHour(0, 0, 1, -1, 3, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex == $iDaySize - 2 || $iTimeIndex == $iDaySize - 1 || $iTimeIndex == $iDaySize) { //not enough $iTime for section
            return false;
        }
        else { //all other cases
            return (helperCheckHour(0, 0, 1, -1,4, $iTimeIndex, $lstDays));
        }
    }
    
    //checked
    function checkHourThreeDayP($iTimeIndex, $lstDays) {
        if ($iTimeIndex == 0) { //beginning day check: !15before + 15after
            return (helperCheckHour(0, 2, 1, 0, 4, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex + 3 == $iDaySize) { //end day check: 15before + !15after
            return (helperCheckHour(0, 2, 1, -1,3, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex == $iDaySize - 2 || $iTimeIndex == $iDaySize - 1 || $iTimeIndex == $iDaySize) { //not enough $iTime for section
            return false;
        }
        else { //all other cases
            return (helperCheckHour(0, 2, 1, -1,4, $iTimeIndex, $lstDays));
        }
    }
    
    //checked
    function checkHourHalfTwoDayP($iTimeIndex, $lstDays) {
        if ($iTimeIndex == 0) { //beginning day check: !15before + 15after
            return (helperCheckHour(0, 1, 1, 0, 6, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex + 5 == $iDaySize) { //end day check: 15before + !15after
            return (helperCheckHour(0, 1, 1, -1,5, $iTimeIndex, $lstDays));
        }
        else if ($iTimeIndex == $iDaySize - 4 || $iTimeIndex == $iDaySize - 3 || $iTimeIndex == $iDaySize - 2 || $iTimeIndex == $iDaySize - 1 || $iTimeIndex == $iDaySize) { //not enough $iTime for section
            return false;
        }
        else { //all other cases
            return (helperCheckHour(0, 1, 1, -1,6, $iTimeIndex, $lstDays));
        }
    }
    
    //---------------------- End Professor Times Check -------------------------//
    //checked
    function isProfessorTimesAvailable($iTimeIndex, $lstDays, $iCredits) {
        if ($lstDays->size() == 1 && $Credits == 1) {
                return checkHourOneDayP($iTimeIndex, $lstDays);
        }
        else if ($lstDays->size() == 3 && $Credits == 3) {
            //first $iTime isn't start of day
            return checkHourThreeDayP($iTimeIndex, $lstDays);
        }
        else if ($lstDays->size() == 2 && $Credits == 3) {
            return checkHourHalfTwoDayP($iTimeIndex, $lstDays);
        }
        else {
            return false;
        }
    }
    
    //checked
    function isRoomTimesAvailable($iTimeIndex, $lstDays, $iCredits, $sDay) {
        $iIndex = getDayIndex($sDay);
        
        //one credit on any day
        if ($iCredits == 1) {
            return checkHourOneDayR($iIndex, $iTimeIndex, $lstDays);

        }
        //before && day. no day. is $sDay correct? 
        else if ($iCredits == 3 && $sDay == "Monday") {
            //0, 2, 4 day indexes for one hour(4)
            return checkHourThreeDayR($iIndex, $iTimeIndex, $lstDays);
        }
        //before && day. no day. is $sDay correct? 
        else if ($iCredits == 3 && $sDay == "Tuesday") {
            // if tuesday check 2 days TR with 1, 3 day indexes for 1.5 hours(6)
            return checkHourHalfTwoDayR($iIndex, $iTimeIndex, $lstDays);
        }
        else {
            return false;
        }
    }
    
    //checked
    function clearAllTimeSlots($lstBestDays) {
        foreach ($lstBestDays as $lstD) {
            foreach ($lstD->getDayTimes() as $oT) {
                $oT->setTimeFilled(false);
            }
        }
        return $lstBestDays;
    }
    
    //checked
    function assignTimeSlots($lstBestDays, $iTimeIndex, $lstDays, $iCredits, $sDay) {
        $iIndex = 0;
        
        switch ($sDay) {
            case "Monday":
                $iIndex = 0;
                break;
            case "Tuesday":
                $iIndex = 0;
                break;
            case "Wednesday":
                $iIndex = 1;
                break;
            case "Thursday":
                $iIndex = 1;
                break;
            case "Friday":
                $iIndex = 2;
                break;
            default:
                $iIndex = 0;
        }
        
        if ($lstBestDays->size() == 2 && $iCredits == 1) {
            //check tuesday or thursday in day string
            //assign off index of day in bestdays for tuesday or thursday for one hour
            for ($i = 0; $i < 4; $i++ ) {
                $lstBestDays->get($iIndex)->getDayTimes()->get($iTimeIndex+$i)->setTimeFilled(true);
            }
        }
        else if ($lstBestDays->size() == 2 && $iCredits == 3) {
            //assign for tuesday and thursday for 1.5 hour on both time slots
            //no need to check day string
            for ($i = 0; $i < 2; $i++) {
                for ($j = 0; $j < 6; $j++ ) {
                    $lstBestDays->get($i)->getDayTimes()->get($iTimeIndex+$j)->setTimeFilled(true);
                }
            }
        }
        else if ($lstBestDays->size() == 3 && $iCredits == 1) {
            //check monday or wednesday or friday in day string
            //assign off index of day in bestdays for monday or wednesday or friday for one hour
            for ($i = 0; $i < 4; $i++ ) {
                $lstBestDays->get($iIndex)->getDayTimes()->get($iTimeIndex+$i)->setTimeFilled(true);
            }
        }
        else if ($lstBestDays->size() == 3 && $iCredits == 3) {
            //assign for Monday and Wednesday and Friday for 1 hour on all time slots
            //no need to check day string
            for ($i = 0; $i < 3; $i++) {
                for ($j = 0; $j < 4; $j++ ) {
                    $lstBestDays->get($i)->getDayTimes()->get($iTimeIndex+$j)->setTimeFilled(true);
                }
            }
        }
        return $lstBestDays;
    }
    
}