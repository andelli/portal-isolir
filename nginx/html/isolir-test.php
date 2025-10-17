<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$ip = $_SERVER['REMOTE_ADDR'];

if (isset($_GET['getip'])) {
    $data = array(
        'ip' => $ip,
        'status' => 'success'
    );
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
} 

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Isolasi Internet</title>
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link href="font.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #7c3aed;
            --accent: #06b6d4;
            --warning: #f59e0b;
            --danger: #ef4444;
            --success: #10b981;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #64748b;
            --border: #e2e8f0;
        }

        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .status-bar {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--danger), #f97316, var(--danger));
            background-size: 200% 100%;
            animation: gradient 3s ease infinite;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .warning-icon {
            font-size: 80px;
            color: var(--danger);
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        h1 {
            color: var(--danger);
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: 700;
        }

        p {
            margin-bottom: 15px;
            font-size: 16px;
            color: var(--gray);
        }

        .info-box {
            background-color: #fef2f2;
            border-left: 4px solid var(--danger);
            padding: 20px;
            margin: 25px 0;
            text-align: left;
            border-radius: 0 12px 12px 0;
        }

        .info-box p {
            margin-bottom: 8px;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, var(--border), transparent);
            margin: 30px 0;
        }

        .warning-text {
            background-color: #fffbeb;
            border: 1px solid var(--warning);
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
            font-weight: 600;
            font-size: 15px;
            line-height: 1.6;
            color: var(--dark);
        }

        .contact-info {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid var(--border);
        }

        .contact-title {
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 18px;
            color: var(--dark);
        }

        .contact-items {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            border-radius: 12px;
            background: var(--light);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .contact-details {
            text-align: left;
            flex: 1;
        }

        .contact-number {
            font-weight: 600;
            font-size: 16px;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .contact-description {
            font-size: 14px;
            color: var(--gray);
            margin-bottom: 4px;
        }

        .contact-action {
            color: var(--primary);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .contact-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .contact-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }

        .ip-info {
            background: var(--light);
            padding: 10px 15px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 13px;
            color: var(--gray);
            border: 1px solid var(--border);
        }

        .demo-selector {
            background: #f1f5f9;
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 13px;
        }

        .demo-selector select {
            margin-left: 8px;
            padding: 4px 8px;
            border: 1px solid var(--border);
            border-radius: 4px;
            background: white;
        }

        .footer {
            text-align: center;
            color: var(--gray);
            font-size: 14px;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 600px;
            margin-top: 20px;
        }

        .footer-company {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .footer-tagline {
            font-size: 13px;
            color: var(--gray);
        }

        /* Mobile Responsive */
        @media (max-width: 640px) {
            .container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .contact-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            
            .contact-btn {
                align-self: stretch;
                justify-content: center;
                padding: 12px;
            }
            
            .contact-details {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 25px 15px;
            }
            
            h1 {
                font-size: 22px;
            }
            
            .warning-icon {
                font-size: 70px;
            }
            
            .info-box, .warning-text {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-bar"></div>
        <div class="warning-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1>Mohon Perhatian!</h1>
        <p>Mohon maaf atas ketidaknyamanan yang terjadi</p>
        
        <div class="info-box">
            <p>Saat ini layanan internet Anda dalam <strong>isolir</strong> karena kami belum menerima pembayaran tagihan terakhir Anda.</p>
            <p>Segera lakukan pembayaran untuk dapat mengaktifkan layanan Internet ini.</p>
        </div>
        
        <div class="divider"></div>
        
        <div class="warning-text">
            PASTIKAN ANDA TELAH MELAKUKAN PEMBAYARAN SEBELUM<br>
            TANGGAL 20 PADA SETIAP BULAN<br>
            UNTUK MENGHINDARI ISOLIR HINGGA PEMUTUSAN LAYANAN SECARA PERMANEN.
        </div>
        
        <div class="contact-info">
            <div class="contact-title">Informasi aduan dapat menghubungi :</div>
            
            <div class="contact-items" id="contactItems">
                <!-- Kontak akan diisi oleh JavaScript -->
                <div class="loading">Mendeteksi wilayah Anda...</div>
            </div>
            
            <div class="ip-info">
                <i class="fas fa-network-wired"></i> 
                Wilayah: <span id="regionInfo">Mendeteksi...</span> | 
                IP: <span id="ipInfo">Mendeteksi...</span>
            </div>

            <!-- <div class="demo-selector">
                <strong>Mode Demo:</strong>
                <select id="demoSelect" onchange="changeDemoMode(this.value)">
                    <option value="auto">Auto Detect</option>
                    <option value="brebes">Brebes Tegal</option>
                    <option value="banyumas">Banyumas</option>
                    <option value="klaten">Klaten</option>
                    <option value="unknown">Unknown IP</option>
                </select>
            </div> -->
        </div>
    </div>

    <div class="footer">
        <div class="footer-company">PT Media Cepat Indonesia</div>
        <div class="footer-tagline">Menyediakan layanan internet cepat dan terpercaya</div>
    </div>

    <script>
        // Data customer service berdasarkan subnet
        const csData = {
            'brebes': {
                region: 'Rapid Home Brebes Tegal',
                ipRange: '10.80.0.0/16',
                contacts: [
                    {
                        number: '+62 812-2760-4388',
                        description: 'Customer Service Brebes 1',
                        phone: '+6281227604388'
                    },
                    {
                        number: '+62 877-7078-0747',
                        description: 'Customer Service Brebes 2', 
                        phone: '+6287770780747'
                    }
                ]
            },
            'banyumas': {
                region: 'Rapid Home Banyumas',
                ipRange: '10.85.0.0/16',
                contacts: [
                    {
                        number: '+62 856-4254-5041',
                        description: 'Customer Service Banyumas 1',
                        phone: '+6285642545041'
                    }
                ]
            },
            'klaten': {
                region: 'Rapid Home Klaten',
                ipRange: '10.88.0.0/16',
                contacts: [
                    {
                        number: '+62 851-7973-3911',
                        description: 'Customer Service Klaten 1',
                        phone: '+6285179733911'
                    }
                ]
            },
            'unknown': {
                region: 'Default',
                ipRange: 'IP Tidak Dikenal',
                contacts: [
                    {
                        number: '+62 812-2760-4388',
                        description: 'Customer Service Brebes 1',
                        phone: '+6281227604388'
                    },
                    {
                        number: '+62 877-7078-0747',
                        description: 'Customer Service Brebes 2',
                        phone: '+6287770780747'
                    }
                ]
            }
        };
        async function getUserIP() {
            try {
                const response = await fetch('?getip');
                const data = await response.json();
                return data.ip;
            } catch (error) {
                console.log('Tidak bisa mendapatkan IP publik, menggunakan simulasi');
            }
        }

        // Fungsi untuk mendeteksi subnet berdasarkan IP
        function detectSubnetFromIP(ip) {
            if (ip.startsWith('10.80.')) return 'brebes';
            if (ip.startsWith('10.85.')) return 'banyumas';
            if (ip.startsWith('10.88.')) return 'klaten';
            return 'unknown';
        }

        // Fungsi untuk menampilkan customer service
        async function displayCustomerService() {
            const contactItems = document.getElementById('contactItems');
            const regionInfo = document.getElementById('regionInfo');
            const ipInfo = document.getElementById('ipInfo');
            
            // Tampilkan loading
            contactItems.innerHTML = '<div class="loading">Mendeteksi wilayah Anda...</div>';
            regionInfo.textContent = 'Mendeteksi...';
            ipInfo.textContent = 'Mendeteksi...';
            
            try {
                // Dapatkan IP pengguna
                const userIP = await getUserIP();
                ipInfo.textContent = userIP;
                
                // Deteksi subnet
                let subnet;
                const urlParams = new URLSearchParams(window.location.search);
                const demoMode = urlParams.get('demo');
                
                if (demoMode && csData[demoMode]) {
                    subnet = demoMode;
                    // Gunakan IP simulasi untuk demo mode
                    ipInfo.textContent = ipSimulations[demoMode] + ' (Demo)';
                } else {
                    subnet = detectSubnetFromIP(userIP);
                }
                
                const csInfo = csData[subnet];
                
                // Update info wilayah
                regionInfo.textContent = `${csInfo.region}`;
                
                // Kosongkan container dan tambahkan kontak
                contactItems.innerHTML = '';
                csInfo.contacts.forEach(contact => {
                    const contactItem = document.createElement('div');
                    contactItem.className = 'contact-item';
                    contactItem.innerHTML = `
                        <div class="contact-details">
                            <div class="contact-number">${contact.number}</div>
                            <div class="contact-description">${contact.description}</div>
                            <div class="contact-action">
                                <i class="fas fa-circle"></i> chat sekarang
                            </div>
                        </div>
                        <button class="contact-btn" onclick="contactCS('${contact.phone}')">
                            <i class="fab fa-whatsapp"></i> Hubungi
                        </button>
                    `;
                    contactItems.appendChild(contactItem);
                });
                
            } catch (error) {
                console.error('Error:', error);
                // Fallback ke default
                regionInfo.textContent = 'Default';
                ipInfo.textContent = 'Tidak terdeteksi';
                displayDefaultCS();
            }
        }

        // Fallback ke customer service default
        function displayDefaultCS() {
            const contactItems = document.getElementById('contactItems');
            const csInfo = csData['unknown'];
            
            contactItems.innerHTML = '';
            csInfo.contacts.forEach(contact => {
                const contactItem = document.createElement('div');
                contactItem.className = 'contact-item';
                contactItem.innerHTML = `
                    <div class="contact-details">
                        <div class="contact-number">${contact.number}</div>
                        <div class="contact-description">${contact.description}</div>
                        <div class="contact-action">
                            <i class="fas fa-circle"></i> chat sekarang
                        </div>
                    </div>
                    <button class="contact-btn" onclick="contactCS('${contact.phone}')">
                        <i class="fab fa-whatsapp"></i> Hubungi
                    </button>
                `;
                contactItems.appendChild(contactItem);
            });
        }

        // Fungsi untuk menghubungi CS
        function contactCS(phoneNumber) {
            const message = "Halo, saya ingin konfirmasi pembayaran dan mengaktifkan kembali layanan internet saya.";
            const url = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
            window.open(url, '_blank');
        }

        // Fungsi untuk mengubah mode demo
        function changeDemoMode(mode) {
            if (mode === 'auto') {
                window.location.href = window.location.pathname;
            } else {
                window.location.href = `${window.location.pathname}?demo=${mode}`;
            }
        }

        // Set demo selector value berdasarkan URL parameter
        function setDemoSelectorValue() {
            const urlParams = new URLSearchParams(window.location.search);
            const demo = urlParams.get('demo');
            const demoSelect = document.getElementById('demoSelect');
            if (demoSelect) {
                demoSelect.value = demo || 'auto';
            }
        }

        // Inisialisasi
        document.addEventListener('DOMContentLoaded', function() {
            displayCustomerService();
            setDemoSelectorValue();
            
            // Animasi tambahan untuk interaksi
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('contact-btn')) {
                    e.target.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        e.target.style.transform = '';
                    }, 200);
                }
            });
        });
    </script>
</body>
</html>