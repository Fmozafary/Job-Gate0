<?php
include("../../php/db.php");
$id = $_GET['id'];
$pdo->prepare("DELETE FROM expert_requests WHERE id = ?")->execute([$id]);
header("Location: ../admin/experts.php");