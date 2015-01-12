<?php

require 'functions.php';

if (!empty($_GET['id'])) {
  $id = $_GET['id'];
  $data = get_status($id);
  if ($data) {
    header('Content-Type: text/json');
    echo json_encode($data);
  } else {
    header('HTTP/1.0 404 Not Found');
  }
} else {
  header('HTTP/1.0 400 Bad Request');
}
