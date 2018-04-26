<?php
require_once('Professor.php');
require_once('Room.php');
require_once('Course.php');
require_once('Section.php');

class SQLDataHandler
{

    private $lstProfessors;
    private $lstRooms;
    private $lstCourses;
    private $db; //PDO
    
    function __construct() {
        require('PDO.php');
        $this->lstProfessors = array();
        $this->lstRooms = array();
        $this->lstCourses = array();
    }
    
    // @param Professor $oProfessor
    // @return int rows affected
    public function addProfessor($oProfessor) {
        $query = 'INSERT INTO professor (id, name, availableDays) VALUES (:id, :name, :availableDays)';
	    $statement = $this->db->prepare($query);
	    $statement->bindValue(':id', $oProfessor->getProfessorsID());
	    $statement->bindValue(':name', $oProfessor->getProfessorsName());
	    $statement->bindValue(':availableDays', $oProfessor->getAvailableDayNames());
	    $result = $statement->execute(); //true or false executed
	    $statement->closeCursor();
	    
	    return $statement->rowCount();
    }
    
    // @param int $iProfessorID
    // @return int rows affected
    public function deleteProfessor($iProfessorID){
        $query = 'DELETE FROM professor WHERE id = :id';
	    $statement = $this->db->prepare($query);
	    $statement->bindValue(':id', $iProfessorID);
	    $result = $statement->execute(); //true or false executed
	    $statement->closeCursor();
	    
	    return $statement->rowCount();
    }
    

    // @param void
    // @return ArrayList $lstProfessors: list of professors
    public function getProfessors() {
		$query = 'SELECT * FROM professor ORDER BY name';
	    $statement = $this->db->prepare($query);
	    $statement->execute();
	    $result = $statement->fetchAll();
	    $statement->closeCursor();
	    
	    foreach($result as $p) {
	        //597045, "Jonathan Norris", "MWF"
	        $professor = new Professor ($p['id'], $p['name'], $p['availableDays']);
	        //mwf: 1 3 5
	        //tr: 2 4
	        $days = (strcmp($p['availableDays'], "MWF") == 0) ? [1,3,5] : [2,4]; //used for column index
	        
	        for ($d = 0; $d < strlen($p['availableDays']); $d++) {
	            for ($t = 0; $t < 52; $t++) {
	                $timeVals = explode(";",$p[($days[$d])."_".$t]);//course placeholder, alternate placeholder, isFilled, isConstraint
	                
	                //set course and alternate placeholder
	                if (strcmp($timeVals[0], "null") != 0) {
	                    $professor->getDayByIndex($d)->getTimeLengthByIndex($t)->setPrimaryPlaceHolder($timeVals[0]);
	                }
	                if (strcmp($timeVals[1], "null") != 0) {
	                    $professor->getDayByIndex($d)->getTimeLengthByIndex($t)->setAlternatePlaceHolder($timeVals[1]);
	                }
	                
	               // //set is filled and is constraint
	                $isFilled = ($timeVals[2] == "true") ? true : false;
	                $isConstraint = ($timeVals[3] == "true") ? true : false;
	                $professor->getDayByIndex($d)->getTimeLengthByIndex($t)->setTimeFilled($isFilled);
	                $professor->getDayByIndex($d)->getTimeLengthByIndex($t)->setIsConstraint($isConstraint);
	            }
	        }
	        $this->lstProfessors[$p['id']] = $professor;
	    }
	   return $this->lstProfessors;
    }
    

    // @param Professor $oProfessor
    // @param int $iDayIndex
    // @param int $iStartIndex
    // @param int $iEndIndex
    // @param boolean $bIsSetting : setting if true, clearing if false
    // @return boolean true succesfully updated/no change false if on wrong day
    public function updateProfessorTimesConstraint($oProfessor, $iDayIndex, $iStartIndex, $iEndIndex, $bIsSetting) {
        
        //only update indexes associated with the professors schedule
        $weekType = $oProfessor->getAvailableDayNames();
        if (($iDayIndex == 2 || $iDayIndex == 4) && strcmp($weekType,"TR") == 0 
            || (($iDayIndex == 1 || $iDayIndex == 3 || $iDayIndex == 5) && strcmp($weekType,"MWF") == 0)) {
                
            $professorID = $oProfessor->getProfessorsID();
            $rowCount = 0;
            
            for ($i = $iStartIndex; $i <= $iEndIndex-1; $i++) {
                $timeString = $this->getTimeStringP($professorID, $iDayIndex, $i);
                
                $timeVals = explode(";",$timeString);//course placeholder, alternate placeholder, isFilled, isConstraint
                $timeVals[2] = ($bIsSetting) ? "true":"false"; //setting or clearing
                $timeVals[3] = ($bIsSetting) ? "true":"false";
                
                //reconstruct string dont keep placeholders anymore
                $timeString = "null;null;".$timeVals[2].";".$timeVals[3].";";
                
                $query = "UPDATE professor SET ".$iDayIndex."_".$i." = :timeString WHERE id = :professorID";
    	        $statement = $this->db->prepare($query);
    	        $statement->bindValue(':timeString', $timeString );
    	        $statement->bindValue(':professorID', $professorID);
    	        $statement->execute();
    	        $statement->closeCursor();
    	        
    	        $rowCount += $statement->rowCount();
            }
            //either rows affected the same or if none affected
            return ($rowCount == (($iEndIndex-1) - $iStartIndex) + 1 || $rowCount == 0) ? true: false; 
        }
        else {
            return false;
        }
    }
    
    // @param void
    // @return void
    public function clearProfessorTimesGenerated() {
        $query = "SELECT * FROM professor";
	    $statement = $this->db->prepare($query);
	    $statement->execute();
	    $result = $statement->fetchAll();
	    $statement->closeCursor();

	    foreach ($result as $r) {
	        
           $days = (strcmp($r['availableDays'], "MWF") == 0) ? [1,3,5] : [2,4]; //used for column index
           $pID = $r['id'];
            
           for ($d = 0; $d < sizeof($days); $d++) {
               for ($t = 0; $t < 52; $t++) {
                  $timeVals = explode(";",$r[($days[$d])."_".$t]); //course placeholder, alternate placeholder, isFilled, isConstraint
                  //clear time if a generated time value
                    if (strcmp($timeVals[2], "true") == 0 && strcmp($timeVals[3], "false") == 0) {
                        $query2 = "UPDATE professor set ".$days[$d]."_".$t." = 'null;null;false;false;' WHERE id = :id";
                        $statement2 = $this->db->prepare($query2);
                        $statement2->bindValue(':id', $pID);
                        $statement2->execute();
                        $statement2->closeCursor();
                    }
               }
           }
	    }
    }
    
    // @param Professor
    // @return void
    public function setProfessorTimesGenerated($oProfessor) {
       
        $days = (strcmp($oProfessor->getAvailableDayNames(), "MWF") == 0) ? [1,3,5] : [2,4]; //used for column index
        
        for ($d = 0; $d < sizeof($days); $d++) {
            for ($t = 0; $t < 52; $t++) {
                $oT = $oProfessor->getDayList()[$d]->getTimeLengths()[$t];
                if ($oT->isTimeFilled() && !$oT->isTimeConstraint()) {
                    $primary = strcmp($oT->getPrimaryPlaceHolder(), "") == 0 ? 'null;' : $oT->getPrimaryPlaceHolder().';';
                    $alternate = strcmp($oT->getAlternatePlaceHolder(), "") == 0 ? 'null;' : $oT->getAlternatePlaceHolder().';';
                    $timeString = $primary.$alternate.'true;false;';
        
                    $query = "UPDATE professor set ".$days[$d]."_".$t." = :timeString WHERE id = :id";
                    $statement = $this->db->prepare($query);
                    $statement->bindValue(':id', $oProfessor->getProfessorsID());
                    $statement->bindValue(':timeString', $timeString);
                    $statement->execute();
                    $statement->closeCursor();     
                }
            }
        }
    }
    
    // @param String $professorID
    // @return Professor
    public function getProfessorByID($professorID) {
		$query = 'SELECT * FROM professor WHERE id = :id';
	    $statement = $this->db->prepare($query);
	    $statement->bindValue(':id', $professorID);
	    $statement->execute();
	    $p = $statement->fetch();
	    $statement->closeCursor();

        //597045, "Jonathan Norris", "MWF"
        $professor = new Professor ($p['id'], $p['name'], $p['availableDays']);
        //mwf: 1 3 5
        //tr: 2 4
        $days = (strcmp($p['availableDays'], "MWF") == 0) ? [1,3,5] : [2,4]; //used for column index
	        
        for ($d = 0; $d < strlen($p['availableDays']); $d++) {
            for ($t = 0; $t < 52; $t++) {
                $timeVals = explode(";",$p[($days[$d])."_".$t]);//course placeholder, alternate placeholder, isFilled, isConstraint
                
                //set course and alternate placeholder
                if (strcmp($timeVals[0], "null") != 0) {
                    $professor->getDayByIndex($d)->getTimeLengthByIndex($t)->setPrimaryPlaceHolder($timeVals[0]);
                }
                if (strcmp($timeVals[1], "null") != 0) {
                    $professor->getDayByIndex($d)->getTimeLengthByIndex($t)->setAlternatePlaceHolder($timeVals[1]);
                }
                
               // //set is filled and is constraint
                $isFilled = ($timeVals[2] == "true") ? true : false;
                $isConstraint = ($timeVals[3] == "true") ? true : false;
                $professor->getDayByIndex($d)->getTimeLengthByIndex($t)->setTimeFilled($isFilled);
                $professor->getDayByIndex($d)->getTimeLengthByIndex($t)->setIsConstraint($isConstraint);
            }
        }
	   return $professor;
    }
    
    //@param String $professorID:
    //@param int $iDayIndex: 1-5 M-F
    //@param int $i: time index
    //@return  String timelength "null;null;false;false;"
    private function getTimeStringP($professorID, $iDayIndex, $i) {
        $query = "SELECT ".$iDayIndex."_".$i." FROM professor WHERE id = :professorID";
        $statement = $this->db->prepare($query);
        $statement->bindValue(':professorID', (int) $professorID); //596045
        $statement->execute();
        $result = $statement->fetch();
        $statement->closeCursor(); 
        
        return $result[$iDayIndex."_".$i];
    }
    
    // @param Room $oRoom
    // @return int rows affected
    public function addRoom($oRoom) {
        $query = 'INSERT INTO room (id, roomNum, building, roomString, seating, type) VALUES (null, :roomNum, :building, :roomString, :seating, :type)';
	    $statement = $this->db->prepare($query);
	    $statement->bindValue(':roomNum', $oRoom->getRoomNumber());
	    $statement->bindValue(':building', $oRoom->getBuilding());
	    $statement->bindValue(':roomString', $oRoom->getBuilding()." ".$oRoom->getRoomNumber());
	    $statement->bindValue(':seating', $oRoom->getSeatingCapacity());
	    $statement->bindValue(':type', $oRoom->getRoomType());
	    $statement->execute(); //true or false executed
	    $statement->closeCursor();
	    
	    return $statement->rowCount();
    }
    
    // @param String $sRoomID: building and room num with space: "EGR 215A"
    // @return int rows affected
    public function deleteRoom($sRoomID) {
        $query = 'DELETE FROM room WHERE roomString = :id';
	    $statement = $this->db->prepare($query);
	    $statement->bindValue(':id', $sRoomID);
	    $statement->execute();
	    $statement->closeCursor();
	    
	    return $statement->rowCount();
    }
    
    // @param void
    // @return ArrayList $lstRooms: list of rooms
    public function getRooms() {
        $query = 'SELECT * FROM room';
	    $statement = $this->db->prepare($query);
	    $statement->execute();
	    $result = $statement->fetchAll();
	    $statement->closeCursor();
	    
	    foreach($result as $r) {
	        $room = new Room($r['roomNum'], $r['building'], $r['seating'], $r['type']);
	        for ($d = 1; $d < 6; $d++) {
	            for ($t = 0; $t < 52; $t++) {
	                
	                $timeVals = explode(";",$r[$d."_".$t]);//course placeholder, alternate placeholder, isFilled, isConstraint
	                //set course and alternate placeholder
	                if (strcmp($timeVals[0], "null") != 0) {
	                    $room->getDayByIndex($d-1)->getTimeLengthByIndex($t)->setPrimaryPlaceHolder($timeVals[0]);
	                }
	                if (strcmp($timeVals[1], "null") != 0) {
	                    $room->getDayByIndex($d-1)->getTimeLengthByIndex($t)->setAlternatePlaceHolder($timeVals[1]);
	                }
	                
	                //set is filled and is constraint
	                $isFilled = ($timeVals[2] == "true") ? true : false;
	                $isConstraint = ($timeVals[3] == "true") ? true : false;
	                $room->getDayByIndex($d-1)->getTimeLengthByIndex($t)->setTimeFilled($isFilled);
	                $room->getDayByIndex($d-1)->getTimeLengthByIndex($t)->setIsConstraint($isConstraint);
	            }
	        }
	        $this->lstRooms[] = $room;
	    }

        return $this->lstRooms;
    }
    
    // @param Room $oRoom
    // @param int $iDayIndex
    // @param int $iStartIndex
    // @param int $iEndIndex
    // @param boolean $bIsSetting : setting if true, clearing if false
    // @return boolean true succesfully updated false not updated
    public function updateRoomTimesConstraint($oRoom, $iDayIndex, $iStartIndex, $iEndIndex, $bIsSetting) {
        
        $roomString = $oRoom->getRoomID();
        $rowCount = 0;
        
        for ($i = $iStartIndex; $i <= $iEndIndex-1; $i++) {
            $timeString = $this->getTimeStringR($roomString, $iDayIndex, $i);
            
            $timeVals = explode(";",$timeString);//course placeholder, alternate placeholder, isFilled, isConstraint
            $timeVals[2] = ($bIsSetting) ? "true":"false"; //setting or clearing
            $timeVals[3] = ($bIsSetting) ? "true":"false";
            //reconstruct string
            $timeString = $timeVals[0].";".$timeVals[1].";".$timeVals[2].";".$timeVals[3].";";
            
            $query = "UPDATE room SET ".$iDayIndex."_".$i." = :timeString WHERE roomString = :roomString";
	        $statement = $this->db->prepare($query);
	        $statement->bindValue(':timeString', $timeString );
	        $statement->bindValue(':roomString', $roomString);
	        $statement->execute();
	        $statement->closeCursor();
	        
	        $rowCount += $statement->rowCount();
        }
        return ($rowCount == (($iEndIndex-1) - $iStartIndex) + 1 || $rowCount == 0) ? true: false;
    }
    
    // @param void
    // @return void
    public function clearRoomTimesGenerated() {
        $query = 'SELECT * FROM room';
	    $statement = $this->db->prepare($query);
	    $statement->execute();
	    $result = $statement->fetchAll();
	    $statement->closeCursor();

	    foreach ($result as $r) {
           $rID = $r['roomString']; //YGR 123A
           
           for ($d = 1; $d < 6; $d++) {
               for ($t = 0; $t < 52; $t++) {
                   $timeVals = explode(";",$r[$d."_".$t]); //course placeholder, alternate placeholder, isFilled, isConstraint
                   
                   //clear time if a generated time value
                    if (strcmp($timeVals[2], "true") == 0 && strcmp($timeVals[3], "false") == 0) {
                        $query2 = "UPDATE room set ".$d."_".$t." = 'null;null;false;false;' WHERE roomString = :id";
                        $statement2 = $this->db->prepare($query2);
                        $statement2->bindValue(':id', $rID);
                        $statement2->execute();
                        $statement2->closeCursor();
                    }
               }
           }
	    }
    }
    
    // @param Room
    // @return void
    public function setRoomTimesGenerated($oRoom) {
        $d = 1;
        foreach ($oRoom->getDayList() as $oD) {
            $t = 0;
            foreach ($oD->getTimeLengths() as $oT) {
                if ($oT->isTimeFilled() && !$oT->isTimeConstraint()) {
                    $primary = strcmp($oT->getPrimaryPlaceHolder(), "") == 0 ? 'null;' : $oT->getPrimaryPlaceHolder().';';
                    $alternate = strcmp($oT->getAlternatePlaceHolder(), "") == 0 ? 'null;' : $oT->getAlternatePlaceHolder().';';
                    $timeString = $primary.$alternate.'true;false;';

                    $query = "UPDATE room set ".$d."_".$t." = :timeString WHERE roomString = :id";
                    $statement = $this->db->prepare($query);
                    $statement->bindValue(':id', $oRoom->getRoomID());
                    $statement->bindValue(':timeString', $timeString);
                    $statement->execute();
                    $statement->closeCursor();
                }
                $t++;
            }
            $d++;
        }
    }

    //@param String $roomID: "YGR 123A"
    //@return  Room
    public function getRoomByID($roomID) {
        $query = 'SELECT * FROM room WHERE roomString = :roomID';
	    $statement = $this->db->prepare($query);
	    $statement->bindValue(':roomID', $roomID);
	    $statement->execute();
	    $r = $statement->fetch();
	    $statement->closeCursor();
	    
	    if (isset($r['roomNum'])) {
	        $room = new Room($r['roomNum'], $r['building'], $r['seating'], $r['type']);
            for ($d = 1; $d < 6; $d++) {
                for ($t = 0; $t < 52; $t++) {
                    
                    $timeVals = explode(";",$r[$d."_".$t]);//course placeholder, alternate placeholder, isFilled, isConstraint
                    //set course and alternate placeholder
                    if (strcmp($timeVals[0], "null") != 0) {
                        $room->getDayByIndex($d-1)->getTimeLengthByIndex($t)->setPrimaryPlaceHolder($timeVals[0]);
                    }
                    if (strcmp($timeVals[1], "null") != 0) {
                        $room->getDayByIndex($d-1)->getTimeLengthByIndex($t)->setAlternatePlaceHolder($timeVals[1]);
                    }
                    
                    //set is filled and is constraint
                    $isFilled = ($timeVals[2] == "true") ? true : false;
                    $isConstraint = ($timeVals[3] == "true") ? true : false;
                    $room->getDayByIndex($d-1)->getTimeLengthByIndex($t)->setTimeFilled($isFilled);
                    $room->getDayByIndex($d-1)->getTimeLengthByIndex($t)->setIsConstraint($isConstraint);
                }
    	    }
            return $room;
	    }
	    else
	        return null;
    }

    //@param String $roomString: "YGR123A"
    //@param int $iDayIndex: 1-5 M-F
    //@param int $i: time index
    //@return  String timelength "null;null;false;false;"
    private function getTimeStringR($roomString, $iDayIndex, $i) {
        $query = "SELECT ".$iDayIndex."_".$i." FROM room WHERE roomString = :roomString";
        $statement = $this->db->prepare($query);
        $statement->bindValue(':roomString', $roomString); //YGR123A
        $statement->execute();
        $result = $statement->fetch();
        $statement->closeCursor(); 
        
        return $result[$iDayIndex."_".$i];
    }
    
    // @param Course $oCourse
    // @return int rows affected
    public function addCourse($oCourse) {
       // return array_push($this->lstCourses, $oCourse);
        $query = 'INSERT INTO course (courseCode, credits, type) VALUES (:courseCode, :credits, :type)';
	    $statement = $this->db->prepare($query);
	    $statement->bindValue(':courseCode', $oCourse->getCourseCode());
	    $statement->bindValue(':credits', (int) $oCourse->getCredits());
	    $statement->bindValue(':type', $oCourse->getCourseType());
	    $statement->execute();
	    $statement->closeCursor();
	    
	    return $statement->rowCount();
    }
    
    // @param String $sCourseCode: EGR101 without section
    // @return int rows affected
    public function deleteCourse($sCourseCode) {
        $sectionsArray = $this->getSectionIDs($sCourseCode);
        $deletedCount = 0;
        
        //delete course
        $query = 'DELETE FROM course WHERE courseCode = :courseCode';
	    $statement = $this->db->prepare($query);
        $statement->bindValue(':courseCode', $sCourseCode); 
        $statement->execute();
        $statement->closeCursor();
        $deletedCount += $statement->rowCount();
        //delete sections of course
        $isSecsDeleted = $this->deleteSections($sectionsArray);
        return ($isSecsDeleted && $deletedCount == 1) ? true: false;
    }
    
    // @param List $lstP: list of professors by key value
    // @return List $lstCourses: list of courses
    public function getCourses($lstP) {
        $query1 = 'SELECT * FROM course ORDER BY credits DESC, courseCode ASC';
	    $statement = $this->db->prepare($query1);
	    $statement->execute();
	    $result = $statement->fetchAll();
	    $statement->closeCursor();
    
        foreach ($result as $c) {
            $course = new Course($c['courseCode'], $c['credits'], $c['type']);
	        //print $c['courseCode'].$c['credits'].$c['type']."<br>";
	        $query2 = 'SELECT * FROM section WHERE courseCode = :cCode ORDER BY sectionID ASC';
    	    $statement2 = $this->db->prepare($query2);
    	    $statement2->bindValue(':cCode', $c['courseCode']);
    	    $statement2->execute();
    	    $result2 = $statement2->fetchAll();
    	    $statement2->closeCursor();
    	    
    	    //assign sections to course
    	    $lstSectionList = array();
    	    foreach ($result2 as $s) {
    	        //assign professor and room to new section
    	        $oProfessor = $lstP[$s['professorID']]; // professorID is key value is Professor
    	        $idP = $oProfessor->getProfessorsID();
    	        $oRoom = self::getRoomByID($s['roomID']);
    	        $section = new Section($s['sectionID'], $c['courseCode'], $s['seating'], $oProfessor);
    	        if (!is_null($oRoom))
    	            $section->setRoomAssigned($oRoom);
    	        
    	        //assign placeholders, is filled and is constraint
    	        for ($d = 1; $d < 6; $d++) {
    	            for ($t = 0; $t < 52; $t++) {
    	                $timeVals = explode(";",$s[$d."_".$t]);//primary placeholder, alternate placeholder, isFilled, isConstraint
    	                if (strcmp($timeVals[0], "null") != 0) {
    	                    $section->getDayByIndex($d-1)->getTimeLengthByIndex($t)->setPrimaryPlaceHolder($timeVals[0]);
    	                }
    	                if (strcmp($timeVals[1], "null") != 0) {
    	                    $section->getDayByIndex($d-1)->getTimeLengthByIndex($t)->setAlternatePlaceHolder($timeVals[1]);
    	                }
    	                
    	                //set is filled and is constraint
    	                $isFilled = ($timeVals[2] == "true") ? true : false;
    	                $isConstraint = ($timeVals[3] == "true") ? true : false;
    	                $section->getDayByIndex($d-1)->getTimeLengthByIndex($t)->setTimeFilled($isFilled);
    	                $section->getDayByIndex($d-1)->getTimeLengthByIndex($t)->setIsConstraint($isConstraint);
    	            } //minute
    	        } //hour
    	        $lstSectionList[] = $section;
    	    } // section
    	    $course->assignAllSections($lstSectionList);
	        $this->lstCourses[] = $course;
        } //course
        return $this->lstCourses;
    }
    
    // @param void
    // @return void
    public function clearCourseTimesGenerated() {
        $query = 'SELECT * FROM section';
	    $statement = $this->db->prepare($query);
	    $statement->execute();
	    $result = $statement->fetchAll();
	    $statement->closeCursor();

	    foreach ($result as $r) {
           //$days = (strcmp($r['availableDays'], "MWF") == 0) ? [1,3,5] : [2,4]; //used for column index
           $cID = $r['sectionID']; //EGR101A
           
           for ($d = 1; $d < 6; $d++) {
               for ($t = 0; $t < 52; $t++) {
                   $timeVals = explode(";",$r[$d."_".$t]); //course placeholder, alternate placeholder, isFilled, isConstraint
                   
                   //clear time if a generated time value
                    if (strcmp($timeVals[2], "true") == 0 && strcmp($timeVals[3], "false") == 0) {
                        $query2 = "UPDATE section set ".$d."_".$t." = 'null;null;false;false;' WHERE sectionID = :id";
                        $statement2 = $this->db->prepare($query2);
                        $statement2->bindValue(':id', $cID);
                        $statement2->execute();
                        $statement2->closeCursor();
                    }
               }
           }
	    }
    }
    
    // @param Section
    // @return void
    public function setSectionTimesGenerated($oSection) {
        
        $days = (sizeof($oSection->getDayTimeAssigned()) == 3) ? [1,3,5] : [2,4]; //used for column index
        $d = 0;
        foreach ($oSection->getDayTimeAssigned() as $oD) {
            $t = 0;
            foreach ($oD->getTimeLengths() as $oT) {
                if ($oT->isTimeFilled() && !$oT->isTimeConstraint()) {
                    $primary = strcmp($oT->getPrimaryPlaceHolder(), "") == 0 ? 'null;' : $oT->getPrimaryPlaceHolder().';';
                    $alternate = strcmp($oT->getAlternatePlaceHolder(), "") == 0 ? 'null;' : $oT->getAlternatePlaceHolder().';';
                    $timeString = $primary.$alternate.'true;false;';

                    $query = "UPDATE section set ".$days[$d]."_".$t." = :timeString WHERE courseSection = :id";
                    $statement = $this->db->prepare($query);
                    $statement->bindValue(':id', $oSection->getSectionID());
                    $statement->bindValue(':timeString', $timeString);
                    $statement->execute();
                    $statement->closeCursor();
                }
                $t++;
            }
            $d++;
        }
    }
    
    // @param String $roomID
    // @param String $sectionID
    // @return void
    public function setRoomToSection($roomID, $sectionID) {
        $query = "UPDATE section set roomID = :rid WHERE courseSection = :sid";
        $statement = $this->db->prepare($query);
        $statement->bindValue(':rid', $roomID);
        $statement->bindValue(':sid', $sectionID);
        $statement->execute();
        $statement->closeCursor();
    }
    
    //clear sections roomIDs before generating a new schedule
    // @param void
    // @return void
    public function clearRoomsAssignedToSections() {
        $query = "UPDATE section set roomID = NULL WHERE id > 0";
        $statement = $this->db->prepare($query);
        $statement->execute();
        $statement->closeCursor();
    }
    
    // @param Section $oSection
    // @param String $sCourseCode : course section is added to
    // @return int rows affected
    public function addSection($oSection, $sCourseCode) {
        $query = 'INSERT INTO section (sectionID, courseCode, courseSection, seating, professorID) 
            VALUES (:sectionID, :courseCode, :courseSection, :seating, :professorID)';
	    $statement = $this->db->prepare($query);

	    $statement->bindValue(':sectionID', $oSection->getSection());
	    $statement->bindValue(':courseCode', $sCourseCode);
	    $statement->bindValue(':courseSection', $oSection->getSectionID());
	    $statement->bindValue(':seating', (int) $oSection->getSectionSize());
	    $statement->bindValue(':professorID', $oSection->getProfessorAssigned()->getProfessorsID());
	    $statement->execute();
	    $statement->closeCursor();
	   
	    return $statement->rowCount();
    }
    
    // @param Array $aSectionIDs : array of all ids to be deleted: ["EGR101A", "EGR101B"]
    // @return boolean true all rows deleted false if not
    public function deleteSections($aSectionIDs) {
        $sectionsCount = sizeof($aSectionIDs);
        $deletedCount = 0;
        
        for ($i = 0; $i < $sectionsCount; $i++) {
            $query = 'DELETE FROM section WHERE courseSection = :sectionID';
	        $statement = $this->db->prepare($query);
	        $statement->bindValue(':sectionID', $aSectionIDs[$i]);
	        $statement->execute();
	        $statement->closeCursor();
	        $deletedCount += $statement->rowCount();
        }
	    
	    return ($sectionsCount == $deletedCount) ? true : false;
    }
    
    //@param String $courseCode: "EGR101
    //@return  array of sectionIDs ["EGR101A","EGR101B"]
    private function getSectionIDs($courseCode) {
        $query = 'SELECT courseSection FROM section WHERE courseCode = :courseCode';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':courseCode', $courseCode); //EGR101
        $statement->execute();
        $result = $statement->fetchAll();
        $statement->closeCursor(); 
        
        $sectionArray = array();
        foreach ($result as $s) {
            $sectionArray[] = $s['courseSection'];
        }
        
        return $sectionArray;
    }
    
    //@param String $email: "aperkins@cbu.ed"
    //@return  String hashed password
    public function getUserPassword($email){
		$query = 'SELECT password FROM users WHERE email = :userEmail';
	    $statement = $this->db->prepare($query);
	    $statement->bindValue(':userEmail', $email);
	    $statement->execute();
	    $result = $statement->fetch();
	    $passwordServer = $result['password'];
	    $statement->closeCursor();
	    
	   return $passwordServer;
	}
	
	//@param 
	//Description: this function is to be called at the beginning of the algorithms' run. 
	//return Version id for current algorithm run
	public function getHistoryVersionId(){
	    //inserting current timestamp to database
	    //NOTE: the Interval is being used in order to keep the current timezone in California. Adjust as necessary
	    $query = 'INSERT INTO VersionsSchedule (versionId, dateCreated) VALUES (null, Now() - INTERVAL 7 HOUR)';
	    $statement = $this->db->prepare($query);
	    $statement->execute();
	    $last_id = $this->db->lastInsertId();  //this acquires the id of last inserted
	    $statement->closeCursor();
	    
	    return $last_id;
	}
	
	//Description: this function takes in the necessary parameters to save a history of the versions that are created by the algorithm. 
	//@param string course name, string section name, string daysAssigned, string time when course takes place
	//       string professors'name , string room building and number
	//@return void 
	public function saveVersionHistoryAssignedSections($last_id, $course, $section, $daysAssigned, $classTime, $profName, $room){
	    
	    $query = 'INSERT INTO Schedule (idVersion, course, section, daysAssigned, time, professor, room) VALUES 
	        (:id, :course, :section, :daysAssigned, :time, :professor, :room)';
	    $statement = $this->db->prepare($query);
	    $statement->bindValue(':id', $last_id, PDO::PARAM_STR);
	    $statement->bindValue(':course', $course, PDO::PARAM_STR);
	    $statement->bindValue(':section', $section, PDO::PARAM_STR);
	    $statement->bindValue(':daysAssigned', $daysAssigned, PDO::PARAM_STR);
	    $statement->bindValue(':time', $classTime, PDO::PARAM_STR);
	    $statement->bindValue(':professor', $profName, PDO::PARAM_STR);
	    $statement->bindValue(':room', $room, PDO::PARAM_STR);
	    $statement->execute();
	    $statement->closeCursor();
	}
	
	//@param string course name, string section name, string daysAssigned, string time when course takes place
	//Description: this function takes in the necessary parameters to save a history of the versions that are created by the algorithm. 
	//@return void
	public function saveVersionHistoryUnassignedSections($last_id, $course, $section, $pName){
	      // create query to save in Schedule whatever was sent from Main
	    $query = 'INSERT into SectionsNotAssigned (idVersions, course, section, professor) VALUES (:id, :course, :section, :professor)';
	    $statement = $this->db->prepare($query);
	    $statement->bindValue(':id', $last_id, PDO::PARAM_STR);
	    $statement->bindValue(':course', $course, PDO::PARAM_STR);
	    $statement->bindValue(':section', $section, PDO::PARAM_STR);
	     $statement->bindValue(':professor', $pName);
	    $statement->execute();
	    $statement->closeCursor();
	}
	
	//Description: function that returns Version History Times for dropdown
	//@params
	//@return array with version History Times
	public function getVersionHistoryTimes(){
	    
	    $query= 'SELECT dateCreated from VersionsSchedule';
	    $statement = $this->db->prepare($query);
	    $statement->execute();
	    $result = $statement->fetchAll();
        $statement->closeCursor(); 
        
        $versionsArray = array();
        foreach ($result as $v) {
            $versionsArray[] = $v['dateCreated'];
        }
        
        return $versionsArray;
	}
	
	//Description function that returns Schedule values pertaining to a specific date
	//@param
	//@return arrays holding schedule information
	public function getFilledSchedulePerDate($date){
	    
	    //query to get id based on date
	    $query= 'SELECT versionId FROM VersionsSchedule WHERE dateCreated = :date';
	    $statement = $this->db->prepare($query);
	    $statement->bindValue(':date', $date, PDO::PARAM_STR);
	    $statement->execute();
	    $result = $statement->fetch();
	    $idVersion = $result['versionId'];
	    
	    //with the acquired id, now query get all values pertaining to that id
	    $query= 'SELECT course, section, daysAssigned, time, professor, room FROM Schedule WHERE idVersion = :id';
	    $statement = $this->db->prepare($query);
	    $statement->bindValue(':id', $idVersion);
	    $statement->execute();
	    $result = $statement->fetchAll();
        $statement->closeCursor(); 
        
        //values of all the columns will be stored in arrays that will then be stored in a two dimensional array
        $scheduleArray = array();
        
        foreach ($result as $r) {
             $scheduleArray[] = array(
             "course" => $r['course'],
             "section" => $r['section'],
             "daysAssigned" => $r['daysAssigned'],
             "time" => $r['time'],
             "professor" => $r['professor'],
             "room" => $r['room']
            );
        }
        return $scheduleArray; //im in main scheduling 
	}
	
    //Description function that returns Schedule values pertaining to a specific date
	//@param
	//@return arrays holding schedule information
	public function getUnfilledSchedulePerDate($date){
	    
	    //query to get id based on date
	    $query= 'SELECT versionId FROM VersionsSchedule WHERE dateCreated = :date';
	    $statement = $this->db->prepare($query);
	    $statement->bindValue(':date', $date, PDO::PARAM_STR);
	    $statement->execute();
	    $result = $statement->fetch();
	    $idVersion = $result['versionId'];
	    
	    //with the acquired id, now query get all values pertaining to that id
	    $query= 'SELECT course, section, professor FROM SectionsNotAssigned WHERE idVersions = :id';
	    $statement = $this->db->prepare($query);
	    $statement->bindValue(':id', $idVersion);
	    $statement->execute();
	    $result = $statement->fetchAll();
        $statement->closeCursor(); 
        
        //values of all the columns will be stored in arrays that will then be stored in a two dimensional array
        $scheduleArray = array();
        
        foreach ($result as $r) {
             $scheduleArray[] = array(
             "course" => $r['course'],
             "section" => $r['section'],
             "professor" => $r['professor']
            );
        }
        return $scheduleArray; //im in main scheduling 
	}

}
?> 