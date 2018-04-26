<!-- ------------------------- ADD SECTION MODAL --------------------------------------------- -->

<div id="modal_add_s" class="modal">
	<!-- Modal content -->
	<div class="modal-content" id="modal_section">
		<span class="close_modal_s"><p>&times;</p></span>
		<h3>ADD SECTION TO <span class="cCode"></span></h3>
		<form method="POST">
		    <!-- SECTION / SEATING -->
			<span class="row">
			    <span class="column-1 columns-left">&nbsp</span>
				<span class="column-2 columns-left section">Section</span>
				<span class="column-3 columns-left input_frame">
				    <input class="modal_input" type="text" name="section" required/>
				</span>
				<span class="column-2 columns-left section">Seating</span>
				<span class="column-3 columns-left input_frame">
				    <input class="modal_input" type="text" name="seating" required/>
				</span>
				<span class="column-1 columns-left">&nbsp</span>
			</span>
			<!-- PROFESSOR -->
			<span class="row">
			    <span class="column-6 columns-left">&nbsp</span>
				<span class="column-2 columns-left section">Professor</span>
				<span class="column-3 columns-left input_frame">
					<select class="modal_input" id="professor_list" name="professor" required>
  						<option value="">Select</option>
					</select>
				</span>
				<span class="column-1 columns-left">&nbsp</span>
			</span>
			<script>document.getElementsByClassName("course_eval2").value = document.getElementsByClassName("cCode")[1].innerHTML</script>
			<input type="hidden" name="course_eval2" id="course_eval2" value="cCode">
			<input type="submit" class="submit_button columns-right" value="Submit" name="submitModalS"/>
		</form>
	</div>
</div>
