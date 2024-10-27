document.getElementById('scanBtn').addEventListener('click', function() {
    const loadingElement = document.getElementById('loading');
    const devicesList = document.getElementById('devicesList');
    
    loadingElement.classList.remove('hidden');
    devicesList.innerHTML = '';
    
    // Simulate a delay to mimic the scanning process
    setTimeout(() => {
        loadingElement.classList.add('hidden');
        
        // Simulate sample device data
        const sampleDevices = [
            '192.168.1.101',
            '192.168.1.102',
            '192.168.1.103',
            '192.168.1.104',
            '192.168.1.105'
        ];
        
        if (sampleDevices.length > 0) {
            const circleRadius = 100; // Radius of the circle in pixels
            const centerX = 100; // Center X coordinate of the circle
            const centerY = 100; // Center Y coordinate of the circle
            
            sampleDevices.forEach((device, index) => {
                const deviceItem = document.createElement('div');
                deviceItem.className = 'device';

                // Calculate position for each device
                const angle = (index / sampleDevices.length) * 2 * Math.PI;
                const x = centerX + circleRadius * Math.cos(angle) - 15; // Subtract half the width of the icon to center it
                const y = centerY + circleRadius * Math.sin(angle) - 15; // Subtract half the height of the icon to center it

                deviceItem.style.position = 'absolute';
                deviceItem.style.left = `${x}px`;
                deviceItem.style.top = `${y}px`;
                
                // Add click event listener
                deviceItem.addEventListener('click', () => {
                    Swal.fire({
                        title: "CONNECTED SUCCESSFULLY!",
                        text: `Connected to device ${device}`,
                        icon: "success"
                    }).then(() => {
                        window.location.href = '../webpages/dashboard.php';
                    });
                });

                devicesList.appendChild(deviceItem);
            });
        } else {
            devicesList.innerHTML = '<p>No devices found.</p>';
        }
    }, 2000); // Simulate a 2-second scan delay
});
