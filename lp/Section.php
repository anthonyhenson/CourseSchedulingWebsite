<?php

class Section
{
    private $sSection; //A or BB
    private $sSectionID; //course and section name
    private $dSectionSize; //seating
    private $oProfessorAssigned;
    
    private $oRoomAssigned;
    private $lstDayTimeAssigned; //days containing times


    // @param String $sID: section id: "A"
    // @param String $sSec: course
    // @param double $dSize: seating
    // @param Professor $oP: 
    // @return void
    public function __construct($sID, $sSec, $dSize, $oP){
        $this->sSection = $sID;
        $this->sSectionID = $sSec.$sID;
        $this->dSectionSize = $dSize;
        
        $this->oProfessorAssigned = $oP;
        $this->oRoomAssigned = null;

        //all days added to section
        $this->lstDayTimeAssigned = array();
        $this->lstDayTimeAssigned[] = new DayTimes("M");
        $this->lstDayTimeAssigned[] = new DayTimes("T");
        $this->lstDayTimeAssigned[] = new DayTimes("W");
        $this->lstDayTimeAssigned[] = new DayTimes("R");
        $this->lstDayTimeAssigned[] = new DayTimes("F");
    }
    
    
    // @param Professor $oP: 
    // @return void
    //TODO: take out if not needed dont think this is used
    //public function setSection($cID) {$this->sSection = $cID;}

    // @param void 
    // @return Professor
    public function getProfessorAssigned() {return $this->oProfessorAssigned;}

    // @param void 
    // @return String $sSectionID: course and section name
    public function getSectionID() {return $this->sSectionID;}
    
    // @param void 
    // @return String $sSection: section alone "A"
    public function getSection() {return $this->sSection;}

    // @param Room $oR
    // @return void
    public function setRoomAssigned($oR) {$this->oRoomAssigned = $oR;}
    
    // @param void
    // @return Room $oR
    public function getRoomAssigned() {return $this->oRoomAssigned;}

    // @param List DayTimes $lstDT:
    // @return void
    public function setDayTimeAssigned($lstDT) {$this->lstDayTimeAssigned = $lstDT;}
    
    // @param void
    // @return List DayTimes $lstDT:
    public function getDayTimeAssigned() {return $this->lstDayTimeAssigned;}
    
    // @param int $iIndex
    // @return DayTimes object
    public function getDayByIndex($iIndex) {
        return $this->lstDayTimeAssigned[$iIndex];
    }
    
    // @param void
    // @return double $dSectionSize;
    public function getSectionSize(){return $this->dSectionSize;}

    public function __toString()
    {
        return (string) "Section: ".$this->sSectionID." Size: ".$this->dSectionSize.
        " Professor: ".$this->oProfessorAssigned->getProfessorsName()."<br>";
    }
}