<?php
include 'dbinit.php';

session_start();

$errors = [];
$success_message = '';
$form_cleared = false;

// Registration
if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($name)) $errors['name'] = "Name is required.";
    if (empty($email)) $errors['email'] = "Email is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Invalid email format.";
    if (empty($phone)) $errors['phone'] = "Phone number is required.";
    if (empty($password)) $errors['password'] = "Password is required.";
    elseif (strlen($password) < 6) $errors['password'] = "Password must be at least 6 characters long.";
    if ($password !== $confirm_password) $errors['confirm_password'] = "Passwords do not match.";

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO user (Name, Email, PhoneNumber, Password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);
        
        if ($stmt->execute()) {
            $success_message = "Registration successful. You can now log in.";
            $form_cleared = true; 
        } else {
            $errors['general'] = "Registration failed. Please try again.";
        }
        $stmt->close();
    }
}

// Login
if (isset($_POST['login'])) {
    $email = trim($_POST['login_email']);
    $password = $_POST['login_password'];

    if (empty($email)) $errors['login_email'] = "Email is required.";
    if (empty($password)) $errors['login_password'] = "Password is required.";

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT Id, Name, Password FROM user WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['Password'])) {
                $_SESSION['user_id'] = $user['Id'];
                $_SESSION['user_name'] = $user['Name'];
                header("Location: index.php");
                exit();
            } else {
                $errors['login_general'] = "Invalid email or password.";
            }
        } else {
            $errors['login_general'] = "Invalid email or password.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account - Toy Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .form-label {
            font-weight: bold;
        }
        .page-title {
            background: linear-gradient(135deg, #0d6efd, #6ea8fe);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .page-title h1 {
            font-weight: 300;
            letter-spacing: 1px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card-header {
            border-bottom: none;
        }
    </style>
</head>
<body class="bg-light">
<div class="page-title">
    <div class="container">
        <h1 class="text-center mb-0">Toy Management System</h1>
    </div>
</div>
<div class="container">
    <div class="row">
        <!-- Registration Form -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h2 class="text-center mb-0">Register</h2>
                </div>
                <div class="card-body">
                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="" novalidate>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name:</label>
                            <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" name="name" value="<?php echo $form_cleared ? '' : htmlspecialchars($_POST['name'] ?? ''); ?>">
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" name="email" value="<?php echo $form_cleared ? '' : htmlspecialchars($_POST['email'] ?? ''); ?>">
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number:</label>
                            <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" name="phone" value="<?php echo $form_cleared ? '' : htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            <?php if (isset($errors['phone'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['phone']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" name="password">
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password:</label>
                            <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" name="confirm_password">
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="text-center">
                            <button type="submit" name="register" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus"></i> Register
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Login Form -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h2 class="text-center mb-0">Login</h2>
                </div>
                <div class="card-body">
                    <?php if (isset($errors['login_general'])): ?>
                        <div class="alert alert-danger"><?php echo $errors['login_general']; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="" novalidate>
                        <div class="mb-3">
                            <label for="login_email" class="form-label">Email:</label>
                            <input type="email" class="form-control <?php echo isset($errors['login_email']) ? 'is-invalid' : ''; ?>" name="login_email" value="<?php echo htmlspecialchars($_POST['login_email'] ?? ''); ?>">
                            <?php if (isset($errors['login_email'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['login_email']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="login_password" class="form-label">Password:</label>
                            <input type="password" class="form-control <?php echo isset($errors['login_password']) ? 'is-invalid' : ''; ?>" name="login_password">
                            <?php if (isset($errors['login_password'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['login_password']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="text-center">
                            <button type="submit" name="login" class="btn btn-success btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
