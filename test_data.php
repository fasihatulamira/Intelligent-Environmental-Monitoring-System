<?php
$servername = "localhost";  // Server name or IP address
$username = "root";         // Database username
$password = "";             // Database password
$dbname = "env_monitor";    // Database name

// Create connection to MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);  // Output error message if connection fails
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

// Function to get temperature description based on value
function getTemperatureDescription($temp) {
    if ($temp >= 30) {
        return "Hot";
    } elseif ($temp >= 15) {
        return "Room";
    } else {
        return "Cold";
    }
}

// Function to get humidity description based on value
function getHumidityDescription($humidity) {
    if ($humidity >= 60) {
        return "Moist";
    } elseif ($humidity >= 30) {
        return "Optimal";
    } else {
        return "Dry";
    }
}

// Function to get distance description based on value
function getDistanceDescription($distance) {
    if ($distance >= 100) {
        return "Far";
    } elseif ($distance >= 30) {
        return "Normal";
    } else {
        return "Near";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Environmental Monitoring System</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;  /* Set body font */
            margin: 40px;                   /* Set margin */
        }
        table {
            width: 100%;                    /* Set table width */
            border-collapse: collapse;      /* Collapse table borders */
        }
        th, td {
            padding: 12px;                  /* Set padding */
            text-align: left;               /* Align text to left */
            border-bottom: 1px solid #ddd;  /* Set bottom border */
        }
        th {
            background-color: #f2f2f2;     /* Set header background color */
        }
        tr:hover {
            background-color: #f5f5f5;     /* Set hover background color */
        }
        .refresh-button {
            margin-top: 20px;               /* Set top margin for button */
            padding: 10px 20px;             /* Set padding for button */
            background-color: #4CAF50;      /* Set button background color */
            color: white;                   /* Set button text color */
            border: none;                   /* Remove button border */
            cursor: pointer;                /* Set cursor to pointer */
        }
        .refresh-button:hover {
            background-color: #45a049;      /* Set button background color on hover */
        }
        .notification {
            background-color: #f44336;      /* Set notification background color */
            color: white;                   /* Set notification text color */
            text-align: center;             /* Center align notification text */
            padding: 10px;                  /* Set padding for notification */
            margin-top: 10px;               /* Set top margin for notification */
        }
    </style>
</head>
<body>
<h1>Intelligent Environmental Monitoring System</h1>
<h2>Environmental Data</h2>

<div id="notification"></div>

<table id="envTable" class="display">
    <thead>
        <tr>
            <th>Timestamp</th>
            <th>ID</th>
            <th>Temperature (°C)</th>
            <th>Temperature Description</th>
            <th>Humidity (%)</th>
            <th>Humidity Description</th>
            <th>Distance (cm)</th>
            <th>Distance Description</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Loop through each row of sensor data and display in table
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['timestamp'] . "</td>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['temperature'] . "</td>";
            echo "<td>" . getTemperatureDescription($row['temperature']) . "</td>";
            echo "<td>" . $row['humidity'] . "</td>";
            echo "<td>" . getHumidityDescription($row['humidity']) . "</td>";
            echo "<td>" . $row['distance'] . "</td>";
            echo "<td>" . getDistanceDescription($row['distance']) . "</td>";
            echo "</tr>";

            // Check conditions for displaying notifications based on sensor data
            $notification = "";
            if (getTemperatureDescription($row['temperature']) === 'Hot') {
                $notification .= "Attention: High temperatures detected (>= 30°C). Stay hydrated and seek shade if possible. ";
            }
            if (getHumidityDescription($row['humidity']) === 'Dry') {
                $notification .= "Caution: Low humidity levels (<= 30%). Remember to moisturize and stay hydrated. ";
            }
            if (getDistanceDescription($row['distance']) === 'Near') {
                $notification .= "Alert: Nearby object detected (<= 30 cm). Maintain awareness of your surroundings.  ";
            }

            // Output JavaScript to display notification if conditions are met
            if (!empty($notification)) {
                echo '<script>';
                echo 'document.getElementById("notification").innerHTML = "<div class=\'notification\'> ' . $notification . '</div>";';
                echo '</script>';
            }
        }
        ?>
    </tbody>
</table>

<button class="refresh-button" onclick="location.reload();">Refresh</button>

<h2>Summary Data</h2>
<table>
    <thead>
        <tr>
            <th>Metric</th>
            <th>Temperature (°C)</th>
            <th>Temperature Description</th>
            <th>Humidity (%)</th>
            <th>Humidity Description</th>
            <th>Distance (cm)</th>
            <th>Distance Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Min</td>
            <td><?php echo $summary['min_temp']; ?></td>
            <td><?php echo getTemperatureDescription($summary['min_temp']); ?></td>
            <td><?php echo $summary['min_humidity']; ?></td>
            <td><?php echo getHumidityDescription($summary['min_humidity']); ?></td>
            <td><?php echo $summary['min_distance']; ?></td>
            <td><?php echo getDistanceDescription($summary['min_distance']); ?></td>
        </tr>
        <tr>
            <td>Max</td>
            <td><?php echo $summary['max_temp']; ?></td>
            <td><?php echo getTemperatureDescription($summary['max_temp']); ?></td>
            <td><?php echo $summary['max_humidity']; ?></td>
            <td><?php echo getHumidityDescription($summary['max_humidity']); ?></td>
            <td><?php echo $summary['max_distance']; ?></td>
            <td><?php echo getDistanceDescription($summary['max_distance']); ?></td>
        </tr>
        <tr>
            <td>Average</td>
            <td><?php echo round($summary['avg_temp'], 2); ?></td>
            <td><?php echo getTemperatureDescription(round($summary['avg_temp'], 2)); ?></td>
            <td><?php echo round($summary['avg_humidity'], 2); ?></td>
            <td><?php echo getHumidityDescription(round($summary['avg_humidity'], 2)); ?></td>
            <td><?php echo round($summary['avg_distance'], 2); ?></td>
            <td><?php echo getDistanceDescription(round($summary['avg_distance'], 2)); ?></td>
        </tr>
    </tbody>
</table>
<button class="refresh-button" onclick="location.reload();">Refresh</button>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>

</body>
</html>

<?php
$conn->close();  // Close database connection
?>
