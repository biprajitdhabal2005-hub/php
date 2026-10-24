<?php
session_start();
echo "Welcome " . $_SESSION['name'] . "! Your role is: " . $_SESSION['role'];
?>