<?php
// index.php

// 1) DATABASE CONNECTION
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
    die("Database connection error: " . $e->getMessage());
}

// 2) UTILITY: GET LAST ROW FROM TABLE
function getLastRow(PDO $pdo, string $table) {
    $stmt = $pdo->query("SELECT * FROM `{$table}` ORDER BY id DESC LIMIT 1");
    return $stmt->fetch();
}

// 3) FETCH DATA FOR EACH SECTION
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
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code&family=Inter:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #E5E7EB; /* Light text */
            scroll-behavior: smooth;
            background: linear-gradient(240deg, #0065ff, #FF61A6, #ffa600);
            animation: gradient 25s ease infinite; /* Slow down animation duration */
            background-size: 600% 600%; /* Allow for smooth gradient transitions */
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensure body is at least full view height */
            margin: 0; /* Remove margin */
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .navbar {
            background-color: rgba(31, 41, 55, 0.9); /* Semi-transparent background */
            backdrop-filter: blur(10px); /* Blur effect on the background */
        }

        .navbar-brand {
            color: #10B981 !important;
            font-weight: bold;
        }

        .nav-link {
            color: #FBBF24 !important;
            transition: color 0.5s;
        }

        .nav-link:hover {
            color: #FFFFFF !important;
        }

        .hero {
            height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            clip-path: polygon(0 0, 100% 0%, 100% 100%, 0 90%);
            position: relative;
            z-index: 1;
        }

        h1, h2 { font-family: 'Fira Code', monospace; }

        .section {
            padding: 30px 0;
            margin-top: 20px;
            opacity: 0;
            transition: opacity 0.5s ease, transform 0.5s ease;
            transform: translateY(30px);
            position: relative;
        }

        .section.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .card {
            background-color: #2A2E35; /* Darker color for better visibility */
            border: none;
            border-radius: 12px;
            color: #D1FAE5; /* Lighter text for cards */
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%; /* Ensuring equal height */
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
        }

        footer {
            background-color: #1F2937;
            color: #94A3B8;
            padding: 20px;
            text-align: center;
            margin-top: auto; /* Footer'ı en aşağıda tutar */
            z-index: 10; /* On top of sections */
            width: 100%; /* Full width */
        }

        .gallery-img {
            max-height: 250px;
            object-fit: cover; /* Cover the area while keeping aspect ratio */
            width: 100%; /* Ensures the image fills the card */
            border-radius: 12px;
        }

        .section-title {
            background: linear-gradient(90deg, #fff200, #23ff00); /* Colorful title */
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2rem;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#hero">Murat.dev</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon text-dark"></span>
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
        <p class="lead">Web Developer &amp; Software Developer</p>
        <a href="#whoami" class="btn btn-primary mt-3">Devam Et</a>
    </div>
</section>

<div class="container">

    <!-- Whoami -->
    <section id="whoami" class="section">
        <h2 class="text-center section-title mb-4">👋 Ben Kimim</h2>
        <div class="card p-4">
            <p><?= nl2br(htmlspecialchars($whoami['whoamiContent'] ?? 'Henüz metin eklenmedi.')) ?></p>
        </div>
    </section>

    <!-- Hakkımda -->
    <section id="about" class="section">
        <h2 class="text-center section-title mb-5">👨‍💼 Hakkımda</h2>
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
        <h2 class="text-center section-title mb-5">✍️ Blog</h2>
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
    <section id="gallery" class="section">
        <h2 class="text-center section-title mb-5">🖼️ Galeri</h2>
        <div class="row g-4">
            <div class="col-md-12 text-center">
                <div class="card p-3">
                    <h5><?= htmlspecialchars($gal['title'] ?? '') ?> (<?= htmlspecialchars($gal['type'] ?? '') ?>)</h5>
                    <?php if (!empty($gal['image_url'])): ?>
                        <img src="<?= htmlspecialchars($gal['image_url']) ?>" class="gallery-img mt-3" alt="Galeri">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- İletişim -->
    <section id="contact" class="section">
        <h2 class="text-center section-title mb-5">📬 İletişim</h2>
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

<script>
    const sections = document.querySelectorAll('.section');
    const links = document.querySelectorAll('.nav-link');

    // Sayfa yüklendiğinde hero section'ı göster
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelector('#hero').classList.add('visible');
    });

    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));

            // Tüm section'ları gizle
            sections.forEach(section => {
                section.classList.remove('visible');
            });

            // Hedef section'ı göster ve scroll et
            target.classList.add('visible');
            setTimeout(() => {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start' // Üst kısımla hizala
                });
            }, 50); // Kısa bir gecikme ekle
        });
    });

    // Scroll event listener'ı ekle (opsiyonel)
    window.addEventListener('scroll', () => {
        sections.forEach(section => {
            const rect = section.getBoundingClientRect();
            if(rect.top < window.innerHeight * 0.8 && rect.bottom >= 0) {
                section.classList.add('visible');
            }
        });
    });
</script>
</body>
</html>