

<?php // Opening PHP tag - REQUIRED


$view = new stdClass();
$view->pageTitle = 'Register';
require_once('Views/register.phtml');

class AuthController {
    private $userModel;

//    public function __construct($db) {
//        $this->userModel = new UserModel($db);
//    }

    public function register() {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate inputs
            $username = trim($_POST['username'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';

            if (empty($username)) {
                $errors[] = "Email is required";
            } elseif (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format";
            }

            if (empty($name)) {
                $errors[] = "Full name is required";
            }

            if (empty($password)) {
                $errors[] = "Password is required";
            } elseif (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters";
            }

            // Proceed if no errors
            if (empty($errors)) {
                try {
                    $finalRole = ($role === 'homeowner') ? 'pending_homeowner' : 'user';
                    
                    $this->userModel->createUser(
                        $username,
                        $name,
                        $password,
                        $finalRole
                    );

                    header("Location: /login?registered=true");
                    exit();
                } catch (Exception $e) {
                    $errors[] = "Registration failed: " . $e->getMessage();
                }
            }
        }

        
    }
}

?> <!-- Closing PHP tag (optional but recommended) -->