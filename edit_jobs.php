<?php
include('db_connect.php');

if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM contractors WHERE id = ".$_GET['id'])->fetch_array();
    foreach($qry as $k => $v){
        $$k = $v;
    }
}
?>

<div class="container-fluid">
    <form method="post" action="" id="manage-contractor">
        
    <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <input type="hidden" name="project_id" value="<?php echo isset($_GET['pid']) ? $_GET['pid'] : '' ?>">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control form-control-sm" name="name"  value="<?php echo isset($name) ? $name : '' ?>" required>
        </div>
        
        <div class="form-group">
            <label for="rate">Total Rate</label>
            <input type="number" name="total_rate" id="rate" class="form-control form-control-sm" value="<?php echo isset($total_rate) ? $total_rate : '' ?>" required>
            <input type="hidden" name="remaining_balance" id="remaining_balance" value="0.0">
        </div>
       
        <input type="hidden" name="action" value="save_contractor">
      
    </form>
</div>



   


<script>
$('#manage-contractor').submit(function(e){
    e.preventDefault();
    start_load();
    $.ajax({
        url: 'ajax.php?action=save_contractor',
        data: new FormData($(this)[0]),
        cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
        type: 'POST',
        success:function(resp){
            console.log(resp);
            if(resp == 1){
                alert_toast('Data successfully saved', "success");
                setTimeout(function(){
                    location.reload();
                }, 1500);
            } else {
                alert_toast('Failed to save data', "error");
                end_load();
            }
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText);
            alert_toast('Failed to save data', "error");
            end_load();
        }
    });
});
</script>
