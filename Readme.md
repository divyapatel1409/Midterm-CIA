### Midterm Assignment

- Start Working at 10:50 PM

#### SQL Script to create tables

```sql

CREATE TABLE IF NOT EXISTS toys (
 `Id` int NOT NULL AUTO_INCREMENT,
 `ToyName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
 `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
 `Price` decimal(10,2) NOT NULL,
 `Stock` int NOT NULL,
 `Brand` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
 `Color` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
 `AgeGroup` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
 `Material` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
 `Image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
 PRIMARY KEY (`Id`)
);

CREATE TABLE IF NOT EXISTS CREATE TABLE `user` (
 `Id` int NOT NULL AUTO_INCREMENT,
 `Name` varchar(255) NOT NULL,
 `Email` varchar(255) NOT NULL,
 `PhoneNumber` varchar(20) NOT NULL,
 `Password` varchar(255) NOT NULL,
 PRIMARY KEY (`Id`),
 UNIQUE KEY `Email` (`Email`)
);
```