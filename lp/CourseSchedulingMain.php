<?php
require_once('SQLDataHandler.php');
require_once('CourseConflict.php');
require_once('LinearProgramming.php');
require_once('Professor.php');
require_once('Room.php');
require_once('Course.php');
require_once('Section.php');
/**
* 
*/
class CourseSchedulingMain {
    
    private $lstFilledSections;
    private $lstMissedSections;
    
    private $lstProfessors;
    private $lstCourses;
    private $lstRooms;
    
    //variable to save id for version History
    private $versionHistoryId;
    
    public function generateSchedule() {
        $dataHandler = new SQLDataHandler();
        $dataHandler->clearProfessorTimesGenerated();
        $this->lstProfessors = $dataHandler->getProfessors();
        $dataHandler->clearCourseTimesGenerated();
        $dataHandler->clearRoomsAssignedToSections();
        $this->lstCourses = $dataHandler->getCourses($this->lstProfessors);
        $dataHandler->clearRoomTimesGenerated();
        $this->lstRooms = $dataHandler->getRooms();
        $LP = new LinearProgramming();
        $CC = new CourseConflict();
    
        $this->lstFilledSections = array();
        $this->lstMissedSections = array();
        $dObjectiveSum = 0.0; //objective value to be maximized
        $dTotalCourseSum = 0.0;//sum of all the sections within a course
        
        //getting version History Id
        $this->versionHistoryId = $dataHandler->getHistoryVersionId();
        
        foreach ($this->lstCourses as $oC) {
            $dTotalSectionSum = 0.0;    
            $bIsSectionAssigned = false;
            $iEarlySectionIndex = 100; //used for best early time
            
            $oCourseSections=array();
            $oCourseSections= $oC->getCourseSections();
            
            foreach ($oCourseSections as $oS) { 
                    
                $oP = $oS->getProfessorAssigned();
                $sProfessorScheduleType = $oP->getAvailableDayNames();
                $dBestSectionSum = 0.0; //best section sum
                $oBestRoom = null; //best room fit
                
                $lstBestMWF = array();
                $lstBestMWF[] = new DayTimes("M");
                $lstBestMWF[] = new DayTimes("W");
                $lstBestMWF[] = new DayTimes("F");
                
                $lstBestTR = array();
                $lstBestTR[] = new DayTimes("T");
                $lstBestTR[] = new DayTimes("R");
                
                //declare day and time indexes that will be the last saved. 
                $timeIndexSaved = -1; // negative to tell not assigned
                $dayIndexSaved = -1;
                $isWeekSection = null; //used for week string in algorithm
                
                //each room in room list
                foreach ($this->lstRooms as $oR) {
                    $lstRoomDays = $oR->getDayList(); //room times with constraints
                    $dHij = $LP->happinessRoomVal($oR, $oC, $oS); //room, course ,section
                    $dRoomEfficiency = $oS->getSectionSize() / $oR->getSeatingCapacity();
                    
                    //okay type and enough seats
                    if ($dHij >= 1) {
                    
                        //1 credit and MWF
                        if ($oC->getCredits() == 1 && (strcmp($sProfessorScheduleType, "MWF") == 0)) {
                            //each professor day
                            for ($i = 0; $i < strlen($sProfessorScheduleType); $i++) {
                                $oD = $oP->getDayList()[$i]; //individual daytime
                                $lstProfessorDays= array();
                                $lstProfessorDays[] = $oD;
    
                                for ($j = 0; $j < sizeof($oD->getTimeLengths()); $j++) {
                                    $bIsHourAvailableForRoom = $LP->isRoomTimesAvailable($j, $lstRoomDays, $oC->getCredits(), $oD->getDay());
                                    $bIsHourAvailableForProfessor = $LP->isProfessorTimesAvailable($j, $lstProfessorDays, $oC->getCredits());
                                    //sectionID, dayIndex, timeIndex, isWeek, scheduleType
                                    $bIsCourseConflict = $CC->doesCourseOverlap($oC->getSectionID, $i, $j, false, $sProfessorScheduleType);
                                    
                                    //professor is available for one hour + 15 minutes before and after
                                    if ($bIsHourAvailableForRoom && $bIsHourAvailableForProfessor && !$bIsCourseConflict) { 
                                        $dSectionSum = $dHij * $dRoomEfficiency;
                                        if ($dSectionSum >= $dBestSectionSum && $j < $iEarlySectionIndex) {
                                            $dBestSectionSum = $dsectionSum;
                                            $lstBestMWF = $LP->clearAllTimeSlots($lstBestMWF);
                                            $lstBestMWF = $LP->assignTimeSlots($lstBestMWF, $j, $oC->getCredits(), $oD->getDay());
                                            $oBestRoom = $oR;
                                            $bIsSectionAssigned = true;
                                            $iEarlySectionIndex = $j;
                                            
                                            $dayIndexSaved = $i; //save day time assigned
                                            $timeIndexSaved = $j;
                                            $isWeekSection = false; //only single day
                                        }  
                                    }
                                }
                            }
                        }
    
                        // 1 credit and TR schedule
                        if ($oC->getCredits() == 1 && (strcmp($sProfessorScheduleType, "TR") == 0)) {
                            //each professor day
                            for ($i = 0; $i < strlen($sProfessorScheduleType); $i++) {
                                $oD = $oP->getDayList()[$i];
                                $lstProfessorDays = array();
                                $lstProfessorDays[] = $oD;
                                
                                for ($j = 0; $j < sizeof($oD->getTimeLengths()); $j++) {
                                    $bIsHourAvailableForRoom = $LP->isRoomTimesAvailable($j, $lstRoomDays, $oC->getCredits(), $oD->getDay());
                                    $bIsHourAvailableForProfessor = $LP->isProfessorTimesAvailable($j, $lstProfessorDays, $oC->getCredits());
                                    //sectionID, dayIndex, timeIndex, isWeek, scheduleType
                                    $bIsCourseConflict = $CC->doesCourseOverlap($oC->getSectionID, $i, $j, false, $sProfessorScheduleType);
                                    
                                    if ($bIsHourAvailableForProfessor && $bIsHourAvailableForRoom && !$bIsCourseConflict) {
                                        $dSectionSum = $dHij * $dRoomEfficiency;
                                        
                                        if ($dSectionSum >= $dBestSectionSum && $j < $iEarlySectionIndex) {
                                            $dBestSectionSum = $dsectionSum;
                                        
                                            $lstBestTR = $LP->clearAllTimeSlots($lstBestTR);
                                            $lstBestTR = $LP->assignTimeSlots($lstBestTR, $j, $oC->getCredits(), $oD->getDay());
                                            $oBestRoom = $oR;
                                            $bIsSectionAssigned = true;
                                            $iEarlySectionIndex = $j;
                                            
                                            $dayIndexSaved = $i; //save day time assigned
                                            $timeIndexSaved = $j;
                                            $isWeekSection = false; //only single day
                                        }
                                    }
                                }
                            }
                        }
                        
                        //3 credits and MWF
                        if ($oC->getCredits() == 3 && (strcmp($sProfessorScheduleType, "MWF") == 0)) {
                            $lstProfessorDays = array();
                            for ($i = 0; $i < strlen($sProfessorScheduleType); $i++) {
                                $oD = $oP->getDayList()[$i];
                                $lstProfessorDays[] = $oD;
                            }
    
                            for ($j = 0; $j < sizeof($lstProfessorDays[0]->getTimeLengths()); $j++) {
                                $bIsHourAvailableForRoom = $LP->isRoomTimesAvailable($j, $lstRoomDays, $oC->getCredits(), $lstProfessorDays[0]->getDay());
                                $bIsHourAvailableForProfessor = $LP->isProfessorTimesAvailable($j, $lstProfessorDays, $oC->getCredits());
                                //sectionID, dayIndex, timeIndex, isWeek, scheduleType
                                $bIsCourseConflict = $CC->doesCourseOverlap($oC->getSectionID, -1, $j, true, $sProfessorScheduleType);
                                        
                                if ($bIsHourAvailableForProfessor && $bIsHourAvailableForRoom && !$bIsCourseConflict) {
                                    $dSectionSum = $dHij * $dRoomEfficiency;
                                        
                                    if ($dSectionSum >= $dBestSectionSum && $j < $iEarlySectionIndex) {
                                        $dBestSectionSum = $dsectionSum;
                                        
                                        $lstBestMWF = $LP->clearAllTimeSlots($lstBestMWF);
                                        $lstBestMWF = $LP->assignTimeSlots($lstBestMWF, $j, $oC->getCredits(), $lstProfessorDays[0]->getDay());
                                        $oBestRoom = $oR;
                                        $bIsSectionAssigned = true;
                                        $iEarlySectionIndex = $j;
                                        
                                        $timeIndexSaved = $j;
                                        $isWeekSection = true; //either MWF or TR
                                    } 
                                }
                            }
                        }
                        
                        // 3 credits and TR schedule
                        if ($oC->getCredits() == 3 && (strcmp($sProfessorScheduleType, "TR") == 0)) {
                            $lstProfessorDays = array();
                            for ($i = 0; $i < strlen($sProfessorScheduleType); $i++) {
                                $oD = $oP->getDayList()[$i];
                                $lstProfessorDays[] = $oD;
                            }
    
                            for ($j = 0; $j < sizeof($lstProfessorDays[0]->getTimeLengths()); $j++) {
                                $bIsHourAvailableForRoom = $LP->isRoomTimesAvailable($j, $lstRoomDays, $oC->getCredits(), $lstProfessorDays[0]->getDay());
                                $bIsHourAvailableForProfessor = $LP->isProfessorTimesAvailable($j, $lstProfessorDays, $oC->getCredits());
                                //sectionID, dayIndex, timeIndex, isWeek, scheduleType
                                $bIsCourseConflict = $CC->doesCourseOverlap($oC->getSectionID, -1, $j, true, $sProfessorScheduleType); 
                                
                                if ($bIsHourAvailableForProfessor && $bIsHourAvailableForRoom && !$bIsCourseConflict) {
                                    $dSectionSum = $dHij * $dRoomEfficiency;
                                        
                                    if ($dSectionSum >= $dBestSectionSum && $j < $iEarlySectionIndex) {
                                        $dBestSectionSum = $dsectionSum;
                                        
                                        $lstBestTR = $LP->clearAllTimeSlots($lstBestTR);
                                        $lstBestTR = $LP->assignTimeSlots($lstBestTR, $j, $oC->getCredits(), $lstProfessorDays[0]->getDay());
                                        $oBestRoom = $oR;
                                        $bIsSectionAssigned = true;
                                        $iEarlySectionIndex = $j;
                                        
                                        $timeIndexSaved = $j;
                                        $isWeekSection = true; //either MWF or TR
                                    } 
                                }
                            }
                        } //end 3 credit TR
                        
                    }//end if happy
                }//end roomlist
                
                if ($bIsSectionAssigned) {
                    $this->lstFilledSections[] = $oS;
                    $lstBestTimes = (strcmp($sProfessorScheduleType, "MWF") == 0) ? $lstBestMWF : $lstBestTR;
                    
                    $oS->setRoomAssigned($oBestRoom); //assign room to section
                    //set placeholders
                    $lstBestTimes = self::setBestTimesPlaceholders($lstBestTimes, 
                        $oS->getProfessorAssigned()->getProfessorsName(), 
                        $oS->getRoomAssigned()->getRoomID());
                    
                    //only generated needed since no constraints for sections
                    $oS->setDayTimeAssigned($lstBestTimes); 
                    
                    $oP->addDayTimes($lstBestTimes, $oS->getSectionID());
                    $oBestRoom->addDayTimes($lstBestTimes, $oS->getSectionID());
                    
                    $bIsSectionAssigned = false; //reset
                    $iEarlySectionIndex = 100; //reset
                    
                    //update DB with generate times for current section and its assigned room and professor
                    $dataHandler->setRoomToSection($oS->getRoomAssigned()->getRoomID(), $oS->getSectionID());
                    $dataHandler->setProfessorTimesGenerated($oP);
                    $dataHandler->setRoomTimesGenerated($oBestRoom);
                    $dataHandler->setSectionTimesGenerated($oS);
                    
                    $dayString = self::getDayString($dayIndexSaved, $isWeekSection, $sProfessorScheduleType);
                    $timeString = self::getTimeString($timeIndexSaved, $oC->getCredits(), $sProfessorScheduleType);
                    
                    //saving information in Version History tables
                    $dataHandler->saveVersionHistoryAssignedSections($this->versionHistoryId, $oC->getCourseCode(), $oS->getSection(), 
                        $dayString, $timeString, $oP->getProfessorsName(), $oBestRoom->getRoomID());
                        
                    $CC->lstCourseConflict[] = array("sectionCode"=>$oS->getSectionID, "dayIndex"=>$dayIndexSaved,  
                        "timeIndex"=>$timeIndexSaved, "isWeek"=>$isWeekSection, "schedule"=>$sProfessorScheduleType); 
                } 
                else {
                    $this->lstMissedSections[] = $oS;
                    
                    //saving information to db of missed sections
                    $dataHandler->saveVersionHistoryUnassignedSections($this->versionHistoryId, $oC->getCourseCode(), $oS->getSection(), $oP->getProfessorsName());
                }
                
                $dTotalSectionSum += $dBestSectionSum;
            }
            
            $dTotalCourseSum += $dTotalSectionSum;
            $dTotalSectionSum = 0.0;
        }
        $dObjectiveSum += $dTotalCourseSum;
        $dTotalCourseSum = 0.0;
    }
    
    private function getTimeString($timeIndexSaved, $credits, $sProfessorScheduleType) {

        //start time
        $timeHourStart = 8;
        $timeMinuteStart = 0;
        for ($i = 1; $i < 52; $i++) {
            if ($timeIndexSaved > $i - 1) {
                if ($i % 4 == 0) 
                    $timeHourStart++;
                // 0 15 30 45
                $timeMinuteStart + 15 == 60 ? $timeMinuteStart = 0 : $timeMinuteStart += 15;
            }
        }
        $hourOrig = $timeHourStart;
        $timeHourStart > 11 ? $ampmStart = "pm" : $ampmStart = "am";
        if ($timeHourStart > 12)
            $timeHourStart %= 12;
        
        //end time
        $credits == 1 || strcmp($sProfessorScheduleType, "MWF") == 0 ? $addMinute = 0 : $addMinute = 30;
        $addMinute + $timeMinuteStart >= 60 ? $timeMinuteEnd = ($addMinute + $timeMinuteStart) % 60 : $timeMinuteEnd = $addMinute + $timeMinuteStart;
        $addMinute + $timeMinuteStart >= 60 ? $addHours = 2 : $addHours = 1;
        $hourOrig + $addHours > 11 ? $ampmEnd = "pm" : $ampmEnd = "am";
        $hourOrig + $addHours > 12 ? $timeHourEnd = ($hourOrig + $addHours) % 12 : $timeHourEnd = $hourOrig + $addHours;
        
        if ($timeMinuteStart == 0) 
            $timeMinuteStart = "00";
        if ($timeMinuteEnd == 0) 
            $timeMinuteEnd = "00";
        
        return $timeHourStart.":".$timeMinuteStart.$ampmStart."-".$timeHourEnd.":".$timeMinuteEnd.$ampmEnd;
    }
    
    private function getDayString($dayIndexSaved, $isWeekSection, $sProfessorScheduleType) {

        if ($isWeekSection){
            return $sProfessorScheduleType;
        } 
        else if (strcmp($sProfessorScheduleType, "MWF") == 0) {
            switch ($dayIndexSaved) {
                case 0:
                    return "M";
                    break;
                case 1:
                    return "W";
                    break;
                case 2:
                    return "F";
                default:
                    return "";
            }
        }
        else {
            switch ($dayIndexSaved) {
                case 0:
                    return "T";
                    break;
                case 1:
                    return "R";
                    break;
                default:
                    return "";
            }
        }
    }
    
    public function outputGenerated() {
        
        foreach ($this->lstFilledSections as $oS) {
            print "filled ".$oS->getSectionID()." ".$oS->getProfessorAssigned()->getProfessorsName()." ".$oS->getRoomAssigned()->getRoomID()."<br>";
        }
        foreach ($this->lstMissedSections as $oS) {
            print "missed ".$oS->getSectionID()." ".$oS->getProfessorAssigned()->getProfessorsName()."<br>";
        }
        
        
        print "************** LIST OF PROFESSORS **************"."<br>";
        foreach($this->lstProfessors as $oP) {
            print $oP;
            foreach ($oP->getDayList() as $oD) {
                print $oD;
                foreach ($oD->getTimeLengths() as $oT) {
                    print $oT;
                }
            }
            print "<br><br><br>";
        }
        
        print "************** LIST OF ROOMS **************"."<br>";
        foreach($this->lstRooms as $oR) {
            print $oR;
            foreach ($oR->getDayList() as $oD) {
                print $oD;
                foreach ($oD->getTimeLengths() as $oT) {
                    print $oT;
                }
            }
            print "<br><br><br>";
        }
        
        print "************** LIST OF COURSES **************"."<br>";
        foreach($this->lstCourses as $oC) {
            print $oC."----------<br>";
            foreach($oC->getCourseSections() as $oS) {
                print $oS;
                foreach ($oS->getDayTimeAssigned() as $oD) {
                    print $oD;
                    foreach ($oD->getTimeLengths() as $oT) {
                        print $oT;
                    }
                }
                print "<br><br><br>";
            }
        }
        
    }
    
    //set primary and alternate placeholders for bestTImes
    private function setBestTimesPlaceholders($lstBestTimes, $pName, $rID) {
        foreach ($lstBestTimes as $oD) {
            foreach ($oD->getTimeLengths() as $oT) {
                if ($oT->isTimeFilled() && !$oT->isTimeConstraint()) {
                    $oT->setPrimaryPlaceHolder($rID);
                    $oT->setAlternatePlaceHolder($pName);
                }
            }
        }
        return $lstBestTimes;
    }
}
