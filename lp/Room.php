<?php
/**
 * NOTES
 * 
 */
require_once('DayTimes.php');

class Room
{
    private $sRoomNum; //215C
    private $sBuilding; //YGR
    private $sRoomID; //"YGR 215C": used for database unique id
    private $dSeatingCapacity;
    private $sRoomType; //projector, lab etc
    private $lstDayList; //list of days containing times

    // @param String $sRoomNum
    // @param String $sBuildingNum
    // @param double $dSeatingCapacity
    // @param String $sRoomType
    // @return void
    public function __construct($sRoomNum, $sBuildingNum, $dSeatingCapacity, $sRoomType){
        $this->sRoomNum = $sRoomNum;
        $this->sBuilding = $sBuildingNum;
        $this->sRoomID = $sBuildingNum." ".$sRoomNum;
        $this->dSeatingCapacity = $dSeatingCapacity;
        $this->sRoomType = $sRoomType;

        //room available all days
        $this->lstDayList = array();
        $this->lstDayList[] = new DayTimes("M");
        $this->lstDayList[] = new DayTimes("T");
        $this->lstDayList[] = new DayTimes("W");
        $this->lstDayList[] = new DayTimes("R");
        $this->lstDayList[] = new DayTimes("F");
    }
    
    // @param List DayTimes $lstTimeToAdd
    // @return void
    public function addDayTimes($lstTimesToAdd, $sectionID){
                //day in daytimes
        foreach (self::getDayList() as $oD){
            //timelength in daytimes
            foreach ($oD->getTimeLengths() as $oT){
                //day to add in list of DayTimes
                foreach ($lstTimesToAdd as $oDayToAdd){
                    //timelength in day to add
                    foreach ($oDayToAdd->getTimeLengths() as $oTimeAdd){
                        if (strcmp($oD->getDay(), $oDayToAdd->getDay()) == 0 &&
                                $oT->getStartTimeHour() == $oTimeAdd->getStartTimeHour() &&
                                $oT->getStartTimeMinute() == $oTimeAdd->getStartTimeMinute() &&
                                $oTimeAdd->isTimeFilled()) {
                                
                            $oT->setTimeFilled(true);
                            $oT->setPrimaryPlaceHolder($sectionID);
                        }
                    }
                }
            }
        }
    }

    // @param void
    // @return String $sRoomNum
    public function getRoomNumber() {return $this->sRoomNum;}
    
    // @param void
    // @return String $sBuilding
    public function getBuilding() {return $this->sBuilding;}
    
    // @param void
    // @return String $sRoomID: "BLDG ROOM"
    public function getRoomID() {return $this->sRoomID;}
    
    // @param void
    // @return double $dSeatingCapacity
    public function getSeatingCapacity() {return $this->dSeatingCapacity;}

    // @param void
    // @return $sRoomType: projector, lab etc
    public function getRoomType() {return $this->sRoomType;}

    // @param void
    // @return List DayTimes $lstDayList
    public function getDayList() {return $this->lstDayList;}
    
    // @param int $iIndex
    // @return DayTimes object
    public function getDayByIndex($iIndex) {
        return $this->lstDayList[$iIndex];
    }

    public function __toString()
    {
        return (string) "RoomID: ".$this->sRoomID." Seating: ".$this->dSeatingCapacity.
        " Type: ".$this->sRoomType."<br>";
    }
}