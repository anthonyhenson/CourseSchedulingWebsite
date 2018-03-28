<?php
/**
 * NOTES
 * implement room placeholder for sections assigned to a room (within course UI)
 * $sRoomPlaceHolder
 * add boolean $bIsConstraint to algorithm
 */

class TimeLength {
    private $sPrimaryPlaceHolder;
    private $sAlternatePlaceHolder; //either holding a room name for professor or a professor name for a room
    private $iStartTimeHour;
    private $iStartTimeMinute;
    private $iEndTimeHour;
    private $iEndTimeMinute;

    private $bIsTimeFilled;
    private $bIsConstraint; //TODO: add into any required classes


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

    // @param int $iX: index of time
    // @return void
    // public function setIsFilled($iX){
    //     if ($iX = 1)
    //         $this->bIsTimeFilled = true;
    //     else
    //         $this->bIsTimeFilled = false;
    // }
    
    // // @param int $iX: index of time
    // // @return void
    // public function setIsConstraint($iX){
    //     if ($iX = 1)
    //         $this->bIsConstraint = true;
    //     else
    //         $this->bIsConstraint = false;
    // }
    
    
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
    
    // @param int $iX: index of time
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
        return (string) $this->iStartTimeHour.":".$this->iStartTimeMinute.
            " is filled: ".$this->bIsTimeFilled." is constraint: ".
            $this->bIsConstraint."<br>".
            "placeholder1: ".$this->sPrimaryPlaceHolder.
            " placeholder2: ".$this->sAlternatePlaceHolder."<br>";
    }
}