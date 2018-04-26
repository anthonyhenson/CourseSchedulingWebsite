<!-- ------------------------- ADD COURSE MODAL --------------------------------------------- -->

<div id="modal_add_c" class="modal">
	<!-- Modal content -->
	<div class="modal-content" id="modal_course">
		<span class="close_modal_c"><p>&times;</p></span>
		<h3>ADD COURSE</h3>
		<form action="" method="POST">
		    <!-- COURSE / TYPE -->
			<span class="row">
			    <span class="column-1 columns-left">&nbsp</span>
				<span class="column-2 columns-left section">Course Code</span>
				<span class="column-3 columns-left input_frame"><input class="modal_input" type="text" name="code" required/></span>
				<span class="column-2 columns-left section">Course Type</span>
				<span class="column-3 columns-left input_frame">
					<select class="modal_input" name="type" required>
  						<option value="standard">Standard</option>
						<option value="projector">Projector</option>
						<option value="lab">Lab</option>
						<option value="computers">Computers</option>
					</select>
				</span>
				<span class="column-1 columns-left">&nbsp</span>
			</span>
			<!-- CREDITS -->
			<span class="row">
			    <span class="column-6 columns-left">&nbsp</span>
				<span class="column-2 columns-left section">Credits</span>
				<span class="column-3 columns-left input_frame">
					<select class="modal_input" name="credits" required>
  						<option value="">Select</option>
						<option value="1">1</option>
						<option value="3">3</option>
					</select>
				</span>
				<span class="column-1 columns-left">&nbsp</span>
			</span>
			<!-- notice -->
			<span class="row center">
				<p>Adding sections and their assigned professors may be completed after adding the course.</p>
			</span>
			
			<input type="submit" class="submit_button columns-right" value="Submit" name="submitModalC"/>
		</form>
	</div>
</div>
<?php
	if (isset($_POST['submitModalC'])) {
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
				<script>alert("Course succesfully added");
				window.location.assign("courses.php");</script> <?php
			}
		}
		else { ?>
			<script>alert("Invalid Course inputs!");</script> <?php
		}
	}
?>