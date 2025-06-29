
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scan QR Code Absensi</title>
    <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .debug-log {
            display: none;
            max-height: 200px;
            overflow-y: auto;
            background-color: #f8f8f8;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 12px;
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-[576px]">
        <h1 class="text-2xl font-semibold text-blue-600 mb-4 text-center">Scan QR Code Absensi</h1>
        
        <!-- Location Status -->
        <div id="location-status" class="hidden mt-4 p-4 rounded-lg text-center"></div>

        <!-- Camera Selector -->
        <select id="camera-select" class="w-full p-2 mb-4 border border-gray-300 rounded-lg hidden">
            <option value="">Pilih Kamera</option>
        </select>
        
        <!-- QR Scanner Container -->
        <div id="reader" class="w-full border-2 border-dashed border-gray-300 rounded-lg overflow-hidden bg-gray-50 hidden" style="min-height: 360px">
            <div id="loading-indicator" class="absolute inset-0 flex flex-col items-center justify-center bg-white bg-opacity-90 z-10">
                <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-3"></div>
                <p class="text-gray-700 font-medium">Memulai kamera...</p>
            </div>
        </div>
        
        <!-- Scan Result -->
        <div id="result" class="hidden mt-4 p-4 rounded-lg text-center"></div>
        
        <!-- Status Message -->
        <p id="status-message" class="text-gray-500 text-sm mt-4 text-center">Memeriksa lokasi...</p>
        
        <!-- Debug Log -->
        <div id="debug-log" class="debug-log"></div>

        <!-- Tombol Kembali ke Dashboard -->
        <div class="mt-6 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Audio Elements -->
    <audio id="success-sound" src="{{ asset('sounds/success.mp3') }}" preload="auto"></audio>
    <audio id="error-sound" src="{{ asset('sounds/error.mp3') }}" preload="auto"></audio>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const resultDiv = document.getElementById('result');
            const locationStatus = document.getElementById('location-status');
            const loadingIndicator = document.getElementById('loading-indicator');
            const cameraSelect = document.getElementById('camera-select');
            const statusMessage = document.getElementById('status-message');
            const debugLog = document.getElementById('debug-log');
            const successSound = document.getElementById('success-sound');
            const errorSound = document.getElementById('error-sound');
            
            let html5QrCode;
            let cameras = [];
            let currentCameraId = null;
            let isProcessing = false;

            // Koordinat sekolah (Jl. Kampus Unima) dan radius (dalam meter)
            const SCHOOL_LAT = 1.322934; // Lintang
            const SCHOOL_LNG = 124.840508; // Bujur
            const RADIUS = 5000; // 5 km dalam meter

            function addDebugLog(message) {
                console.log(message);
                const logEntry = document.createElement('p');
                logEntry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
                debugLog.appendChild(logEntry);
                debugLog.scrollTop = debugLog.scrollHeight;
            }

            // Fungsi Haversine untuk menghitung jarak (dalam meter)
            function calculateDistance(lat1, lon1, lat2, lon2) {
                const R = 6371000; // Radius bumi dalam meter
                const φ1 = lat1 * Math.PI / 180;
                const φ2 = lat2 * Math.PI / 180;
                const Δφ = (lat2 - lat1) * Math.PI / 180;
                const Δλ = (lon2 - lon1) * Math.PI / 180;

                const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                          Math.cos(φ1) * Math.cos(φ2) *
                          Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

                return R * c; // Jarak dalam meter
            }

            // Periksa lokasi pengguna
            function checkLocation() {
                addDebugLog('Memeriksa lokasi pengguna...');
                statusMessage.textContent = 'Memeriksa lokasi...';
                
                if (!navigator.geolocation) {
                    showLocationError('Geolocation tidak didukung oleh browser ini.');
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        const distance = calculateDistance(userLat, userLng, SCHOOL_LAT, SCHOOL_LNG);
                        
                        addDebugLog(`Lokasi pengguna: lat=${userLat}, lng=${userLng}, jarak=${distance.toFixed(2)} meter`);

                        if (distance <= RADIUS) {
                            addDebugLog('Pengguna berada dalam radius 5 km. Menginisialisasi scanner...');
                            statusMessage.textContent = 'Lokasi valid. Memulai scanner...';
                            locationStatus.classList.add('hidden');
                            cameraSelect.classList.remove('hidden');
                            document.getElementById('reader').classList.remove('hidden');
                            initScanner();
                        } else {
                            showLocationError('Anda harus berada di sekolah untuk melakukan absensi (jarak: ' + (distance / 1000).toFixed(2) + ' km).');
                        }
                    },
                    (error) => {
                        let errorMessage = 'Gagal mendapatkan lokasi: ';
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage += 'Izin lokasi ditolak.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage += 'Informasi lokasi tidak tersedia.';
                                break;
                            case error.TIMEOUT:
                                errorMessage += 'Permintaan lokasi timeout.';
                                break;
                            default:
                                errorMessage += error.message;
                        }
                        showLocationError(errorMessage);
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            }

            function showLocationError(message) {
                locationStatus.textContent = message;
                locationStatus.className = 'mt-4 p-4 rounded-lg text-center bg-red-100 text-red-800';
                locationStatus.classList.remove('hidden');
                statusMessage.textContent = 'Gagal memeriksa lokasi.';
                cameraSelect.classList.add('hidden');
                document.getElementById('reader').classList.add('hidden');
                addDebugLog(`Error lokasi: ${message}`);
                errorSound.play().catch(err => addDebugLog(`Gagal memutar suara error: ${err.message}`));
            }

            if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
                showLocationError('Aplikasi harus diakses melalui HTTPS untuk menggunakan kamera dan geolocation.');
                addDebugLog('Error: Protokol bukan HTTPS');
                return;
            }

            function initScanner() {
                addDebugLog('Menginisialisasi scanner...');
                html5QrCode = new Html5Qrcode('reader');

                const config = {
                    fps: 10,
                    qrbox: { width: 300, height: 300 },
                    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    }
                };

                Html5Qrcode.getCameras().then(availableCameras => {
                    addDebugLog(`Kamera ditemukan: ${JSON.stringify(availableCameras.map(c => c.label))}`);
                    if (availableCameras && availableCameras.length) {
                        cameras = availableCameras;
                        cameraSelect.innerHTML = '<option value="">Pilih Kamera</option>';
                        cameras.forEach(camera => {
                            const option = document.createElement('option');
                            option.value = camera.id;
                            option.text = camera.label || `Kamera ${cameraSelect.length}`;
                            cameraSelect.appendChild(option);
                        });
                        currentCameraId = cameras[0].id;
                        addDebugLog(`Kamera default: ${currentCameraId}`);
                        startCamera(currentCameraId, config);
                    } else {
                        showError('Tidak ada kamera yang ditemukan. Pastikan perangkat memiliki kamera.');
                        addDebugLog('Error: Tidak ada kamera ditemukan');
                    }
                }).catch(err => {
                    showError(`Gagal mengakses kamera: ${err.message}. Pastikan izin kamera diberikan.`);
                    addDebugLog(`Error akses kamera: ${err.message}`);
                });
            }

            function startCamera(cameraId, config) {
                addDebugLog(`Memulai kamera dengan ID: ${cameraId}`);
                html5QrCode.start(
                    cameraId,
                    config,
                    onScanSuccess,
                    onScanError
                ).then(() => {
                    loadingIndicator.classList.add('hidden');
                    statusMessage.textContent = 'Arahkan kamera ke QR code untuk memindai';
                    addDebugLog('Kamera berhasil dimulai');
                }).catch(err => {
                    showError(`Gagal memulai kamera: ${err.message}. Coba pilih kamera lain atau periksa izin.`);
                    addDebugLog(`Error memulai kamera: ${err.message}`);
                });
            }

            cameraSelect.addEventListener('change', (e) => {
                if (e.target.value && html5QrCode) {
                    currentCameraId = e.target.value;
                    addDebugLog(`Kamera dipilih: ${currentCameraId}`);
                    loadingIndicator.classList.remove('hidden');
                    html5QrCode.stop().then(() => {
                        const config = {
                            fps: 10,
                            qrbox: { width: 300, height: 300 },
                            formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
                        };
                        startCamera(currentCameraId, config);
                    }).catch(err => {
                        showError('Gagal menghentikan kamera: ' + err.message);
                        addDebugLog(`Error menghentikan kamera: ${err.message}`);
                        loadingIndicator.classList.add('hidden');
                    });
                }
            });

            function onScanSuccess(decodedText) {
                if (isProcessing) return;
                
                addDebugLog(`QR code dipindai: ${decodedText}`);
                isProcessing = true;
                statusMessage.textContent = 'Memproses QR code...';
                
                resultDiv.textContent = 'Memproses QR code...';
                resultDiv.className = 'mt-4 p-4 rounded-lg text-center bg-blue-100 text-blue-800';
                resultDiv.classList.remove('hidden');
                
                processScan(decodedText);
            }

            function onScanError(errorMessage) {
                if (!errorMessage.includes('NotFoundException')) {
                    addDebugLog(`Error pemindaian: ${errorMessage}`);
                }
            }

            function processScan(barcode) {
                addDebugLog('Mengirim data ke server...');
                fetch('{{ route('public.attendance.scan.post') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ barcode: barcode })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    addDebugLog(`Respons server: ${JSON.stringify(data)}`);
                    if (data.success) {
                        let resultText = data.message;
                        if (data.name) resultText += `\nNama: ${data.name}`;
                        if (data.time) resultText += `\nWaktu: ${data.time}`;
                        
                        showResult(resultText, 'success');
                        successSound.play().catch(err => addDebugLog(`Gagal memutar suara sukses: ${err.message}`));
                    } else {
                        showResult(data.message || 'Terjadi kesalahan', 'error');
                        errorSound.play().catch(err => addDebugLog(`Gagal memutar suara error: ${err.message}`));
                    }
                })
                .catch(error => {
                    showResult(error.message || 'Terjadi kesalahan sistem', 'error');
                    addDebugLog(`Error server: ${error.message}`);
                    errorSound.play().catch(err => addDebugLog(`Gagal memutar suara error: ${err.message}`));
                })
                .finally(() => {
                    setTimeout(() => {
                        isProcessing = false;
                        statusMessage.textContent = 'Arahkan kamera ke QR code untuk memindai';
                        addDebugLog('Status pemindaian direset');
                    }, 2000);
                });
            }

            function showResult(message, type) {
                resultDiv.textContent = message;
                resultDiv.className = `mt-4 p-4 rounded-lg text-center ${
                    type === 'success' ? 'bg-green-100 text-green-800' : 
                    type === 'error' ? 'bg-red-100 text-red-800' : 
                    'bg-blue-100 text-blue-800'
                }`;
                resultDiv.classList.remove('hidden');
                addDebugLog(`Menampilkan hasil: ${message} (${type})`);
            }

            function showError(message) {
                resultDiv.textContent = message;
                resultDiv.className = 'mt-4 p-4 rounded-lg text-center bg-red-100 text-red-800';
                resultDiv.classList.remove('hidden');
                loadingIndicator.classList.add('hidden');
                statusMessage.textContent = 'Gagal memulai kamera. Coba periksa izin atau gunakan kamera lain.';
                addDebugLog(`Error: ${message}`);
                errorSound.play().catch(err => addDebugLog(`Gagal memutar suara error: ${err.message}`));
            }

            addDebugLog('Memulai aplikasi...');
            checkLocation();
        });
    </script>
</body>
</html>
