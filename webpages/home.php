<?php
   session_start();
   if (!isset($_SESSION['user']) || $_SESSION['user'] != 'customer'){
    header('location: ../index.html');
  }
include '../classes/database.php'; 
$db = new Database();
$conn = $db->connect();
$incubatorNo = $_GET['incubatorNo'];

$query = "
    SELECT 
        TEMPERATURE, 
        HUMIDITY, 
        date
    FROM 
        incubator
    WHERE 
        incubatorNo = :incubatorNo
    ORDER BY date DESC;
";

$stmt = $conn->prepare($query);
$stmt->bindParam(':incubatorNo', $incubatorNo);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

$timeOfDay = [
    'Early Morning' => ['start' => '00:00:00', 'end' => '06:00:00'],
    'Late Morning' => ['start' => '06:00:00', 'end' => '12:00:00'],
    'Early Afternoon' => ['start' => '12:00:00', 'end' => '15:00:00'],
    'Late Afternoon' => ['start' => '15:00:00', 'end' => '18:00:00'],
    'Early Evening' => ['start' => '18:00:00', 'end' => '21:00:00'],
    'Night' => ['start' => '21:00:00', 'end' => '23:59:59']
];

$latestRecords = [];

function categorizeRecord($dateTime, $timeOfDay) {
    $time = date('H:i:s', strtotime($dateTime));
    foreach ($timeOfDay as $period => $range) {
        if ($time >= $range['start'] && $time <= $range['end']) {
            return $period;
        }
    }
    return 'Unknown';
}

foreach ($records as $record) {
    $period = categorizeRecord($record['date'], $timeOfDay);
    if (!isset($latestRecords[$period]) || strtotime($record['date']) > strtotime($latestRecords[$period]['date'])) {
        $latestRecords[$period] = $record;
    }
}

function formatDate($dateTime) {
    $dt = new DateTime($dateTime);
    return $dt->format('F j, Y');
}

function formatTime($dateTime) {
    $dt = new DateTime($dateTime);
    return $dt->format('g:i A');
}
$query = "SELECT start_date, end_date FROM calendar WHERE incubatorNo = :incubatorNo";
$stmt = $conn->prepare($query);
$stmt->bindParam(':incubatorNo', $incubatorNo);
$stmt->execute();
$calendar = $stmt->fetch(PDO::FETCH_ASSOC);

if ($calendar) {
    $startDate = new DateTime($calendar['start_date']);
    $endDate = new DateTime($calendar['end_date']);

    // Calculate the 7th day of each week
    $firstWeek = clone $startDate;
    $firstWeek->modify('+6 days');
    $secondWeek = clone $firstWeek;
    $secondWeek->modify('+7 days');
    $thirdWeek = clone $secondWeek;
    $thirdWeek->modify('+7 days');

    $weeks = [
        '1st Week' => $firstWeek->format('Y-m-d'),
        '2nd Week' => $secondWeek->format('Y-m-d'),
        '3rd Week' => $thirdWeek->format('Y-m-d')
    ];

    // Function to get infertile eggs for a specific date
    function getInfertileEggs($conn, $incubatorNo, $date) {
        $query = "
            SELECT row_number, column_number
            FROM fertility_status
            WHERE incubatorNo = :incubatorNo
            AND status = 'infertile'
            AND DATE(detection_date) = :date
        ";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':incubatorNo', $incubatorNo);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get infertile eggs for each week's 7th day
    $infertileEggsByWeek = [];
    foreach ($weeks as $week => $date) {
        $infertileEggsByWeek[$week] = getInfertileEggs($conn, $incubatorNo, $date);
    }

    // Get total infertile eggs for all three weeks
    $totalInfertileEggs = [];
    foreach ($weeks as $date) {
        $totalInfertileEggs = array_merge($totalInfertileEggs, getInfertileEggs($conn, $incubatorNo, $date));
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Intelli-Egg</title>
  <link rel="icon" type="image/x-icon" href="../images/logo-home.png">
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../vendor/aos/aos.css" rel="stylesheet">
  <link href="../vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="../vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="../css/main.css" rel="stylesheet">
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
            <li><a href="home.php?incubatorNo=<?php echo $incubatorNo; ?>" class="active"><i class="bi bi-house navicon"></i><span>Home</span></a></li>
            <li><a href="calendar.php?incubatorNo=<?php echo $incubatorNo; ?>"><i class="bi bi-calendar-check navicon"></i><span>Calendar</span></a></li>
            <li><a href="temp_humid.php?incubatorNo=<?php echo $incubatorNo; ?>"><i class="bi bi-thermometer navicon"></i><span>Temp & Humid</span></a></li>
            <li><a href="camera.php?incubatorNo=<?php echo $incubatorNo; ?>"><i class="bi bi-camera-fill navicon"></i><span>Camera</span></a></li>
            <li><a href="settings.php?incubatorNo=<?php echo $incubatorNo; ?>"><i class="bi bi-gear-fill navicon"></i><span>Settings</span></a></li>
            <li><a href="#" id="logoutLink"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>
        </ul>
    </nav>
</header>
  <style>
@media (max-width: 375px) {
       h3{
       font-size: 20px;
}
}
    body{
      background-color: #FDD9A4;
    }
    .container {
      padding: 2em;
      display: flex;
      flex-direction: column;
      align-items: center;
     
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 2em;
    }

    th, td {
      border: 1px solid black;
      padding: 0.5em;
      text-align: center;
      color:black;
    }
    h1{
        margin: 0%;
    }

    th {
      background-color: #D49057;
    }

    button {
      background-color: #D2B48C;
      color: #F2E6D9;
      padding: 0.5em 1em;
      border: none;
      cursor: pointer;
      border-radius: 5px;
    }
    .menu {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 20%;
  height: 100%;
  background-color: #58422F;
  color: #F2E6D9;
  padding: 2em;
  text-align: center;
  z-index: 10;
}
    @media (max-width: 768px) {
      .container {
        padding: 1em;
      }
      

      table {
        font-size: 12px;
      }
    }
  </style>
  <main class="main">
      <div class="container"> 
    <h2 style="color:#FFA458; background-color:white; border-radius:50px; padding:5px 20px 5px 20px;">Egg Incubator</h2>
    <table>
        <thead>
          <tr>
            <th>Time of Day</th>
            <th>Temperature</th>
            <th>Humidity</th>
            <th>Date</th>
            <th>Time</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($timeOfDay as $period => $range) : ?>
            <tr>
              <td style="background-color: #E7BA89;"><?php echo htmlspecialchars($period); ?></td>
              <td style="background-color: white;"><?php echo isset($latestRecords[$period]['TEMPERATURE']) ? htmlspecialchars($latestRecords[$period]['TEMPERATURE']) : ''; ?></td>
              <td style="background-color: white;"><?php echo isset($latestRecords[$period]['HUMIDITY']) ? htmlspecialchars($latestRecords[$period]['HUMIDITY']) : ''; ?></td>
              <td style="background-color: white;"><?php echo isset($latestRecords[$period]['date']) ? formatDate($latestRecords[$period]['date']) : ''; ?></td>
              <td style="background-color: white;"><?php echo isset($latestRecords[$period]['date']) ? formatTime($latestRecords[$period]['date']) : ''; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <h2 style="color:#FFA458; background-color:white; border-radius:50px; padding:5px 20px 5px 20px;">Egg Fertility</h2>
      <table>
  <thead>
    <tr>
      <th>Week</th>
      <th>Row</th>
      <th>Column</th>
      <th>Results</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($weeks as $week => $date) : ?>
      <tr>
        <td style="background-color: #E7BA89;"><?php echo $week; ?> (<?php echo $date; ?>)</td>
        <td style="background-color: white;">
          <?php
          if (!empty($infertileEggsByWeek[$week])) {
              foreach ($infertileEggsByWeek[$week] as $egg) {
                  echo htmlspecialchars($egg['row_number']) . "<br>";
              }
          }
          ?>
        </td>
        <td style="background-color: white;">
          <?php
          if (!empty($infertileEggsByWeek[$week])) {
              foreach ($infertileEggsByWeek[$week] as $egg) {
                  echo htmlspecialchars($egg['column_number']) . "<br>";
              }
          }
          ?>
        </td>
        <td>
        <form action="home_viewmore.php" method="get">
    <input type="hidden" name="incubatorNo" value="<?php echo $incubatorNo; ?>">
    <input type="hidden" name="week" value="<?php echo $week; ?>">
    <button type="submit">View More</button>
</form>
        </td>
      </tr>
    <?php endforeach; ?>
    <tr>
      <td style="background-color: #E7BA89;">Total Infertile Eggs</td>
      <td colspan="2" style="background-color: white;">
        <?php
        if (!empty($totalInfertileEggs)) {
            foreach ($totalInfertileEggs as $egg) {
                echo "Row: " . htmlspecialchars($egg['row_number']) . ", Column: " . htmlspecialchars($egg['column_number']) . "<br>";
            }
        } else {
            echo "No infertile eggs found.";
        }
        ?>
      </td>
      <td>
      <form action="home_viewmore.php" method="get">
    <input type="hidden" name="incubatorNo" value="<?php echo $incubatorNo; ?>">
    <input type="hidden" name="week" value="<?php echo $week; ?>">
    <button type="submit">View More</button>
</form>
      </td>
    </tr>
  </tbody>
</table>
  </div>

      </div>
  <script src="../vendor/waypoints/noframework.waypoints.js"></script>
  <script src="../vendor/glightbox/js/glightbox.min.js"></script>
  <script src="../vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="../vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="../vendor/swiper/swiper-bundle.min.js"></script>
  <script src="../js/main.js"></script>

  <script>
    // Logout confirmation with SweetAlert
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