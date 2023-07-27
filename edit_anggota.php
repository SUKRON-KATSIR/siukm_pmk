<?php
  // periksa apakah user sudah login, cek kehadiran session name
  // jika tidak ada, redirect ke login.php
  // session_start();
  // if (!isset($_SESSION["nama"])) {
  //    header("Location: login.php");
  // }

  // buka koneksi dengan MySQL
  include("connection.php");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Sistem Informasi UKM</title>
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
  <!-- <form id="search" action="tampil_anggota.php" method="get">
    <p>
      <label for="id_anggota">Nama : </label>
      <input type="text" name="nama" id="nama" placeholder="search..." >
      <input type="submit" name="submit" value="Search">
    </p>
  </form> -->
<h2>Edit Data Anggota UMKM</h2>
<?php
  // tampilkan pesan jika ada
  if ((isset($_GET["pesan"]))) {
      echo "<div class=\"pesan\">{$_GET["pesan"]}</div>";
  }
?>
 <table border="1">
  <tr>
  <th>Nomor</th>
  <th>Nama</th>
  <th>Lokasi</th>
  <th>Tanggal Usaha</th>
  <th>Jenis Usaha</th>
  <th>Nama Usaha</th>
  <th>Modal</th>
  <th></th>
  </tr>
  <?php
  // buat query untuk menampilkan seluruh data tabel anggota
  $query = "SELECT * FROM anggota ORDER BY nama ASC";
  $result = mysqli_query($link, $query);

  if(!$result){
      die ("Query Error: ".mysqli_errno($link).
           " - ".mysqli_error($link));
  }

  //buat perulangan untuk element tabel dari data anggota
  while($data = mysqli_fetch_assoc($result))
  {
    echo "<tr>";
    echo "<td>$data[id_anggota]</td>";
    echo "<td>$data[nama]</td>";
    echo "<td>$data[lokasi]</td>";
    echo "<td>$data[tanggal_usaha]</td>";
    echo "<td>$data[jenis_usaha]</td>";
    echo "<td>$data[usaha]</td>";
    echo "<td>$data[modal]</td>";
    echo "<td>";
    ?>
      <form action="form_edit.php" method="post" >
      <input type="hidden" name="id_anggota" value="<?php echo "$data[id_anggota]"; ?>" >
      <input type="submit" name="submit" value="Edit" >
      </form>
    <?php
    echo "</td>";
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
</body>
</html>
