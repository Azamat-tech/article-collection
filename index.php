<?php

// include DB configurations
require_once(__DIR__ . '/data/db_config.php');
// include article DB class
require_once (__DIR__ . '/data/database_collection.php');
// include front controller 
require_once(__DIR__ . '/controller.php');

// Get Articles
$db = new DatabaseCollection(
  $db_config['server'], 
  $db_config['login'], 
  $db_config['password'], 
  $db_config['database']
);
$items = $db->loadArticles();

// Instantiate Controller
$controller = new Controller($items, $db);
$controller->handleRequest();

?>
