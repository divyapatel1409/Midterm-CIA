<?php
include 'dbinit.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hair Accessories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="style.css">

</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Hair Accessories</h2>
    <a href="register.php" class="btn btn-success mb-3">Add New Accessory</a>
    <table id="accessoriesTable" class="table table-striped">
        <thead>
            <tr>
                <th>Accessory ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Gender</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Brand</th>
                <th>Color</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM hair_accessories");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['AccessoryName']}</td>";
                echo "<td>{$row['AccessoryDescription']}</td>";
                echo "<td>{$row['GenderCategory']}</td>";
                echo "<td>{$row['QuantityAvailable']}</td>";
                echo "<td>{$row['Price']}</td>";
                echo "<td>{$row['Brand']}</td>";
                echo "<td>{$row['Color']}</td>";
                echo "<td><img src='{$row['ImagePath']}' alt='Image' width='50'></td>";
                echo "<td>
                        <a href='register.php?edit={$row['id']}' class='text-warning btn-edit'><i class='fas fa-edit'></i></a>
                        <a href='javascript:void(0)' class='text-danger btn-delete' data-id='". $row['id'] ."'> <i class='fas fa-trash-alt text-danger'></i> </a>
                      </td>";
                echo "</tr>";
                // <a href='register.php?delete={$row['id']}' class='text-danger delete-btn' onclick='return confirm(\"Are you sure you want to delete this item?\");'><i class='fas fa-trash'></i></a>

                // <a href='register.php?delete={$row['id']}' class='text-danger btn-delete'><i class='fas fa-trash'></i></a>

                // <a href="javascript:void(0)" class="btn-delete" data-id="'. $row['id'] .'"> <i class="fas fa-trash-alt text-danger"></i> </a>
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#accessoriesTable').DataTable();
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Bind event listener to the delete button
        document.querySelectorAll('.btn-delete').forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.getAttribute('data-id'); // Get the ID of the item to delete

                // SweetAlert2 confirmation dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this record!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If confirmed, make an AJAX call to delete the item
                        deleteItem(id);
                    }
                });
            });
        });
    });

    // Function to delete the item via AJAX
    function deleteItem(id) {
        $.ajax({
            url: 'register.php',  // Your PHP file that handles the deletion
            type: 'POST',
            data: { delete_id: id }, // Send the ID of the item to delete
            success: function(response) {
                location.reload();
            },
            error: function() {
                Swal.fire(
                    'Error!',
                    'There was an issue deleting the item.',
                    'error'
                );
            }
        });
    }

</script>
</body>
</html>