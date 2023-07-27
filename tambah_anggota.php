<?php
  // periksa apakah user sudah login, cek kehadiran session name
  // jika tidak ada, redirect ke login.php
  // session_start();
  // if (!isset($_SESSION["nama"])) {
  //    header("Location: login.php");
  // }

  // buka koneksi dengan MySQL
  include("connection.php");

  // cek apakah form telah di submit
  if (isset($_POST["submit"])) {
    // form telah disubmit, proses data

    // ambil semua nilai form
    $id_anggota   = htmlentities(strip_tags(trim($_POST["id_anggota"])));
    $nama         = htmlentities(strip_tags(trim($_POST["nama"])));
    $lokasi       = htmlentities(strip_tags(trim($_POST["lokasi"])));
    $jenis_usaha  = htmlentities(strip_tags(trim($_POST["jenis_usaha$jenis_usaha"])));
    $usaha        = htmlentities(strip_tags(trim($_POST["usaha"])));
    $modal        = htmlentities(strip_tags(trim($_POST["modal"])));
    $tgl          = htmlentities(strip_tags(trim($_POST["tgl"])));
    $bln          = htmlentities(strip_tags(trim($_POST["bln"])));
    $thn          = htmlentities(strip_tags(trim($_POST["thn"])));

    // siapkan variabel untuk menampung pesan error
    $pesan_error="";

    // cek apakah "id_anggota" sudah diisi atau tidak
    if (empty($id_anggota)) {
      $pesan_error .= "id_anggota belum diisi <br>";
    }
    // id_anggota harus angka dengan 8 digit
    elseif (!preg_match("/^[0-9]{8}$/",$id_anggota) ) {
      $pesan_error .= "Id anggota harus berupa 8 digit angka <br>";
    }

    // cek ke database, apakah sudah ada id_anggota id_anggota yang sama
    // filter data $id_anggota
    $id_anggota = mysqli_real_escape_string($link,$id_anggota);
    $query = "SELECT * FROM anggota WHERE id_anggota='$id_anggota'";
    $hasil_query = mysqli_query($link, $query);

    // cek jumlah record (baris), jika ada, $id_anggota tidak bisa diproses
    $jumlah_data = mysqli_num_rows($hasil_query);
     if ($jumlah_data >= 1 ) {
       $pesan_error .= "Id anggota yang sama sudah digunakan <br>";
    }

    // cek apakah "nama" sudah diisi atau tidak
    if (empty($nama)) {
      $pesan_error .= "Nama belum di isi <br>";
    }

    // cek apakah "lokasi" sudah diisi atau tidak
    if (empty($lokasi)) {
      $pesan_error .= "Lokasi belum diisi <br>";
    }

    // cek apakah "usaha" sudah diisi atau tidak
    if (empty($usaha)) {
      $pesan_error .= "Jenis usaha belum diisi <br>";
    }

    // siapkan variabel untuk menggenerate pilihan jenis_usaha $jenis_usaha
    $select_Industri=""; $select_Dagang=""; $select_Jasa="";

    switch($jenis_usaha) {
     case "Industri" : $select_Industri = "selected";  break;
     case "Dagang"      : $select_Dagang      = "selected";  break;
     case "Jasa"    : $select_Jasa    = "selected";  break;
    }


    // modal harus berupa angka dan tidak boleh negatif
    if (!is_numeric($modal) OR ($modal <=0)) {
      $pesan_error .= "modal harus diisi dengan angka";
    }

    // jika tidak ada error, input ke database
    if ($pesan_error === "") {

      // filter semua data
      $id_anggota          = mysqli_real_escape_string($link,$id_anggota);
      $nama         = mysqli_real_escape_string($link,$nama );
      $lokasi = mysqli_real_escape_string($link,$lokasi);
      $jenis_usaha     = mysqli_real_escape_string($link,$jenis_usaha);
      $usaha      = mysqli_real_escape_string($link,$usaha);
      $tgl          = mysqli_real_escape_string($link,$tgl);
      $bln          = mysqli_real_escape_string($link,$bln);
      $thn          = mysqli_real_escape_string($link,$thn);
      $modal          = (float) $modal;

      //gabungkan format tanggal agar sesuai dengan date MySQL
      $tgl_lhr = $thn."-".$bln."-".$tgl;

      //buat dan jalankan query INSERT
      $query = "INSERT INTO anggota VALUES ";
      $query .= "('$id_anggota', '$nama', '$lokasi', ";
      $query .= "'$tgl_lhr','$jenis_usaha','$usaha',$modal)";

      $result = mysqli_query($link, $query);

      //periksa hasil query
      if($result) {
      // INSERT berhasil, redirect ke tampil_anggota.php + pesan
        $pesan = "Anggota UMKM dengan nama = \"<b>$nama</b>\" sudah berhasil di tambah";
        $pesan = urlencode($pesan);
        header("Location: tampil_anggota.php?pesan={$pesan}");
      }
      else {
      die ("Query gagal dijalankan: ".mysqli_errno($link).
           " - ".mysqli_error($link));
      }
    }
  }
  else {
    // form belum disubmit atau halaman ini tampil untuk pertama kali
    // berikan nilai awal untuk semua isian form
    $pesan_error      = "";
    $id_anggota       = "";
    $nama             = "";
    $lokasi           = "";
    $select_Industri  = "selected";
    $select_Dagang    = ""; $select_Jasa = "";
    $usaha            = "";
    $modal            = "";
    $tgl=1;$bln="1";$thn=2010;
  }

  // siapkan array untuk nama bulan
  $arr_bln = array( "1"=>"Januari",
                    "2"=>"Februari",
                    "3"=>"Maret",
                    "4"=>"April",
                    "5"=>"Mei",
                    "6"=>"Juni",
                    "7"=>"Juli",
                    "8"=>"Agustus",
                    "9"=>"September",
                    "10"=>"Oktober",
                    "11"=>"Nopember",
                    "12"=>"Desember" );
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

  <!-- <form id="search" action="tampil_anggota.php" method="get">
    <p>
      <label for="id_anggota">Nama : </label>
      <input type="text" name="nama" id="nama" placeholder="search..." >
      <input type="submit" name="submit" value="Search">
    </p>
  </form> -->

<h2>Tambah Data UMKM</h2>
<?php
  // tampilkan error jika ada
  if ($pesan_error !== "") {
      echo "<div class=\"error\">$pesan_error</div>";
  }
?>
<form id="form_anggota" action="tambah_anggota.php" method="post">
<fieldset>
<legend>Anggota Baru</legend>
  <p>
    <label for="id_anggota">Id Anggota : </label>
    <input type="text" name="id_anggota" id="id_anggota" value="<?php echo $id_anggota ?>"
    placeholder="Contoh: 12345678">
    (8 digit angka)
  </p>
  <p>
    <label for="nama">Nama : </label>
    <input type="text" name="nama" id="nama" value="<?php echo $nama ?>">
  </p>
  <p>
    <label for="lokasi">Lokasi : </label>
    <input type="text" name="lokasi" id="lokasi"
    value="<?php echo $lokasi ?>">
  </p>
  <p>
    <label for="tgl" >Tanggal Usaha : </label>
      <select name="tgl" id="tgl">
        <?php
          for ($i = 1; $i <= 31; $i++) {
            if ($i==$tgl){
              echo "<option value = $i selected>";
            }
            else {
              echo "<option value = $i >";
            }
            echo str_pad($i,2,"0",STR_PAD_LEFT);
            echo "</option>";
          }
        ?>
      </select>
        <select name="bln">
        <?php
        foreach ($arr_bln as $key => $value) {
          if ($key==$bln){
            echo "<option value=\"{$key}\" selected>{$value}</option>";
          }
          else {
            echo "<option value=\"{$key}\">{$value}</option>";
          }
        }
        ?>
      </select>
      <select name="thn">
        <?php
          for ($i = 1990; $i <= 2050; $i++) {
          if ($i==$thn){
              echo "<option value = $i selected>";
            }
            else {
              echo "<option value = $i >";
            }
            echo "$i </option>";
          }
        ?>
      </select>
  </p>
  <p>
    <label for="jenis usaha" >Jenis Usaha : </label>
      <select name="jenis_usaha" id="jenis_usaha">
        <option value="Industri" <?php echo $select_Industri ?>>
        Industri </option>
        <option value="Dagang" <?php echo $select_Dagang ?>>
        Dagang</option>
        <option value="Jasa" <?php echo $select_Jasa ?>>
        Jasa</option>

      </select>
  </p>
  <p>
    <label for="usaha">Nama Usaha : </label>
    <input type="text" name="usaha" id="usaha" value="<?php echo $usaha ?>">
  </p>
  <p >
    <label for="modal">Modal : </label>
    <input type="text" name="modal" id="modal" value="<?php echo $modal ?>"
    placeholder="   ">
    <!-- (angka desimal dipisah dengan karakter titik ".") -->
  </p>

</fieldset>
  <br>
  <p>
    <input type="submit" name="submit" value="Tambah Data">
  </p>
</form>

  <div id="footer">
  Copyright © <?php echo date("Y"); ?> Mahasiswa Universitas Madura| Designed by: Sukron Katsir
  </div>

</div>

</body>
</html>
<?php
  // tutup koneksi dengan database mysql
  mysqli_close($link);
?>
