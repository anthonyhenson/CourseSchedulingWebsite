<?php
/**
 * NOTES
 * size taken out and put into section
 * check if assignAllSections needed and take out if not
 */

class Course
{
    private $sCourseCode; //EGR101
    private $iCredits; //credits
    private $sCourseType; //standard, project etc
    private $lstCourseSections; //list of sections

    // @param String $sID
    // @param int $iCredits
    // @param String $sType: lab, projector etc
    public function __construct($sID, $iCredits, $sType){
        $this->sCourseCode = $sID;
        $this->iCredits = $iCredits;
        $this->sCourseType = $sType;

        $this->lstCourseSections = array(); //sections only added after course created
    }
    
    // @param Section $sec: single section to be added
    // @return void
    public function addSection($sec) {
        $this->lstCourseSections[] = $sec; //one section added
    }
    
    // @param List Sections $secs: list of sections
    // @return void
    public function assignAllSections($secs) {
        $this->lstCourseSections = $secs; //all sections assigned
    }

    // @param void
    // @return String sCourseCode: "EGR101"
    public function getCourseCode() {return $this->sCourseCode;}
    
    // @param void
    // @return int $iCredits
    public function getCredits() {return $this->iCredits;}

    // @param void
    // @return String $sCourseType: projector, lab etc
    public function getCourseType() {return $this->sCourseType;}
    
    // @param void
    // @return List $lstCourseSections: list of sections
    public function getCourseSections() {return $this->lstCourseSections;}

    public function __toString()
    {
        return (string) "Course: ".$this->sCourseCode." Credits: ".$this->iCredits." Type: ".$this->sCourseType."<br>";
    }
}