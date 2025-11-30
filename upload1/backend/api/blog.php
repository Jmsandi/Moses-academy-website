<?php
/**
 * Blog API Endpoint
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// GET - Retrieve blogs
if ($method === 'GET') {
    // Check if getting single blog
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if ($id) {
        // Get single blog
        $stmt = $db->prepare("SELECT * FROM blogs WHERE id = ?");
        $stmt->execute([$id]);
        $blog = $stmt->fetch();
        
        if (!$blog) {
            http_response_code(404);
            echo json_encode(['error' => 'Blog not found']);
            exit();
        }
        
        echo json_encode($blog);
    } else {
        // Get all blogs
        $published = !isset($_GET['published']) || $_GET['published'] !== 'false';
        
        if ($published) {
            $stmt = $db->query("SELECT * FROM blogs WHERE published = 1 ORDER BY created_at DESC");
        } else {
            $stmt = $db->query("SELECT * FROM blogs ORDER BY created_at DESC");
        }
        
        $blogs = $stmt->fetchAll();
        echo json_encode($blogs);
    }
    exit();
}

// All other methods require authentication
$user = verifyToken();

// POST - Create blog
if ($method === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $excerpt = $_POST['excerpt'] ?? '';
    $author = $_POST['author'] ?? 'Admin';
    $published = isset($_POST['published']) && $_POST['published'] !== 'false' ? 1 : 0;
    
    if (empty($title) || empty($content)) {
        http_response_code(400);
        echo json_encode(['error' => 'Title and content are required']);
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
    
    // Insert blog
    $stmt = $db->prepare("INSERT INTO blogs (title, content, excerpt, image_url, author, published) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $content, $excerpt, $image_url, $author, $published]);
    
    echo json_encode([
        'success' => true,
        'id' => $db->lastInsertId(),
        'message' => 'Blog created successfully'
    ]);
    exit();
}

// PUT - Update blog
if ($method === 'PUT') {
    // Parse PUT data
    parse_str(file_get_contents("php://input"), $_PUT);
    
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Blog ID is required']);
        exit();
    }
    
    $title = $_PUT['title'] ?? '';
    $content = $_PUT['content'] ?? '';
    $excerpt = $_PUT['excerpt'] ?? '';
    $author = $_PUT['author'] ?? 'Admin';
    $published = isset($_PUT['published']) && $_PUT['published'] !== 'false' ? 1 : 0;
    $image_url = $_PUT['image_url'] ?? null;
    
    // Update blog
    $stmt = $db->prepare("UPDATE blogs SET title = ?, content = ?, excerpt = ?, image_url = ?, author = ?, published = ? WHERE id = ?");
    $stmt->execute([$title, $content, $excerpt, $image_url, $author, $published, $id]);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Blog not found']);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Blog updated successfully'
    ]);
    exit();
}

// DELETE - Delete blog
if ($method === 'DELETE') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Blog ID is required']);
        exit();
    }
    
    // Get blog to delete image
    $stmt = $db->prepare("SELECT image_url FROM blogs WHERE id = ?");
    $stmt->execute([$id]);
    $blog = $stmt->fetch();
    
    if ($blog && $blog['image_url']) {
        $imagePath = __DIR__ . '/../' . $blog['image_url'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    // Delete blog
    $stmt = $db->prepare("DELETE FROM blogs WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Blog not found']);
        exit();
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Blog deleted successfully'
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

