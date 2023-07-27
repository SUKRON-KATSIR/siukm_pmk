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
    // form telah disubmit, cek apakah berasal dari edit_anggota.php
    // atau update data dari form_edit.php

    if ($_POST["submit"]=="Edit") {
      //nilai form berasal dari halaman edit_anggota.php

      // ambil nilai id_anggota
      $id_anggota = htmlentities(strip_tags(trim($_POST["id_anggota"])));
      // filter data
      $id_anggota = mysqli_real_escape_string($link,$id_anggota);

      // ambil semua data dari database untuk menjadi nilai awal form
      $query = "SELECT * FROM anggota WHERE id_anggota='$id_anggota'";
      $result = mysqli_query($link, $query);

      if(!$result){
        die ("Query Error: ".mysqli_errno($link).
             " - ".mysqli_error($link));
      }

      // tidak perlu pakai perulangan while, karena hanya ada 1 record
      $data = mysqli_fetch_assoc($result);

      $nama         = $data["nama"];
      $lokasi = $data["lokasi"];
      $jenis_usaha     = $data["jenis_usaha"];
      $usaha      = $data["usaha"];
      $modal          = $data["modal"];

      // untuk tanggal harus dipecah
      $tgl = substr($data["tanggal_usaha"],8,2);
      $bln = substr($data["tanggal_usaha"],5,2);
      $thn = substr($data["tanggal_usaha"],0,4);

    // bebaskan memory
    mysqli_free_result($result);
    }

    else if ($_POST["submit"]=="Update Data") {
      // nilai form berasal dari halaman form_edit.php
      // ambil semua nilai form
      $id_anggota   = htmlentities(strip_tags(trim($_POST["id_anggota"])));
      $nama         = htmlentities(strip_tags(trim($_POST["nama"])));
      $lokasi       = htmlentities(strip_tags(trim($_POST["lokasi"])));
      $jenis_usaha  = htmlentities(strip_tags(trim($_POST["jenis_usaha"])));
      $usaha        = htmlentities(strip_tags(trim($_POST["usaha"])));
      $modal        = htmlentities(strip_tags(trim($_POST["modal"])));
      $tgl          = htmlentities(strip_tags(trim($_POST["tgl"])));
      $bln          = htmlentities(strip_tags(trim($_POST["bln"])));
      $thn          = htmlentities(strip_tags(trim($_POST["thn"])));
    }

    // proses validasi form
    // siapkan variabel untuk menampung pesan error
    $pesan_error="";

    // cek apakah "id_anggota" sudah diisi atau tidak
    if (empty($id_anggota)) {
      $pesan_error .= "id_anggota belum diisi <br>";
    }
   // id_anggota harus angka dengan 8 digit
    elseif (!preg_match("/^[0-9]{8}$/",$id_anggota) ) {
      $pesan_error .= "id_anggota harus berupa 8 digit angka <br>";
    }

    // cek apakah "nama" sudah diisi atau tidak
    if (empty($nama)) {
      $pesan_error .= "Nama belum diisi <br>";
    }

    // cek apakah "lokasi" sudah diisi atau tidak
    if (empty($lokasi)) {
      $pesan_error .= "Lokasi belum diisi <br>";
    }

    // cek apakah "usaha" sudah diisi atau tidak
    if (empty($usaha)) {
      $pesan_error .= "usaha belum diisi <br>";
    }



    // siapkan variabel untuk menggenerate pilihan jenis_usaha
    $select_industri=""; $select_dagang=""; $select_jasa="";

    
    switch($jenis_usaha) {
     case "Industri" : $select_industri = "selected";  break;
     case "Dagang"      : $select_dagang      = "selected";  break;
     case "Jasa"    : $select_jasa    = "selected";  break;

    }


    // modal harus berupa angka dan tidak boleh negatif
    if (!is_numeric($modal) OR ($modal <=0)) {
      $pesan_error .= "modal harus diisi dengan angka";
    }

    // jika tidak ada error, input ke database
    if (($pesan_error === "") AND ($_POST["submit"]=="Update Data")) {

      // buka koneksi dengan MySQL
      include("connection.php");

      // filter semua data
      $id_anggota   = mysqli_real_escape_string($link,$id_anggota);
      $nama         = mysqli_real_escape_string($link,$nama );
      $lokasi       = mysqli_real_escape_string($link,$lokasi);
      $jenis_usaha  = mysqli_real_escape_string($link,$jenis_usaha);
      $usaha        = mysqli_real_escape_string($link,$usaha);
      $tgl          = mysqli_real_escape_string($link,$tgl);
      $bln          = mysqli_real_escape_string($link,$bln);
      $thn          = mysqli_real_escape_string($link,$thn);
      $modal          = (float) $modal;

      //gabungkan format tanggal agar sesuai dengan date MySQL
      $tgl_lhr = $thn."-".$bln."-".$tgl;

      //buat dan jalankan query UPDATE
      $query  = "UPDATE anggota SET ";
      $query .= "nama = '$nama', lokasi = '$lokasi', ";
      $query .= "tanggal_usaha = '$tgl_lhr', jenis_usaha='$jenis_usaha', ";
      $query .= "usaha = '$usaha', modal=$modal ";
      $query .= "WHERE id_anggota = '$id_anggota'";

      $result = mysqli_query($link, $query);

      //periksa hasil query
      if($result) {
      // INSERT berhasil, redirect ke tampil_anggota.php + pesan
        $pesan = "Anggota UMKM dengan nama = \"<b>$nama</b>\" sudah berhasil di update";
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
    // form diakses secara langsung!
    // redirect ke edit_anggota.php
    header("Location: edit_anggota.php");
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
  // tampilkan error jika ada
  if ($pesan_error !== "") {
      echo "<div class=\"error\">$pesan_error</div>";
  }
?>
<form id="form_anggota" action="form_edit.php" method="post">
<fieldset>
<legend>Anggota UMKM Baru</legend>
  <p>
    <label for="id_anggota">Id Anggota : </label>
    <input type="text" name="id_anggota" id="id_anggota" value="<?php echo $id_anggota ?>" readonly>
    (tidak bisa diubah di menu edit)
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
    <label for="jenis_usaha" >Jenis Usaha : </label>
      <select name="jenis_usaha" id="jenis_usaha">
        <option value="Industri" <?php echo $select_industri ?>>
        Industri </option>
        <option value="Dagang" <?php echo $select_dagang ?>>
        Dagang</option>
        <option value="Jasa" <?php echo $select_jasa ?>>
        Jasa</option>
      </select>
  </p>
  <p>
    <label for="usaha">Nama Usaha : </label>
    <input type="text" name="usaha" id="usaha" value="<?php echo $usaha ?>">
  </p>
  <p >
    <label for="modal">Modal : </label>
    <input type="text" name="modal" id="modal" value="<?php echo $modal ?>">
    <!-- (angka desimal dipisah dengan karakter titik ".") -->
  </p>

</fieldset>
  <br>
  <p>
    <input type="submit" name="submit" value="Update Data">
  </p>
</form>

</div>

</body>
</html>
<?php
  // tutup koneksi dengan database mysql
  mysqli_close($link);
?>
