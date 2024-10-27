<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'customer'){
header('location: ../index.html');
}
require_once '../classes/database.php';

$db = new Database();
$pdo = $db->connect();
// Fetch incubators from the database
$sql = "SELECT incubatorNo FROM incubator";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$incubators = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['incubatorNo'])) {
    $_SESSION['incubatorNo'] = $_POST['incubatorNo'];
    header('Location: settings.php'); // Redirect to settings page after changing incubator
    exit; 
}

if (isset($_GET['incubatorNo'])) {
    $_SESSION['incubatorNo'] = $_GET['incubatorNo'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Settings</title>
    <link rel="icon" type="image/x-icon" href="../images/logo-home.png">
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap"
        rel="stylesheet">
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
       

        @media (max-width: 375px) {
            h3 {
                font-size: 20px;
            }
        }

        .card {
            width: 100%;
            height: 70vh;
            background-color: rgba(36, 40, 50, 1);
            background-image: linear-gradient(
                139deg,
                rgb(166, 123, 91) 0%,
                rgb(236, 177, 118) 0%,
                rgb(254, 216, 177) 100%
            );
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            user-select: none;
            border-radius: 10px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .card .list .element label input[type="radio"] {
            display: none;
        }

        .card .separator {
            border-top: 1.5px solid #42434a;
        }

        .card .list {
            list-style-type: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 0px;
            font-size: 30px;

        }

        .card .list .element {
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            border-radius: 6px;
        }

        .card .list .element>label {
            display: flex;
            align-items: center;
            color: #6F4E37;
            gap: 10px;
            transition: all 0.3s ease-out;
            padding: 6px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        .card .list .element label svg {
            width: 19px;
            height: 19px;
            transition: all 0.3s ease-out;
        }

        .card .list .element label:has(input[type="radio"]:checked),
        .card .list .element label:hover {
            background-color: #6F4E37;
            color: var(--hover-color);
        }

        .card .list .element label:active {
            transform: scale(0.96);
        }

        .card .list .element label:has(input[type="radio"]:checked) svg,
        .card .list .element label:hover svg {
            stroke: var(--hover-storke);
        }

        .card .list .element label.active {
            background-color: #6F4E37;
            color: #fff;
        }
    </style>
</head>

<body class="index-page" style="background-color: #FDD9A4;">
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; background-color: #A27C5A;
        margin: 0;">
        <div style="display: flex; align-items: center; justify-content: center; flex-grow: 1;">
            <img src="../images/logo-home.png" alt="logo"
                style="width: 60px; height: 30px; margin-right: 10px;">
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
                <li><a href="camera.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>">
                        <i class="bi bi-camera-fill navicon"></i><span>Camera</span></a>
                </li>
                <li><a href="settings.php?incubatorNo=<?php echo $_SESSION['incubatorNo']; ?>" class="active">
                        <i class="bi bi-gear-fill navicon"></i><span>Settings</span></a>
                </li>
                <li><a href="#" id="logoutLink"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </header>
    <main class="main">
        <div class="container mt-5" style="border: none; text-align:center;">
            <div class="row" style="border: none; text-align:center;">
                <div class="col-12" style="border: none; text-align:center;">
                    <div class="card" style="background-color: #FDD9A4; border: none; text-align:center;">
                        <h1 style="margin-top:20px;">MANAGE INCUBATORS</h1>
                        <div class="card">
                            <ul class="list" id="incubator-list"
                                style="--color:#6F4E37;--hover-storke:#fff; --hover-color:#fff">
                                <!-- List items will be populated here -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Fetch incubators from the PHP script
        fetch('fetch_incubators.php')
            .then(response => response.json())
            .then(data => {
                const incubatorList = document.getElementById('incubator-list');

                // Clear any existing list items
                incubatorList.innerHTML = '';

                if (data.length > 0) {
                    // Use a Set to track unique incubator numbers
                    const uniqueIncubators = new Set();

                    data.forEach(incubator => {
                        if (!uniqueIncubators.has(incubator.incubatorNo)) {
                            uniqueIncubators.add(incubator.incubatorNo);
                            const li = document.createElement('li');
                            li.className = 'element'; // Add class for styling

                            li.innerHTML = `
<label for="${incubator.incubatorNo}" ${incubator.incubatorNo === '<?php echo $_SESSION['incubatorNo']; ?>' ? 'class="active"' : ''}>
<input type="radio" id="${incubator.incubatorNo}" name="filed" ${incubator.incubatorNo === '<?php echo $_SESSION['incubatorNo']; ?>' ? 'checked' : ''} />
<svg class="lucide lucide-egg" stroke-linejoin="round" stroke-linecap="round" stroke-width="2" fill="none" viewBox="0 0 24 24" height="25" width="25">
<path d="M12 2c3.313 0 6 4.029 6 8s-2.687 8-6 8-6-4.029-6-8 2.687-8 6-8z"></path>
</svg> ${incubator.incubatorNo}
</label>
`;

                            incubatorList.appendChild(li);
                        }
                    });
                } else {
                    incubatorList.innerHTML = '<li>No incubators found.</li>';
                }
            })
            .catch(error => {
                console.error('Error fetching incubators:', error);
            });

        // Add event listener to radio buttons
        const incubatorList = document.getElementById('incubator-list');
        incubatorList.addEventListener('click', (event) => {
            if (event.target.tagName === 'INPUT' && event.target.type === 'radio') {
                const incubatorNo = event.target.id;

                // Send a POST request to update the session
                fetch('settings.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'incubatorNo=' + encodeURIComponent(incubatorNo)
                })
                    .then(response => {
                        if (response.ok) {
                            // Redirect to the settings page or update the page without reloading
                            // For example, you could use JavaScript to update the content without reloading
                            window.location.href = 'settings.php'; // Redirect to settings.php
                        } else {
                            console.error('Error updating session:', response.status);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating session:', error);
                    });
            }
        });

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

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>
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

</html>