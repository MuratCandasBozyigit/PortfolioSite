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
        "CREATE TABLE IF NOT EXISTS about (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100),
            content TEXT
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

// POST işlemleri (About)
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
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Hoş geldin, <?= htmlspecialchars($_SESSION['admin']) ?>!</h2>
        <a href="?logout=1" class="btn btn-danger">Çıkış Yap</a>
    </div>

    <div class="accordion" id="accordionPanelsStayOpen">
        <!-- About Bölümü -->
        <div class="mb-4">
            <button class="btn btn-outline-primary animated-btn w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#aboutSection">➕ Hakkımda Bölümünü Aç/Kapat</button>
            <div class="collapse" id="aboutSection">
                <div class="card card-body shadow">
                    <form method="post" class="mb-3" action="admin.php">
                        <input type="hidden" name="about_id" id="about_id" value="">
                        <div class="mb-3">
                            <label>Başlık</label>
                            <input type="text" name="about_title" id="about_title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>İçerik</label>
                            <textarea name="about_content" id="about_content" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" name="save_about" id="save_btn" class="btn btn-success">Kaydet</button>
                        <button type="submit" name="update_about" id="update_btn" class="btn btn-warning" style="display:none;">Güncelle</button>
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
                        $aboutData = $pdo->query("SELECT * FROM about ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($aboutData as $row): ?>
                            <tr id="row_<?= $row['id'] ?>" onclick="selectRow(<?= $row['id'] ?>)">
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= nl2br(htmlspecialchars($row['content'])) ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" onclick="fillAboutForm(<?= $row['id'] ?>, '<?= addslashes($row['title']) ?>', '<?= addslashes($row['content']) ?>'); return false;">Güncelle</button>
                                    <a href="?delete_about=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let selectedRowId = null;

    function selectRow(id) {
        if (selectedRowId !== null) {
            document.getElementById('row_' + selectedRowId).classList.remove('selected-row');
        }
        selectedRowId = id;
        document.getElementById('row_' + id).classList.add('selected-row');
    }

    function fillAboutForm(id, title, content) {
        document.getElementById('about_id').value = id;
        document.getElementById('about_title').value = title;
        document.getElementById('about_content').value = content;
        document.getElementById('save_btn').style.display = 'none';
        document.getElementById('update_btn').style.display = 'inline-block';
        document.getElementById('about_title').focus();
    }

    // Form resetleme için
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector('form').addEventListener('reset', function() {
            document.getElementById('save_btn').style.display = 'inline-block';
            document.getElementById('update_btn').style.display = 'none';
        });
    });
</script>
</body>
</html>
