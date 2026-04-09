<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $password = $data['password'] ?? '';

    if (strlen($name) < 2 || !preg_match('/^\d{11,}$/', $phone) || strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Некорректные данные']);
        exit;
    }

    try {
        $database = new Database();
        $conn = $database->getConnection();

        // Проверка уникальности телефона
        $stmt = $conn->prepare("SELECT id FROM users WHERE phone = :phone");
        $stmt->execute([':phone' => $phone]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Телефон уже зарегистрирован']);
            exit;
        }

        $hashed_password = $password;

        // Вставляем нового пользователя
        $stmt = $conn->prepare("INSERT INTO users (name, phone, password) VALUES (:name, :phone, :password)");
        $stmt->execute([
            ':name' => $name,
            ':phone' => $phone,
            ':password' => $hashed_password
        ]);

        echo json_encode(['success' => true, 'message' => 'Регистрация успешна']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
    }
}
?>