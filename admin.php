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


// WHOAMI KAYDET
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_whoami') {
    $content = trim($_POST['whoami_text']);
    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO whoami (whoamiContent) VALUES (:content)");
        $stmt->execute(['content' => $content]);
        echo json_encode(['status' => 'success', 'message' => 'Başarıyla kaydedildi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Boş içerik gönderilemez.']);
    }
    exit;
}
// WHOAMI GETİR
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_whoami') {
    header('Content-Type: application/json');
    $stmt = $pdo->query("SELECT id, whoamiContent FROM whoami ORDER BY id DESC");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        echo json_encode(['status' => 'success', 'data' => $results]);
    } else {
        echo json_encode(['status' => 'empty', 'data' => []]);
    }
    exit;
}
// WHOAMI GÜNCELLE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_whoami') {
    $id = (int) $_POST['id'];
    $content = trim($_POST['content']);

    if ($id && $content) {
        $stmt = $pdo->prepare("UPDATE whoami SET whoamiContent = :content WHERE id = :id");
        $stmt->execute(['content' => $content, 'id' => $id]);
        echo json_encode(['status' => 'success', 'message' => 'Kayıt güncellendi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Eksik veri.']);
    }
    exit;
}
// WHOAMI SİL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_whoami') {
    $id = (int) $_POST['id'];

    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM whoami WHERE id = :id");
        $stmt->execute(['id' => $id]);
        echo json_encode(['status' => 'success', 'message' => 'Kayıt silindi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID bulunamadı.']);
    }
    exit;
}

// İLETİŞİM BİLGİLERİ KAYDET
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_contact') {
    $phone = trim($_POST['contact_phone']);
    $email = trim($_POST['contact_email']);
    $address = trim($_POST['contact_address']);
    $twitter = trim($_POST['contact_twitter']);
    $linkedin = trim($_POST['contact_linkedin']);
    $instagram = trim($_POST['contact_instagram']);

    if ($phone && $email && $address) {
        $stmt = $pdo->prepare("INSERT INTO contact (phone, email, address, twitter, linkedin, instagram) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$phone, $email, $address, $twitter, $linkedin, $instagram]);
        echo json_encode(['status' => 'success', 'message' => 'İletişim bilgileri kaydedildi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Zorunlu alanlar boş bırakılamaz.']);
    }
    exit;
}
// İLETİŞİM BİLGİLERİ GETİR
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_contact') {
    header('Content-Type: application/json');
    $stmt = $pdo->query("SELECT * FROM contact ORDER BY id DESC");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $results]);
    exit;
}
// İLETİŞİM BİLGİSİ GÜNCELLE (DÜZELTİLMİŞ)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_contact') {
    header('Content-Type: application/json');
    try {
        $id = (int)$_POST['id'];
        $phone = trim($_POST['contact_phone']);
        $email = trim($_POST['contact_email']);
        $address = trim($_POST['contact_address']);
        $twitter = trim($_POST['contact_twitter']);
        $linkedin = trim($_POST['contact_linkedin']);
        $instagram = trim($_POST['contact_instagram']);

        if ($id && $phone && $email && $address) {
            $stmt = $pdo->prepare("UPDATE contact SET phone=?, email=?, address=?, twitter=?, linkedin=?, instagram=? WHERE id=?");
            $stmt->execute([$phone, $email, $address, $twitter, $linkedin, $instagram, $id]);
            echo json_encode(['status' => 'success', 'message' => 'Kayıt güncellendi.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Zorunlu alanlar boş olamaz!']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Güncelleme hatası: ' . $e->getMessage()]); // Hata mesajını göster
    }
    exit;
}
// İLETİŞİM BİLGİSİ SİL (DÜZELTİLMİŞ)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_contact') {
    header('Content-Type: application/json');
    try {
        $id = (int)$_POST['id'];
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM contact WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'Kayıt silindi.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID eksik.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Silme hatası: ' . $e->getMessage()]); // Hata mesajını göster
    }
    exit;
}




// BIOGRAPHY KAYDET
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_biography') {
    $content = trim($_POST['content']);
    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO biography (content) VALUES (:content)");
        $stmt->execute(['content' => $content]);
        echo json_encode(['status' => 'success', 'message' => 'Biyografi başarıyla kaydedildi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Biyografi alanı boş olamaz.']);
    }
    exit;
}

// BIOGRAPHY GETİR
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_biography') {
    header('Content-Type: application/json');
    $stmt = $pdo->query("SELECT id, content FROM biography ORDER BY id DESC");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        echo json_encode(['status' => 'success', 'data' => $results]);
    } else {
        echo json_encode(['status' => 'empty', 'data' => []]);
    }
    exit;
}

// BIOGRAPHY GÜNCELLE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_biography') {
    $id = (int) $_POST['id'];
    $content = trim($_POST['content']);

    if ($id && $content) {
        $stmt = $pdo->prepare("UPDATE biography SET content = :content WHERE id = :id");
        $stmt->execute(['content' => $content, 'id' => $id]);
        echo json_encode(['status' => 'success', 'message' => 'Biyografi güncellendi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Eksik veri.']);
    }
    exit;
}

// BIOGRAPHY SİL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_biography') {
    $id = (int) $_POST['id'];

    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM biography WHERE id = :id");
        $stmt->execute(['id' => $id]);
        echo json_encode(['status' => 'success', 'message' => 'Biyografi silindi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID bulunamadı.']);
    }
    exit;
}

// İlgi Alanlarım - KAYDET
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_interests') {
    $content = trim($_POST['interests_text']);
    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO interests (content) VALUES (:content)");
        $stmt->execute(['content' => $content]);
        echo json_encode(['status' => 'success', 'message' => 'İlgi alanı başarıyla kaydedildi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Boş içerik gönderilemez.']);
    }
    exit;
}

// İlgi Alanlarım - GETİR
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_interests') {
    header('Content-Type: application/json');
    $stmt = $pdo->query("SELECT id, content FROM interests ORDER BY id DESC");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => $results ? 'success' : 'empty',
        'data' => $results
    ]);
    exit;
}

// İlgi Alanlarım - GÜNCELLE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_interests') {
    $id = (int) $_POST['id'];
    $content = trim($_POST['content']);

    if ($id && $content) {
        $stmt = $pdo->prepare("UPDATE interests SET content = :content WHERE id = :id");
        $stmt->execute(['content' => $content, 'id' => $id]);
        echo json_encode(['status' => 'success', 'message' => 'Kayıt güncellendi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Eksik veri.']);
    }
    exit;
}

// İlgi Alanlarım - SİL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_interests') {
    $id = (int) $_POST['id'];

    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM interests WHERE id = :id");
        $stmt->execute(['id' => $id]);
        echo json_encode(['status' => 'success', 'message' => 'Kayıt silindi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID bulunamadı.']);
    }
    exit;
}

// Eğitim ve Deneyim - EKLE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_education') {
    $title = trim($_POST['edu_title']);
    $institution = trim($_POST['edu_institution']);
    $start_date = $_POST['edu_start_date'] ?: null;
    $end_date = $_POST['edu_end_date'] ?: null;
    $description = trim($_POST['edu_description']);

    if ($title && $institution) {
        $stmt = $pdo->prepare("INSERT INTO education_experience (title, institution, start_date, end_date, description) VALUES (:title, :institution, :start_date, :end_date, :description)");
        $stmt->execute([
            'title' => $title,
            'institution' => $institution,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'description' => $description
        ]);
        echo json_encode(['status' => 'success', 'message' => 'Eğitim/deneyim başarıyla eklendi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Zorunlu alanlar eksik.']);
    }
    exit;
}

// Eğitim ve Deneyim - GETİR
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_education') {
    header('Content-Type: application/json');
    $stmt = $pdo->query("SELECT * FROM education_experience ORDER BY start_date DESC");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => $results ? 'success' : 'empty', 'data' => $results]);
    exit;
}

// Eğitim ve Deneyim - GÜNCELLE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_education')  {
    $id = (int) $_POST['id'];
    $title = trim($_POST['title']);
    $institution = trim($_POST['institution']);
    $start_date = $_POST['start_date'] ?: null;
    $end_date = $_POST['end_date'] ?: null;
    $description = trim($_POST['description']);

    if ($id && $title && $institution) {
        $stmt = $pdo->prepare("UPDATE education_experience SET title = :title, institution = :institution, start_date = :start_date, end_date = :end_date, description = :description WHERE id = :id");
        $stmt->execute([
            'id' => $id,
            'title' => $title,
            'institution' => $institution,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'description' => $description
        ]);
        echo json_encode(['status' => 'success', 'message' => 'Kayıt güncellendi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Eksik veri.']);
    }
    exit;
}

// Eğitim ve Deneyim - SİL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_education') {
    $id = (int) $_POST['id'];
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM education_experience WHERE id = :id");
        $stmt->execute(['id' => $id]);
        echo json_encode(['status' => 'success', 'message' => 'Kayıt silindi.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz ID.']);
    }
    exit;
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
            content TEXT NOT NULL
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




<div class="container ">

    <!-- WHOAMI Bölümü -->
    <div class="section-collapse">
        <button class="btn btn-outline-primary animated-btn w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#whoamiSection">
            🧑‍💼 Ben Kimim?
        </button>
        <div class="collapse" id="whoamiSection">
            <div class="card card-body">
                <form id="whoamiForm">
                    <textarea name="whoami_text" id="whoami_textarea" class="form-control mb-2" rows="5" placeholder="Kendinizi tanıtın..." required></textarea>
                    <button type="submit" class="btn btn-success">Kaydet</button>
                </form>
                <div id="whoamiMessage" class="mt-2"></div>

                <hr>
                <h5 class="mt-3">🗂 Önceki Kayıtlar</h5>
                <ul id="whoamiList" class="list-group mt-2"></ul>
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

                    <!-- 🧬 Biyografi -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#bioCollapse">
                                🧬 Biyografi
                            </button>
                        </h2>
                        <div id="bioCollapse" class="accordion-collapse collapse" data-bs-parent="#aboutAccordion">
                            <div class="accordion-body">
                                <form id="biographyForm">
                                    <textarea name="content" id="biography_textarea" class="form-control mb-2" rows="4" placeholder="Kendinizden bahsedin..." required></textarea>
                                    <button type="submit" class="btn btn-success">Kaydet</button>
                                </form>
                                <div id="biographyMessage" class="mt-2"></div>

                                <hr>
                                <h5>📜 Kayıtlı Biyografiler</h5>
                                <ul id="biographyList" class="list-group mt-2"></ul>
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
                                <form id="interestsForm">
                                    <textarea name="interests_text" id="interests_textarea" class="form-control mb-2" rows="4" placeholder="Hobileriniz, tutkularınız..." required></textarea>
                                    <button type="submit" class="btn btn-success">Kaydet</button>
                                </form>
                                <div id="interestsMessage" class="mt-2"></div>
                                <hr>
                                <h6>📌 Önceki Kayıtlar</h6>
                                <ul id="interestsList" class="list-group mt-2"></ul>
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
                                <form id="educationForm">
                                    <input type="hidden" name="action" value="save_education">
                                    <div class="mb-2">
                                        <input type="text" name="edu_title" class="form-control" placeholder="Başlık" required>
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" name="edu_institution" class="form-control" placeholder="Kurum" required>
                                    </div>
                                    <div class="mb-2">
                                        <input type="date" name="edu_start_date" class="form-control" placeholder="Başlangıç Tarihi">
                                    </div>
                                    <div class="mb-2">
                                        <input type="date" name="edu_end_date" class="form-control" placeholder="Bitiş Tarihi">
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="edu_description" class="form-control" rows="3" placeholder="Açıklama"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success">Kaydet</button>
                                </form>

                                <!-- Bootstrap mesaj yeri -->
                                <div id="eduMessage" class="mt-2"></div>

                                <!-- Listeleme alanı -->
                                <ul id="educationList" class="list-group mt-3"></ul>
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
                <form id="contactForm">
                    <input type="text" name="contact_phone" class="form-control mb-2" placeholder="Telefon" required>
                    <input type="email" name="contact_email" class="form-control mb-2" placeholder="E-Posta" required>
                    <textarea name="contact_address" class="form-control mb-2" rows="3" placeholder="Adres" required></textarea>
                    <input type="url" name="contact_twitter" class="form-control mb-2" placeholder="Twitter Linki">
                    <input type="url" name="contact_linkedin" class="form-control mb-2" placeholder="LinkedIn Linki">
                    <input type="url" name="contact_instagram" class="form-control mb-2" placeholder="Instagram Linki">
                    <button type="submit" class="btn btn-success">Kaydet</button>
                </form>
                <div id="contactMessage" class="mt-2"></div>
                <hr>
                <h5>📜 Kayıtlı İletişim Bilgileri</h5>
                <ul id="contactList" class="list-group mt-2"></ul>
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
<!-- WHOAMI SCRİPT -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('whoamiForm');
        const textarea = document.getElementById('whoami_textarea');
        const msg = document.getElementById('whoamiMessage');
        const list = document.getElementById('whoamiList');

        function loadWhoamiList() {
            fetch('admin.php?action=get_whoami')
                .then(res => res.json())
                .then(data => {
                    list.innerHTML = ''; // Önce temizle
                    if (data.status === 'success') {
                        data.data.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item d-flex justify-content-between align-items-center';

                            const contentDiv = document.createElement('div');
                            contentDiv.textContent = item.whoamiContent;

                            const buttonGroup = document.createElement('div');
                            buttonGroup.className = 'btn-group btn-group-sm';

                            const updateBtn = document.createElement('button');
                            updateBtn.textContent = 'Güncelle';
                            updateBtn.className = 'btn btn-warning';
                            updateBtn.onclick = function () {
                                const newContent = prompt('Yeni içerik:', item.whoamiContent);
                                if (newContent !== null && newContent.trim() !== '') {
                                    updateWhoami(item.id, newContent);
                                }
                            };

                            const deleteBtn = document.createElement('button');
                            deleteBtn.textContent = 'Sil';
                            deleteBtn.className = 'btn btn-danger';
                            deleteBtn.onclick = function () {
                                if (confirm('Bu kaydı silmek istediğinize emin misiniz?')) {
                                    deleteWhoami(item.id);
                                }
                            };

                            buttonGroup.appendChild(updateBtn);
                            buttonGroup.appendChild(deleteBtn);

                            li.appendChild(contentDiv);
                            li.appendChild(buttonGroup);
                            list.appendChild(li);
                        });

                        if (data.data.length > 0) {
                            textarea.value = data.data[0].whoamiContent;
                        }
                    } else {
                        list.innerHTML = '<li class="list-group-item text-muted">Hiç kayıt yok.</li>';
                    }
                })
                .catch(err => {
                    console.error('Veri çekme hatası:', err);
                    list.innerHTML = '<li class="list-group-item text-danger">Liste yüklenemedi.</li>';
                });
        }

        function updateWhoami(id, content) {
            const formData = new FormData();
            formData.append('action', 'update_whoami');
            formData.append('id', id);
            formData.append('content', content);

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    msg.innerHTML = `<div class="alert alert-${data.status === 'success' ? 'success' : 'danger'}">${data.message}</div>`;
                    loadWhoamiList();
                });
        }

        function deleteWhoami(id) {
            const formData = new FormData();
            formData.append('action', 'delete_whoami');
            formData.append('id', id);

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    msg.innerHTML = `<div class="alert alert-${data.status === 'success' ? 'success' : 'danger'}">${data.message}</div>`;
                    loadWhoamiList();
                });
        }

        // Sayfa yüklenince veriyi çek
        loadWhoamiList();

        // Form gönderildiğinde
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('action', 'save_whoami');

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        msg.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                        form.reset();
                        loadWhoamiList(); // Listeyi güncelle
                    } else {
                        msg.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                })
                .catch(err => {
                    console.error('Kayıt hatası:', err);
                    msg.innerHTML = `<div class="alert alert-danger">Bir hata oluştu.</div>`;
                });
        });
    });
</script>
<!--İLETİŞİM SCRİPT-->
<script>
    // GLOBAL SCOPE —> HER YERDEN ÇAĞRILABİLSİN
    function deleteContact(id) {
        if (!confirm('Bu kaydı silmek istediğinizden emin misiniz?')) return;

        const formData = new FormData();
        formData.append('action', 'delete_contact');
        formData.append('id', id);

        fetch('admin.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(resp => {
                const contactMsg = document.getElementById('contactMessage');
                contactMsg.innerHTML = `<div class="alert alert-${resp.status === 'success' ? 'success' : 'danger'}">${resp.message}</div>`;
                loadContacts(); // Listeyi yeniden yükle
            })
            .catch(err => console.error('Hata:', err));
    }

    function updateContact(data) {
        const phone = prompt("Telefon:", data.phone);
        const email = prompt("E-Posta:", data.email);
        const address = prompt("Adres:", data.address);
        const twitter = prompt("Twitter:", data.twitter || '');
        const linkedin = prompt("LinkedIn:", data.linkedin || '');
        const instagram = prompt("Instagram:", data.instagram || '');

        if (phone && email && address) {
            const formData = new FormData();
            formData.append('action', 'update_contact');
            formData.append('id', data.id);
            formData.append('contact_phone', phone);
            formData.append('contact_email', email);
            formData.append('contact_address', address);
            formData.append('contact_twitter', twitter);
            formData.append('contact_linkedin', linkedin);
            formData.append('contact_instagram', instagram);

            fetch('admin.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(result => {
                    const contactMsg = document.getElementById('contactMessage');
                    if (result.status === 'success') {
                        contactMsg.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                        loadContacts();
                    } else {
                        contactMsg.innerHTML = `<div class="alert alert-danger">Hata: ${result.message}</div>`;
                    }
                })
                .catch(error => console.error('Hata:', error));
        }
    }

    function loadContacts() {
        fetch('admin.php?action=get_contact')
            .then(res => res.json())
            .then(data => {
                const contactList = document.getElementById('contactList');
                contactList.innerHTML = '';
                if (data.status === 'success') {
                    data.data.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item';

                        li.innerHTML = `
                            <strong>📞 ${item.phone}</strong><br>
                            📧 ${item.email}<br>
                            📍 ${item.address}<br>
                            🌐 <a href="${item.twitter}" target="_blank">Twitter</a> |
                            <a href="${item.linkedin}" target="_blank">LinkedIn</a> |
                            <a href="${item.instagram}" target="_blank">Instagram</a><br>
                            <div class="btn-group mt-2">
                                <button class="btn btn-sm btn-warning" onclick='updateContact(${JSON.stringify(item)})'>Güncelle</button>
                                <button class="btn btn-sm btn-danger" onclick='deleteContact(${item.id})'>Sil</button>
                            </div>
                        `;
                        contactList.appendChild(li);
                    });
                } else {
                    contactList.innerHTML = '<li class="list-group-item text-muted">Hiç kayıt yok.</li>';
                }
            });
    }

    // DOMContentLoaded => Form gönderme ve ilk yükleme
    document.addEventListener('DOMContentLoaded', function () {
        const contactForm = document.getElementById('contactForm');
        const contactMsg = document.getElementById('contactMessage');

        loadContacts(); // İlk yüklemede

        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(contactForm);
            formData.append('action', 'save_contact');

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    contactMsg.innerHTML = `<div class="alert alert-${data.status === 'success' ? 'success' : 'danger'}">${data.message}</div>`;
                    if (data.status === 'success') contactForm.reset();
                    loadContacts();
                });
        });
    });
</script>
<!--HAKKIMDA SCRİPT-->
<!--Biyografi-->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bioForm = document.getElementById('biographyForm');
        const bioTextarea = document.getElementById('biography_textarea');
        const bioMessage = document.getElementById('biographyMessage');
        const bioList = document.getElementById('biographyList');

        function loadBiographies() {
            fetch('admin.php?action=get_biography')
                .then(res => res.json())
                .then(data => {
                    bioList.innerHTML = '';
                    if (data.status === 'success') {
                        data.data.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item d-flex justify-content-between align-items-center';

                            const contentDiv = document.createElement('div');
                            contentDiv.textContent = item.content;

                            const btnGroup = document.createElement('div');
                            btnGroup.className = 'btn-group btn-group-sm';

                            const updateBtn = document.createElement('button');
                            updateBtn.textContent = 'Güncelle';
                            updateBtn.className = 'btn btn-warning';
                            updateBtn.onclick = function () {
                                const newContent = prompt('Yeni içerik:', item.content);
                                if (newContent !== null && newContent.trim() !== '') {
                                    updateBiography(item.id, newContent);
                                }
                            };

                            const deleteBtn = document.createElement('button');
                            deleteBtn.textContent = 'Sil';
                            deleteBtn.className = 'btn btn-danger';
                            deleteBtn.onclick = function () {
                                if (confirm('Bu kaydı silmek istiyor musunuz?')) {
                                    deleteBiography(item.id);
                                }
                            };

                            btnGroup.appendChild(updateBtn);
                            btnGroup.appendChild(deleteBtn);

                            li.appendChild(contentDiv);
                            li.appendChild(btnGroup);
                            bioList.appendChild(li);
                        });

                        if (data.data.length > 0) {
                            bioTextarea.value = data.data[0].content;
                        }
                    } else {
                        bioList.innerHTML = '<li class="list-group-item text-muted">Kayıt bulunamadı.</li>';
                    }
                })
                .catch(err => {
                    console.error('Biyografi verisi alınamadı:', err);
                    bioList.innerHTML = '<li class="list-group-item text-danger">Yüklenemedi.</li>';
                });
        }

        function updateBiography(id, content) {
            const formData = new FormData();
            formData.append('action', 'update_biography');
            formData.append('id', id);
            formData.append('content', content);

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    bioMessage.innerHTML = `<div class="alert alert-${data.status === 'success' ? 'success' : 'danger'}">${data.message}</div>`;
                    loadBiographies();
                });
        }

        function deleteBiography(id) {
            const formData = new FormData();
            formData.append('action', 'delete_biography');
            formData.append('id', id);

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    bioMessage.innerHTML = `<div class="alert alert-${data.status === 'success' ? 'success' : 'danger'}">${data.message}</div>`;
                    loadBiographies();
                });
        }

        // Sayfa yüklenince veriyi çek
        loadBiographies();

        // Form gönderimi
        bioForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(bioForm);
            formData.append('action', 'save_biography');

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    bioMessage.innerHTML = `<div class="alert alert-${data.status === 'success' ? 'success' : 'danger'}">${data.message}</div>`;
                    if (data.status === 'success') {
                        bioForm.reset();
                        loadBiographies();
                    }
                })
                .catch(err => {
                    console.error('Kayıt hatası:', err);
                    bioMessage.innerHTML = `<div class="alert alert-danger">Bir hata oluştu.</div>`;
                });
        });
    });
</script>
<!-- İlgi Alanlarım -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('interestsForm');
        const textarea = document.getElementById('interests_textarea');
        const msg = document.getElementById('interestsMessage');
        const list = document.getElementById('interestsList');

        function loadInterestsList() {
            fetch('admin.php?action=get_interests')
                .then(res => res.json())
                .then(data => {
                    list.innerHTML = '';
                    if (data.status === 'success') {
                        data.data.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item d-flex justify-content-between align-items-center';

                            const contentDiv = document.createElement('div');
                            contentDiv.textContent = item.content;

                            const buttonGroup = document.createElement('div');
                            buttonGroup.className = 'btn-group btn-group-sm';

                            const updateBtn = document.createElement('button');
                            updateBtn.className = 'btn btn-warning';
                            updateBtn.textContent = 'Güncelle';
                            updateBtn.onclick = () => {
                                const newContent = prompt('Yeni içerik:', item.content);
                                if (newContent && newContent.trim() !== '') {
                                    updateInterest(item.id, newContent);
                                }
                            };

                            const deleteBtn = document.createElement('button');
                            deleteBtn.className = 'btn btn-danger';
                            deleteBtn.textContent = 'Sil';
                            deleteBtn.onclick = () => {
                                if (confirm('Bu kaydı silmek istediğinize emin misiniz?')) {
                                    deleteInterest(item.id);
                                }
                            };

                            buttonGroup.appendChild(updateBtn);
                            buttonGroup.appendChild(deleteBtn);

                            li.appendChild(contentDiv);
                            li.appendChild(buttonGroup);
                            list.appendChild(li);
                        });

                        if (data.data.length > 0) {
                            textarea.value = data.data[0].content;
                        }
                    } else {
                        list.innerHTML = '<li class="list-group-item text-muted">Kayıt bulunamadı.</li>';
                    }
                })
                .catch(err => {
                    list.innerHTML = '<li class="list-group-item text-danger">Yükleme hatası!</li>';
                    console.error('Listeleme hatası:', err);
                });
        }

        function updateInterest(id, content) {
            const formData = new FormData();
            formData.append('action', 'update_interests');
            formData.append('id', id);
            formData.append('content', content);

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    msg.innerHTML = `<div class="alert alert-${data.status === 'success' ? 'success' : 'danger'}">${data.message}</div>`;
                    loadInterestsList();
                });
        }

        function deleteInterest(id) {
            const formData = new FormData();
            formData.append('action', 'delete_interests');
            formData.append('id', id);

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    msg.innerHTML = `<div class="alert alert-${data.status === 'success' ? 'success' : 'danger'}">${data.message}</div>`;
                    loadInterestsList();
                });
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('action', 'save_interests');

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    msg.innerHTML = `<div class="alert alert-${data.status === 'success' ? 'success' : 'danger'}">${data.message}</div>`;
                    if (data.status === 'success') {
                        form.reset();
                        loadInterestsList();
                    }
                })
                .catch(err => {
                    msg.innerHTML = `<div class="alert alert-danger">Bir hata oluştu.</div>`;
                    console.error('Kayıt hatası:', err);
                });
        });

        loadInterestsList(); // Sayfa açıldığında otomatik yükle
    });
</script>
<!-- Eğitim ve Deneyim -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('educationForm');
        const list = document.getElementById('educationList');
        const messageBox = document.getElementById('eduMessage');

        function showMessage(type, text) {
            messageBox.innerHTML = `<div class="alert alert-${type}">${text}</div>`;
            setTimeout(() => messageBox.innerHTML = '', 4000);
        }

        // Eğitim ekle
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    showMessage(data.status === 'success' ? 'success' : 'danger', data.message);
                    if (data.status === 'success') {
                        form.reset();
                        fetchEducation();
                    }
                });
        });

        // Listele
        function fetchEducation() {
            fetch('admin.php?action=get_education')
                .then(res => res.json())
                .then(data => {
                    list.innerHTML = '';
                    if (data.status === 'success') {
                        data.data.forEach(item => {
                            list.innerHTML += `
                            <li class="list-group-item d-flex justify-content-between align-items-start" data-id="${item.id}">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">${item.title} - ${item.institution}</div>
                                    ${item.start_date || ''} - ${item.end_date || ''}<br>
                                    ${item.description || ''}
                                </div>
                                <button class="btn btn-sm btn-warning me-1 update-btn">Güncelle</button>
                                <button class="btn btn-sm btn-danger delete-btn">Sil</button>
                            </li>
                        `;
                        });
                    } else {
                        list.innerHTML = '<li class="list-group-item">Kayıt bulunamadı.</li>';
                    }
                });
        }

        fetchEducation();

        // Sil
        list.addEventListener('click', function (e) {
            if (e.target.classList.contains('delete-btn')) {
                const li = e.target.closest('li');
                const id = li.dataset.id;
                const formData = new FormData();
                formData.append('action', 'delete_education');
                formData.append('id', id);
                fetch('admin.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        showMessage(data.status === 'success' ? 'success' : 'danger', data.message);
                        fetchEducation();
                    });
            }

            // Güncelle - inline form
            if (e.target.classList.contains('update-btn')) {
                const li = e.target.closest('li');
                const id = li.dataset.id;
                const contentDiv = li.querySelector('.ms-2');

                const [titleInst, dates, desc] = contentDiv.innerHTML.split('<br>');
                const [title, institution] = titleInst.replace(/<\/?div.*?>/g, '').split(' - ');

                contentDiv.innerHTML = `
                <input class="form-control mb-1" name="title" value="${title.trim()}">
                <input class="form-control mb-1" name="institution" value="${institution.trim()}">
                <input class="form-control mb-1" type="date" name="start_date">
                <input class="form-control mb-1" type="date" name="end_date">
                <textarea class="form-control mb-1" name="description">${desc.trim()}</textarea>
                <button class="btn btn-sm btn-success save-btn">Kaydet</button>
            `;

                li.querySelector('.update-btn').remove();
            }

            // Güncelle Kaydet
            if (e.target.classList.contains('save-btn')) {
                const li = e.target.closest('li');
                const id = li.dataset.id;
                const title = li.querySelector('input[name="title"]').value;
                const institution = li.querySelector('input[name="institution"]').value;
                const start_date = li.querySelector('input[name="start_date"]').value;
                const end_date = li.querySelector('input[name="end_date"]').value;
                const description = li.querySelector('textarea[name="description"]').value;

                const formData = new FormData();
                formData.append('action', 'update_education');
                formData.append('id', id);
                formData.append('title', title);
                formData.append('institution', institution);
                formData.append('start_date', start_date);
                formData.append('end_date', end_date);
                formData.append('description', description);

                fetch('admin.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        showMessage(data.status === 'success' ? 'success' : 'danger', data.message);
                        fetchEducation();
                    });
            }
        });
    });
</script>


</body>
</html>
