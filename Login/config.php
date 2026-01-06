<?php
require_once __DIR__ . "/vendor/autoload.php";

use MongoDB\Client;

const MONGO_URI = 'mongodb://127.0.0.1:27017';
const DB_NAME = 'minae';
const USER_COLLECTION = 'user';

function usersCollection()
{
    static $collection = null;
    if ($collection === null) {
        $client = new Client(MONGO_URI);
        $collection = $client->selectCollection(DB_NAME, USER_COLLECTION);
    }
    return $collection;
}

function startSession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function requireAuth(): void
{
    startSession();
    if (empty($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }
}

function currentUser(): ?array
{
    startSession();
    return $_SESSION['user'] ?? null;
}
