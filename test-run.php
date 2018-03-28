<?php
    require_once('lp/TestDataHandler.php');
    require_once('lp/SQLDataHandler.php');
    
    
    $dataHandler = new SQLDataHandler();
    
    
    //add professor
    //$newP = new Professor(2345342, "Mr TestTR", "TR");
    // $rows = $dataHandler->addProfessor($newP); //add professor to table
    // printDBResult($rows);
    
    //delete professor
    //1457346, 2345342 , 2457342, 4356721, 5234678, 7524890, 8975463, 9765478
    // $rows = $dataHandler->deleteProfessor(2345342); //MR TESTTR
    // printDBResult($rows);
    
    //update professor times
    //Professor, day, starttime, endtime, isSetting
    //monday from 8 to 9 and is setting
    // $rows = $dataHandler->updateProfessorTimesConstraint($newP, 2, 0, 5, false);
    // printDBResult($rows);
    
    $lstProfessors = array();
    $lstProfessors = $dataHandler->getProfessors(); 
    $prof;
    $i = 0;
    
    foreach ($lstProfessors as $professor) {
         //print $professor;
         if ($i = 2)
            $prof = $professor;
         foreach ($professor->getDayList() as $oDT) {
             //print $oDT;
             foreach ($oDT->getTimeLengths() as $oTimeLength) {
                 //print $oTimeLength;
             }
         }
         $i++;
         print "<br><br>";
    }
    print $prof->getProfessorsName();
    
    //add room
    $newR = new Room("100C", "YGR", 40, "standard");
    // $rows = $dataHandler->addRoom($newR); //add professor to table
    // printDBResult($rows);
        
    //delete room    
    // $roomID = $newR->getRoomID();
    // $rows = $dataHandler->deleteRoom($roomID);
    // printDBResult($rows);
    
    //update room times
    //Room, day, starttime, endtime, isSetting
    //monday from 8 to 9 and is setting
    // $rows = $dataHandler->updateRoomTimesConstraint($newR, 2, 0, 3, true);
    // printDBResult($rows);
    
    // $lstRooms = $dataHandler->getRooms(); 
    // foreach ($lstRooms as $r) {
    //     print ($r->getBuilding().$r->getRoomNumber()."<br>");
    //     foreach ($r->getDayList() as $oDT) {
    //         print ($oDT->getDay()."<br>");
    //         foreach ($oDT->getTimeLengths() as $oTimeLength) {
    //             print $oTimeLength->getPrimaryPlaceHolder()." ".$oTimeLength->getAlternatePlaceHolder()."<br>";
    //             print " ".$oTimeLength->getStartTimeHour()." ".$oTimeLength->getStartTimeMinute()."<br>";
    //             print "is filled:".var_export($oTimeLength->isTimeFilled())."<br>";
    //             print "is constraint:".var_export($oTimeLength->isTimeConstraint())."<br>";
    //         }
    //     }
    //     print "<br><br>";
    // }
    
    $lstCourses = $dataHandler->getCourses($lstProfessors);
   // add section
    $section = new Section("A", "EGR102", 100, $professor);
    print $section->getProfessorAssigned()->getProfessorsName();
   // $rows = $dataHandler->addSection($section, "EGR102");
    //printDBResult($rows);
    
    //delete section
    // $sections = array("EGR101E", "EGR101F", "EGR101G");
    // $isDeleted = $dataHandler->deleteSections($sections);
    // printDBResult($isDeleted);
    
    //add course
    $newCourse = new Course("EGR121", 1, "standard");
    // $rows = $dataHandler->addCourse($newCourse);
    // printDBResult($rows);
    
    //delete course
    // $isDeleted = $dataHandler->deleteCourse("EGR121");
    // printDBResult($isDeleted);
     
    // foreach ($lstCourses as $c) {
    //     echo $c;
    //     foreach ($c->getCourseSections() as $s) {
    //         echo $s;
    //         foreach ($s->getDayTimeAssigned() as $dt) {
    //             echo $dt;
    //             foreach ($dt->getTimeLengths() as $t) {
    //                 echo $t;
    //             }
    //         }
    //     }
    //     print "<br><br>";
    // }

    //works for booleans and single row count
    function printDBResult($count) {
        if ($count == 1) {
            print "we're good"."<br>";
        }
        else
            print "you suck"."<br>";
    }
    
?>