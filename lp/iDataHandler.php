<?php
interface iDataHandler
{
    // @param Professor $oProfessor
    // @return int rows affected
    public function addProfessor($oProfessor);
    
    // @param int $iProfessorID
    // @return int rows affected
    public function deleteProfessor($iProfessorID);
    
    // @param void
    // @return ArrayList $lstProfessors: list of professors
    public function getProfessors();
    
    // @param Professor $oProfessor
    // @param int $iDayIndex
    // @param int $iStartIndex
    // @param int $iEndIndex
    // @param boolean $bIsSetting : setting if true, clearing if false
    // @return boolean true succesfully updated 0 not updated
    public function updateProfessorTimesConstraint($oProfessor, $iDayIndex, $iStartIndex, $iEndIndex, $bIsSetting);
    
    // @param Room $oRoom
    // @return int rows affected
    public function addRoom($oRoom);
    
    // @param String $sRoomID: building and room num
    // @return int rows affected
    public function deleteRoom($sRoomID);
    
    // @param void
    // @return ArrayList $lstRooms: list of rooms
    public function getRooms();
    
    // @param Room $oRoom
    // @param int $iDayIndex
    // @param int $iStartIndex
    // @param int $iEndIndex
    // @param boolean $bIsSetting : setting if true, clearing if false
    // @return boolean true succesfully updated 0 not updated
    public function updateRoomTimesConstraint($oRoom, $iDayIndex, $iStartIndex, $iEndIndex, $bIsSetting);
    
    // @param Course $oCourse
    // @return int rows affected
    public function addCourse($oCourse);
    
    // @param String $sCourseCode: EGR101 without section
    // @return int rows affected
    public function deleteCourse($sCourseCode);
    
    // @param List $lstP: list of professors by key value
    // @return List $lstCourses: list of courses
    public function getCourses($lstP);
        
    // @param Section $oSection
    // @param String $sCourseCode : course section is added to
    // @return int rows affected
    public function addSection($oSection, $sCourseCode);
    
    // @param Array $aSectionIDs : array of all ids to be deleted
    // @return boolean true all rows deleted false if not
    public function deleteSections($aSectionIDs);

}

?>