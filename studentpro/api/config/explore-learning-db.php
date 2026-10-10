<?php
declare(strict_types=1);

function exploreLearningDatabase(): PDO
{
    $storageDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage';
    if (!is_dir($storageDirectory) && !mkdir($storageDirectory, 0700, true) && !is_dir($storageDirectory)) {
        throw new RuntimeException('Could not create Explore Learning storage directory.');
    }

    $databasePath = $storageDirectory . DIRECTORY_SEPARATOR . 'explore-learning.sqlite';
    $database = new PDO('sqlite:' . $databasePath, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    $database->exec('PRAGMA foreign_keys = ON');
    $database->exec('PRAGMA busy_timeout = 5000');

    foreach (['001_explore_learning.sql', '002_explore_learning_seed.sql'] as $migration) {
        $migrationPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . $migration;
        $sql = file_get_contents($migrationPath);
        if ($sql === false) {
            throw new RuntimeException('Explore Learning database migration is unavailable.');
        }
        $database->exec($sql);
    }

    return $database;
}

function exploreLearningLearnerKey(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_set_cookie_params([
            'httponly' => true,
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'samesite' => 'Lax'
        ]);
        session_start();
    }

    return hash('sha256', session_id());
}
