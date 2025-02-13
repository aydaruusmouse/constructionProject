<?php

if(!isset($conn)){ include 'db_connect.php'; }
// Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contractor_id = $_POST['contractor_id'];
    $daily_rate = $_POST['daily_rate'];
    $date = $_POST['date'];

    // Update the remaining balance
    $sql_update_balance = "UPDATE contractors SET remaining_balance = remaining_balance - $daily_rate WHERE id = $contractor_id";
    $conn->query($sql_update_balance);

    // Insert the new job
    $sql_insert_job = "INSERT INTO jobs (contractor_id, date, daily_rate) VALUES ($contractor_id, '$date', $daily_rate)";
    $conn->query($sql_insert_job);
}

// Fetch contractor details
$sql_contractors = "SELECT * FROM contractors";
$result_contractors = $conn->query($sql_contractors);

while($row = $result_contractors->fetch_assoc()) {
    $contractor_id = $row['id'];
    $name = $row['name'];
    $total_rate = $row['total_rate'];
    $remaining_balance = $row['remaining_balance'];

    echo "<h2>Contractor: $name</h2>";
    echo "<p>Total Rate: $$total_rate</p>";
    echo "<p>Remaining Balance: $$remaining_balance</p>";

    // Fetch jobs for the contractor
    $sql_jobs = "SELECT * FROM jobs WHERE contractor_id = $contractor_id";
    $result_jobs = $conn->query($sql_jobs);

    echo "<table border='1'>
            <tr>
                <th>Date</th>
                <th>Daily Rate</th>
            </tr>";

    while($job = $result_jobs->fetch_assoc()) {
        echo "<tr>
                <td>{$job['date']}</td>
                <td>{$job['daily_rate']}</td>
              </tr>";
    }

    echo "</table>";

    // Form to add a new job
    echo "<form method='post'>
            <input type='hidden' name='contractor_id' value='$contractor_id'>
            <label for='date'>Date:</label>
            <input type='date' name='date' required>
            <label for='daily_rate'>Daily Rate:</label>
            <input type='number' name='daily_rate' step='0.01' required>
            <button type='submit'>Add Job</button>
          </form>";
}

$conn->close();
?>
