<?php
include 'dbinit.php';

$accessoryName = $accessoryDescription = $genderCategory = $quantityAvailable = $price = $brand = $color = $imagePath = "";
$productAddedBy = "Divyangini"; // Hardcoded value for 'Product Added By'
$update = isset($_POST['update']);
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['AccessoryName'])) {
        $errors['AccessoryName'] = "Accessory Name is required.";
    } else {
        $accessoryName = trim($_POST['AccessoryName']);
    }
    if (empty($_POST['AccessoryDescription'])) {
        $errors['AccessoryDescription'] = "Accessory Description is required.";
    } else {
        $accessoryDescription = trim($_POST['AccessoryDescription']);
    }
    if ($_POST['GenderCategory'] === 'Select') {
        $errors['GenderCategory'] = "Please select a Gender Category.";
    } else {
        $genderCategory = $_POST['GenderCategory'];
    }
    if (empty($_POST['QuantityAvailable'])) {
        $errors['QuantityAvailable'] = "Quantity Available is required.";
    } elseif (!is_numeric($_POST['QuantityAvailable'])) {
        $errors['QuantityAvailable'] = "Quantity Available must be a number.";
    } else {
        $quantityAvailable = (int)$_POST['QuantityAvailable'];
    }
    if (empty($_POST['Price'])) {
        $errors['Price'] = "Price is required.";
    } elseif (!is_numeric($_POST['Price'])) {
        $errors['Price'] = "Price must be a valid number.";
    } else {
        $price = (float)$_POST['Price'];
    }
    if (empty($_POST['Brand'])) {
        $errors['Brand'] = "Brand is required.";
    } else {
        $brand = trim($_POST['Brand']);
    }
    if (empty($_POST['Color'])) {
        $errors['Color'] = "Color is required.";
    } else {
        $color = trim($_POST['Color']);
    }
    // Validate Image Upload
    if (isset($_FILES['Image']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK) {
        $imagePath = 'uploads/' . basename($_FILES['Image']['name']);
        $imageFileType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        // Check file size (optional)
        if ($_FILES['Image']['size'] > 500000) {
            $errors['Image'] = "Sorry, your file is too large.";
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors['Image'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // Move the uploaded file (make sure the 'uploads' directory exists and is writable)
        if (empty($errors)) {
            move_uploaded_file($_FILES['Image']['tmp_name'], $imagePath);
        }
    } elseif ($update) {
        // If updating, use the current image path
        $imagePath = $_POST['CurrentImage'];
    } else {
        $errors['Image'] = "Image is required.";
    }

    
    if (isset($_POST['save'])) {
        
        if (empty($errors)) {
            $sql_query = "INSERT INTO hair_accessories (AccessoryName, AccessoryDescription, GenderCategory, QuantityAvailable, Price, Brand, Color, ProductAddedBy, ImagePath) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_query);
            $stmt->bind_param("sssidssss", $accessoryName, $accessoryDescription, $genderCategory, $quantityAvailable, $price, $brand, $color, $productAddedBy, $imagePath);
            if ($stmt->execute()) {
                header('Location: index.php');
            } else {
                echo "Error: " . $stmt->error;
            }
        }
    }

    if ($update) {
        // echo $imagePath;
        $accessoryID = $_POST['id'];
        $stmt = $conn->prepare("UPDATE hair_accessories SET AccessoryName=?, AccessoryDescription=?, GenderCategory=?, QuantityAvailable=?, Price=?, Brand=?, Color=?, ImagePath=? WHERE id=?");
        $stmt->bind_param("sssidssss", $accessoryName, $accessoryDescription, $genderCategory, $quantityAvailable, $price, $brand, $color, $imagePath, $accessoryID);
        $stmt->execute();
        header('Location: index.php');
    }
}

// Delete accessory
if (isset($_POST['delete_id'])) {
    $accessoryID = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM hair_accessories WHERE id=?");
    $stmt->bind_param("i", $accessoryID);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Record deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting record: ' . $stmt->error]);
    }
}

// Edit accessory
if (isset($_GET['edit'])) {
    $accessoryID = $_GET['edit'];
    $update = true;
    $result = $conn->query("SELECT * FROM hair_accessories WHERE id=$accessoryID");
    $row = $result->fetch_array();
    $accessoryName = $row['AccessoryName'];
    $accessoryDescription = $row['AccessoryDescription'];
    $genderCategory = $row['GenderCategory'];
    $quantityAvailable = $row['QuantityAvailable'];
    $price = $row['Price'];
    $brand = $row['Brand'];
    $color = $row['Color'];
    $imagePath = $row['ImagePath'];
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $update ? "Update Accessory" : "Add New Accessory"; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center"><?php echo $update ? "Update Accessory" : "Add New Accessory"; ?></h2>
    <form method="POST" action="" enctype="multipart/form-data" class="mt-4">
        <input type="hidden" name="id" value="<?php echo $accessoryID; ?>">
        <input type="hidden" name="CurrentImage" value="<?php echo $imagePath; ?>">
        
        <!-- Accessory Name -->
        <div class="mb-3">
            <label for="AccessoryName" class="form-label">Accessory Name:</label>
            <input type="text" class="form-control" name="AccessoryName" value="<?php echo $accessoryName; ?>" >
        </div>

        <!-- Accessory Description -->
        <div class="mb-3">
            <label for="AccessoryDescription" class="form-label">Accessory Description:</label>
            <textarea class="form-control" name="AccessoryDescription" ><?php echo $accessoryDescription; ?></textarea>
        </div>

        <!-- Gender Category -->
        <div class="mb-3">
            <label for="GenderCategory" class="form-label">Gender Category:</label>
            <select class="form-control" name="GenderCategory" >
                <option value="Select" <?php if($genderCategory == 'Select') echo 'selected'; ?>>Select</option>
                <option value="Male" <?php if($genderCategory == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if($genderCategory == 'Female') echo 'selected'; ?>>Female</option>
                <option value="Unisex" <?php if($genderCategory == 'Unisex') echo 'selected'; ?>>Unisex</option>
            </select>
        </div>

        <!-- Quantity Available -->
        <div class="mb-3">
            <label for="QuantityAvailable" class="form-label">Quantity Available:</label>
            <input type="number" class="form-control" name="QuantityAvailable" value="<?php echo $quantityAvailable; ?>" >
        </div>

        <!-- Price -->
        <div class="mb-3">
            <label for="Price" class="form-label">Price:</label>
            <input type="text" class="form-control" name="Price" value="<?php echo $price; ?>" >
        </div>

        <!-- Brand -->
        <div class="mb-3">
            <label for="Brand" class="form-label">Brand:</label>
            <input type="text" class="form-control" name="Brand" value="<?php echo $brand; ?>" >
        </div>

        <!-- Color -->
        <div class="mb-3">
            <label for="Color" class="form-label">Color:</label>
            <input type="text" class="form-control" name="Color" value="<?php echo $color; ?>" >
        </div>

        <!-- Image Upload -->
        <div class="mb-3">
            <label for="Image" class="form-label">Accessory Image:</label>
            <input type="file" class="form-control" name="Image" accept="image/*">
            <?php if ($update): ?>
                <img src="<?php echo $imagePath; ?>" alt="Current Image" class="img-fluid mt-2" width="100">
            <?php endif; ?>
        </div>

        <!-- Product Added By (Hardcoded) -->
        <div class="mb-3">
            <label for="ProductAddedBy" class="form-label">Product Added By:</label>
            <input type="text" class="form-control" value="<?php echo $productAddedBy; ?>" disabled>
        </div>

        <!-- Submit Button -->
        <button type="submit" name="<?php echo $update ? 'update' : 'save'; ?>" class="btn btn-<?php echo $update ? 'warning' : 'primary'; ?>">
            <?php echo $update ? 'Update' : 'Add'; ?>
        </button>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>