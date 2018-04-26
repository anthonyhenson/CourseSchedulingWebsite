<?php
    require_once 'css/cssVersion.php';
    require_once 'lp/SQLDataHandler.php';
	require_once 'session.php';
    $dataHandlerH = new SQLDataHandler();
	
	if (isset($_SESSION['user'])) {
		$userEmail = $_SESSION['user'];
	}
	else {
	    //go back to login
	    header('Location: login.php');
	}
	
	$addedSchedule = array();
	$notAddedSchedule = array();
	if (isset($_POST['date_button_select']) && strcmp($_POST['dateSchedule'], "") != 0) {
		$sDate = filter_input(INPUT_POST, 'dateSchedule');
		$addedSchedule = $dataHandlerH->getFilledSchedulePerDate($sDate);
		$notAddedSchedule = $dataHandlerH->getUnfilledSchedulePerDate($sDate);
	}
	else if (strcmp($_POST['dateSchedule'], "") == 0 && isset($_POST['date_button_select'])) {
		?><script>
			alert("Please select a date to proceed");
		</script><?php
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
	<link rel="stylesheet" media="screen and (min-width: 1001px)" href="./css/home.css?v=<?=$cssVersion?>" />
</head>

<body>

<!-- ------------------------- NAV BAR ----------------------------------------- -->
<header>
<?php
	require_once 'navbar.php';
?>
</header>

<main>
<span class="page_wrap">
<!--  ----------------------------- RESOURCE SELECTION --------------------------------- -->
<span class="row">
	<span class="all_resources">
			<span class="resource_section center">
			    <a href="professors.php"><img src="img/professor.jpg" height="200" width="200"></a>
				<h3 class="resource_title">PROFESSORS</h3>
			</span>
			<span class="resource_section center">
			    <a href="rooms.php"><img src="img/classroom.jpg" height="200" width="200"></a>
				<h3 class="resource_title">CLASSROOMS</h3>
			</span>
			<span class="resource_section center">
			    <a href="courses.php"><img src="img/courses.jpg" height="200" width="200"></a>
				<h3 class="resource_title">COURSES</h3>
			</span>
	</span>
</span>
<hr class="styled marginb_50">

<!-- ------------------------- GENERATE SCHEDULE ----------------------------------------- -->
<span class="row container blue center">
    <span class="column-12" id="generate_title">GENERATE SCHEDULE</span>
</span>

<span class="row container marginb_50" id="schedule_block">
    <img id="generate_image" src="img/calendar.jpg" class="column-2" height="150" width="150">
    <span class="column-10" id="generate_info">
        <p>In order to generate the schedule, please add all resources and constraints. 
    Any changes made to professors, classrooms or courses will be reflected in the generated schedule.</p>
    <form action="home.php" method="POST">
    	<button id="generate_button" type="submit" name="generate_button">GENERATE</button>
    </form>
    </span>
</span>
<hr class="styled">

<!-- ------------------------- VIEW GENERATED SCHEDULE ----------------------------------------- -->
<span class="row container marginb_50 margint_50">
	<span class="column-2 columns-left">&nbsp</span>
    <span class="column-8">
    	<span class="row">
    		<span class="column-4 columns-left"><p>Choose a date to view the generated schedule:</p></span>
    		<form action="home.php" method="POST">
    		<span class="column-4 columns-left">
    			<select name="dateSchedule" class="date_generated_dropdown">
    			<option selected="selected" value="">Schedule Dates</option> <?php
    			$dates = $dataHandlerH->getVersionHistoryTimes();
    			foreach($dates as $date) { ?>
	        		<option value="<?=strtolower($date)?>"> <?=$date?> </option> <?php
    			} ?>
    		</select>
    		</span>
    		<span class="column-4 columns-left">
    			<button type="submit" id="date_button" name="date_button_select" value="Submit">SUBMIT</button>
    		</span>
    		</form>
    	</span>
    </span>
    <span class="column-2 columns-left">&nbsp</span>
</span>
<!-- ------------------------- assigned sections -->
<span class="row container margint_50 marginb_50 view_schedule">
	<span class="column-1 columns-left">&nbsp</span>
	<span class="column-10 columns-left center">
		<table id="course_schedule_table">
			<tr><th colspan="6">Assigned Sections</th></tr>
			<tr>
				<th>Course</th>
				<th>Section</th>
				<th>Days Assigned</th>
				<th>Time</th>
				<th>Professor</th>
				<th>Room</th>
			</tr> <?php
			foreach ($addedSchedule as $s) { ?>
				<tr>
				<td><?=$s['course']?></td>
				<td><?=$s['section']?></td>
				<td><?=$s['daysAssigned']?></td>
				<td><?=$s['time']?></td>
				<td><?=$s['professor']?></td>
				<td><?=$s['room']?></td>
				</tr> <?php
			} ?>
		</table>
	</span>
	<span class="column-1 columns-left">&nbsp</span>
</span>

<!-- ------------------------- unassigned sections -->
<span class="row container margint_50 marginb_50 view_schedule">
	<span class="column-1 columns-left">&nbsp</span>
	<span class="column-10 columns-left center">
		<table id="course_unassigned_table">
			<tr><th colspan="3">Unassigned Sections</th></tr>
			<tr>
				<th>Course</th>
				<th>Section</th>
				<th>Professor</th>
			</tr> <?php
			foreach ($notAddedSchedule as $sn) { ?>
				<tr>
				<td><?=$sn['course']?></td>
				<td><?=$sn['section']?></td>
				<td><?=$sn['professor']?></td>
				</tr> <?php
			} ?>
		</table>
	</span>
	<span class="column-1 columns-left">&nbsp</span>
</span>

</span><!-- page wrap end -->


</main>

<!-- ------------------------- FOOTER ----------------------------------------- -->
</body>
<footer id="footer" class="blue_background center"><img id="footer_logo" src="img/cbu_logo3.png" height="40" width="100">
</footer>

</html>

<?php
	if (isset($_POST['generate_button'])) {
	
		require_once('lp/CourseSchedulingMain.php');
		$main = new CourseSchedulingMain();
		$main->generateSchedule(); ?>
		<script>window.location.assign("home.php");</script> <?php
		//$main->outputGenerated();
	}
?>
