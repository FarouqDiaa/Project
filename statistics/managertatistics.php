<?php
include '../tools/connection.php';

$ageQuery = "SELECT position, AVG(age) AS avg_age FROM staff GROUP BY position";
$ageResult = $conn->query($ageQuery);

$countQuery = "SELECT position, count(position) AS count_pos FROM staff GROUP BY position";
$countResult = $conn->query($countQuery);

$salaryQuery = "SELECT position, AVG(salary) AS av_sal FROM staff GROUP BY position";
$salaryResult = $conn->query($salaryQuery);

$genderQuery = "SELECT gender, count(gender) AS count_gender FROM guest GROUP BY gender";
$genderResult = $conn->query($genderQuery);

$roomserviceQuery = "SELECT is_finished, COUNT(*) AS count FROM roomserviceorder GROUP BY is_finished;";
$roomserviceResult = $conn->query($roomserviceQuery);






?>

<!DOCTYPE html>
<html lang="en">

<head>

    <title>Statistics</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../icons/fontawesome/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Lato:700%7CMontserrat:400,600" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/style.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Statistics</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .navigation {
            margin-top: 30px;
        }

        body {
            background-color: black;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            position: relative;
        }

        .statistics-container {
            background-color: #333;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 1200px;
        }


        .chart-pair {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            width: 70vw;
        }

        .chart-container {
            width: 400px;
            border: 1px solid #555;
            padding: 10px;
            border-radius: 5px;
        }

        canvas {
            width: 100%;
            max-width: 400px;
            height: auto;
        }

        th {
            color: white !important;
        }
    </style>
</head>

<body style="background-color: rgb(5,5,5);">
    <?php include '../tools/navbar.php'; ?>
    <br>
    <div class="container text-center">
        <div class="statistics-container">
            <h2 style="color: white;">Statistics</h2>


            <div class="chart-pair">
                <div class="chart-container">
                    <canvas id="ageChart" width="400" height="200"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="countChart" width="400" height="200"></canvas>
                </div>
            </div>
            <br><br>
            <div class="chart-pair">
                <div class="chart-container">
                    <canvas id="salaryChart" width="400" height="200"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="genderChart" width="400" height="200"></canvas>
                </div>
            </div>
            <br><br>
            <div class="chart-pair">
                <div class="chart-container">
                    <canvas id="requestsChart" width="400" height="200"></canvas>
                </div>
                <div class="chart-container">
                    <h3 style="color:white;">Select Guest ID:</h3>
                    <br>
                    <select id="guestIdComboBox" class="form-select">
                        <?php
                        $guestIdQuery = "SELECT DISTINCT guest_ID FROM booking";
                        $guestIdResult = $conn->query($guestIdQuery);
                        while ($row = $guestIdResult->fetch_assoc()) {
                            echo "<option value='" . $row['guest_ID'] . "'>" . $row['guest_ID'] . "</option>";
                        }
                        ?>
                    </select>
                    <button class="btn btn-danger p-2 m-4" onclick="loadGuestBookings()">Load Bookings</button>
                    <table class="table table-bordered" id="guestBookingsTable">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Staff ID</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <div class="chart-container">
                <br>
                <h3 style="color:white;">Select Staff Position:</h3>
                <br>
                <select id="positionComboBox" class="form-select">
                    <?php
                    $positionQuery = "SELECT DISTINCT position FROM staff";
                    $positionResult = $conn->query($positionQuery);
                    while ($row = $positionResult->fetch_assoc()) {
                        echo "<option value='" . $row['position'] . "'>" . $row['position'] . "</option>";
                    }
                    ?>
                </select>
                <button class="btn btn-danger p-2 m-4" onclick="loadStaffSalaries()">Load Salaries</button>
                <table class="table table-bordered" id="staffSalariesTable">
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th>Total Salary</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <script>
                var ageData = <?php echo json_encode($ageResult->fetch_all(MYSQLI_ASSOC)); ?>;
                var ageLabels = ageData.map(entry => entry.position);
                var ageValues = ageData.map(entry => entry.avg_age);

                var countData = <?php echo json_encode($countResult->fetch_all(MYSQLI_ASSOC)); ?>;
                var countLabels = countData.map(entry => entry.position);
                var countValues = countData.map(entry => entry.count_pos);

                var salaryData = <?php echo json_encode($salaryResult->fetch_all(MYSQLI_ASSOC)); ?>;
                var salaryLabels = salaryData.map(entry => entry.position);
                var salaryValues = salaryData.map(entry => entry.av_sal);

                var genderData = <?php echo json_encode($genderResult->fetch_all(MYSQLI_ASSOC)); ?>;
                var genderLabels = genderData.map(entry => entry.gender);
                var genderValues = genderData.map(entry => entry.count_gender);

                var requestsData = <?php echo json_encode($roomserviceResult->fetch_all(MYSQLI_ASSOC)); ?>;
                var requestsLabels = requestsData.map(entry => entry.is_finished == 1 ? 'Finished' : 'Not Finished');
                var requestsValues = requestsData.map(entry => entry.count);
                function createBarChart(ctx, label, data, backgroundColor, borderColor) {
                    return new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: label,
                                data: data.values,
                                backgroundColor: backgroundColor,
                                borderColor: borderColor,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                }
                            }
                        }
                    });
                }

                function createLineChart(ctx, label, data, borderColor) {
                    return new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: label,
                                data: data.values,
                                borderColor: borderColor,
                                borderWidth: 2,
                                backgroundColor: 'rgba(255, 206, 86, 1)',
                                pointBackgroundColor: 'rgba(255, 206, 86, 1)',
                                fill: false
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                function createPieChart(ctx, label, data, backgroundColor, titleText) {
                    return new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: label,
                                data: data.values,
                                backgroundColor: backgroundColor,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                title: {
                                    display: true,
                                    text: titleText,
                                    font: {
                                        size: 16
                                    }
                                }
                            }
                        }
                    });
                }

                var ageCtx = document.getElementById('ageChart').getContext('2d');
                createBarChart(ageCtx, 'Average Age', { labels: ageData.map(entry => entry.position), values: ageData.map(entry => entry.avg_age) }, 'rgba(75, 192, 192, 0.2)', 'rgba(75, 192, 192, 1)');

                var countCtx = document.getElementById('countChart').getContext('2d');
                createBarChart(countCtx, 'Count', { labels: countData.map(entry => entry.position), values: countData.map(entry => entry.count_pos) }, 'rgba(255, 99, 132, 0.8)', 'rgba(255, 99, 132, 1)');

                var salaryCtx = document.getElementById('salaryChart').getContext('2d');
                createLineChart(salaryCtx, 'Average Salary', { labels: salaryData.map(entry => entry.position), values: salaryData.map(entry => entry.av_sal) }, 'rgba(255, 206, 86, 1)');

                var genderCtx = document.getElementById('genderChart').getContext('2d');
                createPieChart(genderCtx, 'Gender Distribution', { labels: genderData.map(entry => entry.gender), values: genderData.map(entry => entry.count_gender) }, ['#ff6384', '#36a2eb'], 'Gender Distribution of Guests');

                var requestsCtx = document.getElementById('requestsChart').getContext('2d');
                createPieChart(requestsCtx, 'Requests Status', { labels: requestsData.map(entry => entry.is_finished == 1 ? 'Finished' : 'Not Finished'), values: requestsData.map(entry => entry.count) }, ['#4caf50', '#f44336'], 'Room Service Status');
                function loadGuestBookings() {
                    var guestId = document.getElementById('guestIdComboBox').value;
                    var tableBody = document.getElementById('guestBookingsTable').getElementsByTagName('tbody')[0];

                    $.ajax({
                        type: 'POST',
                        url: 'fetch_guest_bookings.php',
                        data: { guestId: guestId },
                        success: function (response) {
                            var bookingsData = JSON.parse(response);
                            tableBody.innerHTML = '';

                            bookingsData.forEach(function (booking) {
                                var newRow = tableBody.insertRow();
                                var cell1 = newRow.insertCell(0);
                                var cell2 = newRow.insertCell(1);

                                cell1.innerHTML = booking['Booking_ID'];
                                if (!booking['staff_ID']) {
                                    booking['staff_ID'] = 'None';
                                }
                                cell2.innerHTML = booking['staff_ID'];
                                cell1.style.color = 'white';
                                cell2.style.color = 'white';
                            });
                        }
                    });
                }
                function loadStaffSalaries() {
                    var position = document.getElementById('positionComboBox').value;
                    var staffSalariesTableBody = document.getElementById('staffSalariesTable').getElementsByTagName('tbody')[0];

                    $.ajax({
                        type: 'POST',
                        url: 'fetch_staff_salaries.php',
                        data: { position: position },
                        success: function (response) {
                            var staffSalariesData = JSON.parse(response);
                            staffSalariesTableBody.innerHTML = '';

                            staffSalariesData.forEach(function (staff) {
                                var newRow = staffSalariesTableBody.insertRow();
                                var cell1 = newRow.insertCell(0);
                                var cell2 = newRow.insertCell(1);

                                cell1.innerHTML = staff['position'];
                                cell2.innerHTML = staff['total_salary'];

                                cell1.style.color = 'white';
                                cell2.style.color = 'white';
                            });
                        }
                    });
                }
            </script>
        </div>
    </div>
</body>

</html>