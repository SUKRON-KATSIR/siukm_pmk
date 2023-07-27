<?php

  // buka koneksi dengan MySQL
     include("connection.php");
  
  // ambil pesan jika ada  
  if (isset($_GET["pesan"])) {
      $pesan = $_GET["pesan"];
  }
     
  // cek apakah form telah di submit
  // berasal dari form pencairan, siapkan query 
  if (isset($_GET["submit"])) {
    // ambil nilai nama
    $nama = htmlentities(strip_tags(trim($_GET["nama"])));
    // filter untuk $nama untuk mencegah sql injection
    $nama = mysqli_real_escape_string($link,$nama);    
    // buat query pencarian nama
    $query  = "SELECT * FROM anggota WHERE nama LIKE '%$nama%' ";
    $query .= "ORDER BY tanggal_usaha ASC";    
    // buat pesan
    $pesan = "Hasil pencarian untuk nama <b>\"$nama\" </b>:";
  } else {
      // bukan dari form pencairan
      // siapkan query untuk menampilkan seluruh data dari tabel anggota
      $query = "SELECT * FROM anggota ORDER BY tanggal_usaha ASC";      
    }

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Sistem Informasi UMKM</title>
  <link href="style3.css" rel="stylesheet" >
  <link rel="icon" href="favicon.png" type="image/png" >
</head>
<body>
<div class="container">
<div id="header">
  <h1 id="logo">Sistem Informasi <span>Usaha Kecil Menengah</span></h1>
  <p id="tanggal"><?php echo date("d M Y"); ?></p>
</div>

<hr>
  <nav>
  <ul>
    <li><a href="index1.php">Kembali</a></li>
    <li><a href="tampil_anggota.php">Tampil</a></li>
    <li><a href="tambah_anggota.php">Tambah</a>
    <li><a href="edit_anggota.php">Edit</a>
    <li><a href="hapus_anggota.php">Hapus</a></li>
    <li><a href="tampil2.php">Cari Lokasi</a></li>
    <li><a href="logout.php">Logout</a>
  </ul>
  </nav>

  <!-- tabel pencarian -->
  <form id="search" action="tampil_anggota.php" method="get">
    <p>
      <label for="id_anggota">Nama : </label> 
      <input type="text" name="nama" id="nama" placeholder="search..." >
      <input type="submit" name="submit" value="Search">
    </p> 
  </form>


  
<h2>Data Anggota UMKM</h2>
<?php
  // tampilkan pesan jika ada
  if (isset($pesan)) {
      echo "<div class=\"pesan\">$pesan</div>";
  }
?>
 <table border="1">
  <tr>
  <th>Id Anggota</th>
  <th>Nama</th>
  <th>Lokasi</th>
  <th>Tanggal Usaha</th>
  <th>Jenis Usaha</th>
  <th>Nama Usaha</th>
  <th>Modal</th>
  </tr>
  <?php
  // jalankan query
  $result = mysqli_query($link, $query);
  
  if(!$result){
      die ("Query Error: ".mysqli_errno($link).
           " - ".mysqli_error($link));
  }
  
  //buat perulangan untuk element tabel dari data anggota
  while($data = mysqli_fetch_assoc($result))
  { 
    // konversi date MySQL (yyyy-mm-dd) menjadi dd-mm-yyyy
    $tanggal_php = strtotime($data["tanggal_usaha"]);
    $tanggal = date("d - m - Y", $tanggal_php);
    
    echo "<tr>";
    echo "<td>$data[id_anggota]</td>";
    echo "<td>$data[nama]</td>";
    echo "<td>$data[lokasi]</td>";
    echo "<td>$tanggal</td>";
    echo "<td>$data[jenis_usaha]</td>";
    echo "<td>$data[usaha]</td>";
    echo "<td>$data[modal]</td>";
    echo "</tr>";
  }
  
  // bebaskan memory 
  mysqli_free_result($result);
  
  // tutup koneksi dengan database mysql
  mysqli_close($link);
  ?>
  </table>
  <div id="footer">
  Copyright © <?php echo date("Y"); ?> Mahasiswa Universitas Madura| Designed by: Sukron Katsir
  </div>
</div>
</body>
</html>