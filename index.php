<?php
// index.php

// 1) VERİTABANI BAĞLANTISI
$host     = '217.195.207.215';
$port     = '3306';
$dbname   = 'dunyani1_Portfolio';
$username = 'murat';
$password = '81644936.Ma';
$charset  = 'utf8mb4';

$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// 2) UTİLİTY: TABLODAN EN SON SATIRI GETİR
function getLastRow(PDO $pdo, string $table) {
    $stmt = $pdo->query("SELECT * FROM `{$table}` ORDER BY id DESC LIMIT 1");
    return $stmt->fetch();
}

// 3) HER BÖLÜM İÇİN VERİLERİ ÇEK
$whoami   = getLastRow($pdo, 'whoami');
$contact  = getLastRow($pdo, 'contact');
$bio      = getLastRow($pdo, 'biography');
$intrests = getLastRow($pdo, 'interests');
$edu      = getLastRow($pdo, 'education_experience');
$ach      = getLastRow($pdo, 'achievements');
$ppost    = getLastRow($pdo, 'personal_posts');
$tnotes   = getLastRow($pdo, 'travel_notes');
$bfrec    = getLastRow($pdo, 'book_film_recommendations');
$tech     = getLastRow($pdo, 'tech_interests');
$gal      = getLastRow($pdo, 'gallery');
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Murat.dev | Yazılım & Web Geliştirici</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code&family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            color: #e2e8f0;
            scroll-behavior: smooth;
        }
        .navbar { background-color: #1e293b; }
        .navbar-brand, .nav-link { color: #e2e8f0 !important; }
        .hero {
            height: 80vh;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            display: flex; align-items: center; justify-content: center;
            text-align: center;
        }
        h1, h2, h5, h6 { font-family: 'Fira Code', monospace; }
        .section { padding: 60px 0; }
        .card {
            background-color: #1e293b;
            border: none; border-radius: 12px;
            color: #cbd5e1;
            transition: transform 0.3s ease;
        }
        .card:hover { transform: scale(1.02); }
        footer {
            background-color: #1e293b;
            color: #94a3b8;
            padding: 20px; text-align: center;
        }
        a { color: #38bdf8; text-decoration: none; }
        .btn-primary {
            background-color: #38bdf8; border: none;
        }
        .btn-primary:hover { background-color: #0ea5e9; }
        .gallery-img { max-height: 250px; object-fit: cover; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#hero">Murat.dev</a>
        <button class="navbar-toggler bg-light" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon text-white"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto gap-3">
                <li class="nav-item"><a class="nav-link" href="#hero">Ana Sayfa</a></li>
                <li class="nav-item"><a class="nav-link" href="#whoami">Ben Kimim</a></li>
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
    <div class="container text-white">
        <h1 class="display-4 fw-bold">Merhaba, ben Murat 👨‍💻</h1>
        <p class="lead">Yazılım & Web Geliştiricisi</p>
        <a href="#whoami" class="btn btn-primary mt-3">Devam Et</a>
    </div>
</section>

<div class="container">

    <!-- Whoami -->
    <section id="whoami" class="section">
        <h2 class="text-center mb-4">👋 Ben Kimim</h2>
        <div class="card p-4">
            <p><?= nl2br(htmlspecialchars($whoami['whoamiContent'] ?? 'Henüz metin eklenmedi.')) ?></p>
        </div>
    </section>

    <!-- Hakkımda -->
    <section id="about" class="section" style="background-color: #0f1a2a;">
        <h2 class="text-center mb-5">👨‍💼 Hakkımda</h2>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Biyografi</h5>
                    <p><?= nl2br(htmlspecialchars($bio['content'] ?? '')) ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5>İlgi Alanlarım</h5>
                    <p><?= nl2br(htmlspecialchars($intrests['content'] ?? '')) ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5>Eğitim &amp; Deneyim</h5>
                    <p><strong><?= htmlspecialchars($edu['title'] ?? '') ?></strong> @ <?= htmlspecialchars($edu['institution'] ?? '') ?></p>
                    <p><?= nl2br(htmlspecialchars($edu['description'] ?? '')) ?></p>
                    <small class="text-muted"><?= htmlspecialchars($edu['start_date'] ?? '') ?> → <?= htmlspecialchars($edu['end_date'] ?? '') ?></small>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog -->
    <section id="blog" class="section">
        <h2 class="text-center mb-5">✍️ Blog</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card p-3">
                    <h6>Kişisel Yazılar</h6>
                    <p><?= nl2br(htmlspecialchars($ppost['content'] ?? '')) ?></p>
                    <small class="text-muted"><?= htmlspecialchars($ppost['created_at'] ?? '') ?></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3">
                    <h6>Seyahat Notları</h6>
                    <p><?= nl2br(htmlspecialchars($tnotes['content'] ?? '')) ?></p>
                    <small class="text-muted"><?= htmlspecialchars($tnotes['created_at'] ?? '') ?></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3">
                    <h6>Kitap &amp; Film &amp; Dizi</h6>
                    <p><?= nl2br(htmlspecialchars($bfrec['content'] ?? '')) ?></p>
                    <small class="text-muted"><?= htmlspecialchars($bfrec['type'] ?? '') ?> • <?= htmlspecialchars($bfrec['created_at'] ?? '') ?></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3">
                    <h6>Teknoloji İlgi Alanlarım</h6>
                    <p><?= nl2br(htmlspecialchars($tech['content'] ?? '')) ?></p>
                    <small class="text-muted"><?= htmlspecialchars($tech['created_at'] ?? '') ?></small>
                </div>
            </div>
        </div>
    </section>

    <!-- Galeri -->
    <section id="gallery" class="section" style="background-color: #0f1a2a;">
        <h2 class="text-center mb-5">🖼️ Galeri</h2>
        <div class="row g-4">
            <div class="col-md-12 text-center">
                <div class="card p-3">
                    <h5><?= htmlspecialchars($gal['title'] ?? '') ?> (<?= htmlspecialchars($gal['type'] ?? '') ?>)</h5>
                    <?php if (!empty($gal['image_url'])): ?>
                        <img src="<?= htmlspecialchars($gal['image_url']) ?>" class="img-fluid gallery-img mt-3" alt="Galeri">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- İletişim -->
    <section id="contact" class="section">
        <h2 class="text-center mb-5">📬 İletişim</h2>
        <div class="card p-4">
            <p><strong>Telefon:</strong> <?= htmlspecialchars($contact['phone'] ?? '') ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($contact['email'] ?? '') ?></p>
            <p><strong>Adres:</strong> <?= nl2br(htmlspecialchars($contact['address'] ?? '')) ?></p>
            <p>
                <?php if ($contact['twitter']):  ?><a href="<?= htmlspecialchars($contact['twitter']) ?>">Twitter</a> <?php endif; ?>
                <?php if ($contact['linkedin']): ?><a href="<?= htmlspecialchars($contact['linkedin']) ?>">LinkedIn</a> <?php endif; ?>
                <?php if ($contact['instagram']): ?><a href="<?= htmlspecialchars($contact['instagram']) ?>">Instagram</a> <?php endif; ?>
            </p>
        </div>
    </section>

</div>

<footer>
    <p>&copy; <?= date('Y') ?> Murat.dev</p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
