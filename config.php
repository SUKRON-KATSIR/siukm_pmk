<!-- menghubungkn database ke server -->
<?php 
 
$server = "localhost";
$user = "root";
$pass = "";
$database = "siukm";
 
$conn = mysqli_connect($server, $user, $pass, $database);
 
if (!$conn) {
    die("<script>alert('Gagal tersambung dengan database.')</script>");
}

?>