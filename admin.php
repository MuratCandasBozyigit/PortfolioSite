<?php
session_start();

// Veritabanı bağlantı bilgileri
$host = '217.195.207.215';
$port = '3306';
$dbname = 'dunyani1_Portfolio';
$username = 'murat';
$password = '81644936.Ma';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    initializeDatabase($pdo);
} catch (PDOException $e) {
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}

function initializeDatabase($pdo) {
    $queries = [
        // Kullanıcılar
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        )",
        // Ben Kimim
        "CREATE TABLE IF NOT EXISTS whoami (
            id INT AUTO_INCREMENT PRIMARY KEY,
            whoamiContent TEXT NOT NULL
        )",

        // İletişim (GÜNCELLENMİŞ)
        "CREATE TABLE IF NOT EXISTS contact (
            id INT AUTO_INCREMENT PRIMARY KEY,
            phone VARCHAR(20),
            email VARCHAR(255),
            address TEXT,
            twitter VARCHAR(255),
            linkedin VARCHAR(255),
            instagram VARCHAR(255)
        )",

        // Hakkımda Tabloları
        "CREATE TABLE IF NOT EXISTS biography (
            id INT AUTO_INCREMENT PRIMARY KEY,
            content TEXT NOT NULL
        )",
        "CREATE TABLE IF NOT EXISTS interests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            interest VARCHAR(255) NOT NULL
        )",
        "CREATE TABLE IF NOT EXISTS education_experience (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL, 
            institution VARCHAR(255) NOT NULL,
            start_date DATE,
            end_date DATE,
            description TEXT
        )",
        "CREATE TABLE IF NOT EXISTS achievements (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            issuer VARCHAR(255),
            date DATE,
            description TEXT
        )",
        //BLOG
        "CREATE TABLE IF NOT EXISTS personal_posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS travel_notes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS book_film_recommendations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS tech_interests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        // Galeri
        "CREATE TABLE IF NOT EXISTS gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100),
            image_url TEXT
        )",


    ];

    foreach ($queries as $query) {
        $pdo->exec($query);
    }
}
// Giriş kontrolü
if (!isset($_SESSION['admin'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = $_POST['username'] ?? '';
        $pass = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$user]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($pass, $admin['password'])) {
            $_SESSION['admin'] = $admin['username'];
            header("Location: admin.php");
            exit;
        } else {
            $error = "Hatalı kullanıcı adı veya şifre!";
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title>Giriş Yap</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header text-center">
                        <h4>Yönetici Girişi</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Kullanıcı Adı</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Şifre</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>

    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .animated-btn {
            transition: all 0.3s ease;
        }
        .animated-btn:hover {
            transform: scale(1.05);
        }
        .card-body {
            padding: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .section-collapse {
            margin-bottom: 2rem;
        }
    </style>
</head>
<body class="bg-light p-4">


<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_whoami') {
    header('Content-Type: application/json'); // <--- Bunu ekle
    $content = trim($_POST['whoami_text'] ?? '');

    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO whoami (whoamiContent) VALUES (?)");
        if ($stmt->execute([$content])) {
            echo json_encode(['status' => 'success', 'message' => 'Kaydedildi.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Boş içerik gönderilemez.']);
    }
    exit;
}

?>


<div class="container ">

    <!-- BEN KİMİM -->
    <div class="section-collapse">
        <button class="btn btn-outline-primary animated-btn w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#whoamiSection">
            🧑‍💼 Ben Kimim?
        </button>
        <div class="collapse" id="whoamiSection">
            <div class="card card-body">
                <form id="whoamiForm">
                    <textarea name="whoami_text" class="form-control mb-2" rows="5" placeholder="Kendinizi tanıtın..." required></textarea>
                    <button type="submit" class="btn btn-success">Kaydet</button>
                </form>
                <div id="whoamiMessage" class="mt-2"></div>
            </div>
        </div>
    </div>


    <!-- HAKKIMDA BÖLÜMÜ -->
    <div class="section-collapse">
        <button class="btn btn-outline-primary animated-btn w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#aboutSection">
            ℹ️ Hakkımda Bölümünü Aç/Kapat
        </button>
        <div class="collapse" id="aboutSection">
            <div class="card card-body">
                <div class="accordion" id="aboutAccordion">

                    <!-- Biyografi -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#bioCollapse">
                                🧬 Biyografi
                            </button>
                        </h2>
                        <div id="bioCollapse" class="accordion-collapse collapse" data-bs-parent="#aboutAccordion">
                            <div class="accordion-body">
                                <form method="post">
                                    <textarea name="about_biography" class="form-control mb-2" rows="4" placeholder="Kendinizden bahsedin..." required></textarea>
                                    <button type="submit" name="save_biography" class="btn btn-success">Kaydet</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- İlgi Alanlarım -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#interestsCollapse">
                                🎯 İlgi Alanlarım
                            </button>
                        </h2>
                        <div id="interestsCollapse" class="accordion-collapse collapse" data-bs-parent="#aboutAccordion">
                            <div class="accordion-body">
                                <form method="post">
                                    <textarea name="about_interests" class="form-control mb-2" rows="4" placeholder="Hobileriniz, tutkularınız..." required></textarea>
                                    <button type="submit" name="save_interests" class="btn btn-success">Kaydet</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Eğitim ve Deneyim -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#eduExpCollapse">
                                🎓 Eğitim ve Deneyim
                            </button>
                        </h2>
                        <div id="eduExpCollapse" class="accordion-collapse collapse" data-bs-parent="#aboutAccordion">
                            <div class="accordion-body">
                                <form method="post">
                                    <div class="mb-2">
                                        <input type="text" name="edu_title" class="form-control" placeholder="Başlık (örn. Bilgisayar Mühendisliği)" required>
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" name="edu_institution" class="form-control" placeholder="Kurum (örn. Boğaziçi Üniversitesi)" required>
                                    </div>
                                    <div class="mb-2">
                                        <label>Başlangıç Tarihi</label>
                                        <input type="date" name="edu_start_date" class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label>Bitiş Tarihi</label>
                                        <input type="date" name="edu_end_date" class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="edu_description" class="form-control" rows="3" placeholder="Açıklama (örn. 4 yıllık lisans programı...)"></textarea>
                                    </div>
                                    <button type="submit" name="save_education" class="btn btn-success">Kaydet</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sertifikalar ve Başarılar -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#certCollapse">
                                🏆 Başarılar & Sertifikalar
                            </button>
                        </h2>
                        <div id="certCollapse" class="accordion-collapse collapse" data-bs-parent="#aboutAccordion">
                            <div class="accordion-body">
                                <form method="post">
                                    <div class="mb-2">
                                        <input type="text" name="ach_title" class="form-control" placeholder="Sertifika/Başarı Başlığı (örn. Google Developer Sertifikası)" required>
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" name="ach_issuer" class="form-control" placeholder="Veren Kurum (örn. Google)">
                                    </div>
                                    <div class="mb-2">
                                        <label>Veriliş Tarihi</label>
                                        <input type="date" name="ach_date" class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="ach_description" class="form-control" rows="3" placeholder="Açıklama (örn. Bulut teknolojileri üzerine 6 haftalık program...)"></textarea>
                                    </div>
                                    <button type="submit" name="save_achievement" class="btn btn-success">Kaydet</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div> <!-- aboutAccordion -->
            </div>
        </div>
    </div>

    <!-- BLOG BÖLÜMÜ -->
    <div class="section-collapse">
        <button class="btn btn-outline-primary animated-btn w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#blogSection">
            📝 Blog Bölümünü Aç/Kapat
        </button>
        <div class="collapse" id="blogSection">
            <div class="card card-body">
                <div class="accordion" id="blogAccordion">

                    <!-- Kişisel Yazılar -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#personalCollapse">
                                🧠 Kişisel Yazılar
                            </button>
                        </h2>
                        <div id="personalCollapse" class="accordion-collapse collapse" data-bs-parent="#blogAccordion">
                            <div class="accordion-body">
                                <form method="post">
                                    <div class="mb-2">
                                        <input type="text" name="personal_title" class="form-control" placeholder="Yazı Başlığı" required>
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="personal_content" class="form-control" rows="4" placeholder="İçerik" required></textarea>
                                    </div>
                                    <button type="submit" name="save_personal" class="btn btn-success">Kaydet</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Seyahat Notları -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#travelCollapse">
                                🌍 Seyahat Notları
                            </button>
                        </h2>
                        <div id="travelCollapse" class="accordion-collapse collapse" data-bs-parent="#blogAccordion">
                            <div class="accordion-body">
                                <form method="post">
                                    <div class="mb-2">
                                        <input type="text" name="travel_title" class="form-control" placeholder="Yazı Başlığı" required>
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="travel_content" class="form-control" rows="4" placeholder="İçerik" required></textarea>
                                    </div>
                                    <button type="submit" name="save_travel" class="btn btn-success">Kaydet</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Kitap & Film Önerileri -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#bookFilmCollapse">
                                📚 Kitap & Film Önerileri
                            </button>
                        </h2>
                        <div id="bookFilmCollapse" class="accordion-collapse collapse" data-bs-parent="#blogAccordion">
                            <div class="accordion-body">
                                <form method="post">
                                    <div class="mb-2">
                                        <input type="text" name="book_film_title" class="form-control" placeholder="Başlık" required>
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="book_film_content" class="form-control" rows="4" placeholder="İçerik" required></textarea>
                                    </div>
                                    <button type="submit" name="save_book_film" class="btn btn-success">Kaydet</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Teknoloji & İlgi Alanları -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#techCollapse">
                                💻 Teknoloji & İlgi Alanları
                            </button>
                        </h2>
                        <div id="techCollapse" class="accordion-collapse collapse" data-bs-parent="#blogAccordion">
                            <div class="accordion-body">
                                <form method="post">
                                    <div class="mb-2">
                                        <input type="text" name="tech_title" class="form-control" placeholder="Başlık" required>
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="tech_content" class="form-control" rows="4" placeholder="İçerik" required></textarea>
                                    </div>
                                    <button type="submit" name="save_tech" class="btn btn-success">Kaydet</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div> <!-- blogAccordion -->
            </div>
        </div>
    </div>

    <!-- İLETİŞİM -->
    <div class="section-collapse">
        <button class="btn btn-outline-primary animated-btn w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#contactSection">
            ✉️ İletişim Bilgileri
        </button>
        <div class="collapse" id="contactSection">
            <div class="card card-body">
                <form method="post">
                    <input type="text" name="contact_phone" class="form-control mb-2" placeholder="Telefon" required>
                    <input type="email" name="contact_email" class="form-control mb-2" placeholder="E-Posta" required>
                    <textarea name="contact_address" class="form-control mb-2" rows="3" placeholder="Adres" required></textarea>

                    <input type="url" name="contact_twitter" class="form-control mb-2" placeholder="Twitter Linki (https://twitter.com/kullaniciadi)">
                    <input type="url" name="contact_linkedin" class="form-control mb-2" placeholder="LinkedIn Linki (https://www.linkedin.com/in/kullaniciadi)">
                    <input type="url" name="contact_instagram" class="form-control mb-2" placeholder="Instagram Linki (https://instagram.com/kullaniciadi)">

                    <button type="submit" name="save_contact" class="btn btn-success">Kaydet</button>
                </form>
            </div>
        </div>
    </div>

    <!-- GALERİ BÖLÜMÜ -->
    <div class="section-collapse">
        <button class="btn btn-outline-primary animated-btn w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#gallerySection">
            🖼️ Galeri Bölümünü Aç/Kapat
        </button>
        <div class="collapse" id="gallerySection">
            <div class="card card-body">
                <div class="accordion" id="galleryAccordion">

                    <!-- Fotoğraflarım -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#photoCollapse">
                                📷 Fotoğraflarım
                            </button>
                        </h2>
                        <div id="photoCollapse" class="accordion-collapse collapse" data-bs-parent="#galleryAccordion">
                            <div class="accordion-body">
                                <form method="post" enctype="multipart/form-data">
                                    <input type="file" name="gallery_photos[]" class="form-control mb-2" multiple required>
                                    <button type="submit" name="save_photos" class="btn btn-success">Yükle</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Hobilerim ile İlgili Görseller -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#hobbyImgCollapse">
                                🧩 Hobilerim ile İlgili Görseller
                            </button>
                        </h2>
                        <div id="hobbyImgCollapse" class="accordion-collapse collapse" data-bs-parent="#galleryAccordion">
                            <div class="accordion-body">
                                <form method="post" enctype="multipart/form-data">
                                    <input type="file" name="gallery_hobbies[]" class="form-control mb-2" multiple required>
                                    <button type="submit" name="save_hobbies" class="btn btn-success">Yükle</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Video & Multimedya -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#videoCollapse">
                                🎥 Video & Multimedya
                            </button>
                        </h2>
                        <div id="videoCollapse" class="accordion-collapse collapse" data-bs-parent="#galleryAccordion">
                            <div class="accordion-body">
                                <form method="post" enctype="multipart/form-data">
                                    <input type="file" name="gallery_videos[]" class="form-control mb-2" multiple required>
                                    <button type="submit" name="save_videos" class="btn btn-success">Yükle</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div> <!-- galleryAccordion -->
            </div>
        </div>
    </div>

</div> <!-- /container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    //AJAX
    document.getElementById('whoamiForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        formData.append('action', 'save_whoami');

        fetch('admin.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json()) // JSON olarak al
            .then(data => {
                const msg = document.getElementById('whoamiMessage');
                if (data.status === 'success') {
                    msg.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    form.reset();
                } else {
                    msg.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Hata:', error);
                document.getElementById('whoamiMessage').innerHTML = `<div class="alert alert-danger">Bir hata oluştu.</div>`;
            });

    });
</script>

</body>
</html>
