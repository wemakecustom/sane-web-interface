<?php

$specs = array(
   1 => array("file", "/dev/null", "w"), // stderr
   2 => array("pipe", "w"), // stderr
);

$process = proc_open('scanimage --progress', $specs, $pipes);

stream_set_blocking($pipes[2], false);

while (!feof($pipes[2])) {
    // echo $line;
    $line = stream_get_contents($pipes[2]);
    if (preg_match('/^Progress: (\d+\.\d+%)/', $line, $matches)) {
      echo $matches[1]."\n";
    }
    usleep(300000);
}
fclose($pipes[2]);

proc_close($process);
