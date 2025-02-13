<?php 
include 'db_connect.php';
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM task_list where id = ".$_GET['id'])->fetch_array();
    foreach($qry as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="manage-task">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <input type="hidden" name="project_id" value="<?php echo isset($_GET['pid']) ? $_GET['pid'] : '' ?>">
      
        <div class="form-group">
            <label for="">Material Name</label>
            <input type="text" class="form-control form-control-sm" name="materialname" value="<?php echo isset($materialname) ? $materialname : '' ?>" required>
        </div>
        
        <div class="form-group">
            <label for="">Quantity</label>
            <input type="number" class="form-control form-control-sm" name="quantity" value="<?php echo isset($quantity) ? $quantity : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="">Cost</label>
            <input type="number" step="0.01" class="form-control form-control-sm" name="cost" value="<?php echo isset($cost) ? $cost : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="">Description</label>
            <input type="text" step="0.01" class="form-control form-control-sm" name="description" value="<?php echo isset($cost) ? $cost : '' ?>" required>
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ol', 'ul', 'paragraph', 'height']],
                ['table', ['table']],
                ['view', ['undo', 'redo', 'fullscreen', 'codeview', 'help']]
            ]
        });
    });

    $('#manage-task').submit(function(e){
        e.preventDefault();
        start_load();
        $.ajax({
            url:'ajax.php?action=save_task',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
				console.log(resp);
                if(resp == 1){
                    alert_toast('Data successfully saved',"success");
                    setTimeout(function(){
                        location.reload();
                    },1500);
                }else{
				
					alert_toast('Failed to save data',"error");
					end_load();
				}
			
            }
        });
    });
</script>
