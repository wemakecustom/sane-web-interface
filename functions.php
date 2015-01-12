<?php

$formats = array(
  'Letter' => array('l' => 0, 't' => 0, 'x' => 215.9, 'y' => 279.4),
  'Legal'  => array('l' => 0, 't' => 0, 'x' => 215.9, 'y' => 355.6),
);

function get_dir($id) {
  return __DIR__ . '/output/' . $id;
}

function get_status($id) {
  $file = get_dir($id) . '/status.json';
  if (file_exists($file)) {
    return json_decode(file_get_contents($file), true);
  } else {
    return array();
  }
}

function set_status($id, $data, $merge = true) {
  $file = get_dir($id) . '/status.json';
  if ($merge) {
    $data = array_merge(get_status($id), $data);
  }
  return file_put_contents($file, json_encode($data));
}
