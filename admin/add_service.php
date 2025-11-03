<?php
include 'check.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $query->insert('services', [
    'title' => $_POST['title'],
    'description' => $_POST['description']
  ]);
}
header('Location: services.php');
exit;
