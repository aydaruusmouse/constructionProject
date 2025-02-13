<?php
if(!isset($conn)){ 
    include 'db_connect.php'; 
}

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$contractor_id = $_GET['contractor_id'];

// Fetch contractor details
$sql_contractor = "SELECT * FROM contractors WHERE id = $contractor_id";
$result_contractor = $conn->query($sql_contractor);
$contractor = $result_contractor->fetch_assoc();

// Fetch jobs for the contractor
$sql_jobs = "SELECT * FROM jobs WHERE contractor_id = $contractor_id";
$result_jobs = $conn->query($sql_jobs);

// Calculate remaining balance
$sql_sum_daily_rate = "SELECT SUM(daily_rate) AS total_daily_rate FROM jobs WHERE contractor_id = $contractor_id";
$result_sum_daily_rate = $conn->query($sql_sum_daily_rate);
$total_daily_rate = $result_sum_daily_rate->fetch_assoc()['total_daily_rate'];
$remaining_balance = $contractor['total_rate'] - $total_daily_rate;
?>

<?php include 'header.php'; ?>

<div class="col-lg-12">
    <div class="container-fluid">
        <h2>Manage Jobs for Contractor: <?php echo $contractor['name']; ?></h2>
        <p>Total Rate: $<?php echo $contractor['total_rate']; ?></p>
        <p>Remaining Balance: $<?php echo number_format($remaining_balance, 2); ?></p>
        
        <button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="add_job_btn"><i class="fa fa-plus"></i> Add Job</button>
        
        <!-- Jobs List -->
        <h3>Jobs</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Daily Rate</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($job = $result_jobs->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $job['date']; ?></td>
                        <td><?php echo $job['daily_rate']; ?></td>
                        
                        <td>
              <a href="#" class="btn btn-sm btn-warning edit_job" data-job-id="<?php echo $job['id']; ?>" data-job-date="<?php echo $job['date']; ?>" data-job-rate="<?php echo $job['daily_rate']; ?>"><i class="fa fa-edit"></i> Edit</a>
              <a href="#" class="btn btn-sm btn-danger delete_job"  data-job-id="<?php echo $job['id']; ?>"><i class="fa fa-trash"></i> Delete</a>
            </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for adding jobs -->
<div class="modal fade" id="addJobModal" tabindex="-1" role="dialog" aria-labelledby="addJobModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" id="save_job_form">
                <div class="modal-header">
                    <h5 class="modal-title" id="addJobModalLabel">Add Job</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="contractor_id" value="<?php echo $contractor_id; ?>">
                    <input type="hidden" name="action" value="save_job">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="daily_rate">Daily Rate</label>
                        <input type="number" class="form-control" name="daily_rate" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Job</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for editing jobs -->
<div class="modal fade" id="editJobModal" tabindex="-1" role="dialog" aria-labelledby="editJobModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" id="update_job_form">
                <div class="modal-header">
                    <h5 class="modal-title" id="editJobModalLabel">Edit Job</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="job_id" id="edit_job_id">
                    <input type="hidden" name="action" value="update_job">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" name="date" id="edit_date" required>
                    </div>
                    <div class="form-group">
                        <label for="daily_rate">Daily Rate</label>
                        <input type="number" class="form-control" name="daily_rate" id="edit_daily_rate" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Job</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$('#add_job_btn').click(function() {
    $('#addJobModal').modal('show');
});

$(document).on('click', '.edit_job', function(e){
    e.preventDefault();
    var job_id = $(this).data('job-id');
    var job_date = $(this).data('job-date');
    var job_rate = $(this).data('job-rate');

    $('#edit_job_id').val(job_id);
    $('#edit_date').val(job_date);
    $('#edit_daily_rate').val(job_rate);

    $('#editJobModal').modal('show');
});

$('#save_job_form').submit(function(e) {
    e.preventDefault();
    $.ajax({
        url: 'ajax.php?action=save_jobs',
        data: $(this).serialize(),
        method: 'POST',
        success:function(resp){
            if(resp == 1){
                alert_toast('Data successfully saved',"success");
                setTimeout(function(){
                    location.reload()
                },1500)
            } else{
                $('#msg').html('<div class="alert alert-danger">An error occurred</div>')
                end_load()
            }
        }
    });
});

$('#update_job_form').submit(function(e) {
  
    e.preventDefault();
    $.ajax({
        url: 'ajax.php?action=update_jobs',
        data: $(this).serialize(),
        method: 'POST',
        success:function(resp){
            if(resp == 1){
                alert_toast('Data successfully updated',"success");
                setTimeout(function(){
                    location.reload()
                },1500)
            } else{
                $('#msg').html('<div class="alert alert-danger">An error occurred</div>')
                end_load()
            }
        }
    });
});

$(document).on('click', '.delete_job', function(e){
  e.preventDefault(); // Prevent default behavior (following the link)
  var job_id = $(this).attr('data-job-id');
  
  // Confirmation message (optional)
  if(confirm("Are you sure you want to delete this job?")) {
    $.ajax({
      url: 'ajax.php?action=delete_jobs',
      method: 'POST',
      data: { job_id: job_id },
      success: function(response) {
        
        if(response == 1) {
          alert_toast("Job deleted successfully!", "success");
          // Reload the page to reflect changes (alternative: update table with AJAX)
          setTimeout(function(){
            location.reload();
          }, 1500);
        } else {
          alert_toast("An error occurred while deleting the job.", "error");
        }
      }
    });
  }
});
</script>

<?php $conn->close(); ?>
<?php include 'footer.php'; ?>
