<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Searching...</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <style>
        .cards {
            padding: 10%;
            display: flex;
            flex-direction: column;
            text-align: center;
            justify-content: center;
            align-items: center;
            gap: 15px;
            background-color: rgba(255, 255, 255, 0.074);
            border: 1px solid rgba(255, 255, 255, 0.222);
            -webkit-backdrop-filter: blur(20px);
            backdrop-filter: blur(20px);
        }

        .cards .card {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            height: 70px;
            width: 400px;
            border-radius: 10px;
            color: black;
            cursor: pointer;
            transition: 400ms;
        }

        .cards .card:hover {
            transform: scale(1.1, 1.1);
        }

        h1 {
            color:white;
        }
        h3{
            color:#6F4E37;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>

<div id="loading" class="hidden">Loading...</div> <!-- Loading element -->
<div class="cards" id="cardsContainer">
<h1>CONNECT TO INCUBATOR</h1>
    <h3>Available Incubators...</h3>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const loadingElement = document.getElementById('loading');
        const cardsContainer = document.getElementById('cardsContainer');

        loadingElement.classList.remove('hidden');

        fetch('../webpages/fetch_incubators.php')
    .then(response => response.json())
    .then(data => {
        loadingElement.classList.add('hidden');
        const displayedIncubators = new Set(); 
        if (data.length > 0) {
            data.forEach(incubator => {
                if (!displayedIncubators.has(incubator.incubatorNo)) {
                    const card = document.createElement('div');
                    card.className = 'card'; 
                    card.style.backgroundColor = getCardColor();

                    const tip = document.createElement('p');
                    tip.className = 'tip';
                    tip.innerHTML = `<i class="bi bi-egg"></i> ${incubator.incubatorNo}`;

                    card.appendChild(tip);
                    cardsContainer.appendChild(card);
                    card.addEventListener('click', () => {
                        Swal.fire({
                            title: "CONNECTED SUCCESSFULLY!",
                            text: "Connected to incubator " + incubator.incubatorNo,
                            icon: "success"
                        }).then(() => {
                            window.location.href = '../webpages/home.php?incubatorNo=' + incubator.incubatorNo;
                        });
                    });

                    displayedIncubators.add(incubator.incubatorNo); 
                }
            });
        } else {
            cardsContainer.innerHTML = '<p>No incubators found.</p>';
        }
    })
    .catch(error => {
        loadingElement.classList.add('hidden');
        cardsContainer.innerHTML = '<p>Error fetching incubators.</p>';
        console.error('Error:', error);
    });

    });

    function getCardColor() {
        const colors = ['#994D1C', '#E48F45', '#F5CCA0']; 
        return colors[Math.floor(Math.random() * colors.length)];
    }
</script>
</body>
</html>
