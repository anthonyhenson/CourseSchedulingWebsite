<?php
    require_once 'css/cssVersion.php';
	require_once 'session.php';
	require_once('lp/SQLDataHandler.php');
    $dataHandler = new SQLDataHandler();
	
	if (isset($_SESSION['user'])) {
		$userEmail = $_SESSION['user'];
		
		if (isset($_POST['professorID'])) {
		    ?><script>var lastSelection = "<?=$_POST['professorID']?>"; 
		    var hasSelection = true;
		    </script><?php
		}
		else {
		    ?><script>var hasSelection = false;</script><?php
		}
		
		//check for deleted professor id is dynamic
		foreach($_POST as $key=>$value) {
		    //only delete proper key
		    if (strcmp($value, "Delete") == 0) {
		        $pDeletedRows = $dataHandler->deleteProfessor($key);
		        
		        if ($pDeletedRows != 1) { ?>
				<script>alert("Error in deleting professor!");</script> <?php
			    }
			    else {  ?>
				<script>alert("Professor succesfully deleted. Previous professor assignments will remain until a new schedule is generated");</script> <?php
			    }
		    } 
		}
		
		 //update professor time constraints
        if (isset($_POST['submitConstraint'])) {
            $isSetting = filter_input(INPUT_POST, 'set-clear') == "set" ? true : false;
		    $dayID = (int)filter_input(INPUT_POST, 'day_selector');
	        $timeStartID = (int)filter_input(INPUT_POST, 'start_time', FILTER_VALIDATE_INT);
	        $timeEndID = (int)filter_input(INPUT_POST, 'end_time');
	        $professorID = filter_input(INPUT_POST, 'professorID');
	        
	        //correct times selected
	        if ($timeEndID > $timeStartID) {
			    $professor = $dataHandler->getProfessorByID($professorID);
			    $isConstraintsAdded = $dataHandler->updateProfessorTimesConstraint($professor, $dayID, $timeStartID, $timeEndID, $isSetting);

    			if (!$isConstraintsAdded) { ?>
    				<script>alert("Error updating professor constraints! Updating already set times will return an error.");</script> <?php
    			}
    			else {  ?>
    				<script>
    				    alert("Professor constraints succesfully updated");
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
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
	<link rel="stylesheet" media="screen and (min-width: 1001px)" href="./css/professors.css?v=<?=$cssVersion?>" />
</head>

<body>
<script> // Global Variables for JavaScript 
    var lstProfessorID = [];
    var lstProfessorName = [];
    var lstDayNames = [];
    var lstDayList = []; //holds DayTimes with TimeLength objects
</script>
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
        <span class="column-12" id="page_title">PROFESSORS <input id="add_professor_button" type="submit" value="Add Professor"/></span>
    </span>
    <?php
	    require_once('lp/Professor.php');
        require_once 'add_professor.php';
        
        $lstProfessors = array();
	    $lstProfessors = $dataHandler->getProfessors(); // list of professors
    ?>
<!--  ----------------------------- VIEW RESOURCE LIST  --------------------------------- -->
<span class="row container">
    <span class="column-4 columns_left"></span>
    <span class="column-4 columns_left resource_all">
        <form method="post" action="professors.php">
            <?php 
            foreach ($lstProfessors as $p) {
                $name = $p->getProfessorsName();
                $id = $p->getProfessorsID();
                $days = $p->getAvailableDayNames();
                echo "<script> var lstDayTimes = []; </script>"; 
                
                foreach ($p->getDayList() as $lstDay) { 
                    echo "<script> var lstTimeLength = []; </script>";
                    foreach ($lstDay->getTimeLengths() as $times) {
                        //creating the timeLength object
                        $primary = $times->getPrimaryPlaceHolder();
                        $alternate = $times->getAlternatePlaceHolder();
                        $filled = $times->isTimeFilled();
                        $constraint = $times->isTimeConstraint();
                        echo "<script> 
                        var timeLength = {};
                        timeLength.primaryHolder = '$primary';
                        timeLength.alternateHolder = '$alternate';
                        timeLength.filled = '$filled';
                        timeLength.constraint = '$constraint';
                        lstTimeLength.push(timeLength);</script>"; //nest
                    }
                    //fill list of timelengths into dayTimes list
                    echo "<script> lstDayTimes.push(lstTimeLength); </script>"; 
                }
                
                echo "<script> lstProfessorID.push('$id');
                    lstProfessorName.push('$name');
                    lstDayNames.push('$days');
                    lstDayList.push(lstDayTimes); 
                </script>";
                // ------------- Individual Resources -------------------   
                
              
                echo "<span class=\"row container resource_x\">
                    <span class=\"column-6 columns_left resource_text\">
                        $name
                    </span>
                    <span class=\"column-6 columns_left resource_buttons\">
                        <input id=\"$id\" type=\"button\" value=\"View / Edit\" />
                        <input id=\"$id\" type=\"submit\" value=\"Delete\" name=\"$id\" onclick=\"return confirm('Are you sure?');\" />
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
            <label>Name:</label>
            <span id="pName"></span>
            <label>ID:</label>
            <span id="pID"></span>
            <label>Schedule:</label>
            <span id="pSchedule"></span>
        </span>
        <span class="column-2 columns_left"></span>
    </span>

<!-- CONSTRAINTS -->
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
            <form action="professors.php" method="POST" id="constraintForm">
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
        		<input type="hidden" name="professorID" id="professorID" value="">
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
    var lstProfessor = [];
    var viewBtn = [];
    var deleteBtn = [];

    //info not displayed by default
    var alter = document.getElementsByClassName("alter_resources");
    alter[0].style.display = "none";
    
    //list for professors view and delete objects
    for (var i = 0; i < lstProfessorName.length; i++) {
        var oProfessor = {}; //everything put into one room object
        oProfessor.name = lstProfessorName[i];
        oProfessor.id = lstProfessorID[i];
        oProfessor.dayNames = lstDayNames[i];
        oProfessor.dayList = lstDayList[i];
        lstProfessor.push(oProfessor);
        
        viewBtn.push(document.getElementById(lstProfessor[i].id));
        deleteBtn.push(document.getElementById(lstProfessor[i].id)[1]);
    }

    function showCalendar(pIndex, days) {
       
        var cell;
        //setting professor times
        var startIndex;
        var endIndex;
        var pDayIndex; //daytimes index for professor
        //resetting times not in day
        var startIndexAlt;
        var endIndexAlt;
        var pDayIndexAlt;
        
        //set MWF or TR constraint and blocked indexes
        days == "MWF" ? (startIndex=0, endIndex=4, pDayIndex=[0,1,2], 
            startIndexAlt=1, endIndexAlt=3, pDayIndexAlt=[0,1]) : 
            (startIndex=1, endIndex=3, pDayIndex=[0,1], startIndexAlt=0, 
            endIndexAlt=4, pDayIndexAlt=[0,1,2]);
            
        //grey out unused cells
        for (var colIndex = startIndexAlt; colIndex <= endIndexAlt; colIndex+=2) {
            for (var rowIndex = 1; rowIndex <= 52; rowIndex++) {
                cell = document.getElementById('main_table').rows[rowIndex].cells[colIndex];
                cell.style.backgroundColor = "rgba(64, 64, 64, 0.8)";
                cell.innerHTML = "";
            }
        }

       var dayIndex = 0; //used for pDayIndex
        for (var colIndex = startIndex; colIndex <= endIndex; colIndex+=2) {
            //1 to 52 for calendar view: 0 to 51 for object
            for (var rowIndex = 1; rowIndex <= 52; rowIndex++) {
                cell = document.getElementById('main_table').rows[rowIndex].cells[colIndex];
                //red for hard constraint
                if (lstProfessor[pIndex].dayList[pDayIndex[dayIndex]][rowIndex - 1].constraint == 1) { 
                    cell.style.backgroundColor = "rgba(255, 0, 0, 0.3)";
                    cell.innerHTML = "";
                }
                // //blue for time filled but not constraint
                else if (lstProfessor[pIndex].dayList[pDayIndex[dayIndex]][rowIndex - 1].filled == 1) { 
                    cell.style.backgroundColor = "rgba(30, 144, 255, 0.3)";
                    cell.innerHTML = lstProfessor[pIndex].dayList[pDayIndex[dayIndex]][rowIndex - 1].primaryHolder;
                }
                // //clear empty cells
                else {
                    cell.style.backgroundColor = "rgba(30, 144, 255, 0.0)";
                    cell.innerHTML = "";
                }
            }
            dayIndex++;
        }
    }
    
    /* ALL MODALS */
    //hide modal if clicked outside	
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    /* PROFESSOR MODAL */
    var modal = document.getElementById("modal_add"),
    	modalBtn = document.getElementById("add_professor_button"),
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
	    var oldBtn = document.getElementById(lastSelection);
	    
	    //clicks oldBtin.id after page finishes loading
	    $( document ).ready(function() {
            $( "#"+oldBtn.id ).trigger( "click" );
	   });
	   
	   hasSelection = false;
	} else {
	    var oldBtn = document.getElementById(lstProfessor[0].id);
	}
	
    // info/calendar population
    viewBtn.forEach(function(btn, i) {
        btn.onclick = function() {
            alter[0].style.display = "inline"; //display professor info
            oldBtn.style.backgroundColor = "#FEB729";// reset color of last clicked
            btn.style.backgroundColor = "#00275E"; //set clicked color
            
            oldBtn = document.getElementById(lstProfessor[i].id); //assign clicked as old
            document.getElementById("professorID").value = oldBtn.id; //set post value
            
            //set info
            document.getElementById("pName").innerHTML = lstProfessor[i].name;
            document.getElementById("pID").innerHTML = lstProfessor[i].id;
            document.getElementById("pSchedule").innerHTML = lstProfessor[i].dayNames;
      
            //disable day selector options by professor schedule
            var dayOps = document.getElementById("day_selector").getElementsByTagName("option");
            if (lstProfessor[i].dayNames == "MWF") {
                dayOps[0].disabled = false;
                dayOps[1].disabled = true;
                dayOps[2].disabled = false;
                dayOps[3].disabled = true;
                dayOps[4].disabled = false;
                document.getElementById("day_selector").selectedIndex = 0;
            }
            else {
                dayOps[0].disabled = true;
                dayOps[1].disabled = false;
                dayOps[2].disabled = true;
                dayOps[3].disabled = false;
                dayOps[4].disabled = true;
                document.getElementById("day_selector").selectedIndex = 1;
            }
            
            showCalendar(i, lstProfessor[i].dayNames);
        }
    });
</script>