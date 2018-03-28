<?php
require_once('TestDataHandler.php');
require_once('SQLDataHandler.php');
require_once('iDataHandler.php');
require_once('Professor.php');
require_once('Room.php');
require_once('Course.php');
require_once('Section.php');
/**
 * Created by PhpStorm.
 * User: AD
 * Date: 2/2/2018
 * Time: 12:52 AM
 */

/**
 * Created by PhpStorm.
 * User: AD
 * Date: 1/31/2018
 * Time: 1:39 AM
 */
//*********** POSSIBLE WAYS OF IMPROVEMENT *************** //
//1. the first sections within the course lists will get priority on taking the first time slots. Possible
//      solution is to loop through the algorithm with every possible ordering of each section in order to
//      maximize the objective funciton further.
//2. add another weighting for faculty preference to either teaching in the morning or afternoon:
//      ex: Pij = {1.5 fits preference, 1 has no preference, .5 doesnt fit preference
//      0 for not fitting preference would 0 out the sum of the section to room fit when it is possibly
//      an ok fit so .5 is more appropriate
//3. currently the main schedule can be viewed from each section: contains the room and times being taught
//      as well as the professor teaching. Possibly add section string to professors and rooms to view what
//      sections are occupying their time slots
//*********** ENGINEERING SCHEDULING REQUIREMENTS *************** //
//1. professor can choose either a MWF or TR schedule type
//2. classes are either 1 or 3 credits
//3. 3 unit classes are either MWF (1 hour) or TR (1.5 hour) based
//4. 1 unit classes can be either MTWRF (1 hour)
//5. classes can start during an 15 minute period of any hour
//      1 credit: TR ONE HOUR (4 times + 1 on each end) = 6 slots
//      1 credit: MWF ONE HOUR (4 times + 1 on each end) = 6 slots
//      3 credits: TR 1.5 HOUR (6 + 1 each end) * 2 = 16 slots
//      3 credits: MWF 1 HOUR (4 times + 1 each end) * 3 = 18 slots
//********** CONSTRAINTS ***************//
//1. no faculty assigned to more than one class (section) at a time
//2. no room holding more than one class (section) at a time
//3. course type (ex: needs projector) must be assigned to a room that fits that constraint with happiness of 1.5
//4. at least 15 minutes between faculties end of class to next assigned class
//5. at least 15 minutes between any end of one class to the start of another class in any room
//6. section size must be less than or equal to room size assigned (efficiency weighting)

class CourseSchedulingMain
{
    
    function main() { #check it

        $dataHandler = new SQLDataHandler();
        $lstProfessors = $dataHandler->getProfessors(); //implement like this
        $lstCourses = $dataHandler->getCourses();
        $lstRooms = $dataHandler->getRooms();
        $LP = new LinearProgramming();
        
        $dTotalCourseSum = 0.0;//sum of all the sections within a course
        $lstFilledSections = array();
        $lstMissedSections = array();
        
        //Removed try/catch block for data output to text document
        
        foreach ($lstCourses as $oC) {
            $dTotalSectionSum = 0.0;
            $bIsSectionAssigned = false;
            $iEarlySectionIndex = 100;
            
            foreach ($oC->getCourseSections() as $oS) {
                $oP = $oS->getProfessorAssigned();
                $sProfesorScheduleType = (strcmp($oP->getAvailableDayNames()[0]->getDay(),"Monday") == 0) ? "MWF" : "TR";
                $dBestSectionSum = 0.0;
                $oBestRoom = null;
                
                $lstBestMWF = array();
                $lstBestMWF[] = "Monday";
                $lstBestMWF[] = "Wednesday";
                $lstBestMWF[] = "Friday";
                
                $lstBestTR = array();
                $lstBestTR[] = "Tuesday";
                $lstBestTR[] = "Thursday";
                
                foreach ($lstRooms as $oR) {
                    $lstRoomDays = $oR->getDayList();
                    $dHij = $LP->happinessRoomVal($oR, $oC, $oS);
                    $dRoomEfficiency = $oS->getSectionSize() / $oR->getSeatingCapacity();
                    
                    if ($dHij >= 1) {
                        if ($oC->getCredits() == 1 && (strcmp($sProfessorScheduleType, "MWF") == 0)) {
                            for ($i = 0; $i < $oP->getAvailableDayNames->size(); $i++) {
                                $oD = $oP->getAvailableDayTimes()[$i];
                                $lstProfessorDays = array();
                                $lstProfessorDays= $oD;
                                
                                for ($j = 0; j < $oD->getTimeLengths()->size(); $j++) {
                                    $bIsHourAvailableForRoom = $LP->isRoomtTimesAvailable($j, $lstRoomDays, $oC->getCredits(), $oD->getDay());
                                    $bIsHourAvailableForProfessor = $LP->isProfessorTimesAvailable($j, $lstProfessorDays, $oC->getCredits());
                                    
                                    if ($bIsHourAvailableForProfessor && $bIsHourAvailableForRoom) {
                                        $dSectionSum = $dHij * $dRoomEfficiency;
                                        
                                        if ($dSectionSum > $dBestSectionSum && $j < $iEarlySectionIndex) {
                                            $dBestSectionSum = $dsectionSum;
                                            $lstBestMWF = $LP->clearAllTimeSlots($lstBestMWF);
                                            $lstBestMWF = $LP->assignTimeSlots($lstBestMWF, $j, $oC->getCredits(), $oD->getDay());
                                            $oBestRoom = $oR;
                                            $bIsSectionAssigned = true;
                                            $iEarlySectionIndex = $j;
                                        } 
                                    }
                                }
                            }
                        }
                        
                        if ($oC->getCredits() == 1 && (strcmp($sProfessorScheduleType, "TR") == 0)) {
                            for ($i = 0; $i < $oP->getAvailableDayNames->size(); $i++) {
                                $oD = $oP->getAvailableDayTimes()[$i];
                                $lstProfessorDays = array();
                                $lstProfessorDays= $oD;
                                
                                for ($j = 0; j < $oD->getTimeLengths()->size(); $j++) {
                                    $bIsHourAvailableForRoom = $LP->isRoomtTimesAvailable($j, $lstRoomDays, $oC->getCredits(), $oD->getDay());
                                    $bIsHourAvailableForProfessor = $LP->isProfessorTimesAvailable($j, $lstProfessorDays, $oC->getCredits());
                                    
                                    if ($bIsHourAvailableForProfessor && $bIsHourAvailableForRoom) {
                                        $dSectionSum = $dHij * $dRoomEfficiency;
                                        
                                        if ($dSectionSum > $dBestSectionSum && $j < $iEarlySectionIndex) {
                                            $dBestSectionSum = $dsectionSum;
                                            $lstBestTR = $LP->clearAllTimeSlots($lstBestTR);
                                            $lstBestTR = $LP->assignTimeSlots($lstBestTR, $j, $oC->getCredits(), $oD->getDay());
                                            $oBestRoom = $oR;
                                            $bIsSectionAssigned = true;
                                            $iEarlySectionIndex = $j;
                                        }
                                    }
                                }
                            }
                        }
                        
                        if ($oC->getCredits() == 3 && (strcmp($sProfessorScheduleType, "MWF") == 0)) {
                            for ($i = 0; $i < $oP->getAvailableDayNames->size(); $i++) {
                                $oD = $oP->getAvailableDayTimes()[$i];
                                $lstProfessorDays = array();
                                $lstProfessorDays = $oD;
                            }
                                
                            for ($j = 0; j < $lstProfessorDays[0]->getDay(); $j++) {
                                $bIsHourAvailableForRoom = $LP->isRoomtTimesAvailable($j, $lstRoomDays, $oC->getCredits(), $lstProfessorDays[0]->getDay());
                                $bIsHourAvailableForProfessor = $LP->isProfessorTimesAvailable($j, $lstProfessorDays, $oC->getCredits());
                                    
                                if ($bIsHourAvailableForProfessor && $bIsHourAvailableForRoom) {
                                    $dSectionSum = $dHij * $dRoomEfficiency;
                                        
                                    if ($dSectionSum > $dBestSectionSum && $j < $iEarlySectionIndex) {
                                        $dBestSectionSum = $dsectionSum;
                                        $lstBestMWF = $LP->clearAllTimeSlots($lstBestMWF);
                                        $lstBestMWF = $LP->assignTimeSlots($lstBestMWF, $j, $oC->getCredits(), $lstProfessorDays[0]->getDay());
                                        $oBestRoom = $oR;
                                        $bIsSectionAssigned = true;
                                        $iEarlySectionIndex = $j;
                                    } 
                                }
                            }
                        }
                    
                        if ($oC->getCredits() == 3 && (strcmp($sProfessorScheduleType, "TR") == 0)) {
                            for ($i = 0; $i < $oP->getAvailableDayNames->size(); $i++) {
                                $oD = $oP->getAvailableDayTimes()[$i];
                                $lstProfessorDays = array();
                                $lstProfessorDays= $oD;
                            }
                                
                            for ($j = 0; j < $lstProfessorDays[0]->getDay(); $j++) {
                                $bIsHourAvailableForRoom = $LP->isRoomtTimesAvailable($j, $lstRoomDays, $oC->getCredits(), $lstProfessorDays[0]->getDay());
                                $bIsHourAvailableForProfessor = $LP->isProfessorTimesAvailable($j, $lstProfessorDays, $oC->getCredits());
                                    
                                if ($bIsHourAvailableForProfessor && $bIsHourAvailableForRoom) {
                                    $dSectionSum = $dHij * $dRoomEfficiency;
                                        
                                    if ($dSectionSum > $dBestSectionSum && $j < $iEarlySectionIndex) {
                                        $dBestSectionSum = $dsectionSum;
                                        $lstBestTR = $LP->clearAllTimeSlots($lstBestTR);
                                        $lstBestTR = $LP->assignTimeSlots($lstBestTR, $j, $oC->getCredits(), $lstProfessorDays[0]->getDay());
                                        $oBestRoom = $oR;
                                        $bIsSectionAssigned = true;
                                        $iEarlySectionIndex = $j;
                                    } 
                                }
                            }
                        }
                    }
                }
                
                if (isSectionAssigned) {
                    $lstBestTimes = (strcmp($sProfessorScheduleType, "MWF") == 0) ? $lstBestMWF : $lstBestTR;
                    
                    foreach ($lstBestTimes as $oD) {
                        for ($i = 0; $i < $lstBestTimes->size(); $i++) {
                            for ($j = 1; j < $oD->getTimeLengths->size(); $j++) {
                                if ($oD->getTimeLengths[$j - 1]->isTimeFilled() && $j == 1) {
                                    $lstBestTimes[i]->getTimeLengths[j - 1]->setCoursePlaceHolder("Course: ".$oS->getSectionID());
                                    $lstBestTimes[i]->getTimeLengths[j]->setCoursePlaceHolder("Professor: ".$oS->getProfessorAssigned());
                                } elseif ($oD->getTimeLengths[j]->isTimeFilled() && !$oD->getTimeLengths[j-1]->isTimeFilled()) {
                                    $lstBestTimes[i]->getTimeLengths[j]->setCoursePlaceHolder("Course: ".$oS->getSectionID());
                                    $lstBestTimes[i]->getTimeLengths[j + 1]->setCoursePlaceHolder("Professor: ".$oS->getProfessorAssigned());
                                }
                            }
                        }
                    }
                    
                    $oS->setRoomAssigned($oBestRoom);
                    $oS->setDayTimeAssigned($lstBestTimes);
                    
                    $oP->setAvailableDayTimes($lstBestTimes);
                    $oP->addDayTimes($lstBestTimes);
                    $oP->getSectionsTaught.add($oS);
                    
                    $oBestRoom->addDayTimes($lstBestTimes);
                    $lstFilledSections[] = $oS;
                    $bIsSectionAssigned = false;
                    $iEarlySectionIndex = 100;
                } else {
                    $lstMissedSections[] = $oS;
                }
                
                $dTotalSectionSum += $dBestSectionSum;
            }
            
            $dTotalCourseSum += $dTotalSectionSum;
        }
        
        outputPCR();
        echo "hi";
    }

    function outPutPCR(){
       //print out list of professors, list of course, list of rooms,
    }

}