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
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Intelli-Egg Camera</title>
  <link rel="icon" type="image/x-icon" href="../images/logo-home.png">
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* Style for the gray box around the camera feed */
    .camera-feed-container {
      background-color: #d3d3d3; /* Light gray background */
      padding: 10px;
      border-radius: 10px; /* Optional rounded corners */
      display: inline-block;
      width:100%;
      height:70vh;
    }

@media (max-width: 375px) {
       h3{
       font-size: 20px;
}
}
  </style>
</head>

<body class="index-page" style="background-color: #FDD9A4;">
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
                <li><a href="calendar.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>">
                        <i class="bi bi-calendar-check navicon"></i><span>Calendar</span></a>
                </li>
                <li><a href="temp_humid.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>">
                        <i class="bi bi-thermometer navicon"></i><span>Temp & Humid</span></a>
                </li>
                <li><a href="camera.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>"class="active">
                        <i class="bi bi-camera-fill navicon"></i><span>Camera</span></a>
                </li>
                <li><a href="settings.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>">
                        <i class="bi bi-gear-fill navicon"></i><span>Settings</span></a>
                </li>
                <li><a href="#" id="logoutLink"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </header>
    <main class="main">
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="card" style="background-color: #FDD9A4; border: none;">
                    <div class="card-body text-center">
                        <div class="camera-feed-container" style="width: 100%; height: 100%; position: relative;">
                            <img src="http://192.168.0.117:7123/stream.mjpg" alt="Camera Feed"
                                class="img-fluid"
                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                        </div>
                      
                        <div class="d-flex align-items-center" style="background-color: #F1E6CF; border: 1px solid #D3C2A8; padding: 1rem; border-radius: 5px; font-size: 1.2rem; margin:10px; justify-content: center;">
                            <div class="form-check form-switch" style="text-align:center;">
                                <input class="form-check-input" type="checkbox" role="switch" id="candlingSwitch" onchange="controlRelay(this.checked ? 'on' : 'off')">
                            </div>
                            <label style="text-align:center;">Candling</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function controlRelay(state) {
  fetch(`http://192.168.0.104/candling?state=${state}`)
    .then(response => response.text())
    .then(data => console.log(data))
    .catch(error => console.error('Error:', error));
}
</script>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <div id="preloader"></div>

  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/aos/aos.js"></script>
  <script src="../vendor/typed.js/typed.umd.js"></script>
  <script src="../vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="../vendor/waypoints/noframework.waypoints.js"></script>
  <script src="../vendor/glightbox/js/glightbox.min.js"></script>
  <script src="../vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="../vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="../vendor/swiper/swiper-bundle.min.js"></script>
  <script src="../js/main.js"></script>

</body>
<script>
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
</html>
