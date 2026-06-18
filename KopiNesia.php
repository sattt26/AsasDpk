<?php
class User {
    public $nama;
    public $poinMember = 0;

    public function __construct($nama){
        $this->nama = $nama;
    }

    public function tambahPoin($totalBayar){
        $this->poinMember += floor($totalBayar / 10000);
    }
}

class Menu {
    public $namaMenu;
    public $harga;

    public function __construct($namaMenu, $harga){
        $this->namaMenu = $namaMenu;
        $this->harga = $harga;
    }

    public function getHarga(){
        return $this->harga;
    }
}

class Voucher {
    public $diskon = 0;

    public function __construct($kode){
        if($kode == "HEMAT10"){
            $this->diskon = 10;
        }
    }

    public function hitung($total){
        return $total * $this->diskon / 100;
    }
}

class Pesanan {
    public static function add($data){
        $_SESSION['riwayat'][] = $data;
    }

    public static function get(){
        return $_SESSION['riwayat'] ?? [];
    }
}

$summary = null;

if($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['clear'])){

    $nama = $_POST['nama'];
    list($menuNama, $menuHarga) = explode('|', $_POST['menu']);
    $menuHarga = (int)$menuHarga;
    $jenis = $_POST['jenis'];
    $ukuran = $_POST['ukuran'];
    $voucherInput = $_POST['voucher'];


    $ukuranHarga = 0;
    if($ukuran == "Medium") ;
    if($ukuran == "Regular") ;

    $user = new User($nama);
    $menu = new Menu($menuNama, $menuHarga);

    $hargaAwal = $menu->getHarga() + $ukuranHarga;

    $voucher = new Voucher($voucherInput);
    $diskon = $voucher->hitung($hargaAwal);

    $totalBayar = $hargaAwal - $diskon;

    $user->tambahPoin($totalBayar);

    Pesanan::add([
        "tanggal" => date("Y-m-d H:i:s"),
        "menu" => $menuNama,
        "harga" => $totalBayar
    ]);

    $summary = [
        "nama" => $nama,
        "menu" => $menuNama,
        "jenis" => $jenis,
        "ukuran" => $ukuran,
        "harga_awal" => $menuHarga,
        "total_bayar" => $totalBayar,
        "voucher" => $voucherInput ?: "-",
        "poin" => $user->poinMember
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
    <title>Coffe System Modern UI</title>

    <style>
    body{
        font-family: Arial;
        display:flex;
        gap:20px;
    }

    .left,.right{
        width:50%;
        padding:20px;
        border:1px solid #ccc;
    }

    input,select{
        display:block;
        margin-bottom:10px;
        width:200px;
        padding:5px;
    }

    button{
        padding:8px 15px;
        cursor:pointer;
    }
    </style>
</head>

    <body>
      <div class="left">
        <h3>Order Coffee</h3>

        <form method="POST">
          <input type="text" name="nama" placeholder="Nama" required>

            <select name="menu">
            <option value="Americano|20000">Americano - Rp20.000</option>
            <option value="Latte|25000">Latte - Rp25.000</option>
            <option value="Matcha|25000">Matcha - Rp25.000</option>
            <option value="Chocolate|30000">Chocolate - Rp30.000</option>
            </select>

            <select name="jenis">
            <option value="Kopi">Coffe</option>
            <option value="Non Kopi">Non Coffe</option>
            </select>

            <select name="ukuran">
            <option value="Small">Small</option>
            <option value="Medium">Medium</option>
            <option value="Large">Large</option>
            </select>

          <input type="text" name="voucher" placeholder="Voucher">

            <button type="submit">Pesan</button>
        </form>
      </div>

<div class="right">
<h3>Order Summary</h3>

<pre>
    <?php
    if($summary){
        echo "Nama: {$summary['nama']}\n";
        echo "Menu: {$summary['menu']}\n";
        echo "Jenis: {$summary['jenis']}\n";
        echo "Ukuran: {$summary['ukuran']}\n";
        echo "Harga Awal: {$summary['harga_awal']}\n";
        echo "Total Bayar: {$summary['total_bayar']}\n";
        echo "Voucher: {$summary['voucher']}\n";
        echo "Poin: {$summary['poin']}\n";
    }else{
        echo "Belum ada pesanan";
    }
    ?>
    </pre>

    <form method="POST">
    <button name="clear">Hapus Riwayat</button>
    </form>

    <h4>Riwayat Transaksi</h4>

    <?php foreach(Pesanan::get() as $r): ?>
    <div>
    <?= $r['tanggal'] ?> - <?= $r['menu'] ?> - Rp <?= $r['harga'] ?>
    </div>
    <?php endforeach; ?>

 </div>
    </body>
</html>