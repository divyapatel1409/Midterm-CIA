<?php
// phpinfo();  
$servername = "db"; // Your database server
$username = "root"; // Your database username
$password = "rootpassword"; // Your database password
$dbname = "ecommerce-accessory"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// CREATE TABLE `ecommerce-accessory`.`hair_accessories` (`id` INT NOT NULL AUTO_INCREMENT , `AccessoryName` VARCHAR(255) NOT NULL , `AccessoryDescription` TEXT NOT NULL , `GenderCategory` ENUM('Male','Female','Unisex') NOT NULL , `QuantityAvailable` INT NOT NULL , `Price` DECIMAL(10,2) NOT NULL , `Brand` VARCHAR(255) NOT NULL , `Color` VARCHAR(255) NOT NULL , `ImagePath` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

// ALTER TABLE `hair_accessories` ADD `ProductAddedBy` VARCHAR(255) NOT NULL AFTER `ImagePath`;


?>