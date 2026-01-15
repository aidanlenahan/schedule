<?php

include 'dbconnect.php';
//quote implementation
include 'quotes.php';
$randomQuote = $quotes[array_rand($quotes)];


$today = date("Y-m-d");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="3600">
    <title>Daily Schedule</title>
    <style>
        body {
            background-color: #f8f8f8;
            color: #222;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .schedule-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            width: 80%;
            max-width: 900px;
            margin: 0;
            padding: 15px 30px 40px 30px;
            text-align: center;
            position: relative;
        }

        .top-bar {
            display: flex;
            justify-content: space-between; /* space between date, quote, time */
            align-items: center; /* vertical alignment */
            color: #701e20;
            font-weight: 600;
            font-size: 1rem;
            margin: 0 0 10px 0;
            gap: 15px; /* optional spacing between items */
        }

        .image-container {
            width: 10%;
            min-width: 75px;
            margin: 0 auto 10px auto; /* reduce bottom margin to shrink gap */
        }

        .quote {
            font-style: italic;
            font-weight: normal;
            color: #444;
            text-align: center;
            margin-bottom: 10px; /* controls spacing between quote and schedule type */
        }


        .image-container img {
            width: 100%;
            border-radius: 8px;
            border: 3px solid #701e20;
            object-fit: cover;
        }

        .schedule-type {
            font-size: 1.3rem;
            font-weight: bold;
            color: #701e20;
            margin: 5px 0 15px 0;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        th {
            background-color: #701e20;
            color: #fff;
            font-weight: 600;
        }

        th, td {
            border: 1px solid #701e20;
            padding: 10px 14px;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .footer {
            color: #555;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        .active-row {
            font-weight: bold;
            background-color: #ffe3e3;
            transition: background-color 0.3s ease;
            color: #000 !important;
        }
        /* Dark mode styles */
        body.dark-mode {
            background-color: #121212;
            color: #eee;
        }

        body.dark-mode .schedule-container {
            background: #1e1e1e;
            color: #eee;
            box-shadow: 0 4px 16px rgba(255,255,255,0.1);
        }

        body.dark-mode th {
            background-color: #c94c4c;
        }
        /* Dark mode active row */
        body.dark-mode .active-row {
            background-color: #3b3b1f !important; /* muted olive tone for visibility */
            color: #fff !important;
        }
        body.dark-mode th, 
        body.dark-mode td {
            border-color: #c94c4c;
        }

        body.dark-mode tr:nth-child(even) {
            background-color: #2a2a2a;
        }

        body.dark-mode .quote {
            color: #ccc;
        }

        body.dark-mode .footer {
            color: #aaa;
        }
        /* image clickable cursor hint */
        .image-container img {
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .image-container img:hover {
            transform: scale(1.05);
        }



    </style>
    <link rel="icon" href="buc.svg" type="image/svg+xml">
    <script>
        function updateTime() {
            const now = new Date();
            const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
            document.getElementById('time').textContent = now.toLocaleTimeString([], options);
        }
        setInterval(updateTime, 1000);
        window.onload = updateTime;

        // highlighting functionality
        function parseTimeRange(rangeStr) {
            const [startStr, endStr] = rangeStr.split('-').map(s => s.trim());

            function toMinutes(str) {
                const [time, modifier] = str.split(' ');
                let [hours, minutes] = time.split(':').map(Number);
                if (modifier === 'PM' && hours !== 12) hours += 12;
                if (modifier === 'AM' && hours === 12) hours = 0;
                return hours * 60 + minutes;
            }

            const start = toMinutes(startStr);
            const end = toMinutes(endStr);
            return [start, end];
        }

        function highlightActiveRow() {
            const now = new Date();
            const currentMinutes = now.getHours() * 60 + now.getMinutes();

            let found = false;
            document.querySelectorAll('tr[data-time]').forEach(row => {
                const [start, end] = parseTimeRange(row.dataset.time);
                if (currentMinutes >= start && currentMinutes < end) {
                    row.classList.add('active-row');
                    found = true;
                } else {
                    row.classList.remove('active-row');
                }
            });
        }

        setInterval(highlightActiveRow, 30000);
        window.onload = () => {
            updateTime();
            highlightActiveRow();
        };
        // dark mode toggle
        document.addEventListener('DOMContentLoaded', () => {
            const body = document.body;
            const img = document.querySelector('.image-container img');

            // Load saved mode from localStorage
            if (localStorage.getItem('theme') === 'dark') {
                body.classList.add('dark-mode');
            }

            img.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                if (body.classList.contains('dark-mode')) {
                    localStorage.setItem('theme', 'dark');
                } else {
                    localStorage.setItem('theme', 'light');
                }
            });
        });

    </script>
</head>
<body>
    <div class="schedule-container">
        <div class="top-bar">
            <div class="date"><?php echo date("l, F jS, Y"); ?></div>
            <div id="time"></div>
        </div>


        <div class="image-container">
            <img src="buc.svg" alt="BUC">
        </div>

        <div class="quote"><?php echo $randomQuote; ?></div>

        <?php
        $sql = "SELECT * FROM rotatingday WHERE date ='$today'";
        $result = $conn->query($sql);

        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $sql = "SELECT * FROM schedule WHERE daytype ='" .$row["times"]."'";
                $resultDay = $conn->query($sql);
                $rowTimes = $resultDay->fetch_assoc();

                $sql = "SELECT * FROM rotations WHERE day ='" .$row["day"] . "'";
                $rotations = $conn->query($sql);
                $day = $rotations->fetch_assoc();

                if ($row["times"] == "fullday") {
                    echo '<div class="schedule-type">Full Day Schedule</div>';
                    echo '<table>';
                        echo '<tr><th>Block</th><th>Period</th><th>Time</th></tr>';
                        echo '<tr data-time="' . $rowTimes["block1"] . '"><td>Block 1</td><td>Period ' . $day["block1"] . '</td><td>' . $rowTimes["block1"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block2"] . '"><td>Block 2</td><td>Period ' . $day["block2"] . '</td><td>' . $rowTimes["block2"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block3"] . '"><td>Block 3</td><td>Period ' . $day["block3"] . '</td><td>' . $rowTimes["block3"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["mod1"] . '"><td>Mod 1</td><td>Lunch/Study</td><td>' . $rowTimes["mod1"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["mod2"] . '"><td>Mod 2</td><td>Lunch/Study</td><td>' . $rowTimes["mod2"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block4"] . '"><td>Block 4</td><td>Period ' . $day["block4"] . '</td><td>' . $rowTimes["block4"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block5"] . '"><td>Block 5</td><td>Period ' . $day["block5"] . '</td><td>' . $rowTimes["block5"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block6"] . '"><td>Block 6</td><td>Period ' . $day["block6"] . '</td><td>' . $rowTimes["block6"] .'</td></tr>';
                    echo '</table>';
                }
                elseif($row["times"] == "halfday") {
                    echo '<div class="schedule-type">Half Day Schedule</div>';
                    echo '<table>';
                        echo '<tr><th>Block</th><th>Period</th><th>Time</th></tr>';
                        echo '<tr data-time="' . $rowTimes["block1"] . '"><td>Block 1</td><td>Period ' . $day["block1"] . '</td><td>' . $rowTimes["block1"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block2"] . '"><td>Block 2</td><td>Period ' . $day["block2"] . '</td><td>' . $rowTimes["block2"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block3"] . '"><td>Block 3</td><td>Period ' . $day["block3"] . '</td><td>' . $rowTimes["block3"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block4"] . '"><td>Block 4</td><td>Period ' . $day["block4"] . '</td><td>' . $rowTimes["block4"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block5"] . '"><td>Block 5</td><td>Period ' . $day["block5"] . '</td><td>' . $rowTimes["block5"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block6"] . '"><td>Block 6</td><td>Period ' . $day["block6"] . '</td><td>' . $rowTimes["block6"] .'</td></tr>';
                    echo '</table>';
                }
                elseif($row["times"] == "2hr") {
                    echo '<div class="schedule-type">2-Hour Delay Schedule</div>';
                    echo '<table>';
                        echo '<tr><th>Block</th><th>Period</th><th>Time</th></tr>';
                        echo '<tr data-time="' . $rowTimes["block1"] . '"><td>Block 1</td><td>Period ' . $day["block1"] . '</td><td>' . $rowTimes["block1"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block2"] . '"><td>Block 2</td><td>Period ' . $day["block2"] . '</td><td>' . $rowTimes["block2"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block3"] . '"><td>Block 3</td><td>Period ' . $day["block3"] . '</td><td>' . $rowTimes["block3"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block1"] . '"><td>Mod 1</td><td>Lunch/Study</td><td>' . $rowTimes["mod1"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block2"] . '"><td>Mod 2</td><td>Lunch/Study</td><td>' . $rowTimes["mod2"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block4"] . '"><td>Block 4</td><td>Period ' . $day["block4"] . '</td><td>' . $rowTimes["block4"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block5"] . '"><td>Block 5</td><td>Period ' . $day["block5"] . '</td><td>' . $rowTimes["block5"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block6"] . '"><td>Block 6</td><td>Period ' . $day["block6"] . '</td><td>' . $rowTimes["block6"] .'</td></tr>';
                    echo '</table>';
                }
                elseif($row["times"] == "testing") {
                    echo '<div class="schedule-type">Testing Schedule</div>';
                    echo '<table>';
                        echo '<tr><th>Block</th><th>Period</th><th>Time</th></tr>';
                        echo '<tr data-time="' . $rowTimes["block1"] . '"><td>Block 1</td><td>Testing Block 1</td><td>' . $rowTimes["block1"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block2"] . '"><td>Block 2</td><td>Testing Block 2</td><td>' . $rowTimes["block2"] .'</td></tr>';
                    echo '</table>';
                }
                else {
                    echo '<div class="schedule-type">No schedule for ' . $today . '</div>';
                }
            }
        }
        else {
            echo '<div class="schedule-type">No schedule found for ' . $today . '</div>';
        }

        $conn->close();
        ?>
    </div>

    <div class="footer">
        &copy; <?php echo date("Y"); ?> Aidan Lenahan - RBR Schedule
    </div>
</body>
</html>
