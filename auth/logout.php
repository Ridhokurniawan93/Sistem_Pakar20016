<?php
session_start();
session_destroy();
$project_folder = basename(dirname(__DIR__));
header("Location: /" . $project_folder . "/auth/login.php");
exit();
?>
