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
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        )",
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
        "CREATE TABLE IF NOT EXISTS about (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100),
            content TEXT
        )",
        "CREATE TABLE IF NOT EXISTS blog (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100),
            content TEXT
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
        "CREATE TABLE IF NOT EXISTS whoami (
            id INT PRIMARY KEY AUTO_INCREMENT,
            whoamiContent TEXT NOT NULL
        )"
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

// WHOAMI İşlemleri
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

// ABOUT İşlemleri
if (isset($_POST['save_about'])) {
    $stmt = $pdo->prepare("INSERT INTO about (title, content) VALUES (?, ?)");
    $stmt->execute([$_POST['about_title'], $_POST['about_content']]);
    header("Location: admin.php");
    exit;
}

if (isset($_POST['update_about'])) {
    $stmt = $pdo->prepare("UPDATE about SET title = ?, content = ? WHERE id = ?");
    $stmt->execute([$_POST['about_title'], $_POST['about_content'], $_POST['about_id']]);
    header("Location: admin.php");
    exit;
}

if (isset($_GET['delete_about'])) {
    $pdo->prepare("DELETE FROM about WHERE id = ?")->execute([$_GET['delete_about']]);
    header("Location: admin.php");
    exit;
}

// BLOG İşlemleri
if (isset($_POST['save_blog'])) {
    $stmt = $pdo->prepare("INSERT INTO blog (title, content) VALUES (?, ?)");
    $stmt->execute([$_POST['blog_title'], $_POST['blog_content']]);
    header("Location: admin.php");
    exit;
}

if (isset($_POST['update_blog'])) {
    $stmt = $pdo->prepare("UPDATE blog SET title = ?, content = ? WHERE id = ?");
    $stmt->execute([$_POST['blog_title'], $_POST['blog_content'], $_POST['blog_id']]);
    header("Location: admin.php");
    exit;
}

if (isset($_GET['delete_blog'])) {
    $pdo->prepare("DELETE FROM blog WHERE id = ?")->execute([$_GET['delete_blog']]);
    header("Location: admin.php");
    exit;
}

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

// CONTACT İşlemleri
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] == "add_contact") {
    $stmt = $pdo->prepare("INSERT INTO contact (name, email, phone, instagram, facebook, linkedin) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST["name"], $_POST["email"], $_POST["phone"], $_POST["instagram"], $_POST["facebook"], $_POST["linkedin"]]);
    header("Location: admin.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["action"] == "update_contact") {
    $stmt = $pdo->prepare("UPDATE contact SET name=?, email=?, phone=?, instagram=?, facebook=?, linkedin=? WHERE id=?");
    $stmt->execute([$_POST["name"], $_POST["email"], $_POST["phone"], $_POST["instagram"], $_POST["facebook"], $_POST["linkedin"], $_POST["id"]]);
    header("Location: admin.php");
}

if (isset($_GET["delete_contact"])) {
    $stmt = $pdo->prepare("DELETE FROM contact WHERE id=?");
    $stmt->execute([$_GET["delete_contact"]]);
    header("Location: admin.php");
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

        <!-- Hakkımda Ana Bölüm -->
        <div class="mb-4">
            <button class="btn btn-outline-primary animated-btn w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#aboutSection">➕ Hakkımda Bölümünü Aç/Kapat</button>
            <div class="collapse" id="aboutSection">
                <div class="card card-body shadow">

                    <!-- Accordion Start -->
                    <div class="accordion" id="aboutAccordion">

                        <!-- 1. Biyografi -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingBio">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBio" aria-expanded="true" aria-controls="collapseBio">
                                    📄 Biyografi
                                </button>
                            </h2>
                            <div id="collapseBio" class="accordion-collapse collapse show" aria-labelledby="headingBio" data-bs-parent="#aboutAccordion">
                                <div class="accordion-body">
                                    <!-- Biyografi Form & Tablo Buraya Gelecek -->
                                </div>
                            </div>
                        </div>

                        <!-- 2. İlgi Alanlarım -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingInterests">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInterests" aria-expanded="false" aria-controls="collapseInterests">
                                    💡 İlgi Alanlarım
                                </button>
                            </h2>
                            <div id="collapseInterests" class="accordion-collapse collapse" aria-labelledby="headingInterests" data-bs-parent="#aboutAccordion">
                                <div class="accordion-body">
                                    <!-- İlgi Alanları Form & Tablo Buraya Gelecek -->
                                </div>
                            </div>
                        </div>

                        <!-- 3. Eğitim ve Deneyim -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEducation">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEducation" aria-expanded="false" aria-controls="collapseEducation">
                                    🎓 Eğitim & Deneyim
                                </button>
                            </h2>
                            <div id="collapseEducation" class="accordion-collapse collapse" aria-labelledby="headingEducation" data-bs-parent="#aboutAccordion">
                                <div class="accordion-body">
                                    <!-- Eğitim ve Deneyim Form & Tablo Buraya Gelecek -->
                                </div>
                            </div>
                        </div>

                        <!-- 4. Başarılar & Sertifikalar -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingAchievements">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAchievements" aria-expanded="false" aria-controls="collapseAchievements">
                                    🏆 Başarılar & Sertifikalar
                                </button>
                            </h2>
                            <div id="collapseAchievements" class="accordion-collapse collapse" aria-labelledby="headingAchievements" data-bs-parent="#aboutAccordion">
                                <div class="accordion-body">
                                    <!-- Başarılar Form & Tablo Buraya Gelecek -->
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- Accordion End -->

                </div>
            </div>
        </div>

        <!-- Blog Bölümü -->
        <div class="mb-4">
            <button class="btn btn-outline-primary animated-btn w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#blogSection">➕ Blog Bölümünü Aç/Kapat</button>
            <div class="collapse" id="blogSection">
                <div class="card card-body shadow">
                    <form method="post" class="mb-3">
                        <input type="hidden" name="blog_id" id="blog_id">
                        <div class="mb-3">
                            <label>Başlık</label>
                            <input type="text" name="blog_title" id="blog_title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>İçerik</label>
                            <textarea name="blog_content" id="blog_content" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" name="save_blog" id="save_blog_btn" class="btn btn-success">Kaydet</button>
                        <button type="submit" name="update_blog" id="update_blog_btn" class="btn btn-warning" style="display:none;">Güncelle</button>
                    </form>

                    <table class="table table-bordered">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Başlık</th>
                            <th>İçerik</th>
                            <th>İşlemler</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $blogData = $pdo->query("SELECT * FROM blog ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($blogData as $row): ?>
                            <tr id="row_<?= $row['id'] ?>" onclick="selectBlogRow(<?= $row['id'] ?>)">
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= nl2br(htmlspecialchars($row['content'])) ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="fillBlogForm(<?= $row['id'] ?>, '<?= addslashes($row['title']) ?>', '<?= addslashes($row['content']) ?>'); return false;">Güncelle</button>
                                    <a href="?delete_blog=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
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
    // About Form Doldurma
    function fillAboutForm(id, title, content) {
        document.getElementById('about_id').value = id;
        document.getElementById('about_title').value = title;
        document.getElementById('about_content').value = content;
        document.getElementById('save_btn').style.display = 'none';
        document.getElementById('update_btn').style.display = 'inline-block';
    }

    // Blog Form Doldurma
    function fillBlogForm(id, title, content) {
        document.getElementById('blog_id').value = id;
        document.getElementById('blog_title').value = title;
        document.getElementById('blog_content').value = content;
        document.getElementById('save_blog_btn').style.display = 'none';
        document.getElementById('update_blog_btn').style.display = 'inline-block';
    }

    // Gallery Form Doldurma
    function fillGalleryForm(id, title, image) {
        document.getElementById('gallery_id').value = id;
        document.getElementById('gallery_title').value = title;
        document.getElementById('gallery_image').value = image;
        document.getElementById('save_gallery_btn').style.display = 'none';
        document.getElementById('update_gallery_btn').style.display = 'inline-block';
    }

    // Satır Seçme
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