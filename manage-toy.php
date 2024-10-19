<?php
include 'dbinit.php';

$toyName = $description = $price = $stock = $brand = $color = $ageGroup = $material = $image = "";
$update = isset($_POST['update']);
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Toy Name
    if (empty($_POST['ToyName'])) {
        $errors['ToyName'] = "Toy Name is required.";
    } else {
        $toyName = trim($_POST['ToyName']);
    }

    // Validate Description
    if (empty($_POST['Description'])) {
        $errors['Description'] = "Description is required.";
    } else {
        $description = trim($_POST['Description']);
    }

    // Validate Price
    if (empty($_POST['Price'])) {
        $errors['Price'] = "Price is required.";
    } elseif (!is_numeric($_POST['Price'])) {
        $errors['Price'] = "Price must be a valid number.";
    } else {
        $price = (float)$_POST['Price'];
    }

    // Validate Stock
    if (empty($_POST['Stock'])) {
        $errors['Stock'] = "Stock is required.";
    } elseif (!is_numeric($_POST['Stock'])) {
        $errors['Stock'] = "Stock must be a number.";
    } else {
        $stock = (int)$_POST['Stock'];
    }

    // Validate Brand
    if (empty($_POST['Brand'])) {
        $errors['Brand'] = "Brand is required.";
    } else {
        $brand = trim($_POST['Brand']);
    }

    // Validate Color
    if (empty($_POST['Color'])) {
        $errors['Color'] = "Color is required.";
    } else {
        $color = trim($_POST['Color']);
    }

    // Validate Age Group
    if (empty($_POST['AgeGroup'])) {
        $errors['AgeGroup'] = "Age Group is required.";
    } else {
        $ageGroup = trim($_POST['AgeGroup']);
    }

    // Validate Material
    if (empty($_POST['Material'])) {
        $errors['Material'] = "Material is required.";
    } else {
        $material = trim($_POST['Material']);
    }

    // Validate Image Upload
    if (isset($_FILES['Image']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK) {
        $image = 'uploads/' . basename($_FILES['Image']['name']);
        $imageFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        // Check file size
        if ($_FILES['Image']['size'] > 500000) {
            $errors['Image'] = "Sorry, your file is too large.";
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors['Image'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // Move the uploaded file
        if (empty($errors)) {
            move_uploaded_file($_FILES['Image']['tmp_name'], $image);
        }
    } elseif ($update) {
        // If updating, keep the current image
        $image = $_POST['CurrentImage'];
    } else {
        $errors['Image'] = "Image is required.";
    }

    // Check if there are no errors before saving/updating
    if (empty($errors)) {
        if (isset($_POST['save'])) {
            $sql_query = "INSERT INTO toys (ToyName, Description, Price, Stock, Brand, Color, AgeGroup, Material, Image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_query);
            $stmt->bind_param("ssdisssss", $toyName, $description, $price, $stock, $brand, $color, $ageGroup, $material, $image);
            if ($stmt->execute()) {
                header('Location: index.php');
                exit(); 
            } else {
                echo "Error: " . $stmt->error;
            }
        }
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $update ? "Update Toy" : "Add New Toy"; ?> - Toy Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .form-label {
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2 class="text-center mb-0"><?php echo $update ? "Update Toy" : "Add New Toy"; ?></h2>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data" class="mt-4" novalidate>
                <input type="hidden" name="id" value="<?php echo $toyId ?? ''; ?>">
                <input type="hidden" name="CurrentImage" value="<?php echo $image ?? ''; ?>">
                
                <div class="row">
                    <!-- Toy Name -->
                    <div class="col-md-6 mb-3">
                        <label for="ToyName" class="form-label">Toy Name:</label>
                        <input type="text" class="form-control <?php echo isset($errors['ToyName']) ? 'is-invalid' : ''; ?>" name="ToyName" value="<?php echo htmlspecialchars($toyName); ?>">
                        <?php if (isset($errors['ToyName'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['ToyName']; ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Brand -->
                    <div class="col-md-6 mb-3">
                        <label for="Brand" class="form-label">Brand:</label>
                        <select class="form-select <?php echo isset($errors['Brand']) ? 'is-invalid' : ''; ?>" name="Brand">
                            <option value="">Select Brand</option>
                            <?php
                            $brands = ['LEGO', 'Mattel', 'Hasbro', 'Fisher-Price', 'Melissa & Doug', 'VTech', 'Playmobil', 'Nerf', 'Hot Wheels', 'Barbie'];
                            foreach ($brands as $brandOption) {
                                $selected = ($brand == $brandOption) ? 'selected' : '';
                                echo "<option value=\"$brandOption\" $selected>$brandOption</option>";
                            }
                            ?>
                        </select>
                        <?php if (isset($errors['Brand'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['Brand']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="Description" class="form-label">Description:</label>
                    <textarea class="form-control <?php echo isset($errors['Description']) ? 'is-invalid' : ''; ?>" name="Description" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                    <?php if (isset($errors['Description'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['Description']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <!-- Price -->
                    <div class="col-md-3 mb-3">
                        <label for="Price" class="form-label">Price:</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control <?php echo isset($errors['Price']) ? 'is-invalid' : ''; ?>" name="Price" value="<?php echo htmlspecialchars($price); ?>">
                        </div>
                        <?php if (isset($errors['Price'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['Price']; ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Stock -->
                    <div class="col-md-3 mb-3">
                        <label for="Stock" class="form-label">Stock:</label>
                        <input type="number" class="form-control <?php echo isset($errors['Stock']) ? 'is-invalid' : ''; ?>" name="Stock" value="<?php echo htmlspecialchars($stock); ?>">
                        <?php if (isset($errors['Stock'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['Stock']; ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Color -->
                    <div class="col-md-3 mb-3">
                        <label for="Color" class="form-label">Color:</label>
                        <select class="form-select <?php echo isset($errors['Color']) ? 'is-invalid' : ''; ?>" name="Color">
                            <option value="">Select Color</option>
                            <?php
                            $colors = ['Red', 'Blue', 'Green', 'Yellow', 'Pink', 'Purple', 'Orange', 'Black', 'White', 'Multi-color'];
                            foreach ($colors as $colorOption) {
                                $selected = ($color == $colorOption) ? 'selected' : '';
                                echo "<option value=\"$colorOption\" $selected>$colorOption</option>";
                            }
                            ?>
                        </select>
                        <?php if (isset($errors['Color'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['Color']; ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Material -->
                    <div class="col-md-3 mb-3">
                        <label for="Material" class="form-label">Material:</label>
                        <select class="form-select <?php echo isset($errors['Material']) ? 'is-invalid' : ''; ?>" name="Material">
                            <option value="">Select Material</option>
                            <?php
                            $materials = ['Plastic', 'Wood', 'Metal', 'Fabric', 'Rubber', 'Foam', 'Electronic'];
                            foreach ($materials as $materialOption) {
                                $selected = ($material == $materialOption) ? 'selected' : '';
                                echo "<option value=\"$materialOption\" $selected>$materialOption</option>";
                            }
                            ?>
                        </select>
                        <?php if (isset($errors['Material'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['Material']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <!-- Age Group -->
                    <div class="col-md-6 mb-3">
                        <label for="AgeGroup" class="form-label">Age Group:</label>
                        <select class="form-select <?php echo isset($errors['AgeGroup']) ? 'is-invalid' : ''; ?>" name="AgeGroup">
                            <option value="">Select Age Group</option>
                            <?php
                            $ageGroups = ['0-2 years', '3-5 years', '6-8 years', '9-12 years', '13+ years'];
                            foreach ($ageGroups as $ageGroupOption) {
                                $selected = ($ageGroup == $ageGroupOption) ? 'selected' : '';
                                echo "<option value=\"$ageGroupOption\" $selected>$ageGroupOption</option>";
                            }
                            ?>
                        </select>
                        <?php if (isset($errors['AgeGroup'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['AgeGroup']; ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Image Upload -->
                    <div class="col-md-6 mb-3">
                        <label for="Image" class="form-label">Toy Image:</label>
                        <input type="file" class="form-control <?php echo isset($errors['Image']) ? 'is-invalid' : ''; ?>" name="Image" accept="image/*">
                        <?php if (isset($errors['Image'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['Image']; ?></div>
                        <?php endif; ?>
                        <?php if ($update && $image): ?>
                            <img src="<?php echo $image; ?>" alt="Current Image" class="img-thumbnail mt-2" width="100">
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-secondary btn-lg me-2">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <button type="submit" name="<?php echo $update ? 'update' : 'save'; ?>" class="btn btn-lg btn-<?php echo $update ? 'warning' : 'primary'; ?>">
                        <i class="fas fa-<?php echo $update ? 'edit' : 'plus-circle'; ?>"></i> 
                        <?php echo $update ? 'Update' : 'Add'; ?> Toy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
