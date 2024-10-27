<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user'] != 'customer') {
    header('location: ../index.html');
    exit;
}

require_once '../classes/database.php';

$db = new Database();
$pdo = $db->connect();

if (isset($_GET['incubatorNo'])) {
    $_SESSION['incubatorNo'] = $_GET['incubatorNo'];
}
$currentDate = date('Y-m-d');
$stmt = $pdo->prepare("SELECT image_data FROM images WHERE DATE(detection_Date) = :detection_Date LIMIT 1");
$stmt->bindParam(':detection_Date', $currentDate);
$stmt->execute();
$image = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT row_number, column_number, status FROM fertility_status WHERE DATE(detection_date) = :detection_date");
$stmt->bindParam(':detection_date', $currentDate);
$stmt->execute();
$eggFertility = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Intelli-Egg</title>
  <link rel="icon" type="image/x-icon" href="../images/logo-home.png">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
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

  <style>
    body {
      background-color: #FDD9A4;
      width: 100%;
    }

    h2 {
      color: #FFA458;
      background-color: white;
      border-radius: 50px;
      padding: 5px 20px;
      margin-top: 20px;
    }

    .container {
      width: 100%; /* Allow full width */
      max-width: 90%; /* Avoid restricting width */
      padding: 0 10px; /* Adjust padding as needed */
      margin-bottom: 50px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    /* Main box styling */
    .big-box {
      display: grid;
      grid-template-columns: repeat(4, 1fr); /* Default for smaller screens */
      grid-template-rows: repeat(8, auto);
      gap: 10px;
      width: 100%;
      max-width: 100%; /* Adjust for smaller screens */
      background-color: #FFF8E7;
      border: 2px solid #D49057;
      border-radius: 10px;
      padding: 10px;
      margin-top: 20px;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    }

    .big-box .col {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      background-color: #FFF;
      border: 1px solid #D49057;
      height: 60px; /* Adjust height for smaller screens */
      font-weight: 500;
      padding: 5px;
    }

    .cell-header {
      font-size: 0.8em;
      color: #666;
    }

    .cell-status {
      flex-grow: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    @media (min-width: 768px) {
      .big-box {
        grid-template-columns: repeat(7, 1fr); /* Maintain 7 columns */
        max-width: 1400px; /* Adjust for medium-large screens */
      }
      .big-box .col {
        height: 80px; /* Adjust height */
      }
    }

    @media (min-width: 1200px) {
      .big-box {
        max-width: 1600px; /* Make the box larger */
      }
      .big-box .col {
        height: 100px; /* Increased height for larger cells */
      }
    }

    @media (max-width: 320px){
      .big-box {
        font-size: 10px;
      }
    }
  </style>
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
        <li><a href="home.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>"class="active">
            <i class="bi bi-house navicon"></i><span>Home</span></a>
        </li>
        <li><a href="calendar.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>">
            <i class="bi bi-calendar-check navicon"></i><span>Calendar</span></a>
        </li>
        <li><a href="temp_humid.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>">
            <i class="bi bi-thermometer navicon"></i><span>Temp & Humid</span></a>
        </li>
        <li><a href="camera.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>">
            <i class="bi bi-camera-fill navicon"></i><span>Camera</span></a>
        </li>
        <li><a href="settings.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>" >
            <i class="bi bi-gear-fill navicon"></i><span>Settings</span></a>
        </li>
        <li><a href="#" id="logoutLink"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>
      </ul>
    </nav>
  </header>



  <main class="main">
    <div class="container">
      <h2>Egg Fertility</h2>
      <?php if ($image !== false): ?>
        <div class="col text-center" style="grid-column: 1 / -1; grid-row: 1;">
          <img src="data:image/png;base64,<?php echo base64_encode($image); ?>" alt="Camera Image" style="max-width: 100%; max-height: 100%;">
        </div>
      <?php else: ?>
        <div class="col text-center" style="grid-column: 1 / -1; grid-row: 1;">
          No image available for today.
        </div>
      <?php endif; ?>
      <div class="big-box">
        <?php
        // Create an associative array to store fertility data
        $fertilityData = [];
        foreach ($eggFertility as $egg) {
          $key = $egg['row_number'] . '-' . $egg['column_number'];
          $fertilityData[$key] = $egg['status'];
        }

        // Generate all 56 boxes
        for ($row = 1; $row <= 8; $row++) {
          for ($col = 1; $col <= 7; $col++) {
            $key = $row . '-' . $col;
            $status = isset($fertilityData[$key]) ? $fertilityData[$key] : '';
            echo "<div class='col' style='grid-column: {$col}; grid-row: " . ($row + 1) . ";'>";
            echo "<div class='cell-header'>R{$row} C{$col}</div>";
            echo "<div class='cell-status'>{$status}</div>";
            echo "</div>";
          }
        }
        ?>
      </div>
    </div>
  </main>

  <script src="../vendor/waypoints/noframework.waypoints.js"></script>
  <script src="../vendor/glightbox/js/glightbox.min.js"></script>
  <script src="../vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="../vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="../vendor/swiper/swiper-bundle.min.js"></script>
  <script src="../js/main.js"></script>
  <script>
    const logoutLink = document.getElementById('logoutLink');
    logoutLink.addEventListener('click', (event) => {
      event.preventDefault(); 
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