<!-- ------------------------- ADD SECTION MODAL --------------------------------------------- -->

<div id="modal_add_s" class="modal">
	<!-- Modal content -->
	<div class="modal-content" id="modal_section">
		<span class="close_modal_s"><p>&times;</p></span>
		<h3>ADD SECTION TO <span id="cCodeAddSection"></span></h3>
		<form action="" method="POST">
		    <!-- SECTION / SEATING -->
			<span class="row">
			    <span class="column-1 columns-left">&nbsp</span>
				<span class="column-2 columns-left section">Section</span>
				<span class="column-3 columns-left input_frame">
				    <input class="modal_input" type="text" required/>
				</span>
				<span class="column-2 columns-left section">Seating</span>
				<span class="column-3 columns-left input_frame">
				    <input class="modal_input" type="text" required/>
				</span>
				<span class="column-1 columns-left">&nbsp</span>
			</span>
			<!-- PROFESSOR -->
			<span class="row">
			    <span class="column-6 columns-left">&nbsp</span>
				<span class="column-2 columns-left section">Professor</span>
				<span class="column-3 columns-left input_frame">
					<select class="modal_input" id="professor_list" required>
  						<option value="">Select</option>
					</select>
				</span>
				<span class="column-1 columns-left">&nbsp</span>
			</span>
			<input type="submit" class="submit_button columns-right" value="Submit" name="submitModalS"/>
		</form>
	</div>
</div>
