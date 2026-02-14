@extends('layouts.main')

@section('title', 'Blog Single')

@section('content')
    @push('css')
        <style>
            /* GANTI CSS AVATAR LAMA ANDA DENGAN YANG INI */

            #ai-avatar {
                width: 180px;
                /* Ukuran bisa disesuaikan */
                height: 180px;
                /* Ganti 'nama-file-gambar-anda.png' dengan nama file yang Anda simpan */
                background-image: url("{{ asset('images/avatar_diah_sprite.webp') }}");
                background-size: cover;
                /* Memastikan gambar pas */
                background-position: center;
                background-repeat: no-repeat;
                border-radius: 50%;
                border: 4px solid #fff;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                /* Transisi untuk animasi yang mulus */
                transition: transform 0.3s ease-in-out;
            }

            /* Kelas ini akan ditambahkan/dihapus oleh JavaScript saat AI berbicara */
            #ai-avatar.is-speaking {
                /* Menjalankan animasi 'pulse' */
                animation: pulse-breathing 2s infinite ease-in-out;
            }

            /* Definisi animasi 'pulse-breathing' */
            @keyframes pulse-breathing {
                0% {
                    transform: scale(1);
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                }

                50% {
                    transform: scale(1.05);
                    /* Sedikit membesar */
                    box-shadow: 0 8px 25px rgba(75, 143, 222, 0.4);
                    /* Bayangan berwarna saat 'aktif' */
                }

                100% {
                    transform: scale(1);
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                }
            }

            /* Tambahkan ini di dalam tag <style> Anda */

            /* Gaya untuk placeholder saat AI sedang berbicara */
            .ai-bubble .speaking-indicator {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 10px 0;
            }

            .ai-bubble .wave-bar {
                background-color: #888;
                /* Warna berbeda untuk animasi di dalam bubble */
                height: 20px;
            }

            /* CSS untuk Animasi Gelombang Suara */
            .wave-bar {
                display: inline-block;
                width: 5px;
                height: 30px;
                margin: 0 2px;
                background-color: #007bff;
                animation: sound-wave 1.2s infinite ease-in-out;
            }

            .wave-bar:nth-child(2) {
                animation-delay: -1.1s;
            }

            .wave-bar:nth-child(3) {
                animation-delay: -1.0s;
            }

            .wave-bar:nth-child(4) {
                animation-delay: -0.9s;
            }

            .wave-bar:nth-child(5) {
                animation-delay: -0.8s;
            }

            @keyframes sound-wave {

                0%,
                40%,
                100% {
                    transform: scaleY(0.4);
                }

                20% {
                    transform: scaleY(1.0);
                }
            }

            /* CSS untuk Balon Chat */
            .ai-bubble,
            .user-bubble {
                padding: 10px 15px;
                border-radius: 20px;
                margin-bottom: 10px;
                max-width: 85%;
                word-wrap: break-word;
            }

            .ai-bubble {
                background-color: #f1f0f0;
                align-self: flex-start;
                text-align: left;
            }

            .user-bubble {
                background-color: #007bff;
                color: white;
                align-self: flex-end;
                margin-left: auto;
                text-align: left;
            }

            #ai-chat-body {
                display: flex;
                flex-direction: column;
            }

            /* ============================================================== */
            /* GAYA UNTUK ASISTEN AI (MOBILE FIRST) */
            /* ============================================================== */

            /* Gaya Dasar untuk Tombol Mikrofon Mengambang */
            /* Ganti nilai z-index pada CSS Anda dengan yang ini */

            /* 1. Tombol Mikrofon Mengambang */
            #start-ai-btn {
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 60px;
                height: 60px;
                z-index: 1052;
                /* Nilai paling tinggi agar selalu di atas */
            }

            /* 2. Jendela Chat Utama */
            #ai-chat-window {
                display: none;
                position: fixed;
                bottom: 90px;
                right: 20px;
                width: 350px;
                max-width: 90%;
                height: 800px !important;
                background-color: white;
                z-index: 1050;
                /* Di atas navbar */
                flex-direction: column;
                transition: all 0.3s ease-in-out;
            }

            /* GANTI CSS LAMA ANDA DENGAN YANG INI */
            #settings-panel {
                /* KUNCI: Ubah ke 'fixed' agar terikat pada layar, bukan pada chat window */
                position: fixed;

                /* Pastikan panel menutupi seluruh layar */
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;

                background-color: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(5px);
                z-index: 1051;
                /* Pastikan di atas jendela chat */

                /* Gunakan flexbox untuk menengahkan kotak konten */
                display: none;
                /* Awalnya tersembunyi */
                align-items: center;
                justify-content: center;

                /* Tambahkan padding agar konten tidak menempel di tepi saat keyboard muncul */
                padding: 20px;
            }

            #settings-panel .settings-content {
                background-color: white;
                padding: 25px;
                border-radius: 10px;

                /* Jaga agar konten tidak menempel di tepi layar */
                width: 100%;

                /* Batasi lebar maksimum untuk layar yang lebih besar (tablet/desktop) */
                max-width: 320px;

                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
                color: #333;
                /* Pastikan teks terlihat jelas di background putih */
            }


            /* ============================================================== */
            /* GAYA UNTUK LAYAR DESKTOP (LEBIH BESAR) */
            /* ============================================================== */

            /* Terapkan gaya ini jika lebar layar 768px atau lebih */
            @media (min-width: 768px) {
                #start-ai-btn {
                    bottom: 30px;
                    right: 30px;
                }

                #ai-chat-window {
                    width: 600px;
                    /* Lebar lebih besar untuk desktop */
                    height: 700px;
                    /* Tinggi lebih besar untuk desktop */
                    bottom: 100px;
                    right: 30px;
                }

                /* Tambahkan ini di dalam tag <style> Anda */
                #settings-panel {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.6);
                    backdrop-filter: blur(5px);
                    z-index: 1050;
                    /* Pastikan di atas konten lain */
                    display: none;
                    /* Awalnya tersembunyi */
                    align-items: center;
                    justify-content: center;
                }

                #settings-panel .settings-content {
                    background-color: white;
                    padding: 25px;
                    border-radius: 10px;
                    width: 90%;
                    max-width: 300px;
                    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
                }
            }
        </style>
        <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Alex+Brush" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('css/open-iconic-bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/animate.css') }}">

        <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/owl.theme.default.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">

        <link rel="stylesheet" href="{{ asset('css/aos.css') }}">

        <link rel="stylesheet" href="{{ asset('css/ionicons.min.css') }}">

        <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.css') }}">
        <link rel="stylesheet" href="{{ asset('css/jquery.timepicker.css') }}">


        <link rel="stylesheet" href="{{ asset('css/flaticon.css') }}">
        <link rel="stylesheet" href="{{ asset('css/icomoon.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endpush

    {{-- ================================================================= --}}
    {{-- ============== BAGIAN UNTUK ASISTEN SUARA AI ================== --}}
    {{-- ================================================================= --}}

    <button id="start-ai-btn" class="btn btn-primary rounded-circle shadow">
        <i class="fas fa-microphone fa-lg"></i>
    </button>

    <div id="ai-chat-window" class="shadow-lg rounded">

        {{-- GANTI BAGIAN HEADER CHAT LAMA ANDA DENGAN YANG INI --}}
        <div class="p-3 d-flex flex-column justify-content-center align-items-center"
            style="background-color: #001f3f; color: white; border-top-left-radius: 0.25rem; border-top-right-radius: 0.25rem; position: relative;">

            {{-- Tombol Pengaturan Bahasa --}}
            <button id="settings-btn" class="btn btn-sm"
                style="position: absolute; top: 10px; left: 15px; color: rgb(255, 0, 0); opacity: 0.8;">
                <i class="fas fa-cog fa-lg"></i>
            </button>


            {{-- Elemen Avatar --}}
            <div id="ai-avatar" class="mb-2"></div>

            <h6 class="mb-0">I'm Kirana, Your Personal Tour Guide</h6>
            <button id="close-ai-btn" type="button" class="close"
                style="color: white; opacity: 1; position: absolute; top: 10px; right: 15px;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        {{-- Letakkan ini TEPAT DI BAWAH blok header di atas --}}
        {{-- Panel Pengaturan Bahasa (Awalnya Tersembunyi) --}}
        <div id="settings-panel" style="display: none;">
            <div class="settings-content">
                <h6 class="text-center mb-3">Language Settings</h6>
                <div class="form-group">
                    <label for="language-select">Recognition Language</label>
                    <select id="language-select" class="form-control">
                        <option value="id-ID">Indonesia</option>
                        <option value="en-US">English </option> {{-- (US) --}}
                        <option value="en-GB">English </option> {{-- (UK) --}}
                        <option value="es-ES">Español </option> {{-- (España) --}}
                        <option value="fr-FR">Français </option> {{-- (France) --}}
                        <option value="de-DE">Deutsch </option> {{-- (Deutschland) --}}
                        <option value="it-IT">Italiano </option> {{-- (Italia) --}}
                        <option value="pt-PT">Português </option> {{-- (Portugal) --}}
                        <option value="nl-NL">Nederlands </option> {{-- (Nederland) --}}
                        <option value="ru-RU">Русский </option> {{-- (Россия) --}}
                        <option value="ja-JP">日本語 </option> {{-- (Giappone) --}}
                        <option value="ko-KR">한국어 </option> {{-- (Corea) --}}
                        <option value="zh-CN">中文 </option> {{-- (Mandarino, Cina --}}
                        <option value="zh-TW">中文 </option> {{-- (Mandarino, Taiwan --}}
                        <option value="ar-SA">العربية </option> {{--  (Arabia Saudita --}}
                        <option value="hi-IN">हिन्दी </option> {{-- (India) --}}
                        <option value="th-TH">ไทย </option> {{-- (Thailandia) --}}
                        <option value="vi-VN">Tiếng Việt </option> {{-- (Vietnam) --}}
                        <option value="ms-MY">Melayu </option> {{-- (Malesia) --}}
                    </select>
                </div>
                <button id="close-settings-btn" class="btn btn-primary btn-block mt-3">Done</button>
            </div>
        </div>

        <div id="ai-chat-body" class="p-3" style="flex-grow: 1; overflow-y: auto;">
            {{-- Gelembung chat sambutan awal --}}
            <div class="ai-bubble">Hello! Can I help you tell me about {{ $destination->title }}?</div>
        </div>
        {{-- ... sisa kode tidak berubah ... --}}

        <div id="ai-chat-footer" class="p-3 text-center d-flex flex-column align-items-center justify-content-center"
            style="border-top: 1px solid #eee;">

            <button id="ask-mic-btn" class="btn btn-primary rounded-circle mb-2" style="width: 50px; height: 50px;">
                <i class="fas fa-microphone"></i>
            </button>

            <div id="ai-status-text" style="color: #888; font-style: italic;">Press the microphone to ask a question</div>

            <div id="sound-wave-animation" style="display: none; margin-top: 10px;">
                <div class="wave-bar"></div>
                <div class="wave-bar"></div>
                <div class="wave-bar"></div>
                <div class="wave-bar"></div>
                <div class="wave-bar"></div>
            </div>
        </div>
    </div>

    <main class="container py-5 px-3 pt-[20px] pb-5">
        {{-- Judul & Alamat di Tengah --}}
        <div class="text-center">

            {{-- Judul Destinasi --}}
            <h1 class="text-4xl font-bold mb-2">{{ $destination->title }}</h1>

            {{-- Alamat Destinasi --}}
            <h5 class="text-lg text-gray-600 mb-6">{{ $destination->address }}</h5>
        </div>

        {{-- Gambar Destinasi --}}
        <div class="d-flex justify-content-center">
            <img src="{{ url('storage/' . $destination->image) }}" alt="Gambar Destinasi"
                class="rounded-lg shadow-md mb-4 img-fluid" style="max-width: 600px; width: 100%; height: auto;" />
        </div>



        {{-- Deskripsi --}}
        <div class="text-justify text-gray-700 leading-relaxed mb-24">
            <p>{{ $destination->created_at }} {!! $destination->desc !!} </p>
        </div>
    </main>
    @push('script')
        <!-- Scripts -->
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/jquery-migrate-3.0.1.min.js') }}"></script>
        <script src="{{ asset('js/popper.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/jquery.easing.1.3.js') }}"></script>
        <script src="{{ asset('js/jquery.waypoints.min.js') }}"></script>
        <script src="{{ asset('js/jquery.stellar.min.js') }}"></script>
        <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
        <script src="{{ asset('js/aos.js') }}"></script>
        <script src="{{ asset('js/jquery.animateNumber.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
        <script src="{{ asset('js/jquery.timepicker.min.js') }}"></script>
        <script src="{{ asset('js/scrollax.min.js') }}"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
        <script src="{{ asset('js/google-map.js') }}"></script>
        <script src="{{ asset('js/main.js') }}"></script>
        <script src="{{ asset('js/main.js') }}"></script>
        <script>
            window.addEventListener('scroll', () => {
                const nav = document.querySelector('nav.navbar');
                const links = nav.querySelectorAll('.nav-link, .navbar-brand, button');

                if (window.scrollY > 30) {
                    nav.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
                    nav.style.backdropFilter = 'blur(10px)';
                    nav.style.webkitBackdropFilter = 'blur(10px)';
                    links.forEach(el => {
                        el.classList.remove('text-dark', 'text-white');
                        el.style.color = '#001f3f'; // Navy
                    });
                } else {
                    nav.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
                    nav.style.backdropFilter = 'blur(10px)';
                    nav.style.webkitBackdropFilter = 'blur(10px)';
                    links.forEach(el => {
                        el.style.color = '';
                        el.classList.remove('text-white');
                        el.classList.add('text-dark');
                    });
                }
            });
        </script>
        <script>
            document.getElementById('toggleSearch').addEventListener('click', function() {
                const bar = document.getElementById('searchBar');
                bar.style.display = bar.style.display === 'none' ? 'flex' : 'none';
            });
        </script>
    @endpush

    @push('script')
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        {{-- ... sisanya ... --}}
        <script src="{{ asset('js/main.js') }}"></script>

        {{-- GANTI SEMUA LOGIKA JAVASCRIPT LAMA ANDA DENGAN YANG DI BAWAH INI --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // ========================================================
                // FUNGSI BANTUAN UNTUK COOKIES
                // ========================================================
                /**
                 * Menyimpan cookie di browser.
                 * @param {string} name - Nama cookie.
                 * @param {string} value - Nilai cookie.
                 * @param {number} days - Jumlah hari sebelum cookie kedaluwarsa.
                 */
                function setCookie(name, value, days) {
                    let expires = "";
                    if (days) {
                        const date = new Date();
                        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                        expires = "; expires=" + date.toUTCString();
                    }
                    document.cookie = name + "=" + (value || "") + expires + "; path=/";
                }

                /**
                 * Mengambil nilai cookie dari browser.
                 * @param {string} name - Nama cookie yang akan diambil.
                 * @returns {string|null} Nilai cookie atau null jika tidak ditemukan.
                 */
                function getCookie(name) {
                    const nameEQ = name + "=";
                    const ca = document.cookie.split(';');
                    for (let i = 0; i < ca.length; i++) {
                        let c = ca[i];
                        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
                    }
                    return null;
                }

                // ========================================================
                // ELEMEN UI
                // ========================================================
                const aiAvatar = document.getElementById('ai-avatar');
                const startAiBtn = document.getElementById('start-ai-btn');
                const closeAiBtn = document.getElementById('close-ai-btn');
                const aiChatWindow = document.getElementById('ai-chat-window');
                const aiChatBody = document.getElementById('ai-chat-body');
                const aiStatusText = document.getElementById('ai-status-text');
                const soundWaveAnimation = document.getElementById('sound-wave-animation');
                const askMicBtn = document.getElementById('ask-mic-btn');
                // Elemen baru untuk pengaturan
                const settingsBtn = document.getElementById('settings-btn');
                const settingsPanel = document.getElementById('settings-panel');
                const closeSettingsBtn = document.getElementById('close-settings-btn');
                const languageSelect = document.getElementById('language-select');

                // ========================================================
                // KONTEKS DARI HALAMAN
                // ========================================================
                const destinationTitle = document.querySelector('h1').textContent;
                const destinationDesc = document.querySelector('.text-justify p').textContent;

                // ========================================================
                // INISIALISASI SPEECH RECOGNITION API
                // ========================================================
                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

                if (!SpeechRecognition) {
                    aiStatusText.textContent = 'Browser Anda tidak mendukung fitur suara.';
                    askMicBtn.disabled = true;
                    settingsBtn.disabled = true;
                    return;
                }

                function updateSettingsButtonText() {
                    // Ambil teks dari option yang sedang terpilih (misal: "Bahasa Indonesia")
                    const selectedText = languageSelect.options[languageSelect.selectedIndex].textContent;

                    // Ganti isi HTML tombol agar ikon tetap ada + teks baru
                    settingsBtn.innerHTML = `<i class="fas fa-cog fa-lg"></i> ${selectedText}`;
                }



                const recognition = new SpeechRecognition();

                // Ambil bahasa dari cookie, atau default ke 'id-ID'
                let currentLanguage = getCookie('language') || 'id-ID';
                recognition.lang = currentLanguage;
                languageSelect.value = currentLanguage;
                // PANGGIL FUNGSI DI SINI (untuk pertama kali saat halaman dimuat)
                updateSettingsButtonText();

                recognition.interimResults = false;
                recognition.continuous = false;

                // ========================================================
                // EVENT LISTENERS UNTUK UI
                // ========================================================
                startAiBtn.addEventListener('click', () => {
                    aiChatWindow.style.display = 'flex';
                    startAiBtn.style.display = 'none';
                });

                closeAiBtn.addEventListener('click', () => {
                    aiChatWindow.style.display = 'none';
                    startAiBtn.style.display = 'block';
                });

                askMicBtn.addEventListener('click', () => {
                    try {
                        // Pastikan bahasa recognition selalu yang terbaru dari cookie
                        recognition.lang = getCookie('language') || 'id-ID';
                        recognition.start();
                    } catch (e) {
                        console.error("Recognition bisa jadi sudah berjalan.", e);
                    }
                });

                // Event listener untuk menu pengaturan
                settingsBtn.addEventListener('click', () => {
                    // Pastikan dropdown menampilkan bahasa yang sedang aktif
                    languageSelect.value = getCookie('language') || 'id-ID';
                    settingsPanel.style.display = 'flex';
                });

                closeSettingsBtn.addEventListener('click', () => {
                    settingsPanel.style.display = 'none';
                });

                // Simpan bahasa ke cookie saat pilihan di dropdown berubah
                languageSelect.addEventListener('change', () => {
                    updateSettingsButtonText();
                    const selectedLanguage = languageSelect.value;
                    setCookie('language', selectedLanguage, 365); // Simpan cookie selama 1 tahun
                    console.info(selectedLanguage);
                    recognition.lang = selectedLanguage; // Langsung ubah bahasa untuk recognition
                    // Anda bisa menutup panel otomatis setelah memilih
                    // settingsPanel.style.display = 'none'; 
                });

                // ========================================================
                // EVENT LISTENERS UNTUK SPEECH RECOGNITION (Tidak berubah)
                // ========================================================
                recognition.onstart = () => {
                    aiStatusText.textContent = 'Listening...';
                    askMicBtn.disabled = true;
                    soundWaveAnimation.style.display = 'flex';
                };

                recognition.onend = () => {
                    aiStatusText.textContent = 'Press the microphone to ask a question';
                    askMicBtn.disabled = false;
                    soundWaveAnimation.style.display = 'none';
                };

                recognition.onerror = (event) => {
                    if (event.error === 'no-speech') {
                        aiStatusText.textContent = 'No speech detected. Please try again.';
                    } else if (event.error === 'language-not-supported') {
                        aiStatusText.textContent = 'Language not supported. Check settings.';
                    } else {
                        aiStatusText.textContent = 'An error occurred. Try again.';
                    }
                    console.error('Speech recognition error:', event.error);
                };

                recognition.onresult = (event) => {
                    const transcript = event.results[0][0].transcript;
                    addBubbleToChat(transcript, 'user');
                    askTheGuide(transcript);
                };


                // ========================================================
                // FUNGSI-FUNGSI UTAMA (Tidak ada perubahan signifikan di sini)
                // ========================================================

                // ... kode sebelumnya ...

                async function askTheGuide(promptText) {
                    aiStatusText.textContent = 'AI is thinking...';
                    const aiBubble = createAiPlaceholderBubble();

                    // PERUBAHAN DI SINI:
                    // Kita tidak lagi mengirim object 'context' yang berisi teks panjang.
                    // Kita hanya mengirim 'destination_id' dan 'prompt'.
                    const payload = {
                        prompt: promptText,
                        // Blade akan mencetak ID destinasi langsung ke dalam script JS
                        destination_id: "{{ $destination->id }}"
                    };

                    try {
                        const response = await fetch("{{ route('ai.ask') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify(payload)
                        });

                        if (!response.ok) throw new Error(`API Error: ${response.statusText}`);

                        const result = await response.json();

                        if (result.status === 'success') {
                            const textAnswer = result.data.text_answer;
                            const base64Audio = result.data.audio_base64;

                            aiStatusText.textContent = 'AI is replying...';
                            if (base64Audio && base64Audio !== "dev_mode_audio_disabled") {
                                playAudio(base64Audio, aiBubble, textAnswer);
                            } else {
                                fillAiBubble(aiBubble, textAnswer);
                                aiStatusText.textContent = 'Press the microphone to ask a question';
                            }
                        } else {
                            fillAiBubble(aiBubble, `Sorry, an error occurred: ${result.message}`);
                        }

                    } catch (error) {
                        console.error('Error calling API:', error);
                        fillAiBubble(aiBubble, 'Oops, there seems to be a connection issue.');
                    }
                }

                // ... kode setelahnya ...

                function addBubbleToChat(text, sender) {
                    const bubble = document.createElement('div');
                    bubble.textContent = text;
                    bubble.classList.add(sender === 'user' ? 'user-bubble' : 'ai-bubble');
                    aiChatBody.appendChild(bubble);
                    scrollToBottom();
                }

                function createAiPlaceholderBubble() {
                    const bubble = document.createElement('div');
                    bubble.classList.add('ai-bubble');
                    bubble.innerHTML = `
            <div class="speaking-indicator">
                <div class="wave-bar"></div>
                <div class="wave-bar"></div>
                <div class="wave-bar"></div>
                <div class="wave-bar"></div>
                <div class="wave-bar"></div>
            </div>
        `;
                    aiChatBody.appendChild(bubble);
                    scrollToBottom();
                    return bubble;
                }



                function fillAiBubble(bubble, text) {
                    bubble.innerHTML = '';
                    bubble.textContent = text;
                }

                function playAudio(base64String, bubble, finalText) {
                    const audioUrl = `data:audio/mp3;base64,${base64String}`;
                    const audio = new Audio(audioUrl);

                    aiAvatar.classList.add('is-speaking');
                    audio.play();

                    audio.onended = () => {
                        aiAvatar.classList.remove('is-speaking');
                        fillAiBubble(bubble, finalText);
                        aiStatusText.textContent = 'Press the microphone to ask a question';
                    };

                    audio.onerror = () => {
                        console.error("Failed to play audio.");
                        aiAvatar.classList.remove('is-speaking');
                        fillAiBubble(bubble, `(Failed to play audio) ${finalText}`);
                        aiStatusText.textContent = 'Press the microphone to ask a question';
                    }
                }

                function scrollToBottom() {
                    aiChatBody.scrollTop = aiChatBody.scrollHeight;
                }

            });
        </script>

        {{-- Hapus <style> duplikat di bawah jika ada, cukup satu di atas --}}
    @endpush
@endsection
