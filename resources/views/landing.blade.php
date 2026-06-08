<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GARDA 5 - Gerakan Sadar Dosis Garam 5 Gram</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Inter:wght@400;500;600&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="landing-body">
    <div class="landing-container" data-aos="fade-in">
        <div class="landing-card">
            <div class="logo-area">
                <i class="fa-solid fa-heart-pulse logo-icon"></i>
                <h1>GARDA 5</h1>
                <p class="tagline">Gerakan Sadar Dosis Garam 5 Gram</p>
            </div>
            
            <div class="intro-text">
                <p>Sistem Aliansi Monitoring Kesehatan & Edukasi Masyarakat Terpadu untuk menekan angka hipertensi melalui pembatasan konsumsi garam harian maksimal <strong>5 Gram (1 Sendok Teh)</strong>.</p>
            </div>

            <div class="features-preview">
                <div class="f-item"><i class="fa-solid fa-tree text-success"></i><span>Pohon Reward Interaktif</span></div>
                <div class="f-item"><i class="fa-solid fa-qrcode text-primary"></i><span>Pencatatan Log Kesehatan</span></div>
                <div class="f-item"><i class="fa-solid fa-video text-danger"></i><span>Pojok Video Edukasi</span></div>
            </div>

            <div class="action-area">
                <a href="{{ route('login') }}" class="btn-main font-weight-bold"><i class="fa-solid fa-right-to-bracket mr-2"></i> Masuk ke Sistem</a>
            </div>
            
            <div class="landing-footer">
                <p>&copy; 2026 Tim Pengembang GARDA 5. All Rights Reserved.</p>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000, once: true });
    </script>
</body>
</html>