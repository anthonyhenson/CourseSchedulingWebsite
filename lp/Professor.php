<?php
/**
 * NOTES
 * 
 */
 require_once('DayTimes.php');

class Professor
{

    private $iProfessorID;
    private $sProfessorName;
    private $aAvailableDayNames; //string of day names "MWF"
    private $lstAvailableDayTimes; //list DayTimes
   // private $lstSectionsTaught; //list of sections DO NOT IMPLEMENT THIS

    // @param String $sN: professors name
    // @param Array $aDays: string of day names available
    // @return void
    public function __construct($iID, $sN, $aDays){
        $this->iProfessorID = $iID;
        $this->sProfessorName = $sN;
        $this->aAvailableDayNames = $aDays; //"MWF" or "TR"
        //$this->lstSectionsTaught = array(); DO NOT IMPLEMENT
        
        //add all days available to professor
        $this->lstAvailableDayTimes = array();
		for ($i = 0; $i < strlen($aDays); $i++) {
			$this->lstAvailableDayTimes[] = new DayTimes(substr($aDays, $i, 1));
		}
    }

    //adds filled best times to professors schedule
    // @param List DayTimes $lstTimeToAdd
    // @return void
    public function addDayTimes($lstTimesToAdd, $sectionID) {
        
        //day in daytimes of professor
        foreach (self::getDayList() as $oD){
            //timelength in daytimes
            foreach ($oD->getTimeLengths() as $oT) {
                //day to add in list of DayTimes
                foreach ($lstTimesToAdd as $oDayToAdd){
                    //timelength in day to add
                    foreach ($oDayToAdd->getTimeLengths() as $oTimeAdd){
                        if (strcmp($oD->getDay(), $oDayToAdd->getDay()) == 0 &&
                                $oT->getStartTimeHour() == $oTimeAdd->getStartTimeHour() &&
                                $oT->getStartTimeMinute() == $oTimeAdd->getStartTimeMinute() &&
                                $oTimeAdd->isTimeFilled()){
                                
                            $oT->setTimeFilled(true);
                            $oT->setPrimaryPlaceHolder($sectionID);
                        }
                    }
                }
            }
        }
    }

    // @param void
    // @return List DayTimes $lstAvailableDayTimes
    public function getDayList() {return $this->lstAvailableDayTimes;}
    

    // @param void
    // @return List Section $lstSectionsTaught
    // DO NOT IMPLEMENT THIS
   // public function getSectionsTaught() {return $this->lstSectionsTaught;}
    
    // @param void
    // @return String $sProfessorName
    public function getProfessorsName() {return $this->sProfessorName;}
    
    // @param void
    // @return int professors id
    public function getProfessorsID() {return $this->iProfessorID;}
    
    // @param int $iIndex
    // @return DayTimes object
    public function getDayByIndex($iIndex) {
        return $this->lstAvailableDayTimes[$iIndex];
    }
    
    // @param void
    // @return String "MWF" or "TR"
    public function getAvailableDayNames() {return $this->aAvailableDayNames;}
    
    public function __toString()
    {
        return (string) "ID: ".$this->iProfessorID." Name: ".$this->sProfessorName.
        " Days: ".$this->aAvailableDayNames."<br>";
    }
}