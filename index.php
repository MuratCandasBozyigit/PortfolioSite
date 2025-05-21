<?php
// index.php - Tüm sayfalar bir arada, scroll navigasyon ile
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kişisel Web Sitem</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        .hero {
            height: 100vh;
            background: linear-gradient(to right, #e0ecff, #fdfdfd);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        section {
            padding: 80px 0;
        }

        h2 {
            font-weight: 800;
        }

        .card {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        footer {
            background: #f9f9f9;
            padding: 30px;
            text-align: center;
            font-size: 0.9rem;
            color: #666;
        }

        .btn-scroll {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#hero">Murat'ın Portfolyosu</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="#hero">Ana Sayfa</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">Hakkımda</a></li>
                <li class="nav-item"><a class="nav-link" href="#blog">Blog</a></li>
                <li class="nav-item"><a class="nav-link" href="#gallery">Galeri</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">İletişim</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero -->
<section id="hero" class="hero">
    <div class="container">
        <h1>Merhaba, ben Murat 👋</h1>
        <p>Kişisel portfolyoma hoş geldiniz!</p>
        <a href="#about" class="btn btn-primary btn-lg btn-scroll">Hakkımda</a>
    </div>
</section>

<!-- Hakkımda -->
<section id="about">
    <div class="container">
        <h2 class="text-center mb-5">Hakkımda</h2>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card p-3">
                    <h5>Biyografi</h5>
                    <p>Kısa bir biyografi metni yer alacak.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <h5>İlgi Alanlarım</h5>
                    <p>Yazılım, müzik, sanat, gezi...</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <h5>Eğitim & Deneyim</h5>
                    <p>Okullar ve iş geçmişi bilgileri burada olacak.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <h5>Sertifikalar</h5>
                    <p>Aldığınız sertifikalar listelenebilir.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blog -->
<section id="blog" style="background-color: #f6f9fc;">
    <div class="container">
        <h2 class="text-center mb-5">Blog</h2>
        <div class="row g-4">
            <div class="col-md-3"><div class="card p-3"><h6>Kişisel Yazılar</h6><p>Blog post içerikleri buraya gelecek.</p></div></div>
            <div class="col-md-3"><div class="card p-3"><h6>Seyahat Notları</h6><p>Gezdiğin yerlerden notlar paylaş.</p></div></div>
            <div class="col-md-3"><div class="card p-3"><h6>Kitap & Film</h6><p>Önerdiğin eserleri paylaş.</p></div></div>
            <div class="col-md-3"><div class="card p-3"><h6>Teknoloji</h6><p>Geliştirdiğin şeyler veya incelemeler.</p></div></div>
        </div>
    </div>
</section>

<!-- Galeri -->
<section id="gallery">
    <div class="container">
        <h2 class="text-center mb-5">Galeri</h2>
        <div class="row g-4">
            <div class="col-md-4"><div class="card p-3"><h6>Fotoğraflarım</h6><p>Resim galerisi eklenecek.</p></div></div>
            <div class="col-md-4"><div class="card p-3"><h6>Hobilerim</h6><p>Hobi görselleri gösterilecek.</p></div></div>
            <div class="col-md-4"><div class="card p-3"><h6>Video & Multimedya</h6><p>Youtube videolar veya medya.</p></div></div>
        </div>
    </div>
</section>

<!-- İletişim -->
<section id="contact" style="background-color: #f6f9fc;">
    <div class="container">
        <h2 class="text-center mb-4">İletişim</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form>
                    <div class="mb-3">
                        <label>Ad Soyad</label>
                        <input type="text" class="form-control" placeholder="Adınız">
                    </div>
                    <div class="mb-3">
                        <label>E-Posta</label>
                        <input type="email" class="form-control" placeholder="E-posta adresiniz">
                    </div>
                    <div class="mb-3">
                        <label>Mesaj</label>
                        <textarea class="form-control" rows="4" placeholder="Mesajınız..."></textarea>
                    </div>
                    <button class="btn btn-primary">Gönder</button>
                </form>
                <div class="mt-4 text-center">
                    <a href="#">Instagram</a> •
                    <a href="#">GitHub</a> •
                    <a href="#">LinkedIn</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    &copy; <?= date("Y") ?> Murat | Tüm hakları saklıdır.
</footer>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
