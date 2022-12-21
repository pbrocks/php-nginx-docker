<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo basename( __DIR__ ); ?></title>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="./css/styles.css">
</head>
<body>
	<div class="container">
  <div class="row">
	<h1>/<?php echo basename( __DIR__ ); ?> Folder</h1>
	<p class="centered">
		Quick Setup
	</p>
  <h2>h2 Bootstrap heading (30px)</h2>
  <h3>h3 Bootstrap heading (24px)</h3>
  <h4>h4 Bootstrap heading (18px)</h4>
  <h5>h5 Bootstrap heading (14px)</h5>
  <h6>h6 Bootstrap heading (12px)</h6>
<?php

	echo '<p style="color:salmon;">' . __FILE__ . '</p>';
?>
  </div>
</div>
</body>
</html>
