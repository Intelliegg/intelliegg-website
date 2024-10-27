<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'customer'){
  header('location: ../index.html');
}
require_once '../classes/database.php';

$db = new Database();
$pdo = $db->connect();
if (isset($_GET['incubatorNo'])) {
    $_SESSION['incubatorNo'] = $_GET['incubatorNo'];
}

function getEvents($incubatorNo, $pdo) {
    $events = [];
    $sql = "SELECT start_date, end_date FROM calendar WHERE incubatorNo = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$incubatorNo]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        $lastDay = new DateTime($row['end_date']);
        $lastDay->modify('+1 day');

        $events[] = [
            'title' => '', // No title for the main event
            'start' => $row['start_date'],
            'end' => $lastDay->format('Y-m-d'),
            'backgroundColor' => 'black', // Semi-transparent white
            'borderColor' => 'white',
            'allDay' => true,
            'display' => 'background' // This will create the highlight effect
        ];

        $events[] = [
            'title' => 'First day of Incubating',
            'start' => $row['start_date'],
            'allDay' => true,
            'display' => 'block'
        ];

        // Add event for the last day
        $events[] = [
            'title' => 'Last day of Incubation',
            'start' => $row['end_date'],
            'allDay' => true,
            'display' => 'block'
        ];
    }
    return $events;
}

function checkIncubation($incubatorNo, $date, $pdo) {
    $sql = "SELECT * FROM calendar
            WHERE incubatorNo = ?
            AND ? BETWEEN start_date AND end_date";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$incubatorNo, $date]);
    $existingPeriod = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingPeriod) {
        return ['exists' => true, 'type' => 'current', 'start' => $existingPeriod['start_date'], 'end' => $existingPeriod['end_date']];
    }

    $endDate = date('Y-m-d', strtotime($date . '+21 days'));
    $sql = "SELECT * FROM calendar
            WHERE incubatorNo = ?
            AND start_date <= ?
            AND end_date >= ?
            ORDER BY start_date ASC
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$incubatorNo, $endDate, $date]);
    $futurePeriod = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($futurePeriod) {
        return ['exists' => true, 'type' => 'future', 'start' => $futurePeriod['start_date'], 'end' => $futurePeriod['end_date']];
    }

    return ['exists' => false];
}

function updateIncubation($incubatorNo, $oldStartDate, $newStartDate, $pdo) {
    $sql = "UPDATE calendar SET start_date = ?, end_date = ? WHERE incubatorNo = ? AND start_date = ?";
    $stmt = $pdo->prepare($sql);
    $newEndDate = date('Y-m-d', strtotime($newStartDate . '+21 days'));
    try {
        $stmt->execute([$newStartDate, $newEndDate, $incubatorNo, $oldStartDate]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error updating incubation data: ' . $e->getMessage());
        return false;
    }
}

function deleteIncubation($incubatorNo, $date, $pdo) {
    $sql = "DELETE FROM calendar
            WHERE incubatorNo = ?
            AND ? BETWEEN start_date AND DATE_ADD(start_date, INTERVAL 20 DAY)";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([$incubatorNo, $date]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error deleting incubation data: ' . $e->getMessage());
        return false;
    }
}

function saveIncubation($incubatorNo, $startDate, $pdo) {
    $endDate = date('Y-m-d', strtotime($startDate . '+20 days'));

    // Check for overlap BEFORE attempting to save
    $result = checkIncubation($incubatorNo, $startDate, $pdo);
    if ($result['exists']) {
        return false; // Indicate failure - there's an overlap
    }

    $sql = "INSERT INTO calendar (incubatorNo, start_date, end_date) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([$incubatorNo, $startDate, $endDate]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log('Error saving incubation data: ' . $e->getMessage());
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $incubatorNo = $_POST['incubatorNo'] ?? null;
    $startDate = $_POST['start_date'] ?? null;
    $oldStartDate = $_POST['old_start_date'] ?? null;
    $delete = $_POST['delete'] ?? null;

    if ($incubatorNo && $startDate && $delete) {
        $deleteSuccess = deleteIncubation($incubatorNo, $startDate, $pdo);
        $message = $deleteSuccess ? 'Incubation data deleted successfully.' : 'No incubation data found for this date.';
        echo json_encode(['success' => $deleteSuccess, 'message' => $message]);
    } elseif ($incubatorNo && $startDate) {
        $existingStartDate = checkIncubation($incubatorNo, $startDate, $pdo);

        if ($existingStartDate['exists'] && $existingStartDate['type'] === 'current' && $oldStartDate !== $startDate) {
            $deleteSuccess = deleteIncubation($incubatorNo, $oldStartDate, $pdo);

            if ($deleteSuccess) {
                $saveSuccess = saveIncubation($incubatorNo, $startDate, $pdo);
                $message = $saveSuccess ? 'Incubation data updated successfully.' : 'Failed to update incubation data.';
            } else {
                $message = 'Failed to delete existing incubation data.';
                $saveSuccess = false;
            }
        } elseif (!$existingStartDate['exists']) {
            $saveSuccess = saveIncubation($incubatorNo, $startDate, $pdo);
            $message = $saveSuccess ? 'Incubation data saved successfully.' : 'Failed to save incubation data.';
        } else {
            $message = 'An incubation period already exists within that range.';
            $saveSuccess = false;
        }
        echo json_encode(['success' => $saveSuccess, 'message' => $message]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing parameters.']);
    }
    exit;
}

if (isset($_GET['events'])) {
    $incubatorNo = $_SESSION['incubatorNo'];
    $events = getEvents($incubatorNo, $pdo);
    echo json_encode($events);
    exit;
}

if (isset($_GET['checkIncubation'])) {
    $incubatorNo = $_SESSION['incubatorNo'];
    $date = $_GET['date'];
    $result = checkIncubation($incubatorNo, $date, $pdo);
    echo json_encode($result);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Intelli-Egg</title>
    <link rel="icon" type="image/x-icon" href="../images/logo-home.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../vendor/aos/aos.css" rel="stylesheet">
    <link href="../vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="../vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css' rel='stylesheet' />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="index-page">
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; background-color: #A27C5A; margin: 0;">
        <div style="display: flex; align-items: center; justify-content: center; flex-grow: 1;">
            <img src="../images/logo-home.png" alt="logo" style="width: 60px; height: 30px; margin-right: 10px;">
            <h3 style="color: #FFA458; margin: 0;">Intelli-Egg</h3>
        </div>
       
    </div>

    <header id="header" class="header d-flex flex-column justify-content-center">
        <i class="header-toggle d-xl-none bi bi-list"></i>
        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="home.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>">
                        <i class="bi bi-house navicon"></i><span>Home</span></a>
                </li>
                <li><a href="calendar.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>" class="active">
                        <i class="bi bi-calendar-check navicon"></i><span>Calendar</span></a>
                </li>
                <li><a href="temp_humid.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>">
                        <i class="bi bi-thermometer navicon"></i><span>Temp & Humid</span></a>
                </li>
                <li><a href="camera.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>">
                        <i class="bi bi-camera-fill navicon"></i><span>Camera</span></a>
                </li>
                <li><a href="settings.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>">
                        <i class="bi bi-gear-fill navicon"></i><span>Settings</span></a>
                </li>
                <li><a href="#" id="logoutLink"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </header>

    <style>
        @media (max-width: 375px) {
            h3 {
                font-size: 20px;
            }
        }

        body {
            background-color: #FDD9A4;
            font-family: 'Poppins', sans-serif;
            width: 100%;
            color: black !important;
        }

        .container {
            padding: 2em;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            background-color: white;
            margin-top: 50px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            color: black !important;
        }

        #calendar {
            width: 100%;
        }
        .fc .fc-daygrid-day-top,
        .fc .fc-daygrid-day-number,
        .fc .fc-event-title,
        .fc .fc-day-sun,
        .fc .fc-daygrid-day,
        .fc .fc-daygrid-day-frame,
        .fc .fc-daygrid-day-bottom {
            color: black !important;
            font-size: 16px;
        }

        .fc-toolbar {
            background-color: #A27C5A;
            border-radius: 15px 15px 0 0;
            color: black;
            padding: 10px;
        }

        .fc-dayGridMonth-view {
            background-color: #F2E6D9;
            border-radius: 0 0 15px 15px;
        }

        .fc-button {
            background-color: white;
            border: none;
            color: black;
            border-radius: 5px;
        }

        .fc-button:hover {
            background-color: #A27C5A;
        }

        .fc-event {
            background-color: gray;
            border: none;
            color: black !important;
        }

        .fc-day-header {
            background-color: #A27C5A;
            color: black;
            padding: 10px 0;
            font-size: 16px;
        }

        .fc-day-sun {
            color: red;
        }

        .fc-day-today {
            background-color: #A27C5A !important;
            color: black !important;
        }

        .fc-toolbar-title {
            font-size: 20px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1em;
            }

            .fc-toolbar {
                flex-direction: column;
            }

            .fc-button {
                margin: 5px 0;
            }
        }

        @media (max-width: 576px) {
            .fc-toolbar-title {
                font-size: 16px;
            }

            .fc-daygrid-day {
                font-size: 12px;
            }

            .fc-day-header {
                font-size: 12px;
            }
        }
    </style>

    <main class="main">
        <div class="modal fade" id="incubateModal" tabindex="-1" aria-labelledby="incubateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="incubateModalLabel">Start Incubating?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="incubateDate" name="incubateDate">
                        <input type="hidden" id="oldStartDate" name="oldStartDate">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <button type="button" id="confirmIncubate" class="btn btn-primary">Yes</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div id="calendar"></div>
        </div>
    </main>

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <div id="preloader"></div>

    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/php-email-form/validate.js"></script>
    <script src="../vendor/aos/aos.js"></script>
    <script src="../vendor/typed.js/typed.umd.js"></script>
    <script src="../vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="../vendor/waypoints/noframework.waypoints.js"></script>
    <script src="../vendor/glightbox/js/glightbox.min.js"></script>
    <script src="../vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="../vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="../vendor/swiper/swiper-bundle.min.js"></script>
    <script src="../js/main.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js'></script>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var incubatorNo = "<?php echo $_SESSION['incubatorNo']; ?>";
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: function (info, successCallback, failureCallback) {
            fetch('calendar.php?events=1', {
                method: 'GET'
            })
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
        },
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        dateClick: function (info) {
    var today = new Date();
    today.setHours(0, 0, 0, 0); // Set time to midnight for accurate date comparison
    var selectedDate = new Date(info.dateStr);

    if (selectedDate < today) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'You cannot add incubation for past days.',
        });
        return;
    }

    document.getElementById('incubateDate').value = info.dateStr;

    fetch('calendar.php?checkIncubation=1&date=' + info.dateStr, {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        if (data.exists) {
            if (data.type === 'current') {
                // If the date is within an existing incubation period, show delete modal
                document.getElementById('incubateModalLabel').textContent = 'Want to delete this incubation date?';
                document.getElementById('confirmIncubate').textContent = 'Yes, delete';
                document.getElementById('confirmIncubate').onclick = function() {
                    deleteIncubation(info.dateStr);
                };
                var incubateModal = new bootstrap.Modal(document.getElementById('incubateModal'));
                incubateModal.show();
            } else if (data.type === 'future') {
                // If the date conflicts with a future incubation period, show alert
                Swal.fire({
                    icon: 'warning',
                    title: 'Conflict',
                    text: `This date conflicts with an existing incubation period from ${data.start} to ${data.end}.`,
                });
            }
        } else {
            // If not occupied, show the incubation modal to start new incubation
            document.getElementById('incubateModalLabel').textContent = 'Start Incubating?';
            document.getElementById('confirmIncubate').textContent = 'Yes';
            document.getElementById('confirmIncubate').onclick = function() {
                startIncubation(info.dateStr);
            };
            var incubateModal = new bootstrap.Modal(document.getElementById('incubateModal'));
            incubateModal.show();
        }
    });
}
    });

    calendar.render();

    function deleteIncubation(selectedDate) {
        fetch('calendar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'start_date=' + encodeURIComponent(selectedDate) +
                '&incubatorNo=' + encodeURIComponent(incubatorNo) +
                '&delete=1'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                var incubateModal = bootstrap.Modal.getInstance(document.getElementById('incubateModal'));
                incubateModal.hide();
                calendar.refetchEvents();
                alert(data.message);
            } else {
                console.error(data.error || data.message);
                alert(data.error || data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred. Please try again.');
        });
    }

    function startIncubation(selectedDate) {
        fetch('calendar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'start_date=' + encodeURIComponent(selectedDate) +
                '&incubatorNo=' + encodeURIComponent(incubatorNo)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                var incubateModal = bootstrap.Modal.getInstance(document.getElementById('incubateModal'));
                incubateModal.hide();
                calendar.refetchEvents();
                alert(data.message);
            } else {
                console.error(data.error || data.message);
                alert(data.error || data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred. Please try again.');
        });
    }
});
const logoutLink = document.getElementById('logoutLink');
        logoutLink.addEventListener('click', (event) => {
            event.preventDefault(); // Prevent default link behavior

            Swal.fire({
                title: 'Are you sure you want to logout?',
                text: 'You will be redirected to the login page.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php'; // Redirect to logout.php
                }
            });
        });
    </script>
</body>
</html>