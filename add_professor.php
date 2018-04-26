<!-- ------------------------- ADD PROFESSOR MODAL --------------------------------------------- -->

<div id="modal_add" class="modal">
	<!-- Modal content -->
	<div class="modal-content" id="modal_professor">
		<span class="close_modal"><p>&times;</p></span>
		<h3>ADD PROFESSOR</h3>
		<form action="" method="POST">
		    <!-- FIRST/LAST NAME # -->
			<span class="row">
			    <span class="column-1 columns-left">&nbsp</span>
				<span class="column-2 columns-left section">First Name</span>
				<span class="column-3 columns-left input_frame"><input class="modal_input" type="text" name="first" required/></span>
				<span class="column-2 columns-left section">Last Name</span>
				<span class="column-3 columns-left input_frame"><input class="modal_input" type="text" name="last" required/></span>
				<span class="column-1 columns-left">&nbsp</span>
			</span>
			<!-- ID / SCHEDULE TYPE -->
			<span class="row">
			    <span class="column-1 columns-left">&nbsp</span>
				<span class="column-2 columns-left section">ID</span>
				<span class="column-3 columns-left input_frame"><input class="modal_input" type="text" name="professorID" required/></span> 
				<span class="column-2 columns-left section">Schedule Type</span>
				<span class="column-3 columns-left input_frame">
					<select class="modal_input" name="scheduleType" required>
  						<option value="MWF">MWF</option>
						<option value="TR">TR</option>
					</select>
				</span>
				<span class="column-1 columns-left">&nbsp</span>
			</span>
			
			<input type="submit" class="submit_button columns-right" value="Submit" name="submitModal"/>
		</form>
	</div>
</div>

<?php
	if (isset($_POST['submitModal'])) {
		$sFirst = filter_input(INPUT_POST, 'first');
		$sLast = filter_input(INPUT_POST, 'last');
	    $iProfessorID = (int)filter_input(INPUT_POST, 'professorID', FILTER_VALIDATE_INT);
	    $sAvailableDayNames = filter_input(INPUT_POST, 'scheduleType');
	    $sProfessorName = $sFirst . " " . $sLast;
	    
	  
	    //not negative or incorrect input
	    if ($iProfessorID > 0) {
	    	$professor = new Professor($iProfessorID, $sProfessorName, $sAvailableDayNames);
			$isProfessorAdded = $dataHandler->addProfessor($professor);

			if ($isProfessorAdded != 1) { ?>
				<script>alert("Error in adding professor!");</script> <?php
			}
			else {  ?>
				<script>alert("Professor succesfully added");
				window.location.assign("professors.php");</script> <?php
			}
		}
		else { ?>
			<script>alert("Invalid professor inputs!");</script> <?php
				
		}
	}
?>