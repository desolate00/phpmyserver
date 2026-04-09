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
    $phone = trim($data['phone'] ?? '');
    $password = $data['password'] ?? '';

    if (!preg_match('/^\d{11,}$/', $phone) || strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Некорректные данные']);
        exit;
    }

    try {
        $database = new Database();
        $conn = $database->getConnection();

        $stmt = $conn->prepare("SELECT * FROM users WHERE phone = :phone");
        $stmt->execute([':phone' => $phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            echo json_encode(['success' => true, 'message' => 'Успешный вход', 'user' => ['name' => $user['name'], 'phone' => $user['phone']]]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
    }
}
?>