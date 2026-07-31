@extends('layouts.admin')

@section('title', 'Scanner Garantie QR Code')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.warranties.index') }}" class="text-gray-400 hover:text-gray-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Scanner Garantie</h1>
        <p class="text-sm text-gray-500 mt-0.5">Scannez le QR Code collé sur le produit pour vérifier la garantie instantanément.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Scanner Card -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex flex-col items-center">
        <div class="w-full mb-4 flex justify-between items-center">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Caméra de Scan</span>
            <select id="camera-select" class="text-xs border-gray-300 rounded-md focus:ring-gold-500 focus:border-gold-500 max-w-xs">
                <option value="">Chargement des caméras...</option>
            </select>
        </div>

        <!-- Scanner viewport -->
        <div class="relative w-full max-w-md aspect-square bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl overflow-hidden flex items-center justify-center p-2">
            <div id="reader" class="w-full h-full rounded-xl overflow-hidden"></div>
            
            <!-- Scanning overlay animation -->
            <div class="absolute inset-0 pointer-events-none flex flex-col justify-between p-6">
                <div class="flex justify-between">
                    <div class="w-8 h-8 border-t-4 border-l-4 border-gold-500 rounded-tl-md"></div>
                    <div class="w-8 h-8 border-t-4 border-r-4 border-gold-500 rounded-tr-md"></div>
                </div>
                <!-- Laser line -->
                <div class="w-full h-0.5 bg-gold-500 animate-pulse shadow-[0_0_8px_#d97706] my-auto"></div>
                <div class="flex justify-between">
                    <div class="w-8 h-8 border-b-4 border-l-4 border-gold-500 rounded-bl-md"></div>
                    <div class="w-8 h-8 border-b-4 border-r-4 border-gold-500 rounded-br-md"></div>
                </div>
            </div>
        </div>

        <div class="mt-4 flex gap-2">
            <button id="start-btn" class="px-4 py-2 bg-navy-600 hover:bg-navy-700 text-white text-xs font-bold uppercase tracking-wider rounded transition">
                Démarrer
            </button>
            <button id="stop-btn" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold uppercase tracking-wider rounded transition" disabled>
                Arrêter
            </button>
        </div>
    </div>

    <!-- Manual fallback and Instructions -->
    <div class="space-y-6">
        <!-- Manual Input -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Saisie Manuelle</h2>
            <form action="{{ route('admin.warranties.scanSearch') }}" method="GET" class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Code Garantie ou S/N</label>
                    <input type="text" name="code" placeholder="Ex: GAR-2026-0001" required
                           class="block w-full border-gray-300 rounded-md text-sm focus:ring-gold-500 focus:border-gold-500">
                </div>
                <button type="submit" class="w-full py-2 bg-navy-600 hover:bg-navy-700 text-white text-xs font-bold uppercase tracking-wider rounded transition">
                    Rechercher
                </button>
            </form>
        </div>

        <!-- Instructions -->
        <div class="bg-navy-900 text-white rounded-lg p-6 shadow-sm">
            <h2 class="text-xs font-bold text-gold-400 uppercase tracking-wider mb-3">Comment ça marche ?</h2>
            <ul class="text-xs space-y-3 text-gray-300 leading-relaxed list-decimal pl-4">
                <li>Autorisez l'accès à la caméra de votre appareil.</li>
                <li>Sélectionnez la caméra souhaitée (caméra arrière recommandée pour les smartphones).</li>
                <li>Placez le QR Code de la garantie au centre du viseur.</li>
                <li>Une fois détecté, le système effectue un bip sonore et vous redirige instantanément vers la fiche de garantie.</li>
            </ul>
        </div>
    </div>
</div>

<!-- HTML5 QR Code Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const html5QrCode = new Html5Qrcode("reader");
        const cameraSelect = document.getElementById("camera-select");
        const startBtn = document.getElementById("start-btn");
        const stopBtn = document.getElementById("stop-btn");
        let activeCameraId = null;

        // Sound Feedback generator using Web Audio API
        function playSuccessBeep() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);

                oscillator.type = 'sine';
                oscillator.frequency.value = 1000; // 1kHz beep
                gainNode.gain.setValueAtTime(0.3, audioCtx.currentTime);

                oscillator.start();
                setTimeout(() => {
                    oscillator.stop();
                    audioCtx.close();
                }, 150);
            } catch (e) {
                console.error("Audio feedback not supported or blocked", e);
            }
        }

        // Get list of cameras
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                cameraSelect.innerHTML = "";
                devices.forEach((device, index) => {
                    const option = document.createElement("option");
                    option.value = device.id;
                    option.text = device.label || `Caméra ${index + 1}`;
                    cameraSelect.appendChild(option);
                });
                activeCameraId = devices[0].id;
            } else {
                cameraSelect.innerHTML = "<option value=''>Aucune caméra détectée</option>";
            }
        }).catch(err => {
            console.error("Error getting cameras", err);
            cameraSelect.innerHTML = "<option value=''>Erreur de permission caméra</option>";
        });

        cameraSelect.addEventListener("change", function(e) {
            activeCameraId = e.target.value;
        });

        // Start scanning
        startBtn.addEventListener("click", function() {
            if (!activeCameraId) {
                alert("Veuillez sélectionner ou autoriser une caméra.");
                return;
            }

            startBtn.disabled = true;
            stopBtn.disabled = false;

            html5QrCode.start(
                activeCameraId,
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                (decodedText, decodedResult) => {
                    // Success callback
                    playSuccessBeep();
                    html5QrCode.stop().then(() => {
                        // Redirect to scanSearch
                        window.location.href = "{{ route('admin.warranties.scanSearch') }}?code=" + encodeURIComponent(decodedText);
                    });
                },
                (errorMessage) => {
                    // Verbose error, ignore
                }
            ).catch(err => {
                console.error("Unable to start scanner", err);
                alert("Erreur lors du démarrage du scanner: " + err);
                startBtn.disabled = false;
                stopBtn.disabled = true;
            });
        });

        // Stop scanning
        stopBtn.addEventListener("click", function() {
            startBtn.disabled = false;
            stopBtn.disabled = true;

            html5QrCode.stop().catch(err => {
                console.error("Unable to stop scanner", err);
            });
        });
    });
</script>
@endsection
