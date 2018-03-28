<?php
require_once('iDataHandler.php');
require_once('Professor.php');
require_once('Room.php');
require_once('Course.php');
require_once('Section.php');

class TestDataHandler implements iDataHandler
{

    private static $lstProfessors;
    private static $lstRooms;
    private static $lstCourses;
    
    function __construct() {
        
        $this->lstProfessors = array();
        $this->lstRooms = array();
        $this->lstCourses = array();
    }

    // @param Professor $oProfessor
    // @return boolean true succesfully added 0 not added
    public function addProfessor($oProfessor){
        
    }
    
    // @param int $iProfessorID
    // @return boolean true  succesfully deleted 0 not deleted
    public function deleteProfessor($iProfessorID){
        
    }
    
    // @param void
    // @return ArrayList $lstProfessors: list of professors
    public function getProfessors() {
        $mwf = "MWF";
        $tr = "TR";
        $this->lstProfessors[] = new Professor("Anthony Corso", $mwf);
        $this->lstProfessors[] = new Professor("Larry Clement", $tr);
        $this->lstProfessors[] = new Professor("Creed Jones", $mwf);
        $this->lstProfessors[] = new Professor("Mi Han", $tr);
        $this->lstProfessors[] = new Professor("Kyungsoo Im", $mwf);
        $this->lstProfessors[] = new Professor("Michael Kolta", $tr);
        $this->lstProfessors[] = new Professor("Arlene Perkins", $mwf);
        $this->lstProfessors[] = new Professor("Mr TestTR", $tr);
        
        return $this->lstProfessors;
    }
    
    // @param Professor $oProfessor
    // @param int $iDayIndex
    // @param int $iStartIndex
    // @param int $iEndIndex
    // @param boolean $bIsSetting : setting if true, clearing if false
    // @return boolean true succesfully updated 0 not updated
    public function updateProfessorTimesConstraint($oProfessor, $iDayIndex, $iStartIndex, $iEndIndex, $bIsSetting) {
        
    }
    
    // @param Room $oRoom
    // @return boolean true succesfully added 0 not added
    public function addRoom($oRoom) {
        
    }
    
    // @param String $sRoomID: building and room num
    // @return boolean true succesfully deleted 0 not deleted
    public function deleteRoom($sRoomID) {
        
    }
    
    // @param void
    // @return ArrayList $lstRooms: list of rooms
    public function getRooms() {
        $a = new Room("100J", "JMS", 55, "standard");
        $b = new Room("215E", "EGR", 33, "standard");
        $c = new Room("340A", "YGR", 40, "standard");
        
        $this->lstRooms[] = $a;
        $this->lstRooms[] = $b;
        $this->lstRooms[] = $c;

        //        //testing room convert to php
        //        for(int i = 0; i < 5; i++){
        //            for (int j = 0; j < 52; j++) {
        //                test.getDayList().get(i).getDayTimes().get(j).setTimeFilled(true);
        //            }
        //        }

        return $this->lstRooms;
    }
    
    // @param Room $oRoom
    // @param int $iDayIndex
    // @param int $iStartIndex
    // @param int $iEndIndex
    // @param boolean $bIsSetting : setting if true, clearing if false
    // @return boolean true succesfully updated 0 not updated
    public function updateRoomTimesConstraint($oRoom, $iDayIndex, $iStartIndex, $iEndIndex, $bIsSetting) {
        
    }
    
    // @param Course $oCourse
    // @return void
    public function addCourse($oCourse) {
        return array_push($this->lstCourses, $oCourse);
    }
    
    // @param String $sCourseCode: EGR101 without section
    // @return boolean true succesfully deleted 0 not deleted
    public function deleteCourse($sCourseCode) {
        
    }
    
    // @param void
    // @return ArrayList $lstCourse: list of courses and their sections
    public function getCourses($lstProfessors) {
        
        //$lstProfessors = self::getProfessors();
        
        $sectionIds = array('A', 'B', 'C', 'D');
        $courseName = array("EGR101", "EGR102", "EGR103", "EGR104", "EGR105", "EGR106", "EGR107", "EGR108",
            "EGR109", "EGR110", "EGR111", "EGR112", "EGR113", "EGR114", "EGR115", "EGR116", "EGR117", "EGR118",
            "EGR119", "EGR120");
        $courseTypes = array("computer", "lab", "projector", "standard");

        //EGR101
        $lstSectionList101 = array();
        $lstSectionList101[] = new Section($sectionIds[0], $courseName[0].$sectionIds[0], 32, $lstProfessors[0]);
        $lstSectionList101[] = new Section($sectionIds[1], $courseName[0].$sectionIds[1], 32, $lstProfessors[1]);
        $lstSectionList101[] = new Section($sectionIds[2], $courseName[0].$sectionIds[2], 32, $lstProfessors[2]);
        $lstSectionList101[] = new Section($sectionIds[3], $courseName[0].$sectionIds[3], 32, $lstProfessors[3]);
        $c = new Course($courseName[0], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList101);
        $this->lstCourses[] = $c;
        
        //EGR102
        $lstSectionList102 = array();
        $lstSectionList102[] = new Section($sectionIds[0], $courseName[1].$sectionIds[0], 50, $lstProfessors[4]);
        $lstSectionList102[] = new Section($sectionIds[1], $courseName[1].$sectionIds[1], 50, $lstProfessors[5]);
        $lstSectionList102[] = new Section($sectionIds[2], $courseName[1].$sectionIds[2], 50, $lstProfessors[6]);
        $lstSectionList102[] = new Section($sectionIds[3], $courseName[1].$sectionIds[3], 50, $lstProfessors[5]);
        $c = new Course($courseName[1], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList102);
        $this->lstCourses[] = $c;


        //EGR103
        $lstSectionList103 = array();
        $lstSectionList103[] = new Section($sectionIds[0], $courseName[2].$sectionIds[0], 15, $lstProfessors[0]);
        $lstSectionList103[] = new Section($sectionIds[1], $courseName[2].$sectionIds[1], 15, $lstProfessors[1]);
        $lstSectionList103[] = new Section($sectionIds[2], $courseName[2].$sectionIds[2], 15, $lstProfessors[2]);
        $lstSectionList103[] = new Section($sectionIds[3], $courseName[2].$sectionIds[3], 15, $lstProfessors[3]);
        $c = new Course($courseName[2], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList103);
        $this->lstCourses[] = $c;
        
        //EGR104
        $lstSectionList104 = array();
        $lstSectionList104[] = new Section($sectionIds[0], $courseName[3].$sectionIds[0], 20, $lstProfessors[4]);
        $lstSectionList104[] = new Section($sectionIds[1], $courseName[3].$sectionIds[1], 20, $lstProfessors[5]);
        $lstSectionList104[] = new Section($sectionIds[2], $courseName[3].$sectionIds[2], 20, $lstProfessors[6]);
        $lstSectionList104[] = new Section($sectionIds[3], $courseName[3].$sectionIds[3], 20, $lstProfessors[5]);
        $c = new Course($courseName[3], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList104);
        $this->lstCourses[] = $c;

        //EGR105
        $lstSectionList105 = array();
        $lstSectionList105[] = new Section($sectionIds[0], $courseName[4].$sectionIds[0], 40, $lstProfessors[0]);
        $lstSectionList105[] = new Section($sectionIds[1], $courseName[4].$sectionIds[1], 40, $lstProfessors[1]);
        $lstSectionList105[] = new Section($sectionIds[2], $courseName[4].$sectionIds[2], 40, $lstProfessors[2]);
        $lstSectionList105[] = new Section($sectionIds[3], $courseName[4].$sectionIds[3], 40, $lstProfessors[3]);
        $c = new Course($courseName[4], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList105);
        $this->lstCourses[] = $c;

        //EGR106
        $lstSectionList106 = array();
        $lstSectionList106[] = new Section($sectionIds[0], $courseName[5].$sectionIds[0], 50, $lstProfessors[0]);
        $lstSectionList106[] = new Section($sectionIds[1], $courseName[5].$sectionIds[1], 50, $lstProfessors[1]);
        $lstSectionList106[] = new Section($sectionIds[2], $courseName[5].$sectionIds[2], 50, $lstProfessors[2]);
        $lstSectionList106[] = new Section($sectionIds[3], $courseName[5].$sectionIds[3], 50, $lstProfessors[3]);
        $c = new Course($courseName[5], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList106);
        $this->lstCourses[] = $c;

        //EGR107
        $lstSectionList107 = array();
        $lstSectionList107[] = new Section($sectionIds[0], $courseName[6].$sectionIds[0], 10, $lstProfessors[0]);
        $lstSectionList107[] = new Section($sectionIds[1], $courseName[6].$sectionIds[1], 10, $lstProfessors[1]);
        $lstSectionList107[] = new Section($sectionIds[2], $courseName[6].$sectionIds[2], 10, $lstProfessors[2]);
        $lstSectionList107[] = new Section($sectionIds[3], $courseName[6].$sectionIds[3], 10, $lstProfessors[3]);
        $c = new Course($courseName[6], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList107);
        $this->lstCourses[] = $c;

        //EGR108
        $lstSectionList108 = array();
        $lstSectionList108[] = new Section($sectionIds[0], $courseName[7].$sectionIds[0], 40, $lstProfessors[0]);
        $lstSectionList108[] = new Section($sectionIds[1], $courseName[7].$sectionIds[1], 40, $lstProfessors[1]);
        $lstSectionList108[] = new Section($sectionIds[2], $courseName[7].$sectionIds[2], 40, $lstProfessors[2]);
        $lstSectionList108[] = new Section($sectionIds[3], $courseName[7].$sectionIds[3], 40, $lstProfessors[3]);
        $c = new Course($courseName[7], 1, $courseTypes[3]);
        $c->assignAllSections($lstSectionList108);
        $this->lstCourses[] = $c;

        //EGR109
        $lstSectionList109 = array();
        $lstSectionList109[] = new Section($sectionIds[0], $courseName[8].$sectionIds[0], 30, $lstProfessors[4]);
        $lstSectionList109[] = new Section($sectionIds[1], $courseName[8].$sectionIds[1], 30, $lstProfessors[5]);
        $lstSectionList109[] = new Section($sectionIds[2], $courseName[8].$sectionIds[2], 30, $lstProfessors[6]);
        $lstSectionList109[] = new Section($sectionIds[3], $courseName[8].$sectionIds[3], 30, $lstProfessors[7]);
        $c = new Course($courseName[8], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList109);
        $this->lstCourses[] = $c;
        
        //EGR110
        $lstSectionList110 = array();
        $lstSectionList110[] = new Section($sectionIds[0], $courseName[9].$sectionIds[0], 30, $lstProfessors[4]);
        $lstSectionList110[] = new Section($sectionIds[1], $courseName[9].$sectionIds[1], 30, $lstProfessors[5]);
        $lstSectionList110[] = new Section($sectionIds[2], $courseName[9].$sectionIds[2], 30, $lstProfessors[6]);
        $lstSectionList110[] = new Section($sectionIds[3], $courseName[9].$sectionIds[3], 30, $lstProfessors[7]);
        $c = new Course($courseName[9], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList110);
        $this->lstCourses[] = $c;
        
        //EGR111
        $lstSectionList111 = array();
        $lstSectionList111[] = new Section($sectionIds[0], $courseName[10].$sectionIds[0], 33, $lstProfessors[4]);
        $lstSectionList111[] = new Section($sectionIds[1], $courseName[10].$sectionIds[1], 33, $lstProfessors[5]);
        $lstSectionList111[] = new Section($sectionIds[2], $courseName[10].$sectionIds[2], 33, $lstProfessors[6]);
        $lstSectionList111[] = new Section($sectionIds[3], $courseName[10].$sectionIds[3], 33, $lstProfessors[7]);
        $c = new Course($courseName[10], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList111);
        $this->lstCourses[] = $c;
        
        //EGR112
        $lstSectionList112 = array();
        $lstSectionList112[] = new Section($sectionIds[0], $courseName[11].$sectionIds[0], 50, $lstProfessors[4]);
        $lstSectionList112[] = new Section($sectionIds[1], $courseName[11].$sectionIds[1], 50, $lstProfessors[5]);
        $lstSectionList112[] = new Section($sectionIds[2], $courseName[11].$sectionIds[2], 50, $lstProfessors[6]);
        $lstSectionList112[] = new Section($sectionIds[3], $courseName[11].$sectionIds[3], 50, $lstProfessors[7]);
        $c = new Course($courseName[11], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList112);
        $this->lstCourses[] = $c;
        
        //EGR113
        $lstSectionList113 = array();
        $lstSectionList113[] = new Section($sectionIds[0], $courseName[12].$sectionIds[0], 30, $lstProfessors[4]);
        $lstSectionList113[] = new Section($sectionIds[1], $courseName[12].$sectionIds[1], 30, $lstProfessors[5]);
        $lstSectionList113[] = new Section($sectionIds[2], $courseName[12].$sectionIds[2], 30, $lstProfessors[6]);
        $lstSectionList113[] = new Section($sectionIds[3], $courseName[12].$sectionIds[3], 30, $lstProfessors[7]);
        $c = new Course($courseName[12], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList113);
        $this->lstCourses[] = $c;
        
        //EGR114
        $lstSectionList114 = array();
        $lstSectionList114[] = new Section($sectionIds[0], $courseName[13].$sectionIds[0], 30, $lstProfessors[4]);
        $lstSectionList114[] = new Section($sectionIds[1], $courseName[13].$sectionIds[1], 30, $lstProfessors[5]);
        $lstSectionList114[] = new Section($sectionIds[2], $courseName[13].$sectionIds[2], 30, $lstProfessors[6]);
        $lstSectionList114[] = new Section($sectionIds[3], $courseName[13].$sectionIds[3], 30, $lstProfessors[7]);
        $c = new Course($courseName[13], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList114);
        $this->lstCourses[] = $c;
        
        //EGR115
        $lstSectionList115 = array();
        $lstSectionList115[] = new Section($sectionIds[0], $courseName[14].$sectionIds[0], 30, $lstProfessors[4]);
        $lstSectionList115[] = new Section($sectionIds[1], $courseName[14].$sectionIds[1], 30, $lstProfessors[5]);
        $lstSectionList115[] = new Section($sectionIds[2], $courseName[14].$sectionIds[2], 30, $lstProfessors[6]);
        $lstSectionList115[] = new Section($sectionIds[3], $courseName[14].$sectionIds[3], 30, $lstProfessors[7]);
        $c = new Course($courseName[14], 1, $courseTypes[3]);
        $c->assignAllSections($lstSectionList115);
        $this->lstCourses[] = $c;
        
        //EGR116
        $lstSectionList116 = array();
        $lstSectionList116[] = new Section($sectionIds[0], $courseName[15].$sectionIds[0], 30, $lstProfessors[4]);
        $lstSectionList116[] = new Section($sectionIds[1], $courseName[15].$sectionIds[1], 30, $lstProfessors[5]);
        $lstSectionList116[] = new Section($sectionIds[2], $courseName[15].$sectionIds[2], 30, $lstProfessors[6]);
        $lstSectionList116[] = new Section($sectionIds[3], $courseName[15].$sectionIds[3], 30, $lstProfessors[7]);
        $c = new Course($courseName[15], 1, $courseTypes[3]);
        $c->assignAllSections($lstSectionList116);
        $this->lstCourses[] = $c;
        
        //EGR117
        $lstSectionList117 = array();
        $lstSectionList117[] = new Section($sectionIds[0], $courseName[16].$sectionIds[0], 30, $lstProfessors[4]);
        $lstSectionList117[] = new Section($sectionIds[1], $courseName[16].$sectionIds[1], 30, $lstProfessors[5]);
        $lstSectionList117[] = new Section($sectionIds[2], $courseName[16].$sectionIds[2], 30, $lstProfessors[6]);
        $lstSectionList117[] = new Section($sectionIds[3], $courseName[16].$sectionIds[3], 30, $lstProfessors[7]);
        $c = new Course($courseName[16], 1, $courseTypes[3]);
        $c->assignAllSections($lstSectionList117);
        $this->lstCourses[] = $c;
        
        //EGR118
        $lstSectionList118 = array();
        $lstSectionList118[] = new Section($sectionIds[0], $courseName[17].$sectionIds[0], 50, $lstProfessors[4]);
        $lstSectionList118[] = new Section($sectionIds[1], $courseName[17].$sectionIds[1], 50, $lstProfessors[5]);
        $lstSectionList118[] = new Section($sectionIds[2], $courseName[17].$sectionIds[2], 50, $lstProfessors[6]);
        $lstSectionList118[] = new Section($sectionIds[3], $courseName[17].$sectionIds[3], 50, $lstProfessors[7]);
        $c = new Course($courseName[17], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList118);
        $this->lstCourses[] = $c;
        
        //EGR119
        $lstSectionList119 = array();
        $lstSectionList119[] = new Section($sectionIds[0], $courseName[18].$sectionIds[0], 30, $lstProfessors[1]);
        $lstSectionList119[] = new Section($sectionIds[1], $courseName[18].$sectionIds[1], 30, $lstProfessors[2]);
        $lstSectionList119[] = new Section($sectionIds[2], $courseName[18].$sectionIds[2], 30, $lstProfessors[6]);
        $lstSectionList119[] = new Section($sectionIds[3], $courseName[18].$sectionIds[3], 30, $lstProfessors[4]);
        $c = new Course($courseName[18], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList119);
        $this->lstCourses[] = $c;
        
        //EGR120
        $lstSectionList120 = array();
        $lstSectionList120[] = new Section($sectionIds[0], $courseName[19].$sectionIds[0], 50, $lstProfessors[4]);
        $lstSectionList120[] = new Section($sectionIds[1], $courseName[19].$sectionIds[1], 50, $lstProfessors[5]);
        $lstSectionList120[] = new Section($sectionIds[2], $courseName[19].$sectionIds[2], 50, $lstProfessors[6]);
        $lstSectionList120[] = new Section($sectionIds[3], $courseName[19].$sectionIds[3], 50, $lstProfessors[7]);
        $c = new Course($courseName[19], 3, $courseTypes[3]);
        $c->assignAllSections($lstSectionList120);
        $this->lstCourses[] = $c;
        
        return $this->lstCourses;
    }
    
    // @param Section $oSection
    // @param String $sCourseCode : course section is added to
    // @return boolean true succesfully added 0 not added
    public function addSection($oSection, $sCourseCode) {
        
    }
    
    // @param String $sCourseCode: EGR101 without section
    // @param Array $aSectionIDs : array of all ids to be deleted
    // @return boolean true succesfully deleted 0 not deleted
    public function deleteSections($aSectionIDs) {
        
    }

}

?>