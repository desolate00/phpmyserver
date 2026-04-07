<?php
// Настройки подключения к базе данных
$host = '127.0.0.1';
$db = 'user_auth';
$user = 'root';
$pass_db = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass_db, $opt);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Ошибка подключения к БД: ' . $e->getMessage()]);
    exit;
}

// ============================================
// ОБРАБОТКА POST-ЗАПРОСОВ
// ============================================
if ($_POST !== null) {
    


    
    if (isset($_POST['name']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['password'])) {
        try {
            $name = htmlspecialchars($_POST['name']);
            $phone = htmlspecialchars($_POST['phone']);
            $password_hash = htmlspecialchars($_POST['password_hash']);
            $created_at = htmlspecialchars($_POST['created_at'])

            $stmt = $pdo->prepare("INSERT INTO users (name, phone, password_hash, created_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->bindParam(1, $name);
            $stmt->bindParam(2, $phone);
            $stmt->bindParam(3, $password_hash);
            $stmt->bindParam(4, $created_at);
            $stmt->execute();

            echo json_encode(['status' => 'success', 'message' => 'Регистрация успешна!']);

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Пользователь с таким именем уже существует']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Ошибка: ' . $e->getMessage()]);
            }
        }
        exit;
    }
    

    // Вход (login)
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        try {
            $name = htmlspecialchars($_POST['name']);
            $phone = htmlspecialchars($_POST['phone'])
            $password_hash = htmlspecialchars($_POST['password_hash']);

            $stmt = $pdo->prepare("SELECT * FROM users WHERE name = ? AND phone = ? AND password_hash = ?");
            $stmt->execute([$name, $phone, $password_hash]);
            $user = $stmt->fetch();

            if ($user) {
                echo json_encode(['status' => 'success', 'message' => 'Вход выполнен!']);
            } else {
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => 'Неверное имя пользователя или пароль']);
            }

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Ошибка: ' . $e->getMessage()]);
        }
        exit;
    }

}