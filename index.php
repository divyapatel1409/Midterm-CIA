<?php
include 'dbinit.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: account.php");
    exit();
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: account.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toy Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
<div class="user-info">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                </div>
                <h4>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h4>
            </div>
            <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>?logout=1" class="btn logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2 class="text-center mb-0">Toy Management System</h2>
        </div>
        <div class="card-body">
            <a href="manage-toy.php" class="btn btn-success mb-3">
                <i class="fas fa-plus-circle"></i> Add New Toy
            </a>
            <div class="table-responsive">
                <table id="toysTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Toy Name</th>
                            <th>Brand</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Age Group</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM toys");
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><img src='{$row['Image']}' alt='Toy Image' class='toy-image'></td>";
                            echo "<td>{$row['ToyName']}</td>";
                            echo "<td>{$row['Brand']}</td>";
                            echo "<td>$" . number_format($row['Price'], 2) . "</td>";
                            echo "<td>{$row['Stock']}</td>";
                            echo "<td>{$row['AgeGroup']}</td>";
                            echo "<td>
                                    <a href='manage-toy.php?edit={$row['Id']}' class='btn btn-sm btn-warning me-1' title='Edit'><i class='fas fa-edit'></i></a>
                                    <a href='javascript:void(0)' class='btn btn-sm btn-danger btn-delete' data-id='{$row['Id']}' title='Delete'><i class='fas fa-trash-alt'></i></a>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#toysTable').DataTable({
            responsive: true,
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: [0, 6] }
            ]
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-delete').forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteItem(id);
                    }
                });
            });
        });
    });

    function deleteItem(id) {
        $.ajax({
            url: 'manage-toy.php',
            type: 'POST',
            data: { delete_id: id },
            success: function(response) {
                Swal.fire(
                    'Deleted!',
                    'The toy has been deleted.',
                    'success'
                ).then(() => {
                    location.reload();
                });
            },
            error: function() {
                Swal.fire(
                    'Error!',
                    'There was an issue deleting the toy.',
                    'error'
                );
            }
        });
    }
</script>
</body>
</html>
