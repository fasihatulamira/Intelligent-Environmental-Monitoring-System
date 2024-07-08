<?php
$servername = "localhost";  // Server name or IP address
$username = "root";         // Database username
$password = "";             // Database password
$dbname = "env_monitor";    // Database name

// Include test_data.php for function definitions
include 'test_data.php';

// Create connection to MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);  // Output error message if connection fails
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $temperature = $_POST['temperature'];   // Get temperature value from POST data
    $humidity = $_POST['humidity'];         // Get humidity value from POST data
    $distance = $_POST['distance'];         // Get distance value from POST data
    
    // SQL query to insert sensor data into database
    $sql = "INSERT INTO sensor_data (temperature, humidity, distance) VALUES ('$temperature', '$humidity', '$distance')";
    
    // Execute SQL query and check for success or failure
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";  // Output success message
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;  // Output error message if query fails
    }
}

// SQL query to fetch latest 20 records from sensor_data table
$sql = "SELECT id, temperature, humidity, distance, timestamp FROM sensor_data ORDER BY timestamp DESC LIMIT 20";
$result = $conn->query($sql);

// SQL query to fetch summary data (min, max, avg) from sensor_data table
$summary_sql = "SELECT MIN(temperature) as min_temp, MAX(temperature) as max_temp, AVG(temperature) as avg_temp, 
                MIN(humidity) as min_humidity, MAX(humidity) as max_humidity, AVG(humidity) as avg_humidity,
                MIN(distance) as min_distance, MAX(distance) as max_distance, AVG(distance) as avg_distance
                FROM sensor_data";
$summary_result = $conn->query($summary_sql);
$summary = $summary_result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Environmental Data</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <style>
        body {
            font-family: Arial, sans-serif;   /* Set body font */
            margin: 40px;                     /* Set margin */
        }
        table {
            width: 100%;                      /* Set table width */
            border-collapse: collapse;        /* Collapse table borders */
            margin-bottom: 20px;              /* Set bottom margin */
        }
        th, td {
            padding: 12px;                    /* Set padding */
            text-align: left;                 /* Align text to left */
            border-bottom: 1px solid #ddd;    /* Set bottom border */
        }
        th {
            background-color: #f2f2f2;       /* Set header background color */
        }
        tr:hover {
            background-color: #f5f5f5;       /* Set hover background color */
        }
        .refresh-button {
            margin-top: 20px;                 /* Set top margin for button */
            padding: 10px 20px;               /* Set padding for button */
            background-color: #4CAF50;        /* Set button background color */
            color: white;                     /* Set button text color */
            border: none;                     /* Remove button border */
            cursor: pointer;                  /* Set cursor to pointer */
        }
        .refresh-button:hover {
            background-color: #45a049;        /* Set button background color on hover */
        }
        .chart-container {
            width: 600px;                     /* Set chart container width */
            margin: auto;                     /* Center align chart container */
        }
    </style>
</head>
<body>

<h2>Environmental Analysis</h2>

<!-- Temperature Chart -->
<div class="chart-container">
    <canvas id="temperatureChart"></canvas>
</div>

<!-- Humidity Chart -->
<div class="chart-container">
    <canvas id="humidityChart"></canvas>
</div>

<!-- Distance Chart -->
<div class="chart-container">
    <canvas id="distanceChart"></canvas>
</div>

<!-- Include jQuery and DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>

<!-- Include Chart.js for charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready( function () {
        $('#envTable').DataTable();  // Initialize DataTable for sensor data table

        // Arrays to store data for charts
        var temperatures = [];
        var humidities = [];
        var distances = [];

        <?php
        // SQL query to fetch latest 20 records for charts
        $chart_data_sql = "SELECT temperature, humidity, distance FROM sensor_data ORDER BY timestamp DESC LIMIT 20";
        $chart_data_result = $conn->query($chart_data_sql);
        while($row = $chart_data_result->fetch_assoc()) {
            echo "temperatures.push(" . $row['temperature'] . ");";  // Push temperature data to array
            echo "humidities.push(" . $row['humidity'] . ");";        // Push humidity data to array
            echo "distances.push(" . $row['distance'] . ");";          // Push distance data to array
        }
        ?>

        // Temperature Chart
        var ctxTemp = document.getElementById('temperatureChart').getContext('2d');
        var tempChart = new Chart(ctxTemp, {
            type: 'line',
            data: {
                labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20'],
                datasets: [{
                    label: 'Temperature (°C)',
                    data: temperatures,
                    fill: false,
                    borderColor: 'rgb(255, 99, 132)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Temperature Chart'
                    }
                }
            }
        });

        // Humidity Chart
        var ctxHumidity = document.getElementById('humidityChart').getContext('2d');
        var humidityChart = new Chart(ctxHumidity, {
            type: 'line',
            data: {
                labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20'],
                datasets: [{
                    label: 'Humidity (%)',
                    data: humidities,
                    fill: false,
                    borderColor: 'rgb(54, 162, 235)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Humidity Chart'
                    }
                }
            }
        });

        // Distance Chart
        var ctxDistance = document.getElementById('distanceChart').getContext('2d');
        var distanceChart = new Chart(ctxDistance, {
            type: 'line',
            data: {
                labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20'],
                datasets: [{
                    label: 'Distance (cm)',
                    data: distances,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Distance Chart'
                    }
                }
            }
        });
    });
</script>

</body>
</html>

<?php
$conn->close();  // Close database connection
?>
