<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbName = 'db_kavana';

try {
    $connUser = new PDO("mysql:host=$host", $username, $password);
    $connUser->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connUser->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Database '$dbName' Created or Already Exists.\n";
    $connUser = null;

    $dsn = "mysql:host=$host;dbname=$dbName;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    migrateUsers($conn);
    migrateMoods($conn);
    migrateSolutions($conn);
    migrateAgendas($conn);
    migrateSavings($conn);


    $conn = null;
    echo "\nMigration process completed successfully.\n";
} catch (PDOException $e) {
    echo json_encode([
        "error" => "Connection/Migration Failed: " . $e->getMessage(),
    ]);
    $connUser = null;
    $conn = null;
    exit(1);
}

function migrateUsers($conn)
{
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `users` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(50) NOT NULL,
                `email` VARCHAR(100) UNIQUE NOT NULL,
                `password` VARCHAR(255) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $conn->exec($sql);
        echo "Table 'users' ensured successfully.";
    } catch (PDOException $e) {
        echo "Error ensuring table 'users': " . $e->getMessage();
    }
    echo "\n";
}

function migrateMoods($conn)
{
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `moods` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) UNSIGNED NOT NULL,
                `level` INT(1) NOT NULL,
                `created_at` DATETIME NOT NULL,
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $conn->exec($sql);
        echo "Table 'moods' ensured successfully.";
    } catch (PDOException $e) {
        echo "Error ensuring table 'moods': " . $e->getMessage();
    }
    echo "\n";
}

function migrateSolutions($conn)
{
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `solutions` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) UNSIGNED NOT NULL,
                `summary` TEXT NOT NULL,
                `problem` TEXT NOT NULL,
                `solution` TEXT NOT NULL,
                `reference` TEXT NULL,
                `created_at` DATETIME NOT NULL,
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $conn->exec($sql);
        echo "Table 'solutions' ensured successfully.";
    } catch (PDOException $e) {
        echo "Error ensuring table 'solutions': " . $e->getMessage();
    }
    echo "\n";
}

function migrateAgendas($conn)
{
    global $dbName;
    try {
        $sqlCreateTable = "CREATE TABLE IF NOT EXISTS `agendas` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) UNSIGNED NOT NULL,
                `title` VARCHAR(100) NOT NULL,
                `category` VARCHAR(50) NOT NULL,
                `start_event` DATETIME NOT NULL,
                `end_event` DATETIME NOT NULL,
                `description` TEXT NULL,
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $conn->exec($sqlCreateTable);
        echo "Table 'agendas' ensured successfully.";

        $tableName = 'agendas';

        function columnExists($conn, $dbName, $tableName, $columnName)
        {
            $sqlCheck = "SELECT COUNT(*)
                         FROM INFORMATION_SCHEMA.COLUMNS
                         WHERE TABLE_SCHEMA = :dbname
                           AND TABLE_NAME = :tablename
                           AND COLUMN_NAME = :colname";
            $stmt = $conn->prepare($sqlCheck);
            $stmt->execute([
                ':dbname' => $dbName,
                ':tablename' => $tableName,
                ':colname' => $columnName
            ]);
            return $stmt->fetchColumn() > 0;
        }

        $columnName1 = 'location_name';
        if (!columnExists($conn, $dbName, $tableName, $columnName1)) {
            $conn->exec("ALTER TABLE `$tableName` ADD COLUMN `$columnName1` VARCHAR(255) NULL AFTER `description`;");
            echo " Column '$columnName1' added.";
        } else {
            echo " Column '$columnName1' already exists.";
        }

        $columnName2 = 'latitude';
        if (columnExists($conn, $dbName, $tableName, $columnName1) && !columnExists($conn, $dbName, $tableName, $columnName2)) {
            $conn->exec("ALTER TABLE `$tableName` ADD COLUMN `$columnName2` DECIMAL(10, 8) NULL AFTER `$columnName1`;");
            echo " Column '$columnName2' added.";
        } elseif (!columnExists($conn, $dbName, $tableName, $columnName1)) {
            echo " Skipping '$columnName2' because previous column '$columnName1' does not exist.";
        } else {
            echo " Column '$columnName2' already exists.";
        }

        $columnName3 = 'longitude';
        if (columnExists($conn, $dbName, $tableName, $columnName2) && !columnExists($conn, $dbName, $tableName, $columnName3)) {
            $conn->exec("ALTER TABLE `$tableName` ADD COLUMN `$columnName3` DECIMAL(11, 8) NULL AFTER `$columnName2`;");
            echo " Column '$columnName3' added.";
        } elseif (!columnExists($conn, $dbName, $tableName, $columnName2)) {
            echo " Skipping '$columnName3' because previous column '$columnName2' does not exist.";
        } else {
            echo " Column '$columnName3' already exists.";
        }
    } catch (PDOException $e) {
        echo " Error modifying table 'agendas': " . $e->getMessage();
    }
    echo "\n";
}


function migrateSavings($conn) {
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `savings` (
                `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) UNSIGNED NOT NULL,
                `amount` DECIMAL(15, 2) NOT NULL,
                `note` TEXT NULL,
                `created_at` DATETIME NOT NULL,
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $conn->exec($sql);
        echo "Table 'savings' ensured successfully.\n";
    } catch (PDOException $e) {
        echo "Error ensuring table 'savings': " . $e->getMessage() . "\n";
    }
}
?>