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

function initializeDatabase($pdo) {
    $queries = [
            //Users
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        )",
           //Hakkımda Tabloları
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
            //Ben Kimim
        "CREATE TABLE IF NOT EXISTS whoami (
            id INT PRIMARY KEY AUTO_INCREMENT,
            whoamiContent TEXT NOT NULL
        )",
        "CREATE TABLE IF NOT EXISTS blog_entries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category VARCHAR(100) NOT NULL, -- 'personal', 'travel', 'recommend', 'tech'
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
          )",

        "CREATE TABLE IF NOT EXISTS gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100),
            image_url TEXT
        )",
        "CREATE TABLE IF NOT EXISTS contact (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255),
            email VARCHAR(255),
            phone VARCHAR(20),
            instagram VARCHAR(255),
            facebook VARCHAR(255),
            linkedin VARCHAR(255)
         )",


    ];

    foreach ($queries as $query) {
        $pdo->exec($query);
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->rowCount() === 0) {
        $hash = password_hash("123456", PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (username, password) VALUES ('admin', ?)")->execute([$hash]);
    }
}

if (!isset($_SESSION['admin'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = $_POST['username'];
        $pass = $_POST['password'];
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
        <title>Admin Girişi</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h3 class="text-center mb-4">Yönetici Girişi</h3>
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label>Kullanıcı Adı</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Şifre</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">Giriş Yap</button>
                </form>
            </div>
        </div>
    </div>
    </body>
    </html>
    <?php exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

$whoamiContent = '';
$stmt = $pdo->query("SELECT whoamiContent FROM whoami WHERE id = 1");
if ($stmt && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $whoamiContent = $row['whoamiContent'];
}

if (isset($_POST['saveWhoami'])) {
    $newContent = $_POST['whoamiContent'];
    $stmt = $pdo->prepare("REPLACE INTO whoami (id, whoamiContent) VALUES (1, ?)");
    $stmt->execute([$newContent]);
    $whoamiContent = $newContent;
    header("Location: admin.php");
    exit;
}


//Hakkımda İşlemleri
if (isset($_POST['save_biography'])) {
    $stmt = $pdo->prepare("INSERT INTO biography (content) VALUES (?)");
    $stmt->execute([$_POST['bio_content']]);
}

if (isset($_POST['save_interest'])) {
    $stmt = $pdo->prepare("INSERT INTO interests (interest) VALUES (?)");
    $stmt->execute([$_POST['interest_content']]);
}

if (isset($_POST['save_education'])) {
    $stmt = $pdo->prepare("INSERT INTO education_experience (title, institution, start_date, end_date, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['edu_title'],
        $_POST['edu_institution'],
        $_POST['start_year'],
        $_POST['end_year'],
        $_POST['edu_description']
    ]);
}

if (isset($_POST['save_achievement'])) {
    $stmt = $pdo->prepare("INSERT INTO achievements (title, issuer, description, date) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['achieve_title'],
        $_POST['achieve_issuer'],
        $_POST['achieve_description'],
        $_POST['date_obtained']
    ]);
}
// CONTACT İşlemleri
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "add_contact") {
    $stmt = $pdo->prepare("INSERT INTO contact (name, email, phone, instagram, facebook, linkedin) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST["name"], $_POST["email"], $_POST["phone"], $_POST["instagram"], $_POST["facebook"], $_POST["linkedin"]]);
    header("Location: admin.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "update_contact") {
    $stmt = $pdo->prepare("UPDATE contact SET name=?, email=?, phone=?, instagram=?, facebook=?, linkedin=? WHERE id=?");
    $stmt->execute([$_POST["name"], $_POST["email"], $_POST["phone"], $_POST["instagram"], $_POST["facebook"], $_POST["linkedin"], $_POST["id"]]);
    header("Location: admin.php");
}

if (isset($_GET["delete_contact"])) {
    $stmt = $pdo->prepare("DELETE FROM contact WHERE id=?");
    $stmt->execute([$_GET["delete_contact"]]);
    header("Location: admin.php");
}

// BLOG islemleri
if (isset($_POST['save_personal_blog'])) {
    $stmt = $pdo->prepare("INSERT INTO blog_entries (category, title, content) VALUES (?, ?, ?)");
    $stmt->execute(['personal', $_POST['personal_title'], $_POST['personal_content']]);
}

if (isset($_POST['save_travel_blog'])) {
    $stmt = $pdo->prepare("INSERT INTO blog_entries (category, title, content) VALUES (?, ?, ?)");
    $stmt->execute(['travel', $_POST['travel_title'], $_POST['travel_content']]);
}

if (isset($_POST['save_recommend_blog'])) {
    $stmt = $pdo->prepare("INSERT INTO blog_entries (category, title, content) VALUES (?, ?, ?)");
    $stmt->execute(['recommend', $_POST['recommend_title'], $_POST['recommend_content']]);
}

if (isset($_POST['save_tech_blog'])) {
    $stmt = $pdo->prepare("INSERT INTO blog_entries (category, title, content) VALUES (?, ?, ?)");
    $stmt->execute(['tech', $_POST['tech_title'], $_POST['tech_content']]);
}
$categories = [
    'personal' => 'personalContent',
    'travel' => 'travelContent',
    'recommend' => 'recommendContent',
    'tech' => 'techContent'
];
if (isset($_GET['delete_blog'])) {
    $blogId = intval($_GET['delete_blog']); // Blog ID'sini al
    $stmt = $pdo->prepare("DELETE FROM blog_entries WHERE id = ?");
    $stmt->execute([$blogId]);

    // Silme işleminden sonra admin sayfasına yönlendir
    header("Location: admin.php");
    exit;
}

foreach ($categories as $category => $contentId):
    $blogs = $pdo->prepare("SELECT * FROM blog_entries WHERE category = :cat ORDER BY id DESC");
    $blogs->execute(['cat' => $category]);
    $rows = $blogs->fetchAll(PDO::FETCH_ASSOC);

    if ($rows):
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    const container = document.getElementById('$contentId');
                    container.innerHTML = `" .
            "<table class='table table-bordered'>" .
            "<thead class='table-light'>" .
            "<tr>" .
            "<th>ID</th><th>Başlık</th><th>İçerik</th><th>Tarih</th><th>İşlemler</th>" .
            "</tr>" .
            "</thead><tbody>";

        foreach ($rows as $row) {
            $id = $row['id'];
            $title = htmlspecialchars($row['title']);
            $content = nl2br(htmlspecialchars($row['content']));
            $date = $row['created_at'];

            echo "<tr>
                    <td>$id</td>
                    <td>$title</td>
                    <td>$content</td>
                    <td>$date</td>
                    <td>
                        <a href='?delete_blog=$id' class='btn btn-danger btn-sm' onclick='return confirm(\"Silinsin mi?\")'>Sil</a>
                    </td>
                </tr>";
        }

        echo "</tbody></table>`; 
                });
            </script>";
    endif;
endforeach;




// GALLERY İşlemleri
if (isset($_POST['save_gallery'])) {
    $stmt = $pdo->prepare("INSERT INTO gallery (title, image_url) VALUES (?, ?)");
    $stmt->execute([$_POST['gallery_title'], $_POST['gallery_image']]);
    header("Location: admin.php");
    exit;
}

if (isset($_POST['update_gallery'])) {
    $stmt = $pdo->prepare("UPDATE gallery SET title = ?, image_url = ? WHERE id = ?");
    $stmt->execute([$_POST['gallery_title'], $_POST['gallery_image'], $_POST['gallery_id']]);
    header("Location: admin.php");
    exit;
}

if (isset($_GET['delete_gallery'])) {
    $pdo->prepare("DELETE FROM gallery WHERE id = ?")->execute([$_GET['delete_gallery']]);
    header("Location: admin.php");
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
        .selected-row {
            background-color: #e0e0e0;
        }
        .editable {
            background-color: #e0ffe0;
        }
        .card-body {
            padding: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Hoş geldin, <?= htmlspecialchars($_SESSION['admin']) ?>!</h2>
        <a href="?logout=1" class="btn btn-danger">Çıkış Yap</a>
    </div>

    <div class="accordion" id="accordionPanelsStayOpen">
        <!-- whıami Bölümü -->
        <div class="mb-4">
            <button class="btn btn-outline-dark w-100 mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseWhoami">
                ✍️ Kendimi Tanıtıyorum
            </button>

            <div class="collapse mb-4" id="collapseWhoami">
                <div class="card card-body">
                    <form method="POST">
                        <div class="form-group mb-3">
                            <label for="whoamiContent">Tanıtım Yazısı:</label>
                            <textarea name="whoamiContent" id="whoamiContent" class="form-control" rows="5" required><?= htmlspecialchars($whoamiContent) ?></textarea>
                        </div>
                        <button type="submit" name="saveWhoami" class="btn btn-success">Kaydet</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Hakkımda Ana Accordion -->
        <div class="mb-4">
            <button class="btn btn-outline-primary animated-btn w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#hakkimdaSection">
                ➕ Hakkımda Bölümünü Aç/Kapat
            </button>

            <div class="collapse" id="hakkimdaSection">
                <div class="card card-body shadow">

                    <div class="accordion" id="aboutMainAccordion">

                        <!-- Biyografi -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="bioHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#bioCollapse">
                                    ✍️ Biyografi
                                </button>
                            </h2>
                            <div id="bioCollapse" class="accordion-collapse collapse" data-bs-parent="#aboutMainAccordion">
                                <div class="accordion-body">
                                    <form method="post">
                                        <textarea name="bio_content" rows="4" class="form-control mb-2" placeholder="Biyografinizi yazın..." required></textarea>
                                        <button type="submit" name="save_biography" class="btn btn-success">Kaydet</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- İlgi Alanlarım -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="interestHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#interestCollapse">
                                    ⭐ İlgi Alanlarım
                                </button>
                            </h2>
                            <div id="interestCollapse" class="accordion-collapse collapse" data-bs-parent="#aboutMainAccordion">
                                <div class="accordion-body">
                                    <form method="post">
                                        <textarea name="interest_content" rows="3" class="form-control mb-2" placeholder="İlgi alanlarınızı yazın..." required></textarea>
                                        <button type="submit" name="save_interest" class="btn btn-success">Kaydet</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Eğitim & Deneyim -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="eduHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#eduCollapse">
                                    🎓 Eğitim & Deneyim
                                </button>
                            </h2>
                            <div id="eduCollapse" class="accordion-collapse collapse" data-bs-parent="#aboutMainAccordion">
                                <div class="accordion-body">
                                    <form method="post">
                                        <input type="text" name="edu_title" class="form-control mb-2" placeholder="Başlık (Okul / İş)" required>
                                        <input type="text" name="edu_institution" class="form-control mb-2" placeholder="Kurum (Üniversite / Şirket)" required>
                                        <textarea name="edu_description" rows="3" class="form-control mb-2" placeholder="Açıklama" required></textarea>
                                        <div class="row">
                                            <div class="col"><input type="date" name="start_year" class="form-control mb-2" placeholder="Başlangıç Tarihi"></div>
                                            <div class="col"><input type="date" name="end_year" class="form-control mb-2" placeholder="Bitiş Tarihi"></div>
                                        </div>
                                        <button type="submit" name="save_education" class="btn btn-success">Kaydet</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Başarılar & Sertifikalar -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="achieveHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#achieveCollapse">
                                    🏆 Başarılar & Sertifikalar
                                </button>
                            </h2>
                            <div id="achieveCollapse" class="accordion-collapse collapse" data-bs-parent="#aboutMainAccordion">
                                <div class="accordion-body">
                                    <form method="post">
                                        <input type="text" name="achieve_title" class="form-control mb-2" placeholder="Başlık" required>
                                        <input type="text" name="achieve_issuer" class="form-control mb-2" placeholder="Kurumu (Sertifikayı Veren)">
                                        <textarea name="achieve_description" rows="2" class="form-control mb-2" placeholder="Açıklama"></textarea>
                                        <input type="date" name="date_obtained" class="form-control mb-2">
                                        <button type="submit" name="save_achievement" class="btn btn-success">Kaydet</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div> <!-- /aboutMainAccordion -->
                </div>
            </div>
        </div>

        <!-- Blog Ana Accordion -->
        <div class="mb-4">
            <button class="btn btn-outline-primary animated-btn w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#blogSection">
                ➕ Blog Bölümünü Aç/Kapat
            </button>

            <div class="collapse" id="blogSection">
                <!-- BLOG AKORDİYON BUTONU -->
                <button class="btn btn-outline-primary animated-btn w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#getBlogs">
                    ➕ Kayıtlı Blog Bilgileri
                </button>

                <!-- BLOG ANA COLLAPSE -->
                <div class="collapse" id="getBlogs">
                    <div class="card card-body">

                        <!-- KATEGORİ BUTONLARI -->
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#personalBlogs">Kişisel</button>
                            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#travelBlogs">Seyahat</button>
                            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#recommendBlogs">Öneriler</button>
                            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#techBlogs">Teknoloji</button>
                        </div>

                        <!-- KİŞİSEL BLOG TABLOSU -->
                        <div class="collapse" id="personalBlogs">
                            <h5>📝 Kişisel Yazılar</h5>
                            <div id="personalContent"></div>
                        </div>

                        <!-- SEYAHAT BLOG TABLOSU -->
                        <div class="collapse" id="travelBlogs">
                            <h5>🌍 Seyahat Yazıları</h5>
                            <div id="travelContent"></div>
                        </div>

                        <!-- ÖNERİ BLOG TABLOSU -->
                        <div class="collapse" id="recommendBlogs">
                            <h5>👍 Öneriler</h5>
                            <div id="recommendContent"></div>
                        </div>

                        <!-- TEKNOLOJİ BLOG TABLOSU -->
                        <div class="collapse" id="techBlogs">
                            <h5>💻 Teknoloji Yazıları</h5>
                            <div id="techContent"></div>
                        </div>

                    </div>
                </div>

                <div class="card card-body shadow">
                    <div class="accordion" id="blogMainAccordion">

                        <!-- Kişisel Yazılar -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="personalHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#personalCollapse">
                                    📝 Kişisel Yazılar
                                </button>
                            </h2>
                            <div id="personalCollapse" class="accordion-collapse collapse" data-bs-parent="#blogMainAccordion">
                                <div class="accordion-body">
                                    <form method="post">
                                        <input type="text" name="personal_title" class="form-control mb-2" placeholder="Başlık" required>
                                        <textarea name="personal_content" rows="4" class="form-control mb-2" placeholder="İçerik" required></textarea>
                                        <button type="submit" name="save_personal_blog" class="btn btn-success">Kaydet</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Seyahat Notları -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="travelHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#travelCollapse">
                                    🌍 Seyahat Notları
                                </button>
                            </h2>
                            <div id="travelCollapse" class="accordion-collapse collapse" data-bs-parent="#blogMainAccordion">
                                <div class="accordion-body">
                                    <form method="post">
                                        <input type="text" name="travel_title" class="form-control mb-2" placeholder="Başlık" required>
                                        <textarea name="travel_content" rows="4" class="form-control mb-2" placeholder="İçerik" required></textarea>
                                        <button type="submit" name="save_travel_blog" class="btn btn-success">Kaydet</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Kitap & Film Önerileri -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="recommendHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#recommendCollapse">
                                    🎬 Kitap & Film Önerileri
                                </button>
                            </h2>
                            <div id="recommendCollapse" class="accordion-collapse collapse" data-bs-parent="#blogMainAccordion">
                                <div class="accordion-body">
                                    <form method="post">
                                        <input type="text" name="recommend_title" class="form-control mb-2" placeholder="Başlık" required>
                                        <textarea name="recommend_content" rows="4" class="form-control mb-2" placeholder="İçerik" required></textarea>
                                        <button type="submit" name="save_recommend_blog" class="btn btn-success">Kaydet</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Teknoloji & İlgi Alanları -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="techHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#techCollapse">
                                    💻 Teknoloji & İlgi Alanları
                                </button>
                            </h2>
                            <div id="techCollapse" class="accordion-collapse collapse" data-bs-parent="#blogMainAccordion">
                                <div class="accordion-body">
                                    <form method="post">
                                        <input type="text" name="tech_title" class="form-control mb-2" placeholder="Başlık" required>
                                        <textarea name="tech_content" rows="4" class="form-control mb-2" placeholder="İçerik" required></textarea>
                                        <button type="submit" name="save_tech_blog" class="btn btn-success">Kaydet</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div> <!-- /blogMainAccordion -->
                </div>
            </div>
        </div>

        <!-- Galeri Bölümü -->
        <div class="mb-4">
            <button class="btn btn-outline-primary animated-btn w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#gallerySection">➕ Galeri Bölümünü Aç/Kapat</button>
            <div class="collapse" id="gallerySection">
                <div class="card card-body shadow">
                    <form method="post" class="mb-3">
                        <input type="hidden" name="gallery_id" id="gallery_id">
                        <div class="mb-3">
                            <label>Başlık</label>
                            <input type="text" name="gallery_title" id="gallery_title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Resim URL</label>
                            <input type="text" name="gallery_image" id="gallery_image" class="form-control" required>
                        </div>
                        <button type="submit" name="save_gallery" id="save_gallery_btn" class="btn btn-success">Kaydet</button>
                        <button type="submit" name="update_gallery" id="update_gallery_btn" class="btn btn-warning" style="display:none;">Güncelle</button>
                    </form>

                    <table class="table table-bordered">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Başlık</th>
                            <th>Görsel</th>
                            <th>İşlemler</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $galleryData = $pdo->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($galleryData as $row): ?>
                            <tr id="gallery_row_<?= $row['id'] ?>">
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Resim" style="max-width: 100px;"></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="fillGalleryForm(<?= $row['id'] ?>, '<?= addslashes($row['title']) ?>', '<?= addslashes($row['image_url']) ?>'); return false;">Güncelle</button>
                                    <a href="?delete_gallery=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- İletişim Bölümü -->
        <div class="mb-4">
            <button class="btn btn-outline-primary animated-btn w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#contactSection">➕ İletişim Bilgileri</button>
            <div class="collapse" id="contactSection">
                <div class="card card-body">
                    <form method="POST" class="row g-2">
                        <input type="hidden" name="action" value="add_contact">
                        <div class="col-md-4">
                            <input type="text" name="name" class="form-control" placeholder="Adınız">
                        </div>
                        <div class="col-md-4">
                            <input type="email" name="email" class="form-control" placeholder="Email (Gmail)">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="phone" class="form-control" placeholder="Telefon Numarası">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="instagram" class="form-control" placeholder="Instagram Linki">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="facebook" class="form-control" placeholder="Facebook Linki">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="linkedin" class="form-control" placeholder="LinkedIn Linki">
                        </div>
                        <div class="col-12">
                            <button class="btn btn-success w-100" type="submit">Kaydet</button>
                        </div>
                    </form>

                    <hr>

                    <table class="table mt-3">
                        <thead>
                        <tr>
                            <th>Ad</th>
                            <th>Email</th>
                            <th>Telefon</th>
                            <th>Instagram</th>
                            <th>Facebook</th>
                            <th>LinkedIn</th>
                            <th>İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM contact");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            if (isset($_GET['edit_contact']) && $_GET['edit_contact'] == $row['id']) {
                                echo '<form method="POST">';
                                echo '<input type="hidden" name="action" value="update_contact">';
                                echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
                                echo "<td><input type='text' name='name' value='{$row['name']}' class='form-control'></td>";
                                echo "<td><input type='email' name='email' value='{$row['email']}' class='form-control'></td>";
                                echo "<td><input type='text' name='phone' value='{$row['phone']}' class='form-control'></td>";
                                echo "<td><input type='text' name='instagram' value='{$row['instagram']}' class='form-control'></td>";
                                echo "<td><input type='text' name='facebook' value='{$row['facebook']}' class='form-control'></td>";
                                echo "<td><input type='text' name='linkedin' value='{$row['linkedin']}' class='form-control'></td>";
                                echo "<td><button type='submit' class='btn btn-sm btn-primary'>Kaydet</button></td>";
                                echo '</form>';
                            } else {
                                echo "<td>{$row['name']}</td>";
                                echo "<td>{$row['email']}</td>";
                                echo "<td>{$row['phone']}</td>";
                                echo "<td><a href='{$row['instagram']}' target='_blank'>Instagram</a></td>";
                                echo "<td><a href='{$row['facebook']}' target='_blank'>Facebook</a></td>";
                                echo "<td><a href='{$row['linkedin']}' target='_blank'>LinkedIn</a></td>";
                                echo "<td>
                                    <a href='?edit_contact={$row['id']}' class='btn btn-sm btn-warning'>Güncelle</a>
                                    <a href='?delete_contact={$row['id']}' class='btn btn-sm btn-danger'>Sil</a>
                                </td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>

    function selectRow(id) {
        document.querySelectorAll('tr[id^="row_"]').forEach(row => {
            row.classList.remove('selected-row');
        });
        document.getElementById('row_' + id).classList.add('selected-row');
    }

    function selectBlogRow(id) {
        document.querySelectorAll('tr[id^="row_"]').forEach(row => {
            row.classList.remove('selected-row');
        });
        document.getElementById('row_' + id).classList.add('selected-row');
    }
</script>
</body>
</html>