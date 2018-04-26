<!-- ------------------------- ADD ROOM MODAL --------------------------------------------- -->

<div id="modal_add" class="modal">
	<!-- Modal content -->
	<div class="modal-content" id="modal_room">
		<span class="close_modal"><p>&times;</p></span>
		<h3>ADD ROOM</h3>
		<form method="POST">
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