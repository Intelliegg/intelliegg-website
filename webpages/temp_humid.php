<?php
 session_start();
 if (!isset($_SESSION['user']) || $_SESSION['user'] != 'customer'){
  header('location: ../index.html');
}
if(isset($_GET['incubatorNo'])) {
    $_SESSION['incubatorNo'] = $_GET['incubatorNo'];
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
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css' rel='stylesheet' />
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js'></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/justgage@1.3.5/justgage.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<style>

@media (max-width: 375px) {
       h3{
       font-size: 20px;
}
}
</style>
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
                <li><a href="temp_humid.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>"class="active">
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
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card mb-4" style="border: none; border-radius: 20px; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);">
            <div class="card-body text-center">
              <h3 class="card-title">INCUBATOR HUMIDITY</h3>
              <div id="humidity-gauge" style="width: 100%; height: 400px;"></div>
             
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card mb-4" style="border: none; border-radius: 20px; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);">
            <div class="card-body text-center">
              <h3 class="card-title">INCUBATOR TEMPERATURE</h3>
              <div id="temperature-gauge" style="width: 100%; height: 400px;"></div>
             
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

  <script>
  $(document).ready(function() {
    var humidityGauge = new JustGage({
      id: "humidity-gauge",
      value: 0,
      min: 0,
      max: 100,
      symbol: "%",
      pointer: true,
      gaugeWidthScale: 0.6,
      decimals: 1, // Enable decimals in the gauge
      customSectors: [{
        color: "#ff0000",
        lo: 0,
        hi: 20
      }, {
        color: "#ff9900",
        lo: 21,
        hi: 40
      }, {
        color: "#ffff00",
        lo: 41,
        hi: 60
      }, {
        color: "#00ff00",
        lo: 61,
        hi: 80
      }, {
        color: "#0000ff",
        lo: 81,
        hi: 100
      }]
    });

    var temperatureGauge = new JustGage({
      id: "temperature-gauge",
      value: 0,
      min: 0,
      max: 100,
      symbol: "°C",
      pointer: true,
      gaugeWidthScale: 0.6,
      decimals: 1, // Enable decimals in the gauge
      customSectors: [{
        color: "#ff0000",
        lo: 0,
        hi: 20
      }, {
        color: "#ff9900",
        lo: 21,
        hi: 40
      }, {
        color: "#ffff00",
        lo: 41,
        hi: 60
      }, {
        color: "#00ff00",
        lo: 61,
        hi: 80
      }, {
        color: "#0000ff",
        lo: 81,
        hi: 100
      }]
    });

    function fetchData() {
      $.ajax({
        url: '../classes/fetch_data.php',
        method: 'GET',
        success: function(data) {
          var result = JSON.parse(data);
          var humidity = parseFloat(result.humidity).toFixed(1); 
          var temperature = parseFloat(result.temperature).toFixed(1); 
          humidityGauge.refresh(humidity);
          temperatureGauge.refresh(temperature); 
          $('#humidity-level').text(humidity + '%');
          $('#temperature-level').text(temperature + '°C');
        }
      });
    }
    fetchData();
    setInterval(fetchData, 5000);
  });
</script>
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
</body>
</html>
