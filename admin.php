<?php
session_start();

// 🔌 Uzak veritabanı bağlantı bilgileri
$host = '217.195.207.215'; // Hosting sunucunun IP adresi
$port = '3306';
$dbname = 'dunyani1_Portfolio';
$username = 'murat';
$password = '81644936.Ma'; // Şifreni buraya doğru şekilde yazdım

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    initializeDatabase($pdo); // Veritabanı tablolarını oluştur (code first)
} catch (PDOException $e) {
    die("Veritabanı bağlantısı başarısız: " . $e->getMessage());
}

// 🧱 Tablo oluşturma fonksiyonu (Code First)
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
        )",

        "CREATE TABLE IF NOT EXISTS blog (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category VARCHAR(50),
            title VARCHAR(100),
            content TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )",

        "CREATE TABLE IF NOT EXISTS gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category VARCHAR(50),
            image_path VARCHAR(255),
            description TEXT
        )",

        "CREATE TABLE IF NOT EXISTS faq (
            id INT AUTO_INCREMENT PRIMARY KEY,
            question TEXT,
            answer TEXT
        )",

        "CREATE TABLE IF NOT EXISTS contact (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            email VARCHAR(100),
            message TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )"
    ];

    foreach ($queries as $query) {
        $pdo->exec($query);
    }

    // İlk admin kullanıcıyı oluştur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->rowCount() === 0) {
        $hash = password_hash("123456", PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (username, password) VALUES ('admin', ?)")
            ->execute([$hash]);
    }
}

// 🔐 Giriş yapılmamışsa, login formu göster
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

    // Giriş Formu (Bootstrap)
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
    <?php
    exit;
}

// 🎉 Giriş başarılı → panel
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Hoş geldin, <?= htmlspecialchars($_SESSION['admin']) ?>!</h2>
    <p>Veritabanı tabloları başarıyla oluşturuldu ve giriş başarılı.</p>
    <a href="?logout=1" class="btn btn-danger">Çıkış Yap</a>
</div>
</body>
</html>

<?php
// Çıkış işlemi
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
}
?>
