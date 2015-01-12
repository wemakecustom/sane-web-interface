<?php

require 'functions.php';

if (empty($_POST['id'])) {
  $id = sha1(uniqid(). '^%$EDFGHJKDtrdghjsdorirj');
  header('Content-Type: text/json');
  echo json_encode(array('id' => $id));
  die();
}

$id = $_POST['id'];
$dir = __DIR__ . '/output/' . $id;

session_start();
$_SESSION['ids'][] = $id;
session_write_close();


function run_scan($id, $batch_pattern, $options, $resolution) {
  $args = "";
  foreach ($options as $key => $value) {
    $args .= " -$key $value";
  }
    
  $specs = array(
     2 => array("pipe", "w"), // stderr
  );

  $process = proc_open("scanimage --progress --batch=$batch_pattern $args --resolution $resolution", $specs, $pipes);
  set_status($id, array('step' => 'connecting'));

  stream_set_blocking($pipes[2], false);

  while (!feof($pipes[2])) {
      $line = stream_get_contents($pipes[2]);
      if (preg_match('/^Progress: (\d+\.\d+)%/', $line, $matches)) {
        set_status($id, array('step' => 'scanning', 'perc' => $matches[1]));
      }
      if (preg_match('/Document feeder out of documents/', $line)) {
        proc_terminate($process);
        break;
      }
      usleep(300000);
  }
  fclose($pipes[2]);
  proc_close($process);

  set_status($id, array('perc' => 100));
}


$success = false;

$format = 'Letter';
if (isset($_POST['format']) && isset($formats[$_POST['format']])) $format = $_POST['format'];

$resolution = 400;
if (isset($_POST['resolution']) && preg_match('/^\d+00$/', $_POST['resolution'])) $resolution = $_POST['resolution'];

if (mkdir($dir)) {
    run_scan($id, "$dir/out%d.pnm", $formats[$format], $resolution);

    if (file_exists("$dir/out1.pnm")) {
        set_status($id, array('step' => 'assembling'));
        $pnm = glob("$dir/out*.pnm");
        $strip = strlen("$dir/out");
        usort($pnm, function($a, $b) use ($strip) {
          $a = (int) substr($a, $strip);
          $b = (int) substr($b, $strip);
          return $a - $b;
        });
        $inputs = implode(' ', $pnm);
        exec("convert $inputs -compress jpeg -quality 100 $dir/out.pdf", $output);

        if (file_exists("$dir/out.pdf")) {
            array_map('unlink', glob("$dir/*.pnm"));

            $success = true;
        }
    }
}

header('Content-Type: text/json');
set_status($id, array('step' => 'done', 'url' => "output/$id/out.pdf"));
echo json_encode(array('url' => "output/$id/out.pdf"));
