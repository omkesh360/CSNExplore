<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script>
	var w3_speedster_html_cache_purge = '<?php echo $this->createSecureKey('w3_speedster_html_cache_purge');?>';
	var w3_speedster_cache_purge = '<?php echo $this->createSecureKey('w3_speedster_cache_purge');?>';
	var w3_speedster_critical_cache_purge = '<?php echo $this->createSecureKey('w3_speedster_critical_cache_purge');?>';
	</script>
    <?php $this->enqueueScripts(); $w3admin = $this;?>
    <title>Document</title>
</head>
<body class="wp-core-ui">
    <?php include W3SPEEDSTER_PATH . "/admin/Admin.php";?>
</body>
</html>
