<?php
// index.php - Hero bölümü ekranın tamamını kaplar
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kişisel Web Sitem</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f8ff;
            color: #222;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .navbar-brand {
            font-weight: 800;
        }

        .hero {
            background: linear-gradient(to right, #e0ecff, #fdfdfd);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 20px;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.3rem;
            color: #555;
        }

        footer {
            background-color: #fff;
            border-top: 1px solid #eee;
            padding: 30px 0;
            text-align: center;
            font-size: 0.9rem;
            color: #777;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">Murat'ın Portfolyosu</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="#">Ana Sayfa</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Hakkımda</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Biyografi</a></li>
                        <li><a class="dropdown-item" href="#">İlgi Alanlarım</a></li>
                        <li><a class="dropdown-item" href="#">Eğitim & Deneyim</a></li>
                        <li><a class="dropdown-item" href="#">Başarılar & Sertifikalar</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Blog</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Kişisel Yazılar</a></li>
                        <li><a class="dropdown-item" href="#">Seyahat Notları</a></li>
                        <li><a class="dropdown-item" href="#">Kitap & Film Önerileri</a></li>
                        <li><a class="dropdown-item" href="#">Teknoloji & İlgi Alanları</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Galeri</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Fotoğraflarım</a></li>
                        <li><a class="dropdown-item" href="#">Hobilerim</a></li>
                        <li><a class="dropdown-item" href="#">Video & Multimedya</a></li>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link" href="#">SSS</a></li>
                <li class="nav-item"><a class="nav-link" href="#">İletişim</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section (Full Screen) -->
<section class="hero">
    <div class="container">
        <h1>Merhaba, ben Murat 👋</h1>
        <p>Kişisel portfolyoma hoş geldiniz! Yazılım, tasarım ve teknoloji tutkumu burada paylaşıyorum.</p>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        &copy; <?= date("Y") ?> Murat | Tüm hakları saklıdır.
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
