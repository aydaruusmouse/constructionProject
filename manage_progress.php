<?php 
include 'db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM user_productivity where id = ".$_GET['id'])->fetch_array();
	foreach($qry as $k => $v){
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<form action="" id="manage-progress">
		
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<input type="hidden" name="project_id" value="<?php echo isset($_GET['pid']) ? $_GET['pid'] : '' ?>">
		<div class="col-lg-12">
			<div class="row">
				<div class="col-md-5">
				
			
					<div class="form-group">
						<label for="">Work Plan</label>
						<input type="text" class="form-control form-control-sm" name="workplan" value="<?php echo isset($workplan) ? $workplan : '' ?>" required>
					</div>
					
					<div class="form-group">
						<label for="">Cost</label>
						<input type="number" class="form-control form-control-sm" name="cost" value="<?php echo isset($cost) ? $cost : '' ?>" required>
					</div>
					
					<div class="form-group">
						<label for="">Date</label>
						<input type="date" class="form-control form-control-sm" name="date" value="<?php echo isset($subject) ? $subject : '' ?>" required>
					</div>
					
					
					
				</div>
				<div class="col-md-7">
					<div class="form-group">
						<label for="">Comment/Progress Description</label>
						<textarea name="comment" id="" cols="30" rows="10" class="summernote form-control" required="">
							<?php echo isset($comment) ? $comment : '' ?>
						</textarea>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	console.log('sees heree');
	$(document).ready(function(){
	$('.summernote').summernote({
        height: 200,
        toolbar: [
            [ 'style', [ 'style' ] ],
            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
            [ 'fontname', [ 'fontname' ] ],
            [ 'fontsize', [ 'fontsize' ] ],
            [ 'color', [ 'color' ] ],
            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
            [ 'table', [ 'table' ] ],
            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
        ]
    })
     $('.select2').select2({
	    placeholder:"Please select here",
	    width: "100%"
	  });
     })
    $('#manage-progress').submit(function(e){
		console.log('manage progress');
    	e.preventDefault()
    	start_load()
    	$.ajax({
    		url:'ajax.php?action=save_progress',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp == 1){
					alert_toast('Data successfully saved',"success");
					setTimeout(function(){
						location.reload()
					},1500)
				} else{
					$('#msg').html('<div class="alert alert-danger">An error occured</div>')
					end_load()
				
				}
			}
    	})
    })
</script>