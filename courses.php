<?php
	require_once 'css/cssVersion.php';
	require_once 'session.php';
	require_once('lp/SQLDataHandler.php');
    $dataHandler = new SQLDataHandler();
	
	if (isset($_SESSION['user'])) {
		$userEmail = $_SESSION['user'];
		
		//TODO: set last course selected selected: section not necessary
		//check for deleted professor id is dynamic
		
		//iterate POSTS to delete course
		foreach($_POST as $key=>$value) {
		    //only delete proper key
		    if (strcmp($value, "Delete") == 0) {
		        $isCourseAndSecsDeleted = $dataHandler->deleteCourse($key);

		        if (!$isCourseAndSecsDeleted) { ?>
				<script>alert("Error in deleting course and its' sections!");</script> <?php
			    }
			    else {  ?>
				<script>alert("Course and it's sections have been succesfully deleted. Previous course assignments will remain until a new schedule is generated");</script> <?php
			    }
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
	<link rel="stylesheet" media="screen and (min-width: 1001px)" href="./css/courses.css?v=<?=$cssVersion?>" />
</head>

<body>
	
<script> 

//----- COURSE OBJECT DECLARATIONS
    var lstCourseCode = [];
    var lstCourseCredits = [];
    var lstCourseType = [];
    var lstCourseSections = [];
    
	var lstProfessors = {}; //hashed professors by id => "name scheduleType"
	var lastCourseCode; //saves last course code selected
	
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
        <span class="column-12" id="page_title">COURSES <input id="add_courses_button" type="submit" value="Add Course"/></span>
    </span> 
	<?php
    
    require_once('lp/Course.php');
    require_once('lp/Section.php');
    
    //store professors for adding sections and gettingCourses
    $lstProfessors = array();
    $lstProfessors = $dataHandler->getProfessors();
    foreach ($lstProfessors as $p) {
        $ID = $p->getProfessorsID();
        $name = $p->getProfessorsName();
        $schType = $p->getAvailableDayNames(); ?>
        <script>
            lstProfessors[<?=$ID?>] = "<?=$name?> (<?=$schType?>)"; //hashed professor array
        </script> <?php
    }
    
    $lstCourses = array();
    $lstCourses = $dataHandler->getCourses($lstProfessors); // list of courses
    
    require_once 'add_course.php';
    require_once 'add_section.php';
	?>

<!--  ----------------------------- VIEW RESOURCE LIST  --------------------------------- -->
<span class="row container">
    <span class="column-4 columns_left"></span>
    <span class="column-4 columns_left resource_all">
        <form method="POST" action="courses.php">
            <?php
            
            foreach ($lstCourses as $oCourse) {
                $courseCode = $oCourse->getCourseCode();
                $courseType = $oCourse->getCourseType();
                $courseCredits = $oCourse->getCredits();
                
                // ----------- SECTION OBJECT VARIABLE DECLARATIONS --------------------
                echo "<script> var lstSectionID = [];
                            var lstSectionChar = [];
                            var lstSectionProfessor = [];
                            var lstSectionSeating = [];
                            var lstSectionRoomID = [];
                            var lstDayList = []; </script>"; //sections within course
                            
                foreach ($oCourse->getCourseSections() as $oSection) {
                	
                	$sectionChar = $oSection->getSection();
                	null != $oSection->getProfessorAssigned() ? $profName = $oSection->getProfessorAssigned()->getProfessorsName() : $profName = "UNASSIGNED";
                	$profName = $oSection->getProfessorAssigned()->getProfessorsName();
                	$seating = $oSection->getSectionSize();
                	$sectionID = $oSection->getSectionID();
                	$roomID = $oSection->getRoomAssigned()->getRoomID();
	                echo "<script> var lstDayTimes = []; </script>"; //each day
	                
	                foreach ($oSection->getDayTimeAssigned() as $lstDay) { 
	                    
	                    echo "<script> var lstTimeLength = []; </script>";
	                    foreach ($lstDay->getTimeLengths() as $times) {
	                        //creating the timeLength object
	                        $primary = $times->getPrimaryPlaceHolder();
	                        $alternate = $times->getAlternatePlaceHolder();
	                        $filled = $times->isTimeFilled();
	                        echo "<script> 
	                        var timeLength = {};
	                        timeLength.primaryHolder = '$primary';
	                        timeLength.alternateHolder = '$alternate';
	                        timeLength.filled = '$filled';
	                        lstTimeLength.push(timeLength);</script>"; 
	                    } //timelength
	                    echo "<script> lstDayTimes.push(lstTimeLength); </script>"; 
	                } //day
	                
	                // ------------- SECTION OBJECT VARIABLES INITIALIZED -------------------
	                echo "<script> 
	                        lstSectionChar.push('$sectionChar');
	                        lstSectionID.push('$sectionID');
	                        lstSectionProfessor.push('$profName');
	                        lstSectionSeating.push('$seating');
	                        lstSectionRoomID.push('$roomID');
	                        lstDayList.push(lstDayTimes);
	                        </script>";
                } //section
                
                // ------------- SECTION OBJECT CREATED ------------------------
                echo "<script>
                var lstSection = []
                for (var i = 0; i < lstSectionID.length; i++) {
                    var oSection = {}; //everything put into one room object
                    oSection.char = lstSectionChar[i];
                    oSection.ID = lstSectionID[i];
                    oSection.roomID = lstSectionRoomID[i];
                    oSection.seating = lstSectionSeating[i];
                    oSection.professor = lstSectionProfessor[i];
                    oSection.dayList = lstDayList[i];
                    lstSection.push(oSection);
                }
                </script>";
                
                // ------------- COURSE VARIABLES INITIALIZED -------------------
                echo "<script> 
                        lstCourseCode.push('$courseCode');
                        lstCourseCredits.push('$courseCredits'); 
                        lstCourseType.push('$courseType');
                        lstCourseSections.push(lstSection);
                </script>";
                        
                // ------------- HTML COURSE LIST -------------------   
                echo "<span class=\"row container resource_x\">
                    <span class=\"column-6 columns_left resource_text\">
                        $courseCode
                    </span>
                    <span class=\"column-6 columns_left resource_buttons\">
                        <input id=\"$courseCode\" type=\"button\" value=\"View / Edit\" />
                        <input id=\"$courseCode\" type=\"submit\" value=\"Delete\" name=\"$courseCode\" onclick=\"return confirm('Are you sure?');\" />
                    </span>
                </span>";
            } //course
            ?>
        </form>
    </span>
    <span class="column-4 columns_left"></span>
</span>
  

<!--  ----------------------------- VIEW SELECTED COURSE INFO  --------------------------------- -->
<span class="alter_resources">
    <span class="row container">
        <span class="column-2 columns_left"></span>
        <span class="column-8 columns_left alter_items">
        	<label>Course:</label>
        	<span id="cCode"></span>
        	<label>Type:</label>
        	<span id="cType"></span>
        	<label>Credits:</label>
       		<span id="cCredits"></span>
       		<span class="resource_buttons">
        		<input id="add_sections_button" type="submit" value="Add Section"/>
        	</span>
        </span>
        <span class="column-2 columns_left"></span>
    </span>
    
<!-- // individual sections DYNAMICALLY OUTPUT ONCE READY -->
<span class="row container">
    <span class="column-1 columns_left"></span>
    <span class="column-10 columns_left">
    	<span class="row container">
	    	<button class="column-11 columns_left alter_section_button" style="background-color:#00275E;">>
				<span class="alter_items_sub">
			        <label>Section:</label>
			        <span>A</span>
			        <label>Professor:</label>
			        <span>Kyungsoo Im (MWF)</span>
			        <label>Seating:</label>
			        <span>35</span>
			        <label>Room:</label>
			        <span>YGR123C</span>
		        </span>
	    	</button>
	    	<span class="column-1 columns_left section_delete">
	    		<input type="checkbox" />
	    	</span>
    	</span>
    </span>
    <span class="column-1 columns_left"></span>
</span>


<span class="row container">
    <span class="column-1 columns_left"></span>
    <span class="column-10 columns_left">
    	<span class="row container">
	    	<button class="column-11 columns_left alter_section_button">
				<span class="alter_items_sub">
			        <label>Section:</label>
			        <span>B</span>
			        <label>Professor:</label>
			        <span>Creed Jones (TR)</span>
			        <label>Seating:</label>
			        <span>20</span>
			        <label>Room:</label>
			        <span>ENGR215</span>
		        </span>
	    	</button>
	    	<span class="column-1 columns_left section_delete">
	    		<input type="checkbox" />
	    	</span>
    	</span>
    </span>
    <span class="column-1 columns_left"></span>
</span>

<!-- CONSTRAINTS -->
<span class="row container">
    <span class="column-9 columns_left"></span>
	<span class="column-3 columns_left">
			<input id="delete_section" type="submit" value="Delete Section"/>
		</span>
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
<!-- load add section to work with -->
<iframe src="add_section.php" id="iframeAddSection" style="display:none"></iframe>
<script>
	var lstCourses = [];
    var viewBtn = [];
    var deleteBtn = [];
    alert(lstCourses[0].sections[0]); 
    
    //info not displayed by default
    //var alter = document.getElementsByClassName("alter_resources");
    //alter[0].style.display = "none";
    
    
    //list for courses view and delete objects
    for (var i = 0; i < lstCourses.length; i++) {
        var oCourse = {};
     	oCourse.code = lstCourseCode[i];
     	oCourse.type = lstCourseType[i];
     	oCourse.credits = lstCourseCredits[i];
        oCourse.sections = lstCourseSections[i];
        lstCourses.push(oCourse);
        
        viewBtn.push(document.getElementById(lstCourses[i].code));
        deleteBtn.push(document.getElementById(lstCourses[i].code)[1]);
    }

	/* COURSE MODAL */
	var modalC = document.getElementById('modal_add_c'),
		modalBtnC = document.getElementById("add_courses_button"),
		spanModalC = document.getElementsByClassName("close_modal_c")[0];
	
	// display modal
	modalBtnC.onclick = function() {
	    modalC.style.display = "block";
	}
	//hide modal if exited
	spanModalC.onclick = function() {
	    modalC.style.display = "none";
	}
	
	/* SECTION MODAL */
	var modalS = document.getElementById('modal_add_s'),
		modalBtnS = document.getElementById("add_sections_button"),
		spanModalS = document.getElementsByClassName("close_modal_s")[0];
	
	// display modal
	modalBtnS.onclick = function() {
	    modalS.style.display = "block";
	}
	//hide modal if exited
	spanModalS.onclick = function() {
	    modalS.style.display = "none";
	}
	
	/* ALL MODALS */
	//hide modal if clicked outside	
	window.onclick = function(event) {
	    if (event.target == modalC) {
	        modalC.style.display = "none";
	    }
	}
	window.onclick = function(event) {
	    if (event.target == modalS) {
	        modalS.style.display = "none";
	    }
	}
	
	//something is not working in this javascript area because none of the alerts are working
	var oldBtn = document.getElementById(lstCourses[0].code);
	lastCourseCode = oldBtn.id;
    // info/calendar population
    viewBtn.forEach(function(btn, i) {
        btn.onclick = function() {
            //alter[0].style.display = "inline"; //display course info
            oldBtn.style.backgroundColor = "#FEB729";// reset color of last clicked
            btn.style.backgroundColor = "#00275E"; //set clicked color
            
            oldBtn = document.getElementById(lstCourses[i].code); //assign clicked as old
            lastCourseCode = oldBtn.id; //store id for add section
            document.getElementById("cCodeAddSection").innerHTML = lastCourseCode; //set add_section title
            alert(lastCourseCode); 
            //set basic course info
            alert(lstCourses[i].code);
            document.getElementById("cCode").innerHTML = lstCourses[i].code;
            document.getElementById("cType").innerHTML = lstCourses[i].type;
            document.getElementById("cCredits").innerHTML = lstCourses[i].credits;
            
            //showCalendar(i, lstProfessor[i].dayNames);
        }
    });

    //professor dropdown list options
    var sel = document.getElementById('professor_list');
	for (var key in lstProfessors) {
	    var opt = document.createElement('option');
	    opt.innerHTML = lstProfessors[key];
	    opt.value = key;
	    sel.appendChild(opt);
	}
	
</script>
<?php
    //must be handled in courses.php and not add_section.php
	if (isset($_POST['submitModalS'])) {
	   
		$sCode = filter_input(INPUT_POST, 'code');
		$sType = filter_input(INPUT_POST, 'type');
		$iCredits = filter_input(INPUT_POST, 'credits');
	   
	    if ($iCredits != "" && $sCode != "") {
	    	$course = new Course($sCode, $iCredits, $sType);
			$isCourseAdded = $dataHandler->addCourse($course);

			if ($isCourseAdded != 1) { ?>
				<script>alert("Error in adding course!");</script> <?php
			}
			else {  ?>
				<script>alert("Course succesfully added");</script> <?php
			}
		}
		else { ?>
			<script>alert("Invalid Course inputs!");</script> <?php
		}
	}
?>


<script>
   


    // function showCalendar(pIndex, days) {
       
    //     var cell;
    //     //setting professor times
    //     var startIndex;
    //     var endIndex;
    //     var pDayIndex; //daytimes index for professor
    //     //resetting times not in day
    //     var startIndexAlt;
    //     var endIndexAlt;
    //     var pDayIndexAlt;
        
    //     days == "MWF" ? (startIndex=0, endIndex=4, pDayIndex=[0,1,2], 
    //         startIndexAlt=1, endIndexAlt=3, pDayIndexAlt=[0,1]) : 
    //         (startIndex=1, endIndex=3, pDayIndex=[0,1], startIndexAlt=0, 
    //         endIndexAlt=4, pDayIndexAlt=[0,1,2]);
            
    //     //grey out unused cells
    //     for (var colIndex = startIndexAlt; colIndex <= endIndexAlt; colIndex+=2) {
    //         for (var rowIndex = 1; rowIndex <= 52; rowIndex++) {
    //             cell = document.getElementById('main_table').rows[rowIndex].cells[colIndex];
    //             cell.style.backgroundColor = "rgba(64, 64, 64, 0.8)";
    //         }
    //     }

    //   var dayIndex = 0; //used for pDayIndex
    //     for (var colIndex = startIndex; colIndex <= endIndex; colIndex+=2) {
    //         //1 to 52 for calendar view: 0 to 51 for object
    //         for (var rowIndex = 1; rowIndex <= 52; rowIndex++) {
    //             cell = document.getElementById('main_table').rows[rowIndex].cells[colIndex];
    //             //red for hard constraint
    //             if (lstProfessor[pIndex].dayList[pDayIndex[dayIndex]][rowIndex - 1].constraint == 1) { 
    //                 cell.style.backgroundColor = "rgba(255, 0, 0, 0.3)";
    //                 cell.innerHTML = "";
    //             }
    //             // //blue for time filled but not constraint
    //             else if (lstProfessor[pIndex].dayList[pDayIndex[dayIndex]][rowIndex - 1].filled == 1) { 
    //                 cell.style.backgroundColor = "rgba(30, 144, 255, 0.3)";
    //                 cell.innerHTML = lstProfessor[pIndex].id + "  " /*+ lstRoom[roomActive].dayList[colIndex][rowIndex - 1].primaryHolder*/;
    //             }
    //             // //clear empty cells
    //             else {
    //                 cell.style.backgroundColor = "rgba(30, 144, 255, 0.0)";
    //                 cell.innerHTML = "";
    //             }
    //         }
    //         dayIndex++;
    //     }
    // }
    
    // /* ALL MODALS */
    // //hide modal if clicked outside	
    // window.onclick = function(event) {
    //     if (event.target == modal) {
    //         modal.style.display = "none";
    //     }
    // }
    // /* PROFESSOR MODAL */
    // var modal = document.getElementById("modal_add"),
    // 	modalBtn = document.getElementById("add_professor_button"),
    // 	spanModal = document.getElementsByClassName("close_modal")[0];
    // // display modal
    // modalBtn.onclick = function() {
    //     modal.style.display = "block";
    // }
    // //hide modal if exited
    // spanModal.onclick = function() {
    //     modal.style.display = "none";
    // }
    
    // var oldBtn = document.getElementById(lstProfessor[0].id);
    // // info/calendar population
    // viewBtn.forEach(function(btn, i) {
    //     btn.onclick = function() {
    //         alter[0].style.display = "inline"; //display professor info
    //         oldBtn.style.backgroundColor = "#FEB729";// reset color of last clicked
    //         btn.style.backgroundColor = "#00275E"; //set clicked color
            
    //         oldBtn = document.getElementById(lstProfessor[i].id); //assign clicked as old
    //         document.getElementById("professorID").value = oldBtn.id; //set post value
            
    //         //set info
    //         document.getElementById("pName").innerHTML = lstProfessor[i].name;
    //         document.getElementById("pID").innerHTML = lstProfessor[i].id;
    //         document.getElementById("pSchedule").innerHTML = lstProfessor[i].dayNames;
      
    //         //disable day selector options by professor schedule
    //         var dayOps = document.getElementById("day_selector").getElementsByTagName("option");
    //         if (lstProfessor[i].dayNames == "MWF") {
    //             dayOps[0].disabled = false;
    //             dayOps[1].disabled = true;
    //             dayOps[2].disabled = false;
    //             dayOps[3].disabled = true;
    //             dayOps[4].disabled = false;
    //             document.getElementById("day_selector").selectedIndex = 0;
    //         }
    //         else {
    //             dayOps[0].disabled = true;
    //             dayOps[1].disabled = false;
    //             dayOps[2].disabled = true;
    //             dayOps[3].disabled = false;
    //             dayOps[4].disabled = true;
    //             document.getElementById("day_selector").selectedIndex = 1;
    //         }
            
    //         showCalendar(i, lstProfessor[i].dayNames);
    //     }
    // });
</script>