<?php
    require_once 'css/cssVersion.php';
    require_once 'lp/SQLDataHandler.php';
	require_once 'session.php';
	
	if (isset($_SESSION['user'])) {
		$userEmail = $_SESSION['user'];
	}
	else {
	    //go back to login
	    header('Location: login.php');
	}
//<i class="fa fa-user" aria-hidden="true"></i>
//<i class="fa fa-university" aria-hidden="true"></i>
//<i class="fa fa-users" aria-hidden="true"></i>
//<i class="fa fa-file-code-o" aria-hidden="true"></i>
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
    <button id="generate_button">GENERATE</button>
    </span>
</span>
<hr class="styled">
<span class="row container margint_50 marginb_50">
	<span class="column-1 columns-left">&nbsp</span>
	<span class="column-10 columns-left center">
		<table id="course_schedule_table">
			<tr>
				<th>Course</th>
				<th>Section</th>
				<th>Days Assigned</th>
				<th>Time</th>
				<th>Professor</th>
				<th>Room</th>
			</tr>
			<tr>
				<td>EGR324</td>
				<td>A</td>
				<td>MWF</td>
				<td>8:00-9:00</td>
				<td>Kyungsoo, Im</td>
				<td>YGR 215A</td>
			</tr>
			<tr>
				<td>EGR222</td>
				<td>C</td>
				<td>TR</td>
				<td>9:15-10:45</td>
				<td>Creed, Jones</td>
				<td>EGR 110</td>
			</tr>
			<tr>
				<td>CSC342</td>
				<td>B</td>
				<td>TR</td>
				<td>9:15-10:45</td>
				<td>Louis, Perkins</td>
				<td>EGR 114</td>
			</tr>
			<tr>
				<td>EGR222</td>
				<td>A</td>
				<td>M</td>
				<td>9:15-10:45</td>
				<td>Creed, Jones</td>
				<td>EGR 110</td>
			</tr>
		</table>
	</span>
	<span class="column-1 columns-left">&nbsp</span>
</span>

</span><!-- page wrap end -->

<?php
require_once('lp/CourseSchedulingMain.php');
$testing = new CourseSchedulingMain();
$testing->main();

?>
</main>

<!-- ------------------------- FOOTER ----------------------------------------- -->
</body>
<footer id="footer" class="blue_background center"><img id="footer_logo" src="img/cbu_logo3.png" height="40" width="100">
</footer>

</html>
