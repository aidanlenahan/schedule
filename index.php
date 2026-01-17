<?php

include 'dbconnect.php';
//quote implementation
include 'quotes.php';
$randomQuote = $quotes[array_rand($quotes)];

// Allow date navigation via URL parameter
if (isset($_GET['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date'])) {
    $today = $_GET['date'];
} else {
    $today = date("Y-m-d");
}

// Calculate previous and next dates
$yesterday = date("Y-m-d", strtotime($today . ' -1 day'));
$tomorrow = date("Y-m-d", strtotime($today . ' +1 day'));

// Check if viewing today and format the display date
$isToday = ($today === date("Y-m-d"));
$displayDate = date("F jS, Y", strtotime($today));

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
            cursor: pointer;
            transition: opacity 0.2s ease;
        }
        .schedule-type:hover {
            opacity: 0.7;
        }
        .date-link {
            color: #701e20;
            text-decoration: none;
            transition: opacity 0.2s ease;
        }
        .date-link:hover {
            opacity: 0.7;
            text-decoration: underline;
        }
        body.dark-mode .date-link {
            color: #c94c4c;
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.2s ease;
        }
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease;
        }
        body.dark-mode .modal-content {
            background-color: #1e1e1e;
            color: #eee;
        }
        .modal-header {
            font-size: 1.5rem;
            font-weight: bold;
            color: #701e20;
            margin-bottom: 20px;
            text-align: center;
        }
        body.dark-mode .modal-header {
            color: #c94c4c;
        }
        .modal-body {
            margin-bottom: 20px;
        }
        .modal-body label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #701e20;
        }
        body.dark-mode .modal-body label {
            color: #c94c4c;
        }
        .modal-body input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #701e20;
            border-radius: 5px;
            font-size: 1rem;
            background-color: #fff;
            color: #222;
        }
        body.dark-mode .modal-body input[type="date"] {
            background-color: #2a2a2a;
            color: #eee;
            border-color: #c94c4c;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .modal-button {
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .modal-button.primary {
            background-color: #701e20;
            color: #fff;
        }
        .modal-button.primary:hover {
            background-color: #8b2426;
        }
        body.dark-mode .modal-button.primary {
            background-color: #c94c4c;
        }
        body.dark-mode .modal-button.primary:hover {
            background-color: #d65e5e;
        }
        .modal-button.secondary {
            background-color: #ccc;
            color: #222;
        }
        .modal-button.secondary:hover {
            background-color: #bbb;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
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
        .image-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }
        .image-container img {
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .image-container img:hover {
            transform: scale(1.05);
        }
        .nav-arrow {
            font-size: 2rem;
            color: #701e20;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.2s ease;
            user-select: none;
            font-weight: bold;
        }
        .nav-arrow:hover {
            background-color: rgba(112, 30, 32, 0.1);
            transform: scale(1.1);
        }
        body.dark-mode .nav-arrow {
            color: #c94c4c;
        }
        body.dark-mode .nav-arrow:hover {
            background-color: rgba(201, 76, 76, 0.2);
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

        // Date picker modal functionality
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('dateModal');
            const scheduleType = document.querySelector('.schedule-type');
            const closeBtn = document.getElementById('closeModal');
            const goBtn = document.getElementById('goToDate');
            const dateInput = document.getElementById('dateInput');

            if (scheduleType) {
                scheduleType.addEventListener('click', () => {
                    modal.style.display = 'block';
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    modal.style.display = 'none';
                });
            }

            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });

            if (goBtn && dateInput) {
                goBtn.addEventListener('click', () => {
                    const selectedDate = dateInput.value;
                    if (selectedDate) {
                        window.location.href = '?date=' + selectedDate;
                    }
                });

                dateInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        goBtn.click();
                    }
                });
            }
        });

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
    <!-- Date Picker Modal -->
    <div id="dateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Go to Date</div>
            <div class="modal-body">
                <label for="dateInput">Select a date:</label>
                <input type="date" id="dateInput" value="<?php echo $today; ?>">
            </div>
            <div class="modal-buttons">
                <button class="modal-button primary" id="goToDate">Go</button>
                <button class="modal-button secondary" id="closeModal">Cancel</button>
            </div>
        </div>
    </div>

    <div class="schedule-container">
        <div class="top-bar">
            <div class="date">
                <?php if (!$isToday): ?>
                    <a href="index.php" class="date-link"><?php echo date("l, F jS, Y"); ?></a>
                <?php else: ?>
                    <?php echo date("l, F jS, Y"); ?>
                <?php endif; ?>
            </div>
            <div id="time"></div>
        </div>


        <div class="image-container">
            <a href="?date=<?php echo $yesterday; ?>" class="nav-arrow">&lt;</a>
            <img src="buc.svg" alt="BUC">
            <a href="?date=<?php echo $tomorrow; ?>" class="nav-arrow">&gt;</a>
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
                    echo '<div class="schedule-type">Full Day Schedule - ' . $displayDate . '</div>';
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
                    echo '<div class="schedule-type">Half Day Schedule - ' . $displayDate . '</div>';
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
                    echo '<div class="schedule-type">2-Hour Delay Schedule - ' . $displayDate . '</div>';
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
                    echo '<div class="schedule-type">Testing Schedule - ' . $displayDate . '</div>';
                    echo '<table>';
                        echo '<tr><th>Block</th><th>Period</th><th>Time</th></tr>';
                        echo '<tr data-time="' . $rowTimes["block1"] . '"><td>Block 1</td><td>Testing Block 1</td><td>' . $rowTimes["block1"] .'</td></tr>';
                        echo '<tr data-time="' . $rowTimes["block2"] . '"><td>Block 2</td><td>Testing Block 2</td><td>' . $rowTimes["block2"] .'</td></tr>';
                    echo '</table>';
                }
                else {
                    echo '<div class="schedule-type">No schedule for ' . $displayDate . '</div>';
                }
            }
        }
        else {
            echo '<div class="schedule-type">No schedule found for ' . $displayDate . '</div>';
        }

        $conn->close();
        ?>
    </div>

    <div class="footer">
        &copy; <?php echo date("Y"); ?> Aidan Lenahan - RBR Schedule
    </div>
</body>
</html>
