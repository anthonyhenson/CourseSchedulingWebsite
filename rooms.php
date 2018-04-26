
<script>
//------- ROOM OBJECT DECLARATIONS
    var hasSelection = false;
    var lstRoomName = [];
    var lstBuilding = [];
    var lstRoomID = [];
    var dSeatingCapacity = [];
    var sRoomType = [];
    var lstDayList = [];
    
</script>
<?php
    require_once('css/cssVersion.php');
	require_once('session.php');
	require_once('lp/SQLDataHandler.php');
    $dataHandler = new SQLDataHandler();
	
	
	if (isset($_SESSION['user'])) {
		$userEmail = $_SESSION['user']; //set current user
		
		if (isset($_POST['roomID'])) {
		    ?><script>
		    var lastSelection = "<?=$_POST['roomID']?>"; 
		    if (lastSelection != "") { 
		        hasSelection = true;
		    }</script><?php
		}
		else {
		    ?><script>var hasSelection = false;</script><?php
		}
		
		if (isset($_POST['submitModal'])) {
    		$sRoomNum = filter_input(INPUT_POST, 'roomNum');
    		$sBuildingNum = filter_input(INPUT_POST, 'bldg');
    	    $dSeatingCapacity = (int)filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT);
    	    $sRoomType = filter_input(INPUT_POST, 'roomType');
    	    
    	  
    	    //not negative or incorrect input
    	    if ($dSeatingCapacity > 0) {
    			$room = new Room($sRoomNum, $sBuildingNum, $dSeatingCapacity, $sRoomType);
    			$isRoomAdded = $dataHandler->addRoom($room );
    
    			if ($isRoomAdded != 1) { ?>
    				<script>alert("Error in adding room!");</script> <?php
    			}
    			else {  ?>
    				<script>alert("Room succesfully added");</script> <?php
    			}
    		}
    		else { ?>
    			<script>alert("Invalid room inputs!");</script> <?php
    				
    		}
	    }
	
		//check for deleted room id is dynamic
		foreach($_POST as $key=>$value) {
		    //only delete proper key
		    if (strcmp($value, "Delete") == 0) {
		        //post puts room id as bldg_rom change to "bldg num"
		        $keyVals = explode("_", $key);
		        $roomID = $keyVals[0]. " ".$keyVals[1];
		        $roomDeletedRows = $dataHandler->deleteRoom($roomID);
		        
		        if ($roomDeletedRows != 1) { ?>
				<script>alert("Error in deleting room!");</script> <?php
			    }
			    else {  ?>
				<script>alert("Room succesfully deleted. Previous room assignments will remain until a new schedule is generated");</script> <?php
			    }
		    }
		}
		
        //update room time constraints
        if (isset($_POST['submitConstraint'])) {
            $isSetting = filter_input(INPUT_POST, 'set-clear') == "set" ? true : false;
		    $dayID = (int)filter_input(INPUT_POST, 'day_selector');
	        $timeStartID = (int)filter_input(INPUT_POST, 'start_time', FILTER_VALIDATE_INT);
	        $timeEndID = (int)filter_input(INPUT_POST, 'end_time');
	        $roomID = filter_input(INPUT_POST, 'roomID');
	        
	        //correct times selected
	        if ($timeEndID > $timeStartID) {
			    $room = $dataHandler->getRoomByID($roomID);
			    $isConstraintsAdded = $dataHandler->updateRoomTimesConstraint($room, $dayID, $timeStartID, $timeEndID, $isSetting);

    			if (!$isConstraintsAdded) { ?>
    				<script>alert("Error updating room constraints! Updating already set times will return an error");</script> <?php
    			}
    			else {  ?>
    				<script>
    				      alert("Room constraints succesfully updated");
    				</script> <?php
    			}
    		}
    		else { ?>
    			<script>alert("Invalid time selection!");</script> <?php
    		}
        }
	}
	else {
	    //go back to login
	    header("Location: login.php");
	}

?>
<!-- ------------------------- HEAD ----------------------------------------- -->
<!DOCTYPE html>
<html lang="en-us">
<head>
	<title>CBU</title>
  	<meta charset="UTF-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  	<link rel="shortcut icon" href="./img/favicon.ico">
  	<!-- reset styling -->
	<link rel="stylesheet" href="./css/reset.css">
  	<!-- common styling -->
	<link rel="stylesheet" href="./css/main.css">
	<!-- font awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<!-- individual page css -->
	<link rel="stylesheet" media="screen and (min-width: 1001px)" href="./css/rooms.css?v=<?=$cssVersion?>" />
	<!-- jquery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>

<body>

<!-- ------------------------- NAV BAR ----------------------------------------- -->
<header>
<?php
	require_once 'navbar.php';
?>
</header>
<!--  -----------------------------MAIN --------------------------------- -->
<main>
  <!--  ----------------------------- PAGE HEADER  --------------------------------- -->
     <span class="row container">
            <span class="column-12 center"><img src="img/cbu_logo2.jpg" height="150" width="300"/></span>
    </span>
    <hr class="styled">
    <span class="row container blue center">
        <span class="column-12" id="page_title">CLASSROOMS <input id="add_room_button" type="submit" value="Add Room"/></span>
    </span>
    <?php
	    require_once('lp/Room.php'); // room class
        require_once 'add_room.php'; //add room modal
        
        $lstRooms = array();
	    $lstRooms = $dataHandler->getRooms(); // list of rooms
    ?>
  
<!--  ----------------------------- VIEW RESOURCE LIST  --------------------------------- -->
<span class="row container">
    <span class="column-4 columns_left"></span>
    <span class="column-4 columns_left resource_all">
        <form method="post" action="rooms.php">
       
           <!-- Loops through the php Room object and stores the value into a Javascript object -->
            <?php foreach ($lstRooms as $roomName) {
                $sRoomName = $roomName->getRoomNumber();
                $sBuilding = $roomName->getBuilding();
                $sRoomID = $roomName->getRoomID();
                $dSeatingCapacity = $roomName->getSeatingCapacity();
                $sRoomType = $roomName->getRoomType();
                
                $lstDayList = $roomName->getDayList();
                echo "<script> var lstDayTimes = []; </script>"; 
                foreach ($lstDayList as $lstDay) { 
                    
                    echo "<script> var lstTimeLength = []; </script>";
                    foreach ($lstDay->getTimeLengths() as $times) {
                        $primaryPlaceholder = $times->getPrimaryPlaceHolder();
                        $alternatePlaceholder = $times->getAlternatePlaceHolder();
                        $timeConstraint = $times->isTimeFilled();
                        $hardConstraint = $times->isTimeConstraint();
                        
                        //creating the timeLength object
                        echo "<script> 
                        var timeLength = {};
                        timeLength.primaryHolder = '$primaryPlaceholder';
                        timeLength.alternateHolder = '$alternatePlaceholder';
                        timeLength.filled = '$timeConstraint';
                        timeLength.constraint = '$hardConstraint';
                        lstTimeLength.push(timeLength);</script>"; //nest
                        
                    }
                    //fill list of timelengths into dayTimes list
                    echo "<script> lstDayTimes.push(lstTimeLength); </script>"; 
                }
                echo "<script> lstRoomName.push('$sRoomName');
                        lstBuilding.push('$sBuilding');
                        lstRoomID.push('$sRoomID');
                        dSeatingCapacity.push('$dSeatingCapacity');
                        sRoomType.push('$sRoomType');
                        lstDayList.push(lstDayTimes); 
                    </script>";
                    
                // ------------- Individual Resources -------------------    
                echo "<span class=\"row container resource_x\">
                        <span class=\"column-6 columns_left resource_text\">
                            $sRoomID
                        </span>
                        <span class=\"column-6 columns_left resource_buttons\">
                            <input id=\"$sRoomID\" type=\"button\" value=\"View / Edit\"/>
                            <input id=\"$sRoomID\" type=\"submit\" value=\"Delete\" name=\"$sRoomID\" onclick=\"return confirm('Are you sure?');\" />
                        </span>
                    </span>";
            } ?>
        </form>
    </span>
    <span class="column-4 columns_left"></span>
    
</span>
  

<!--  ----------------------------- VIEW SELECTED RESOURCE INFO  --------------------------------- -->
<span class="alter_resources">
    <span class="row container">
        <span class="column-2 columns_left"></span>
        <span class="column-8 columns_left alter_items">
            <label>Building:</label>
            <span id="building"></span>
            <label>Room:</label>
            <span id="room"></span>
            <label>Capacity:</label>
            <span id="capacity"></span>
            <label>Type:</label>
            <span id="type"></span>
        </span>
        <span class="column-2 columns_left"></span>
    </span>

<!-- ADD CONSTRAINTS -->
    <span class="row container constraint_header">
        <span class="column-2 columns_left"></span>
        <span class="column-8 columns_left constraint_title">
            <span class="column-3 columns-left">&nbsp
            </span>
            <span class="column-3 columns-left">
                Day of Week
            </span>
            <span class="column-2 columns-left">
                Start Time
            </span>
            <span class="column-2 columns-left">
                End Time
            </span>
            <span class="column-2 columns-left">
            </span>
        </span>
    </span>
    <span class="row container marginb_20">
        <span class="column-2 columns_left"></span>
        <span class="column-8 columns_left">
            <!-- CONSTRAINT FORM -->
            <form action="rooms.php" method="POST" id="constraintForm">
                <span class="column-3 columns-left center radio_constraint">
                    <input id="r1" name="set-clear" type="radio" value="set" required/>
                    <label for="r1">SET</label>
                    <input id="r2" name="set-clear" type="radio" value="clear"/>
                    <label for="r2">CLEAR</label>
                </span>
                
                <!-- day of week -->
                <span class="column-3 columns-left">
        			<select id="day_selector" class="day_dropdown" name="day_selector" required>
          				<option value="1">Monday</option>
        				<option value="2">Tuesday</option>
        				<option value="3">Wednesday</option>
        				<option value="4">Thursday</option>
        				<option value="5">Friday</option>
        			</select>
        		</span>
        		<!-- start time -->
        		<span class="column-2 columns-left">
        			<select id="start_selector" class="time_dropdown" name="start_time" required>
          				<option value="0">8:00am</option>
          				<option value="1">8:15am</option>
          				<option value="2">8:30am</option>
          				<option value="3">8:45am</option>
          				<option value="4">9:00am</option>
          				<option value="5">9:15am</option>
          				<option value="6">9:30am</option>
          				<option value="7">9:45am</option>
          				<option value="8">10:00am</option>
          				<option value="9">10:15am</option>
          				<option value="10">10:30am</option>
          				<option value="11">10:45am</option>
          				<option value="12">11:00am</option>
          				<option value="13">11:15am</option>
          				<option value="14">11:30am</option>
          				<option value="15">11:45am</option>
          				<option value="16">12:00pm</option>
          				<option value="17">12:15pm</option>
          				<option value="18">12:30pm</option>
          				<option value="19">12:45pm</option>
          				<option value="20">1:00pm</option>
          				<option value="21">1:15pm</option>
          				<option value="22">1:30pm</option>
          				<option value="23">1:45pm</option>
          				<option value="24">2:00pm</option>
          				<option value="25">2:15pm</option>
          				<option value="26">2:30pm</option>
          				<option value="27">2:45pm</option>
          				<option value="28">3:00pm</option>
          				<option value="29">3:15pm</option>
          				<option value="30">3:30pm</option>
          				<option value="31">3:45pm</option>
          				<option value="32">4:00pm</option>
          				<option value="33">4:15pm</option>
          				<option value="34">4:30pm</option>
          				<option value="35">4:45pm</option>
          				<option value="36">5:00pm</option>
          				<option value="37">5:15pm</option>
          				<option value="38">5:30pm</option>
          				<option value="39">5:45pm</option>
          				<option value="40">6:00pm</option>
          				<option value="41">6:15pm</option>
          				<option value="42">6:30pm</option>
          				<option value="43">6:45pm</option>
          				<option value="44">7:00pm</option>
          				<option value="45">7:15pm</option>
          				<option value="46">7:30pm</option>
          				<option value="47">7:45pm</option>
          				<option value="48">8:00pm</option>
          				<option value="49">8:15pm</option>
          				<option value="50">8:30pm</option>
          				<option value="51">8:45pm</option>
        			</select>
        		</span>
        		<!-- end time -->
        		<span class="column-2 columns-left">
        			<select id="end_selector" class="time_dropdown" name="end_time" required>
          				<option value="1">8:15am</option>
          				<option value="2">8:30am</option>
          				<option value="3">8:45am</option>
          				<option value="4">9:00am</option>
          				<option value="5">9:15am</option>
          				<option value="6">9:30am</option>
          				<option value="7">9:45am</option>
          				<option value="8">10:00am</option>
          				<option value="9">10:15am</option>
          				<option value="10">10:30am</option>
          				<option value="11">10:45am</option>
          				<option value="12">11:00am</option>
          				<option value="13">11:15am</option>
          				<option value="14">11:30am</option>
          				<option value="15">11:45am</option>
          				<option value="16">12:00pm</option>
          				<option value="17">12:15pm</option>
          				<option value="18">12:30pm</option>
          				<option value="19">12:45pm</option>
          				<option value="20">1:00pm</option>
          				<option value="21">1:15pm</option>
          				<option value="22">1:30pm</option>
          				<option value="23">1:45pm</option>
          				<option value="24">2:00pm</option>
          				<option value="25">2:15pm</option>
          				<option value="26">2:30pm</option>
          				<option value="27">2:45pm</option>
          				<option value="28">3:00pm</option>
          				<option value="29">3:15pm</option>
          				<option value="30">3:30pm</option>
          				<option value="31">3:45pm</option>
          				<option value="32">4:00pm</option>
          				<option value="33">4:15pm</option>
          				<option value="34">4:30pm</option>
          				<option value="35">4:45pm</option>
          				<option value="36">5:00pm</option>
          				<option value="37">5:15pm</option>
          				<option value="38">5:30pm</option>
          				<option value="39">5:45pm</option>
          				<option value="40">6:00pm</option>
          				<option value="41">6:15pm</option>
          				<option value="42">6:30pm</option>
          				<option value="43">6:45pm</option>
          				<option value="44">7:00pm</option>
          				<option value="45">7:15pm</option>
          				<option value="46">7:30pm</option>
          				<option value="47">7:45pm</option>
          				<option value="48">8:00pm</option>
          				<option value="49">8:15pm</option>
          				<option value="50">8:30pm</option>
          				<option value="51">8:45pm</option>
          				<option value="52">9:00pm</option>
        			</select>
        		</span>
        		<input type="hidden" name="roomID" id="roomID" value="">
        		<span class="column-2 columns_left save_resource_buttons">
        		    <input id="edit_constraint" name="submitConstraint" type="submit" value="Save"/>
        		</span>
                
            </form>
            
        </span>
        <span class="column-2 columns_left"></span>
    </span>
</span>
<!--  ----------------------------- CALENDAR  --------------------------------- -->
	<?php
	  require_once 'calendar.php';
	?>

</main>
<!-- ------------------------- FOOTER ----------------------------------------- -->
<footer id="footer" class="blue_background center"><img id="footer_logo" src="img/cbu_logo3.png" height="40" width="100">
</footer>

</body>
</html>


<script> 
    var lstRoom = [];
    var viewBtn = [];
    var deleteBtn = [];
    
    //info not displayed by default
    var alter = document.getElementsByClassName("alter_resources");
    alter[0].style.display = "none";

    //list for room view and delete objects
    for (var i = 0; i < lstRoomName.length; i++) {
        var oRoom = {}; //everything put into one room object
        oRoom.roomNumber = lstRoomName[i];
        oRoom.building = lstBuilding[i];
        oRoom.roomID = lstRoomID[i];
        oRoom.seatingCapacity = dSeatingCapacity[i];
        oRoom.roomType = sRoomType[i];
        oRoom.dayList = lstDayList[i];
        lstRoom.push(oRoom);
        
        viewBtn.push(document.getElementById(lstRoom[i].roomID));
        deleteBtn.push(document.getElementById(lstRoom[i].roomID)[1]);
    }
    
    var oldBtn;
    
    function showCalendar(index) {
        var roomActive = index;
        var cell;
        document.getElementById("roomID").value = oldBtn.id;
            
        for (var colIndex = 0; colIndex <= 4; colIndex++) {
            for (var rowIndex = 1; rowIndex <= 52; rowIndex++) {
                cell = document.getElementById('main_table').rows[rowIndex].cells[colIndex];
                //red for hard constraint
                if (lstRoom[roomActive].dayList[colIndex][rowIndex - 1].constraint == 1) { 
                    cell.style.backgroundColor = "rgba(255, 0, 0, 0.3)";
                    cell.innerHTML = "";
                }
                //blue for time filled
                else if (lstRoom[roomActive].dayList[colIndex][rowIndex - 1].filled == 1) { 
                    cell.style.backgroundColor = "rgba(30, 144, 255, 0.3)";
                    cell.innerHTML = lstRoom[roomActive].dayList[colIndex][rowIndex - 1].primaryHolder + "  " /*+ lstRoom[roomActive].dayList[colIndex][rowIndex - 1].primaryHolder*/;
                }
                //clear empty cells
                else {
                    cell.style.backgroundColor = "rgba(30, 144, 255, 0.0)";
                    cell.innerHTML = "";
                }
            }
        }
    }
    

    /* ALL MODALS */
    //hide modal if clicked outside	
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    /* ROOM MODAL */
    var modal = document.getElementById("modal_add"),
    	modalBtn = document.getElementById("add_room_button"),
    	spanModal = document.getElementsByClassName("close_modal")[0];
    // display modal
    modalBtn.onclick = function() {
        modal.style.display = "block";
    }
    //hide modal if exited
    spanModal.onclick = function() {
        modal.style.display = "none";
    }
    
	//saves information for previous button clicked
    if (hasSelection) {
        // i was kind of looking into this problem and couldnt really think of a way around it unless we use session storage. ill look around a little more to see if there is a javascript workaround though
	    oldBtn = document.getElementById(lastSelection);
	    //clicks oldBtin.id after page finishes loading
	    $( document ).ready(function() {
                    $( "#"+oldBtn.id ).trigger( "click" );
        	   });
	   
	   hasSelection = false;
	} else {
	    oldBtn = document.getElementById(lstRoom[0].roomID);
	}
	
    // info/calendar population
    viewBtn.forEach(function(btn, i) {
        btn.onclick = function() {
            alter[0].style.display = "inline"; //display room info
            oldBtn.style.backgroundColor = "#FEB729";// reset color of last clicked
            btn.style.backgroundColor = "#00275E"; //set clicked color
            
            oldBtn = document.getElementById(lstRoom[i].roomID); //assign clicked as old
            document.getElementById("roomID").value = oldBtn.id; //set post value
           
            //set info
            document.getElementById("building").innerHTML = lstRoom[i].building;
            document.getElementById("room").innerHTML = lstRoom[i].roomNumber; //
            document.getElementById("capacity").innerHTML = lstRoom[i].seatingCapacity;
            document.getElementById("type").innerHTML = lstRoom[i].roomType;
            
            showCalendar(i); //fill calendar
        }
    });
    
</script>