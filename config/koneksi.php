<?php
$host="localhost";
$user='root';
$password='Yahya123#';
$db="payroll";

$conn=mysqli_connect($host, $user, $password, $db);

if(!$conn){
    die('Koneksi Gagal: '.mysqli_connect_error());
}