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
    <form method="post" action="" id="save-jobs">
        
    <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <input type="hidden" name="contractor_id" value="<?php echo isset($_GET['contractor_id']) ? $_GET['contractor_id'] : '' ?>">
        <div class="form-group">
            <label for="name">Date</label>
            <input type="date" class="form-control form-control-sm" name="date"  value="<?php echo isset($date) ? $date : '' ?>" required>
        </div>
        
        <div class="form-group">
            <label for="rate">Daily Rate</label>
            <input type="number" name="total_rate" id="rate" class="form-control form-control-sm" value="<?php echo isset($total_rate) ? $total_rate : '' ?>" required>
            <input type="hidden" name="remaining_balance" id="remaining_balance" value="0.0">
        </div>
       
        <input type="hidden" name="action" value="save_jobs">
      
    </form>
</div>



   


<script>
$('#save-jobs').submit(function(e){
    console.log('clicked save jobs');
    e.preventDefault();
    start_load();
    $.ajax({
        url: 'ajax.php?action=save_job',
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
