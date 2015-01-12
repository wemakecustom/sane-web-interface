<?php

require 'functions.php';

session_start();

?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scan</title>
</head>
<body>
    <form id="form" action="start.php" method="post">
        <p>
          <label>
            Format:
            <select name="format">
              <?php foreach ($formats as $format => $values): ?>
              <option><?php echo $format; ?></option>
              <?php endforeach ?>
            </select>
          </label>
        </p>
        <p>
          <label>
            Resolution:
            <select name="resolution">
              <option>100</option>
              <option selected>200</option>
              <option>400</option>
              <option>600</option>
              <option>1200</option>
            </select>
          </label>
        </p>
        <input type="submit" name="scan" value="Scan">
    </form>

    <p>Status: <span id="status-step">Idle</span></p>
    <p id="progress" style="transition: 0.5s all linear; box-sizing: border-box; padding: 5px; color: white; background: green; display: none"></p>
    <a target="_blank" id="url" style="display:none">Download</a>

    <table>
      <caption>Past jobs</caption>
      <tr>
      <?php foreach($_SESSION['ids'] as $id): if (!($status = get_status($id))) continue; ?>
        <td><a <?php if (isset($status['url'])) echo 'href="'.$status['url'].'"'; ?>><?php echo $id ?></a></td>
        <td><?php echo $status['step']; ?></td>
      </tr>
      <?php endforeach ?>
    </table>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
