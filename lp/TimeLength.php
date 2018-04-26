<?php

class TimeLength {
    private $sPrimaryPlaceHolder; //used for sectionID for room and professor
    private $sAlternatePlaceHolder; //either holding a room name for professor or a professor name for a room
    private $iStartTimeHour;
    private $iStartTimeMinute;
    private $iEndTimeHour;
    private $iEndTimeMinute;

    private $bIsTimeFilled; //time filled
    private $bIsConstraint; //only constraint if not time filled by schedule generator


    // @param int $iSH: start hour
    // @param int $iSM: start minute
    // @param int $iEH: end hour
    // @param int $iEM: end minute
    // @param String $sCP: course placeholder
    // @param String $sRP: room placeholder
    // @return void
    public function __construct($iSH, $iSM, $iEH, $iEM, $sPP = "", $sAP = "") {
        //times
        $this->iStartTimeHour = $iSH;
        $this->iStartTimeMinute = $iSM;
        $this->iEndTimeHour = $iEH;
        $this->iEndTimeMinute = $iEM;
        //booleans
        $this->bIsTimeFilled = false;
        $this->bIsConstraint = false;
        //placeholders
        $this->sPrimaryPlaceHolder = $sPP;
        $this->sAlternatePlaceHolder = $sAP;
    }
    
    // @param void
    // @return String $sCoursePlaceHolder: course placeholder
    public function getPrimaryPlaceHolder(){ return $this->sPrimaryPlaceHolder;}
    
    // @param String $sCourse: placeholder for course
    // @return void
    public function setPrimaryPlaceHolder($sP){$this->sPrimaryPlaceHolder = $sP;}
    
    // @param void
    // @return String $sRoomPlaceHolder: room placeholder
    public function getAlternatePlaceHolder(){ return $this->sAlternatePlaceHolder;}
    
    // @param String $sA: room or professor placeholder
    // @return void
    public function setAlternatePlaceHolder($sA){$this->sAlternatePlaceHolder = $sA;}
    
    // @param int $iX: index of time
    // @return void
    public function getStartTimeHour(){return $this->iStartTimeHour;}
    
    // @param int $iX: index of time
    // @return void
    public function getStartTimeMinute(){return $this->iStartTimeMinute;}
    
    // @param void
    // @return boolean
    public function isTimeFilled(){return $this->bIsTimeFilled;}
    
    // @param boolean
    // @return void
    public function setTimeFilled($bTimeFilled){$this->bIsTimeFilled = $bTimeFilled;}
    
    // @param int $iX: index of time
    // @return void
    public function setIsConstraint($bIsConstraint){$this->bIsConstraint = $bIsConstraint;}
    
     // @param void
    // @return boolean
    public function isTimeConstraint(){return $this->bIsConstraint;}
    
    public function __toString()
    {
        $filled = $this->bIsTimeFilled ? "yes" : "no";
        $constraint = $this->bIsConstraint ? "yes" : "no";
        return (string) $this->iStartTimeHour.":".$this->iStartTimeMinute.
            ": ".$filled." // ".$constraint." // ".$this->sPrimaryPlaceHolder." // ". $this->sAlternatePlaceHolder."<br>";
    }
}