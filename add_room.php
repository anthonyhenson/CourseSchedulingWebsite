<!-- ------------------------- ADD ROOM MODAL --------------------------------------------- -->

<div id="modal_add" class="modal">
	<!-- Modal content -->
	<div class="modal-content" id="modal_room">
		<span class="close_modal"><p>&times;</p></span>
		<h3>ADD ROOM</h3>
		<form action="" method="POST">
		    <!-- BUILDING / ROOM # -->
			<span class="row">
			    <span class="column-1 columns-left">&nbsp</span>
				<span class="column-2 columns-left room_section">Building</span>
				<span class="column-3 columns-left room_input_frame"><input class="room_modal_input" name="bldg" type="text" required/></span>
				<span class="column-2 columns-left yellow_background room_section">Room #</span>
				<span class="column-3 columns-left room_input_frame"><input class="room_modal_input" name="roomNum" type="text" required/></span>
				<span class="column-1 columns-left">&nbsp</span>
			</span>
			<!-- CAPACITY / ROOM TYPE -->
			<span class="row">
			    <span class="column-1 columns-left">&nbsp</span>
				<span class="column-2 columns-left room_section">Capacity</span>
				<span class="column-3 columns-left room_input_frame"><input class="room_modal_input" name="capacity" type="text" required/></span>
				<span class="column-2 columns-left yellow_background room_section">Room Type</span>
				<span class="column-3 columns-left room_input_frame">
					<select class="room_modal_input" name="roomType" required>
  						<option value="standard">Standard</option>
						<option value="projector">Projector</option>
						<option value="lab">Lab</option>
						<option value="computers">Computers</option>
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
?>