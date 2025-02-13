<?php include 'db_connect.php';

if(isset($_GET['id'])){
  // Fetch contractor details and project cost
  // $qry = $conn->query("
  //     SELECT c.*, p.project_cost 
  //     FROM contractors c
  //     JOIN project_list p ON c.project_id = p.id
  //     WHERE c.id = ".$_GET['id']
  // );

  // if($qry) {
  //   $result = $qry->fetch_array();
  //   foreach($result as $k => $v){
  //       $$k = $v;
  //   }
  // } else {
  //   echo "Error fetching contractor details: " . $conn->error;
  // }

  // Calculate total material costs
  $material_cost_result = $conn->query("SELECT SUM(cost) as total_material_cost FROM task_list WHERE project_id = {$project_id}");
  echo $material_cost;
  if($material_cost_result && $material_cost_result->num_rows > 0) {
    $material_cost = $material_cost_result->fetch_array()['total_material_cost'];
  } else {
    $material_cost = 0;
  }

  // Calculate workplan costs
  $workplan_cost_result = $conn->query("SELECT SUM(cost) as workplan_cost FROM user_productivity WHERE project_id = {$project_id}");
  if($workplan_cost_result && $workplan_cost_result->num_rows > 0) {
    $workplan_cost = $workplan_cost_result->fetch_array()['workplan_cost'];
  } else {
    $workplan_cost = 0;
  }

  // Calculate total subcontractor costs
  $subcontractor_cost_result = $conn->query("SELECT SUM(rate) as total_subcontractor_cost FROM contractors WHERE project_id = {$project_id}");
  if($subcontractor_cost_result && $subcontractor_cost_result->num_rows > 0) {
    $subcontractor_cost = $subcontractor_cost_result->fetch_array()['total_subcontractor_cost'];
  } else {
    $subcontractor_cost = 0;
  }

  // Calculate remaining project budget
  $total_cost = $material_cost + $subcontractor_cost;
  $remaining_budget = $project_cost - $total_cost - $workplan_cost;

    // Fetch contractor jobs
    $jobs_result = $conn->query("SELECT * FROM jobs WHERE contractor_id = $contractor_id");
    $jobs_details = $jobs_result->fetch_all(MYSQLI_ASSOC);
  
} 

?>


<div class="col-md-12">
  <div class="card card-outline card-success">
    <div class="card-header">
      <b>Project Progress</b>
      <div class="card-tools">
        <button class="btn btn-flat btn-sm bg-gradient-success btn-success" id="print"><i class="fa fa-print"></i> Print</button>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive" id="printable">
        <table class="table m-0 table-bordered">
          <thead>
            <th>#</th>
            <th>Project</th>
            <th>Task</th>
            <th>Completed Task</th>
            <th>Work Duration</th>
            
            <th>Status</th>
            <th>Project Cost</th>
           
            <th>Subtractor Cost</th>
            <th>Work Plan Cost</th>
            <th>Material Cost</th>
            <th>Contractor Jobs</th>
         
          </thead>
          <tbody>
            <?php
            $i = 1;
            $stat = array("Pending","Started","On-Progress","On-Hold","Over Due","Done");
            $where = "";
            if($_SESSION['login_type'] == 2){
              $where = " where manager_id = '{$_SESSION['login_id']}' ";
            }elseif($_SESSION['login_type'] == 3){
              $where = " where concat('[',REPLACE(user_ids,',','],['),']') LIKE '%[{$_SESSION['login_id']}]%' ";
            }

            $qry = $conn->query("SELECT * FROM project_list $where order by name asc");
            while($row = $qry->fetch_assoc()):
              // echo '<pre>';
              // print_r($row); 
              // echo $row['project_cost'];// or var_dump($row);
              // echo '</pre>';
   
              $subcontractor_cost_result = $conn->query("SELECT SUM(total_rate) as total_subcontractor_cost FROM contractors WHERE project_id = {$row['id']}");
$subcontractor_cost = $subcontractor_cost_result->num_rows > 0 ? $subcontractor_cost_result->fetch_array()['total_subcontractor_cost'] : 0;
              
              $material_cost_result = $conn->query("SELECT SUM(cost) as total_material_cost FROM task_list WHERE project_id = {$row['id']}");
$material_cost = $material_cost_result->num_rows > 0 ? $material_cost_result->fetch_array()['total_material_cost'] : 0;
$workplan_cost= $conn->query("SELECT SUM(cost) as workplan_cost FROM user_productivity WHERE project_id = {$row['id']}");

$workplan_cost = $workplan_cost->num_rows > 0 ? $workplan_cost->fetch_array()['workplan_cost'] : 0;
         
         $project_cost = $row['project_cost'];
         $total_cost = $material_cost  + $subcontractor_cost;
        //  $remaining_budget = $project_cost - $total_cost - $workplan_cost;
         
         
              $tprog = $conn->query("SELECT * FROM task_list where project_id = {$row['id']}")->num_rows;
              $cprog = $conn->query("SELECT * FROM task_list where project_id = {$row['id']} and status = 3")->num_rows;
              $prog = $tprog > 0 ? ($cprog/$tprog) * 100 : 0;
              $prog = $prog > 0 ? number_format($prog, 2) : $prog;
              $workplan = $conn->query("SELECT * FROM user_productivity where project_id = {$row['id']}")->num_rows;
              $prod = $conn->query("SELECT * FROM user_productivity where project_id = {$row['id']}")->num_rows;

              if($row['status'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['start_date'])){
                if($prod > 0 || $cprog > 0)
                  $row['status'] = 2;
                else
                  $row['status'] = 1;
              } elseif($row['status'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['end_date'])){
                $row['status'] = 4;
              }
            ?>
            <tr>
              <td><?php echo $i++ ?></td>
              <td><a><?php echo ucwords($row['name']) ?></a><br><small>Due: <?php echo date("Y-m-d", strtotime($row['end_date'])) ?></small></td>
              <td class="text-center"><?php echo number_format($tprog) ?></td>
              <td class="text-center"><?php echo number_format($cprog) ?></td>
              <td class="text-center"><?php echo $workplan ?></td>
             
              <td class="project-state">
                <?php
                $status_badge = "";
                if ($stat[$row['status']] == 'Pending') $status_badge = 'secondary';
                elseif ($stat[$row['status']] == 'Started') $status_badge = 'primary';
                elseif ($stat[$row['status']] == 'On-Progress') $status_badge = 'info';
                elseif ($stat[$row['status']] == 'On-Hold') $status_badge = 'warning';
                elseif ($stat[$row['status']] == 'Over Due') $status_badge = 'danger';
                elseif ($stat[$row['status']] == 'Done') $status_badge = 'success';
                echo "<span class='badge badge-{$status_badge}'>{$stat[$row['status']]}</span>";
                ?>
              </td>
              <td class="text-center"><?php echo $project_cost ?></td>
              <!-- <td class="text-center"><?php echo number_format($remaining_budget ?? 0) ?></td> -->
              
              <td class="text-center"><?php echo number_format($subcontractor_cost ?? 0) ?></td>
              <td class="text-center"><?php echo $workplan_cost ?></td>
              
              <td class="text-center"><?php echo $material_cost ?></td>
              
              <td class="text-center">
                <?php
                // Fetch contractors for the project
                $contractors_result = $conn->query("SELECT * FROM contractors WHERE project_id = {$row['id']}");
                while ($contractor = $contractors_result->fetch_assoc()) {
                  echo "<b>Contractor: {$contractor['name']}</b><br>";
                  // Fetch jobs for each contractor
                  $jobs_result = $conn->query("SELECT * FROM jobs WHERE contractor_id = {$contractor['id']}");
                  while ($job = $jobs_result->fetch_assoc()) {
                    echo "<a href='job_details.php?id={$job['id']}'>Daily Rate: {$job['daily_rate']}</a><br>";
                    echo "<a href='job_details.php?id={$job['id']}'>Date: ". date("Y-m-d", strtotime($job['date'])) ."</a><br>";
                  }
                }
                ?>
              </td>
              
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
$('#print').click(function(){
  start_load()
  var _h = $('head').clone()
  var _p = $('#printable').clone()
  var _d = "<p class='text-center'><b>Project Progress Report as of (<?php echo date("F d, Y") ?>)</b></p>"
  _p.prepend(_d)
  _p.prepend(_h)
  var nw = window.open("","","width=900,height=600")
  nw.document.write(_p.html())
  nw.document.close()
  nw.print()
  setTimeout(function(){
    nw.close()
    end_load()
  },750)
})
</script>

