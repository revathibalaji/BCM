<?php
session_start();
include('connection.php');
$firstname=$_POST['firstname'];
$lastname=$_POST['lastname'];
$contact=$_POST['contact'];
$username=$_POST['username'];
$password=$_POST['password'];
$gender=$_POST['gender'];
$email=$_POST['email'];
mysql_query("INSERT INTO register(username,password,firstname,lastname,email,contact,gender)VALUES('$username', '$password', '$firstname', '$lastname', '$email', '$contact', '$gender')");
header("location: index.php?remarks=success");
mysql_close($con);
?>