<?php
/**
 * NOTES
 * fill day constraint added to add correct boolean for timelength
 * check if method fillDay fillDayConstraint needed or alternate way of setting since fillDay is never used
 */
 require_once('TimeLength.php');
 
class DayTimes
{
    private $sDay; // "M or T or W or R or F"
    private $lstTimeLengths; // Timelength list

    // @param Sting $sD: m-f
    // @return void
    function __construct($sD) {
        $this->sDay = $sD;

        //TODO: check if start at 8am or 7am
        $this->lstTimeLengths = array();
        for ($i = 8; $i < 21; $i++) {
            for ($j = 0; $j <= 45; $j+=15) {
                if (j == 45){
                    $this->lstTimeLengths[] = new TimeLength($i, $j, $i+1, 0, "", "");
                }
                else {
                    $this->lstTimeLengths[] = new TimeLength($i, $j, $i, $j+15, "", "");
                }
            }
        }
        
    }

    // @param void 
    // @return String $sDay
    public function getDay(){return $this->sDay;}
    
    // @param void
    // @return List TimeLength $lstTimeLengths
    public function getTimeLengths(){return $this->lstTimeLengths;}
    
    // @param int $iIndex
    // @return TimeLength
    public function getTimeLengthByIndex($iIndex) {
        return $this->lstTimeLengths[$iIndex];
    }
    
    //TODO: delete two methods below if not needed ----------------------------------
    // @param String $sD: single day string
    // @param int $iX: time index
    // @return void
    public function fillDay($sD, $iX){
        if (strcmp($this->sDay, $sD) == 0) {
            $this->lstTimeLengths[$iX]->setIsFilled(1);
        }
    }
    
    // @param String $sD: single day string
    // @param int $iX: time index
    // @return void
    public function fillDayConstraint($sD, $iX){
        if (strcmp($this->sDay, $sD) == 0) {
            $this->lstTimeLengths[$iX]->setIsFilled(1);
            $this->lstTimeLengths[$iX]->setIsConstraint(1);
        }
    }
    
    public function __toString()
    {
        return (string) "DayTime: ".$this->sDay." Time: ".implode(" ", $this->lstTimeLengths)."<br>";
    }
}