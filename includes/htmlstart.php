<?php
/** @var string $strWebTitle */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($strWebTitle ?? 'Welcome') ?> | WebbyCMS</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/custom.css?version=0.1">
</head>
<body>
<?php include __DIR__ . '/error.php'; ?>
