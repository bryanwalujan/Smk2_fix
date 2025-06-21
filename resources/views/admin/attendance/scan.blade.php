<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scan QR Code Absensi</title>
    <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
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
    <!-- Back Button in top-left corner -->
    <div class="absolute top-4 left-4">
        <a href="{{ route('attendance.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <!-- Enlarged card by 20% -->
    <div class="bg-white rounded-xl shadow-lg p-6 w-full" style="max-width: 576px"> <!-- 20% larger than max-w-md (480px) -->
        <h1 class="text-2xl font-semibold text-blue-600 mb-4 text-center">Scan QR Code Absensi</h1>
        
        <!-- Camera Selector -->
        <select id="camera-select" class="w-full p-2 mb-4 border border-gray-300 rounded-lg">
            <option value="">Pilih Kamera</option>
        </select>
        
        <!-- QR Scanner Container - enlarged by 20% -->
        <div id="reader" class="w-full border-2 border-dashed border-gray-300 rounded-lg overflow-hidden bg-gray-50" style="min-height: 360px"> <!-- 20% larger than 300px -->
            <div id="loading-indicator" class="absolute inset-0 flex flex-col items-center justify-center bg-white bg-opacity-90 z-10">
                <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-3"></div>
                <p class="text-gray-700 font-medium">Memulai kamera...</p>
            </div>
        </div>
        
        <!-- Scan Result -->
        <div id="result" class="hidden mt-4 p-4 rounded-lg text-center"></div>
        
        <!-- Status Message -->
        <p id="status-message" class="text-gray-500 text-sm mt-4 text-center">Arahkan kamera ke QR code untuk memindai</p>
        
        <!-- Debug Log -->
        <div id="debug-log" class="debug-log"></div>
    </div>

    <!-- Audio Elements -->
    <audio id="success-sound" src="{{ asset('sounds/success.mp3') }}" preload="auto"></audio>
    <audio id="error-sound" src="{{ asset('sounds/error.mp3') }}" preload="auto"></audio>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const resultDiv = document.getElementById('result');
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

            // Fungsi untuk menambahkan log debugging
            function addDebugLog(message) {
                console.log(message);
                const logEntry = document.createElement('p');
                logEntry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
                debugLog.appendChild(logEntry);
                debugLog.scrollTop = debugLog.scrollHeight;
                // Tampilkan debug log hanya saat debugging diperlukan
                // debugLog.style.display = 'block'; // Uncomment untuk menampilkan log
            }

            // Periksa apakah HTTPS digunakan
            if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
                showError('Aplikasi harus diakses melalui HTTPS untuk menggunakan kamera.');
                addDebugLog('Error: Protokol bukan HTTPS');
                return;
            }

            // Inisialisasi scanner
            function initScanner() {
                addDebugLog('Menginisialisasi scanner...');
                html5QrCode = new Html5Qrcode('reader');

                const config = {
                    fps: 10,
                    qrbox: { width: 300, height: 300 }, // Enlarged by 20%
                    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    }
                };

                // Minta izin kamera dan dapatkan daftar kamera
                Html5Qrcode.getCameras().then(availableCameras => {
                    addDebugLog(`Kamera ditemukan: ${JSON.stringify(availableCameras.map(c => c.label))}`);
                    if (availableCameras && availableCameras.length) {
                        cameras = availableCameras;
                        
                        // Isi dropdown kamera
                        cameraSelect.innerHTML = '<option value="">Pilih Kamera</option>';
                        cameras.forEach(camera => {
                            const option = document.createElement('option');
                            option.value = camera.id;
                            option.text = camera.label || `Kamera ${cameraSelect.length}`;
                            cameraSelect.appendChild(option);
                        });
                        cameraSelect.classList.remove('hidden');
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

            // Mulai kamera dengan ID tertentu
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

            // Tangani perubahan pilihan kamera
            cameraSelect.addEventListener('change', (e) => {
                if (e.target.value && html5QrCode) {
                    currentCameraId = e.target.value;
                    addDebugLog(`Kamera dipilih: ${currentCameraId}`);
                    loadingIndicator.classList.remove('hidden');
                    html5QrCode.stop().then(() => {
                        const config = {
                            fps: 10,
                            qrbox: { width: 300, height: 300 }, // Enlarged by 20%
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

            // Tangani keberhasilan pemindaian
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

            // Tangani error pemindaian
            function onScanError(errorMessage) {
                if (!errorMessage.includes('NotFoundException')) {
                    addDebugLog(`Error pemindaian: ${errorMessage}`);
                }
            }

            // Proses data hasil pemindaian
            function processScan(barcode) {
                addDebugLog('Mengirim data ke server...');
                fetch('{{ route('attendance.scan.post') }}', {
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

            // Tampilkan pesan hasil
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

            // Tampilkan pesan error
            function showError(message) {
                resultDiv.textContent = message;
                resultDiv.className = 'mt-4 p-4 rounded-lg text-center bg-red-100 text-red-800';
                resultDiv.classList.remove('hidden');
                loadingIndicator.classList.add('hidden');
                statusMessage.textContent = 'Gagal memulai kamera. Coba periksa izin atau gunakan kamera lain.';
                addDebugLog(`Error: ${message}`);
                errorSound.play().catch(err => addDebugLog(`Gagal memutar suara error: ${err.message}`));
            }

            // Mulai scanner
            addDebugLog('Memulai aplikasi...');
            initScanner();
        });
    </script>
</body>
</html>