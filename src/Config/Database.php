<?php

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                // Cloud Deployments (Render, Supabase, etc)
                $dbUrl = getenv('DATABASE_URL');
                $dbHost = getenv('DB_HOST');
                
                if ($dbUrl) {
                    // Example: postgres://user:pass@host:port/dbname
                    $dbopts = parse_url($dbUrl);
                    $dsn = "pgsql:host={$dbopts["host"]};port={$dbopts["port"]};dbname=" . ltrim($dbopts["path"], '/');
                    $user = $dbopts["user"];
                    $pass = $dbopts["pass"];

                    self::$instance = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                } elseif ($dbHost) {
                    $dbName = getenv('DB_NAME') ?: 'codegames';
                    $dbUser = getenv('DB_USER') ?: 'root';
                    $dbPass = getenv('DB_PASS') ?: '';
                    
                    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
                    
                    self::$instance = new PDO($dsn, $dbUser, $dbPass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                } else {
                    // Fallback to local SQLite (Persistent on traditional hosts)
                    $dbPath = __DIR__ . '/../../database.sqlite';
                    $dsn = "sqlite:$dbPath";
                    
                    self::$instance = new PDO($dsn, null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                    
                    // Enable foreign keys for SQLite
                    self::$instance->exec('PRAGMA foreign_keys = ON;');
                }
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    public static function generateUuid(): string {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
}

