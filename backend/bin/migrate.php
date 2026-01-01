<?php
require_once __DIR__.'/../utils_loader.php';

// Prepare migrations table
$migrationsTableCreated = Database::getPdo()->prepare('
CREATE TABLE IF NOT EXISTS `migrations` (
    `migration` VARCHAR(255) NOT NULL,
    `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `runtime` FLOAT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
')->execute();

if (!$migrationsTableCreated) {
    die('Failed to create migrations table.');
}

$appliedMigrations = Database::getPdo()->query('SELECT `migration` FROM `migrations`')->fetchAll(PDO::FETCH_COLUMN);

$availableMigrations = array_diff(scandir(__DIR__ . '/../migrations'), ['.', '..', '.htaccess']);
$availableMigrations = array_map(fn ($file) => pathinfo($file, PATHINFO_FILENAME), $availableMigrations);

$notAppliedMigrations = array_diff($availableMigrations, $appliedMigrations);

print "Found " . count($notAppliedMigrations) . " new migrations.".PHP_EOL;
foreach ($notAppliedMigrations as $migration) {
    print "Applying migration: $migration".PHP_EOL;
    $startTime = microtime(true);

    $migrationContent = file_get_contents(__DIR__ . '/../migrations/' . $migration . '.sql');
    if ($migrationContent === false) {
        die("Failed to read migration file: $migration.sql");
    }
    if (empty($migrationContent)) {
        die("Migration file is empty: $migration.sql");
    }

    $result = Database::getPdo()->exec($migrationContent);
    if ($result === false) {
        $errorInfo = Database::getPdo()->errorInfo();
        die("Failed to apply migration: $migration. Error: " . $errorInfo[2]);
    }

    $endTime = microtime(true);
    $runtime = ($endTime - $startTime); // in seconds

    $stmt = Database::getPdo()->prepare('INSERT INTO `migrations` (`migration`, `runtime`) VALUES (:migration, :runtime)');
    $stmt->execute(['migration' => $migration, 'runtime' => $runtime]);

    print "Applied migration: $migration in {$runtime}s".PHP_EOL;
}

print PHP_EOL."All migrations applied.".PHP_EOL;