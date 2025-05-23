<?php
session_start();

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
// WHOAMI CRUD
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

// İLETİŞİM BİLGİLERİ CRUD
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
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_contact') {
    header('Content-Type: application/json');
    $stmt = $pdo->query("SELECT * FROM contact ORDER BY id DESC");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $results]);
    exit;
}
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

// BIOGRAPHY CRUD
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

// İlgi Alanlarım CRUD
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

// Eğitim ve Deneyim CRUD
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
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_education') {
    header('Content-Type: application/json');
    $stmt = $pdo->query("SELECT * FROM education_experience ORDER BY start_date DESC");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => $results ? 'success' : 'empty', 'data' => $results]);
    exit;
}
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

//BAŞARIM CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    if ($action === 'create_achievement') {
        $title = $_POST['ach_title'] ?? '';
        $issuer = $_POST['ach_issuer'] ?? '';
        $date = $_POST['ach_date'] ?? null;
        $desc = $_POST['ach_description'] ?? '';

        $stmt = $pdo->prepare("INSERT INTO achievements (title, issuer, date, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $issuer, $date, $desc]);

        echo json_encode(['status' => 'success', 'message' => 'Kayıt başarıyla eklendi.']);
        exit;
    }

    if ($action === 'delete_achievement') {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM achievements WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'Kayıt silindi.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID belirtilmedi.']);
        }
        exit;
    }

    if ($action === 'update_achievement') {
        $id = $_POST['id'] ?? null;
        $title = $_POST['title'] ?? '';
        $issuer = $_POST['issuer'] ?? '';
        $date = $_POST['date'] ?? '';
        $desc = $_POST['description'] ?? '';

        if ($id) {
            $stmt = $pdo->prepare("UPDATE achievements SET title=?, issuer=?, date=?, description=? WHERE id=?");
            $stmt->execute([$title, $issuer, $date, $desc, $id]);
            echo json_encode(['status' => 'success', 'message' => 'Kayıt güncellendi.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID belirtilmedi.']);
        }
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? null;

    if ($action === 'get_achievements') {
        $stmt = $pdo->query("SELECT * FROM achievements ORDER BY date DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $data]);
        exit;
    }
}

// KİŞİSEL YAZI CRUD
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    // LİSTELEME
    if ($_GET['action'] === 'get_personal_posts') {
        $stmt = $pdo->query("SELECT * FROM personal_posts ORDER BY created_at DESC");
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'posts' => $posts]);
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    // EKLEME
    if ($_GET['action'] === 'save_personal_post') {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (empty($title) || empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Başlık ve içerik gereklidir!']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO personal_posts (title, content) VALUES (?, ?)");
            $stmt->execute([$title, $content]);
            echo json_encode(['status' => 'success', 'message' => 'Yazı kaydedildi!']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: '.$e->getMessage()]);
        }
        exit;
    }

    // GÜNCELLEME
    if ($_GET['action'] === 'update_personal_post') {
        $id = (int)$_POST['id'];
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        try {
            $stmt = $pdo->prepare("UPDATE personal_posts SET title=?, content=? WHERE id=?");
            $stmt->execute([$title, $content, $id]);
            echo json_encode(['status' => 'success', 'message' => 'Yazı güncellendi!']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Güncelleme hatası: '.$e->getMessage()]);
        }
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete_personal_post' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM personal_posts WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Yazı silindi!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Silme hatası: '.$e->getMessage()]);
    }
    exit;
}

// SEYAHAT NOTLARI CRUD
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    // LİSTELEME
    if ($_GET['action'] === 'get_travel_notes') {
        $stmt = $pdo->query("SELECT * FROM travel_notes ORDER BY created_at DESC");
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'notes' => $notes]);
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    // EKLEME
    if ($_GET['action'] === 'save_travel_note') {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (empty($title) || empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Başlık ve içerik gereklidir!']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO travel_notes (title, content) VALUES (?, ?)");
            $stmt->execute([$title, $content]);
            echo json_encode(['status' => 'success', 'message' => 'Not kaydedildi!']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: '.$e->getMessage()]);
        }
        exit;
    }

    // GÜNCELLEME
    if ($_GET['action'] === 'update_travel_note') {
        $id = (int)$_POST['id'];
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        try {
            $stmt = $pdo->prepare("UPDATE travel_notes SET title=?, content=? WHERE id=?");
            $stmt->execute([$title, $content, $id]);
            echo json_encode(['status' => 'success', 'message' => 'Not güncellendi!']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Güncelleme hatası: '.$e->getMessage()]);
        }
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete_travel_note' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM travel_notes WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Not silindi!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Silme hatası: '.$e->getMessage()]);
    }
    exit;
}

// KITAP & FILM ÖNERİLERİ CRUD
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    // LİSTELEME
    if ($_GET['action'] === 'get_book_film_recommendations') {
        $stmt = $pdo->query("SELECT * FROM book_film_recommendations ORDER BY created_at DESC");
        $recommendations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'recommendations' => $recommendations]);
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    // EKLEME
    if ($_GET['action'] === 'save_book_film_recommendation') {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $type = trim($_POST['type']);

        if (empty($title) || empty($content) || empty($type)) {
            echo json_encode(['status' => 'error', 'message' => 'Tüm alanlar zorunludur!']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO book_film_recommendations (title, content, type) VALUES (?, ?, ?)");
            $stmt->execute([$title, $content, $type]);
            echo json_encode(['status' => 'success', 'message' => 'Öneri başarıyla kaydedildi!']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: '.$e->getMessage()]);
        }
        exit;
    }

    // GÜNCELLEME
    if ($_GET['action'] === 'update_book_film_recommendation') {
        $id = (int)$_POST['id'];
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $type = trim($_POST['type']);

        try {
            $stmt = $pdo->prepare("UPDATE book_film_recommendations SET title=?, content=?, type=? WHERE id=?");
            $stmt->execute([$title, $content, $type, $id]);
            echo json_encode(['status' => 'success', 'message' => 'Öneri başarıyla güncellendi!']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Güncelleme hatası: '.$e->getMessage()]);
        }
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete_book_film_recommendation' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM book_film_recommendations WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Öneri başarıyla silindi!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Silme hatası: '.$e->getMessage()]);
    }
    exit;
}

// TEKNOLOJI İLGİ ALANLARI CRUD
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    // LİSTELEME
    if ($_GET['action'] === 'get_tech_interests') {
        $stmt = $pdo->query("SELECT * FROM tech_interests ORDER BY created_at DESC");
        $interests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'interests' => $interests]);
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    // EKLEME
    if ($_GET['action'] === 'save_tech_interest') {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (empty($title) || empty($content)) {
            echo json_encode(['status' => 'error', 'message' => 'Başlık ve içerik zorunludur!']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO tech_interests (title, content) VALUES (?, ?)");
            $stmt->execute([$title, $content]);
            echo json_encode(['status' => 'success', 'message' => 'İlgi alanı kaydedildi!']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: '.$e->getMessage()]);
        }
        exit;
    }

    // GÜNCELLEME
    if ($_GET['action'] === 'update_tech_interest') {
        $id = (int)$_POST['id'];
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        try {
            $stmt = $pdo->prepare("UPDATE tech_interests SET title=?, content=? WHERE id=?");
            $stmt->execute([$title, $content, $id]);
            echo json_encode(['status' => 'success', 'message' => 'İlgi alanı güncellendi!']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Güncelleme hatası: '.$e->getMessage()]);
        }
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete_tech_interest' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM tech_interests WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'İlgi alanı silindi!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Silme hatası: '.$e->getMessage()]);
    }
    exit;
}

// GALERİ CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'upload_gallery') {
    $type = $_POST['type'];
    $uploadedFiles = $_FILES[$type === 'videos' ? 'videos' : 'images'];
    $uploadDir = 'uploads/gallery/';

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $responses = [];

    foreach ($uploadedFiles['tmp_name'] as $key => $tmpName) {
        $fileName = uniqid() . '_' . basename($uploadedFiles['name'][$key]);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO gallery (title, type, image_url) VALUES (?, ?, ?)");
                $stmt->execute([
                    pathinfo($uploadedFiles['name'][$key], PATHINFO_FILENAME),
                    $type,
                    $targetPath
                ]);
                $responses[] = ['status' => 'success', 'file' => $fileName];
            } catch (PDOException $e) {
                $responses[] = ['status' => 'error', 'message' => $e->getMessage()];
            }
        } else {
            $responses[] = ['status' => 'error', 'message' => 'Dosya yüklenemedi'];
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => count($responses) . ' dosyadan ' .
            count(array_filter($responses, fn($r) => $r['status'] === 'success')) . ' tanesi başarıyla yüklendi'
    ]);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    // GALERİ ÖĞELERİNİ GETİR
    if ($_GET['action'] === 'get_gallery_items' && isset($_GET['type'])) {
        $type = $_GET['type'];
        $stmt = $pdo->prepare("SELECT * FROM gallery WHERE type = ? ORDER BY id DESC");
        $stmt->execute([$type]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'items' => $items]);
        exit;
    }

    // GALERİ ÖĞESİ SİLME
    if ($_GET['action'] === 'delete_gallery_item' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];

        try {
            // Önce dosyayı bul
            $stmt = $pdo->prepare("SELECT image_url FROM gallery WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();

            if ($item) {
                // Dosyayı sil
                if (file_exists($item['image_url'])) {
                    unlink($item['image_url']);
                }

                // Veritabanından sil
                $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
                $stmt->execute([$id]);

                echo json_encode(['status' => 'success', 'message' => 'Öğe silindi']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Öğe bulunamadı']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Silme hatası: ' . $e->getMessage()]);
        }
        exit;
    }
}

//SSS
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {

    // SSS Listele
    if ($_GET['action'] === 'get_faqs') {
        try {
            $stmt = $pdo->query("SELECT * FROM faq ORDER BY created_at DESC");
            $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'faqs' => $faqs]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Veriler alınamadı: ' . $e->getMessage()]);
        }
        exit;
    }

    // SSS Sil
    if ($_GET['action'] === 'delete_faq' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM faq WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'Soru başarıyla silindi!']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Silme başarısız: ' . $e->getMessage()]);
        }
        exit;
    }
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
        //SSS
        "CREATE TABLE IF NOT EXISTS faq (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            question TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
            type ENUM('book', 'film', 'series') NOT NULL,
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
            type ENUM('photos', 'hobies', 'videos') NOT NULL,
            image_url TEXT
        )",


    ];

    foreach ($queries as $query) {
        $pdo->exec($query);
    }
}
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

                                <div id="eduMessage" class="mt-2"></div>

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
                                <form id="achievementForm">
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
                                    <button type="submit" class="btn btn-success">Kaydet</button>
                                </form>

                                <!-- Liste buraya gelecek -->
                                <ul id="achievementList" class="list-group mt-3"></ul>
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
                                <form id="personalPostForm">
                                    <div class="mb-2">
                                        <input type="text" name="title" class="form-control" placeholder="Yazı Başlığı" required>
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="content" class="form-control" rows="4" placeholder="İçerik" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success">Kaydet</button>
                                </form>

                                <div class="mt-4">
                                    <h5>Kayıtlı Yazılar</h5>
                                    <ul id="personalPostsList" class="list-group"></ul>
                                </div>
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
                                <form id="travelNoteForm">
                                    <div class="mb-2">
                                        <input type="text" name="title" class="form-control" placeholder="Yazı Başlığı" required>
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="content" class="form-control" rows="4" placeholder="İçerik" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success">Kaydet</button>
                                </form>

                                <div class="mt-4">
                                    <h5>Kayıtlı Notlar</h5>
                                    <ul id="travelNotesList" class="list-group"></ul>
                                </div>
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
                                <form id="bookFilmForm">
                                    <div class="mb-2">
                                        <input type="text" name="title" class="form-control" placeholder="Başlık (Örnek: Yüzüklerin Efendisi)" required>
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="content" class="form-control" rows="4" placeholder="İçerik (Örnek: J.R.R. Tolkien'in epik fantastik serisi...)" required></textarea>
                                    </div>
                                    <div class="mb-2">
                                        <select name="type" class="form-select" required>
                                            <option value="">Tür Seçin</option>
                                            <option value="book">Kitap</option>
                                            <option value="film">Film</option>
                                            <option value="series">Dizi</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success">Kaydet</button>
                                </form>

                                <div class="mt-4">
                                    <h5>Kayıtlı Öneriler</h5>
                                    <ul id="bookFilmList" class="list-group"></ul>
                                </div>
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
                                <form id="techInterestForm">
                                    <div class="mb-2">
                                        <input type="text" name="title" class="form-control" placeholder="Başlık (Örnek: Yapay Zeka)" required>
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="content" class="form-control" rows="4" placeholder="İçerik (Örnek: Makine öğrenmesi ve derin öğrenme alanlarındaki gelişmeler...)" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success">Kaydet</button>
                                </form>

                                <div class="mt-4">
                                    <h5>Kayıtlı İlgi Alanları</h5>
                                    <ul id="techInterestsList" class="list-group"></ul>
                                </div>
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
                                <form id="photoForm" enctype="multipart/form-data">
                                    <input type="file" name="images[]" class="form-control mb-2" multiple required>
                                    <button type="submit" class="btn btn-success">Yükle</button>
                                </form>
                                <div class="mt-3" id="photosGallery"></div>
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
                                <form id="hobbyForm" enctype="multipart/form-data">
                                    <input type="file" name="images[]" class="form-control mb-2" multiple required>
                                    <button type="submit" class="btn btn-success">Yükle</button>
                                </form>
                                <div class="mt-3" id="hobbiesGallery"></div>
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
                                <form id="videoForm" enctype="multipart/form-data">
                                    <input type="file" name="videos[]" class="form-control mb-2" multiple required>
                                    <button type="submit" class="btn btn-success">Yükle</button>
                                </form>
                                <div class="mt-3" id="videosGallery"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- S.S.S (FAQ) -->
    <div class="section-collapse">
        <button class="btn btn-outline-primary animated-btn w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#faqSection">
            ❓ Sıkça Sorulan Sorular
        </button>
        <div class="collapse" id="faqSection">
            <div class="card card-body">
                <div id="faqMessage" class="mb-2"></div>
                <h5>📋 Gönderilmiş Sorular</h5>
                <ul id="faqList" class="list-group mt-2"></ul>
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

        function fetchEducation() {
            fetch('admin.php?action=get_education')
                .then(res => res.json())
                .then(data => {
                    list.innerHTML = '';
                    if (data.status === 'success') {
                        data.data.forEach(item => {
                            list.innerHTML += `
                        <li class="list-group-item d-flex justify-content-between align-items-start"
                            data-id="${item.id}"
                            data-title="${escapeHtml(item.title)}"
                            data-institution="${escapeHtml(item.institution)}"
                            data-start-date="${item.start_date || ''}"
                            data-end-date="${item.end_date || ''}"
                            data-description="${escapeHtml(item.description || '')}">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">${escapeHtml(item.title)} - ${escapeHtml(item.institution)}</div>
                                ${item.start_date || ''} - ${item.end_date || ''}<br>
                                ${escapeHtml(item.description || '')}
                            </div>
                            <button class="btn btn-sm btn-warning me-1 update-btn">Güncelle</button>
                            <button class="btn btn-sm btn-danger delete-btn">Sil</button>
                        </li>`;
                        });
                    } else {
                        list.innerHTML = '<li class="list-group-item">Kayıt bulunamadı.</li>';
                    }
                });
        }

        list.addEventListener('click', function (e) {
            const li = e.target.closest('li');
            if (!li) return;
            const id = li.dataset.id;

            if (e.target.classList.contains('delete-btn')) {
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

            if (e.target.classList.contains('update-btn')) {
                const contentDiv = li.querySelector('.ms-2');
                contentDiv.innerHTML = `
                <input class="form-control mb-1" name="title" value="${li.dataset.title}">
                <input class="form-control mb-1" name="institution" value="${li.dataset.institution}">
                <input class="form-control mb-1" type="date" name="start_date" value="${li.dataset.startDate}">
                <input class="form-control mb-1" type="date" name="end_date" value="${li.dataset.endDate}">
                <textarea class="form-control mb-1" name="description">${li.dataset.description}</textarea>
                <button class="btn btn-sm btn-success save-btn">Kaydet</button>
            `;
                li.querySelector('.update-btn').remove();
            }

            if (e.target.classList.contains('save-btn')) {
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

        // HTML escape fonksiyonu
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        fetchEducation();
    });
</script>
<!-- Sertifikalar ve Başarılar -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('achievementForm');
        const listContainer = document.getElementById('achievementList');

        function showMessage(type, text) {
            const msg = document.createElement('div');
            msg.className = `alert alert-${type}`;
            msg.innerText = text;
            form.insertAdjacentElement('beforebegin', msg);
            setTimeout(() => msg.remove(), 4000);
        }

        function fetchAchievements() {
            fetch('admin.php?action=get_achievements')
                .then(res => res.json())
                .then(data => {
                    listContainer.innerHTML = '';
                    if (data.status === 'success') {
                        data.data.forEach(item => {
                            listContainer.innerHTML += `
                        <li class="list-group-item d-flex justify-content-between align-items-start"
                            data-id="${item.id}"
                            data-title="${escapeHtml(item.title)}"
                            data-issuer="${escapeHtml(item.issuer || '')}"
                            data-date="${item.date || ''}"
                            data-description="${escapeHtml(item.description || '')}">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">${escapeHtml(item.title)}${item.issuer ? ' - ' + escapeHtml(item.issuer) : ''}</div>
                                ${item.date || ''}<br>
                                ${escapeHtml(item.description || '')}
                            </div>
                            <button class="btn btn-sm btn-warning me-1 update-btn">Güncelle</button>
                            <button class="btn btn-sm btn-danger delete-btn">Sil</button>
                        </li>`;
                        });
                    } else {
                        listContainer.innerHTML = '<li class="list-group-item">Kayıt bulunamadı.</li>';
                    }
                });
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('action', 'create_achievement');

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    showMessage(data.status === 'success' ? 'success' : 'danger', data.message);
                    if (data.status === 'success') {
                        form.reset();
                        fetchAchievements();
                    }
                });
        });

        listContainer.addEventListener('click', function (e) {
            const li = e.target.closest('li');
            if (!li) return;
            const id = li.dataset.id;

            if (e.target.classList.contains('delete-btn')) {
                const fd = new FormData();
                fd.append('action', 'delete_achievement');
                fd.append('id', id);
                fetch('admin.php', {
                    method: 'POST',
                    body: fd
                })
                    .then(res => res.json())
                    .then(data => {
                        showMessage(data.status === 'success' ? 'success' : 'danger', data.message);
                        fetchAchievements();
                    });
            }

            if (e.target.classList.contains('update-btn')) {
                const contentDiv = li.querySelector('.ms-2');
                contentDiv.innerHTML = `
                <input class="form-control mb-1" name="title" value="${li.dataset.title}">
                <input class="form-control mb-1" name="issuer" value="${li.dataset.issuer}">
                <input class="form-control mb-1" type="date" name="date" value="${li.dataset.date}">
                <textarea class="form-control mb-1" name="description">${li.dataset.description}</textarea>
                <button class="btn btn-sm btn-success save-btn">Kaydet</button>
            `;
                li.querySelector('.update-btn').remove();
            }

            if (e.target.classList.contains('save-btn')) {
                const title = li.querySelector('input[name="title"]').value;
                const issuer = li.querySelector('input[name="issuer"]').value;
                const date = li.querySelector('input[name="date"]').value;
                const description = li.querySelector('textarea[name="description"]').value;

                const fd = new FormData();
                fd.append('action', 'update_achievement');
                fd.append('id', id);
                fd.append('title', title);
                fd.append('issuer', issuer);
                fd.append('date', date);
                fd.append('description', description);

                fetch('admin.php', {
                    method: 'POST',
                    body: fd
                })
                    .then(res => res.json())
                    .then(data => {
                        showMessage(data.status === 'success' ? 'success' : 'danger', data.message);
                        fetchAchievements();
                    });
            }
        });

        // HTML escape fonksiyonu
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        fetchAchievements();
    });
</script>
<!-- BLOG BÖLÜMÜ -->
<!-- Kişisel Yazılar -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('personalPostForm');
        const list = document.getElementById('personalPostsList');

        // FORM GÖNDERME
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);

            Swal.fire({
                title: 'Kaydediliyor...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('admin.php?action=save_personal_post', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: data.message,
                            confirmButtonColor: '#3085d6'
                        });
                        form.reset();
                        fetchPersonalPosts();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: data.message,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
        });

        // LİSTELEME
        function fetchPersonalPosts() {
            fetch('admin.php?action=get_personal_posts')
                .then(res => res.json())
                .then(data => {
                    list.innerHTML = '';
                    if (data.status === 'success') {
                        data.posts.forEach(post => {
                            list.innerHTML += `
                    <li class="list-group-item" data-id="${post.id}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-1">${escapeHtml(post.title)}</h5>
                                <p class="mb-0">${escapeHtml(post.content.substring(0, 100))}...</p>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-warning me-1 update-btn">Güncelle</button>
                                <button class="btn btn-sm btn-danger delete-btn">Sil</button>
                            </div>
                        </div>
                    </li>`;
                        });

                        // Silme ve Güncelleme Eventleri
                        document.querySelectorAll('.delete-btn').forEach(btn => {
                            btn.addEventListener('click', deletePost);
                        });
                        document.querySelectorAll('.update-btn').forEach(btn => {
                            btn.addEventListener('click', updatePostUI);
                        });
                    }
                });
        }

        // SİLME
        function deletePost(e) {
            const id = e.target.closest('li').dataset.id;

            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu yazı kalıcı olarak silinecek!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`admin.php?action=delete_personal_post&id=${id}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire('Silindi!', data.message, 'success');
                                fetchPersonalPosts();
                            }
                        });
                }
            });
        }

        // GÜNCELLEME ARAYÜZÜ
        function updatePostUI(e) {
            const li = e.target.closest('li');
            const id = li.dataset.id;
            const title = li.querySelector('h5').textContent;
            const content = li.querySelector('p').textContent + '...'; // Kesilmiş içeriği tamamlama

            Swal.fire({
                title: 'Yazıyı Güncelle',
                html: `
                <input id="swal-title" class="swal2-input" value="${escapeHtml(title)}" required>
                <textarea id="swal-content" class="swal2-textarea" required>${escapeHtml(content)}</textarea>
            `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Kaydet',
                cancelButtonText: 'İptal',
                preConfirm: () => {
                    return {
                        title: document.getElementById('swal-title').value,
                        content: document.getElementById('swal-content').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updatePost(id, result.value.title, result.value.content);
                }
            });
        }

        // GÜNCELLEME AJAX
        function updatePost(id, title, content) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('title', title);
            formData.append('content', content);

            fetch('admin.php?action=update_personal_post', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Başarılı!', data.message, 'success');
                        fetchPersonalPosts();
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                });
        }

        // HTML Escape
        function escapeHtml(unsafe) {
            return unsafe.replace(/[&<"'>]/g, function(m) {
                return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m];
            });
        }

        // İlk yükleme
        fetchPersonalPosts();
    });
</script>
<!-- Seyahat Notları -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('travelNoteForm');
        const list = document.getElementById('travelNotesList');

        // FORM GÖNDERME
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);

            Swal.fire({
                title: 'Kaydediliyor...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('admin.php?action=save_travel_note', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: data.message,
                            confirmButtonColor: '#3085d6'
                        });
                        form.reset();
                        fetchTravelNotes();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: data.message,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
        });

        // LİSTELEME
        function fetchTravelNotes() {
            fetch('admin.php?action=get_travel_notes')
                .then(res => res.json())
                .then(data => {
                    list.innerHTML = '';
                    if (data.status === 'success') {
                        data.notes.forEach(note => {
                            list.innerHTML += `
                    <li class="list-group-item" data-id="${note.id}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-1">${escapeHtml(note.title)}</h5>
                                <p class="mb-0">${escapeHtml(note.content.substring(0, 100))}...</p>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-warning me-1 update-btn">Güncelle</button>
                                <button class="btn btn-sm btn-danger delete-btn">Sil</button>
                            </div>
                        </div>
                    </li>`;
                        });

                        // Event listeners
                        document.querySelectorAll('.delete-btn').forEach(btn => {
                            btn.addEventListener('click', deleteTravelNote);
                        });
                        document.querySelectorAll('.update-btn').forEach(btn => {
                            btn.addEventListener('click', updateTravelNoteUI);
                        });
                    }
                });
        }

        // SİLME
        function deleteTravelNote(e) {
            const id = e.target.closest('li').dataset.id;

            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu seyahat notu silinecek!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`admin.php?action=delete_travel_note&id=${id}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire('Silindi!', data.message, 'success');
                                fetchTravelNotes();
                            }
                        });
                }
            });
        }

        // GÜNCELLEME ARAYÜZÜ
        function updateTravelNoteUI(e) {
            const li = e.target.closest('li');
            const id = li.dataset.id;
            const title = li.querySelector('h5').textContent;
            const content = li.querySelector('p').textContent + '...';

            Swal.fire({
                title: 'Notu Güncelle',
                html: `
                <input id="swal-title" class="swal2-input" value="${escapeHtml(title)}" required>
                <textarea id="swal-content" class="swal2-textarea" required>${escapeHtml(content)}</textarea>
            `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Kaydet',
                cancelButtonText: 'İptal',
                preConfirm: () => {
                    return {
                        title: document.getElementById('swal-title').value,
                        content: document.getElementById('swal-content').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateTravelNote(id, result.value.title, result.value.content);
                }
            });
        }

        // GÜNCELLEME AJAX
        function updateTravelNote(id, title, content) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('title', title);
            formData.append('content', content);

            fetch('admin.php?action=update_travel_note', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Başarılı!', data.message, 'success');
                        fetchTravelNotes();
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                });
        }

        // HTML Escape
        function escapeHtml(unsafe) {
            return unsafe.replace(/[&<"'>]/g, function(m) {
                return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m];
            });
        }

        // İlk yükleme
        fetchTravelNotes();
    });
</script>
<!-- Kitap & Film Önerileri -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('bookFilmForm');
        const list = document.getElementById('bookFilmList');

        // FORM GÖNDERME
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);

            Swal.fire({
                title: 'Kaydediliyor...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('admin.php?action=save_book_film_recommendation', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: data.message,
                            confirmButtonColor: '#3085d6'
                        });
                        form.reset();
                        fetchBookFilmRecommendations();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: data.message,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
        });

        // LİSTELEME
        function fetchBookFilmRecommendations() {
            fetch('admin.php?action=get_book_film_recommendations')
                .then(res => res.json())
                .then(data => {
                    list.innerHTML = '';
                    if (data.status === 'success') {
                        data.recommendations.forEach(item => {
                            const badgeColor = item.type === 'book' ? 'bg-primary' : item.type === 'film' ? 'bg-danger' : 'bg-warning';
                            list.innerHTML += `
                    <li class="list-group-item" data-id="${item.id}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge ${badgeColor} me-2">${item.type === 'book' ? 'Kitap' : item.type === 'film' ? 'Film' : 'Dizi'}</span>
                                <h5 class="d-inline-block mb-1">${escapeHtml(item.title)}</h5>
                                <p class="mb-0">${escapeHtml(item.content.substring(0, 100))}...</p>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-warning me-1 update-btn">Güncelle</button>
                                <button class="btn btn-sm btn-danger delete-btn">Sil</button>
                            </div>
                        </div>
                    </li>`;
                        });

                        // Event listeners
                        document.querySelectorAll('.delete-btn').forEach(btn => {
                            btn.addEventListener('click', deleteBookFilmRecommendation);
                        });
                        document.querySelectorAll('.update-btn').forEach(btn => {
                            btn.addEventListener('click', updateBookFilmRecommendationUI);
                        });
                    }
                });
        }

        // SİLME
        function deleteBookFilmRecommendation(e) {
            const id = e.target.closest('li').dataset.id;

            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu öneri kalıcı olarak silinecek!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`admin.php?action=delete_book_film_recommendation&id=${id}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire('Silindi!', data.message, 'success');
                                fetchBookFilmRecommendations();
                            }
                        });
                }
            });
        }

        // GÜNCELLEME ARAYÜZÜ
        function updateBookFilmRecommendationUI(e) {
            const li = e.target.closest('li');
            const id = li.dataset.id;
            const type = li.querySelector('.badge').textContent.trim();
            const title = li.querySelector('h5').textContent;
            const content = li.querySelector('p').textContent + '...';

            Swal.fire({
                title: 'Öneriyi Güncelle',
                html: `
                <select id="swal-type" class="swal2-select mb-2">
                    <option value="book" ${type === 'Kitap' ? 'selected' : ''}>Kitap</option>
                    <option value="film" ${type === 'Film' ? 'selected' : ''}>Film</option>
                    <option value="series" ${type === 'Dizi' ? 'selected' : ''}>Dizi</option>
                </select>
                <input id="swal-title" class="swal2-input" value="${escapeHtml(title)}" required>
                <textarea id="swal-content" class="swal2-textarea" required>${escapeHtml(content)}</textarea>
            `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Kaydet',
                cancelButtonText: 'İptal',
                preConfirm: () => {
                    return {
                        type: document.getElementById('swal-type').value,
                        title: document.getElementById('swal-title').value,
                        content: document.getElementById('swal-content').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateBookFilmRecommendation(id, result.value.type, result.value.title, result.value.content);
                }
            });
        }

        // GÜNCELLEME AJAX
        function updateBookFilmRecommendation(id, type, title, content) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('type', type);
            formData.append('title', title);
            formData.append('content', content);

            fetch('admin.php?action=update_book_film_recommendation', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Başarılı!', data.message, 'success');
                        fetchBookFilmRecommendations();
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                });
        }

        // HTML Escape
        function escapeHtml(unsafe) {
            return unsafe.replace(/[&<"'>]/g, function(m) {
                return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m];
            });
        }

        // İlk yükleme
        fetchBookFilmRecommendations();
    });
</script>
<!-- Teknoloji & İlgi Alanları -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('techInterestForm');
        const list = document.getElementById('techInterestsList');

        // FORM GÖNDERME
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);

            Swal.fire({
                title: 'Kaydediliyor...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('admin.php?action=save_tech_interest', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: data.message,
                            confirmButtonColor: '#3085d6'
                        });
                        form.reset();
                        fetchTechInterests();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: data.message,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
        });

        // LİSTELEME
        function fetchTechInterests() {
            fetch('admin.php?action=get_tech_interests')
                .then(res => res.json())
                .then(data => {
                    list.innerHTML = '';
                    if (data.status === 'success') {
                        data.interests.forEach(interest => {
                            list.innerHTML += `
                    <li class="list-group-item" data-id="${interest.id}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-1">${escapeHtml(interest.title)}</h5>
                                <p class="mb-0">${escapeHtml(interest.content.substring(0, 100))}...</p>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-warning me-1 update-btn">Güncelle</button>
                                <button class="btn btn-sm btn-danger delete-btn">Sil</button>
                            </div>
                        </div>
                    </li>`;
                        });

                        // Event listeners
                        document.querySelectorAll('.delete-btn').forEach(btn => {
                            btn.addEventListener('click', deleteTechInterest);
                        });
                        document.querySelectorAll('.update-btn').forEach(btn => {
                            btn.addEventListener('click', updateTechInterestUI);
                        });
                    }
                });
        }

        // SİLME
        function deleteTechInterest(e) {
            const id = e.target.closest('li').dataset.id;

            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu kayıt kalıcı olarak silinecek!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`admin.php?action=delete_tech_interest&id=${id}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire('Silindi!', data.message, 'success');
                                fetchTechInterests();
                            }
                        });
                }
            });
        }

        // GÜNCELLEME ARAYÜZÜ
        function updateTechInterestUI(e) {
            const li = e.target.closest('li');
            const id = li.dataset.id;
            const title = li.querySelector('h5').textContent;
            const content = li.querySelector('p').textContent + '...';

            Swal.fire({
                title: 'İlgi Alanını Güncelle',
                html: `
                <input id="swal-title" class="swal2-input" value="${escapeHtml(title)}" required>
                <textarea id="swal-content" class="swal2-textarea" required>${escapeHtml(content)}</textarea>
            `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Kaydet',
                cancelButtonText: 'İptal',
                preConfirm: () => {
                    return {
                        title: document.getElementById('swal-title').value,
                        content: document.getElementById('swal-content').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateTechInterest(id, result.value.title, result.value.content);
                }
            });
        }

        // GÜNCELLEME AJAX
        function updateTechInterest(id, title, content) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('title', title);
            formData.append('content', content);

            fetch('admin.php?action=update_tech_interest', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Başarılı!', data.message, 'success');
                        fetchTechInterests();
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                });
        }

        // HTML Escape
        function escapeHtml(unsafe) {
            return unsafe.replace(/[&<"'>]/g, function(m) {
                return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m];
            });
        }

        // İlk yükleme
        fetchTechInterests();
    });
</script>
<!--GALERİ SCRİPT-->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fotoğraf Yükleme
        const photoForm = document.getElementById('photoForm');
        photoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            uploadMedia('photos', this);
        });

        // Hobi Görsel Yükleme
        const hobbyForm = document.getElementById('hobbyForm');
        hobbyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            uploadMedia('hobies', this);
        });

        // Video Yükleme
        const videoForm = document.getElementById('videoForm');
        videoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            uploadMedia('videos', this);
        });

        // Medya Yükleme Fonksiyonu
        function uploadMedia(type, form) {
            const formData = new FormData(form);
            formData.append('type', type);

            Swal.fire({
                title: 'Yükleniyor...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('admin.php?action=upload_gallery', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: data.message,
                            confirmButtonColor: '#3085d6'
                        });
                        form.reset();
                        fetchGalleryItems(type);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: data.message,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
        }

        // Galeri Öğelerini Getir
        function fetchGalleryItems(type) {
            fetch(`admin.php?action=get_gallery_items&type=${type}`)
                .then(res => res.json())
                .then(data => {
                    const galleryContainer = document.getElementById(`${type}Gallery`);
                    galleryContainer.innerHTML = '';

                    if (data.status === 'success' && data.items.length > 0) {
                        data.items.forEach(item => {
                            const mediaElement = item.type === 'videos' ?
                                `<video controls class="img-thumbnail m-2" style="width:200px;height:200px;object-fit:cover;">
                            <source src="${item.image_url}" type="video/mp4">
                        </video>` :
                                `<img src="${item.image_url}" class="img-thumbnail m-2" style="width:200px;height:200px;object-fit:cover;">`;

                            galleryContainer.innerHTML += `
                    <div class="d-inline-block position-relative">
                        ${mediaElement}
                        <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-media"
                                data-id="${item.id}" data-type="${type}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>`;
                        });

                        // Silme butonlarına event ekle
                        document.querySelectorAll('.delete-media').forEach(btn => {
                            btn.addEventListener('click', deleteMedia);
                        });
                    } else {
                        galleryContainer.innerHTML = '<p class="text-muted">Henüz öğe eklenmemiş.</p>';
                    }
                });
        }

        // Medya Silme
        function deleteMedia(e) {
            const id = e.target.closest('button').dataset.id;
            const type = e.target.closest('button').dataset.type;

            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu öğe kalıcı olarak silinecek!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`admin.php?action=delete_gallery_item&id=${id}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire('Silindi!', data.message, 'success');
                                fetchGalleryItems(type);
                            }
                        });
                }
            });
        }

        // Sayfa yüklendiğinde galerileri getir
        fetchGalleryItems('photos');
        fetchGalleryItems('hobies');
        fetchGalleryItems('videos');
    });

    // Font Awesome ikonları için
    document.head.insertAdjacentHTML('beforeend', '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">');
</script>
<!--SSS-->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        fetchFaqs();

        // Listeleme
        function fetchFaqs() {
            fetch('admin.php?action=get_faqs')
                .then(res => res.json())
                .then(data => {
                    const list = document.getElementById("faqList");
                    list.innerHTML = "";
                    if (data.status === "success") {
                        data.faqs.forEach(faq => {
                            const li = document.createElement("li");
                            li.className = "list-group-item d-flex justify-content-between align-items-start flex-wrap";
                            li.innerHTML = `
                            <div>
                                <strong>${faq.name}</strong> (${faq.email}, ${faq.phone})<br>
                                <em>${faq.question}</em>
                            </div>
                            <button class="btn btn-sm btn-danger mt-2" onclick="deleteFaq(${faq.id})">Sil</button>
                        `;
                            list.appendChild(li);
                        });
                    } else {
                        list.innerHTML = `<li class="list-group-item text-danger">${data.message}</li>`;
                    }
                });
        }

        // Silme
        window.deleteFaq = function (id) {
            if (!confirm("Bu soruyu silmek istediğinize emin misiniz?")) return;
            fetch(`admin.php?action=delete_faq&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById("faqMessage").innerHTML = `<div class="alert alert-${data.status === 'success' ? 'success' : 'danger'}">${data.message}</div>`;
                    fetchFaqs();
                });
        };
    });
</script>
</body>
</html>
