<?php
/**
 * Stories API Endpoint
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// GET - Retrieve stories
if ($method === 'GET') {
    // Check if getting single story
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if ($id) {
        // Get single story
        $stmt = $db->prepare("SELECT * FROM stories WHERE id = ?");
        $stmt->execute([$id]);
        $story = $stmt->fetch();
        
        if (!$story) {
            http_response_code(404);
            echo json_encode(['error' => 'Story not found']);
            exit();
        }
        
        echo json_encode($story);
    } else {
        // Get all stories
        $published = !isset($_GET['published']) || $_GET['published'] !== 'false';
        
        if ($published) {
            $stmt = $db->query("SELECT * FROM stories WHERE published = 1 ORDER BY created_at DESC");
        } else {
            $stmt = $db->query("SELECT * FROM stories ORDER BY created_at DESC");
        }
        
        $stories = $stmt->fetchAll();
        echo json_encode($stories);
    }
    exit();
}

// All other methods require authentication
$user = verifyToken();

// POST - Create story
if ($method === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $author = $_POST['author'] ?? 'Bayan Team';
    $published = isset($_POST['published']) && $_POST['published'] !== 'false' ? 1 : 0;
    
    if (empty($title) || empty($description)) {
        http_response_code(400);
        echo json_encode(['error' => 'Title and description are required']);
        exit();
    }
    
    // Handle image upload
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_url = handleImageUpload($_FILES['image']);
        if (!$image_url) {
            http_response_code(400);
            echo json_encode(['error' => 'Image upload failed']);
            exit();
        }
    }
    
    // Insert story
    $stmt = $db->prepare("INSERT INTO stories (title, description, image_url, author, published) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $image_url, $author, $published]);
    
    echo json_encode([
        'success' => true,
        'id' => $db->lastInsertId(),
        'message' => 'Story created successfully'
    ]);
    exit();
}

// PUT - Update story
if ($method === 'PUT') {
    // Parse PUT data
    parse_str(file_get_contents("php://input"), $_PUT);
    
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Story ID is required']);
        exit();
    }
    
    $title = $_PUT['title'] ?? '';
    $description = $_PUT['description'] ?? '';
    $author = $_PUT['author'] ?? 'Bayan Team';
    $published = isset($_PUT['published']) && $_PUT['published'] !== 'false' ? 1 : 0;
    $image_url = $_PUT['image_url'] ?? null;
    
    // Update story
    $stmt = $db->prepare("UPDATE stories SET title = ?, description = ?, image_url = ?, author = ?, published = ? WHERE id = ?");
    $stmt->execute([$title, $description, $image_url, $author, $published, $id]);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Story not found']);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Story updated successfully'
    ]);
    exit();
}

// DELETE - Delete story
if ($method === 'DELETE') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Story ID is required']);
        exit();
    }
    
    // Get story to delete image
    $stmt = $db->prepare("SELECT image_url FROM stories WHERE id = ?");
    $stmt->execute([$id]);
    $story = $stmt->fetch();
    
    if ($story && $story['image_url']) {
        $imagePath = __DIR__ . '/../' . $story['image_url'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    // Delete story
    $stmt = $db->prepare("DELETE FROM stories WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Story not found']);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Story deleted successfully'
    ]);
    exit();
}

/**
 * Handle image upload
 */
function handleImageUpload($file) {
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    // Check file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return false;
    }
    
    // Generate unique filename
    $filename = time() . '-' . rand(1000, 9999) . '.' . $extension;
    $uploadPath = UPLOAD_DIR . $filename;
    
    // Create uploads directory if it doesn't exist
    if (!file_exists(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return 'uploads/' . $filename;
    }
    
    return false;
}
?>

