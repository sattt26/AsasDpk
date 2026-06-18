<?php
class User {
    public $namaPelanggan;
    public $poin = 0;

    public function __construct($nama) {
        $this->namaPelanggan = $nama;
    }
    public function getnama(){
        return $this->namaPelanggan; 
    }
    public function setpoin() {
        return $this->poin;
    }
    public function hitungPoin($totalBayar) {
        $this->poin = floor($totalBayar / 10000);
    }
}
class Menu {
    public $namaMenu;
    public $hargaDasar;

    public function __construct($nama, $harga) {
        $this->namaMenu = $nama;
        $this->hargaDasar = $harga;
    }

    public function hitungHargaTotalUkuran($ukuran) {
        $tambahanBiaya = 0;

        if ($ukuran == "Medium") {
            $tambahanBiaya = 3000;  
        } else if ($ukuran == "Large") {
            $tambahanBiaya = 5000;  
        } else {
            $tambahanBiaya = 0;     
        }
        return $this->hargaDasar + $tambahanBiaya;
    }
}
class MenuPromo extends Menu {
    public function hitungHargaTotalUkuran($ukuran) {
        $tambahanBiaya = 0;

        if ($ukuran == "Medium") {
            $tambahanBiaya = 1500;  
        } else if ($ukuran == "Large") {
            $tambahanBiaya = 2000;  
        } else {
            $tambahanBiaya = 0;
        }

        return $this->hargaDasar + $tambahanBiaya;
    }
}
class Voucher {
    public $kodeVoucher;
    public $persenDiskon = 0;

    public function __construct($kode) {
        $this->kodeVoucher = $kode;
        if ($kode == "HEMAT10") {
            $this->persenDiskon = 10;
        }
    }

    public function hitungPotongan($totalHarga) {
        return $totalHarga * $this->persenDiskon / 100;
    }
}

$summary = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $namaInput = $_POST['nama'];
    $menuInput = $_POST['menu']; 
    $jenisInput = $_POST['jenis'];
    $ukuranInput = $_POST['ukuran'];
    $statusMenu = $_POST['status_menu']; 
    $voucherInput = $_POST['voucher'];

    $pecahMenu = explode('|', $menuInput);
    $namaMenu = $pecahMenu[0];
    $hargaMenu = (int)$pecahMenu[1];

    if ($statusMenu == "Promo") {
        $menu = new MenuPromo($namaMenu, $hargaMenu);
    } else {
        $menu = new Menu($namaMenu, $hargaMenu);
    }
    
    $hargaAwal = $menu->hitungHargaTotalUkuran($ukuranInput);
    $voucher = new Voucher($voucherInput);
    $potonganDiskon = $voucher->hitungPotongan($hargaAwal);
    $totalAkhir = $hargaAwal - $potonganDiskon;
    $user = new User($namaInput);
    $user->hitungPoin($totalAkhir);
    $summary = [
        "nama" => $user->namaPelanggan,
        "menu" => $menu->namaMenu,
        "jenis" => $jenisInput,
        "ukuran" => $ukuranInput,
        "status" => $statusMenu,
        "harga_awal" => $hargaAwal,
        "diskon" => $potonganDiskon,
        "total_bayar" => $totalAkhir,
        "voucher" => $voucher->kodeVoucher ?: "-",
        "poin" => $user->poin
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Coffee System - OOP Inheritance</title>
    <style>
    body{ 
        font-family: Arial, sans-serif;
        display: flex; 
        gap: 20px; 
        padding: 20px; 
    }
    .left,.right{ 
        width: 50%; 
        padding: 20px; 
        border: 1px solid #ccc; 
        border-radius: 8px; 
    }
    input, select{ 
        display: block; 
        margin-bottom: 12px; 
        width: 100%; 
        max-width: 250px; 
        padding: 6px; 
    }
    button{ 
        padding: 8px 15px; 
        cursor: pointer; 
        background-color: #28a745; 
        color: white; 
        border: none; 
        border-radius: 4px; 
    }
    button:hover{ 
        background-color: #218838; 
    }
    </style>
</head>
<body>
    <div class="left">
        <h3>Order Coffee & Food</h3>
        <form method="POST">
            <input type="text" name="nama" placeholder="Nama Pelanggan" required>
          
            <select name="jenis" id="jenis" onchange="updateMenu()">
                <option value="Minuman">Minuman</option>
                <option value="Makanan">Makanan</option>
            </select>

            <select name="menu" id="menu">
                </select>

            <select name="status_menu">
                <option value="Reguler">Harga Reguler</option>
                <option value="Promo">Harga Promo</option>
            </select>

            <select name="ukuran">
                <option value="Small">Small</option>
                <option value="Medium">Medium</option>
                <option value="Large">Large</option>
            </select>

            <input type="text" name="voucher" placeholder="Kode Voucher (Contoh: HEMAT10)">

            <button type="submit">Pesan Sekarang</button>
        </form>
    </div>
    
    <div class="right">
        <h3>Order Summary</h3>
        <hr>
        
        <?php if ($summary): ?>
            <p><strong>Nama:</strong> <?= $summary['nama'] ?></p>
            <p><strong>Menu:</strong> <?= $summary['menu'] ?> (<?= $summary['jenis'] ?>)</p>
            <p><strong>Jenis Harga:</strong> <?= $summary['status'] ?></p>
            <p><strong>Ukuran:</strong> <?= $summary['ukuran'] ?></p>
            <p><strong>Harga Total + Ukuran:</strong> Rp <?= number_format($summary['harga_awal']) ?></p>
            <p><strong>Potongan Diskon:</strong> Rp <?= number_format($summary['diskon']) ?> (Voucher: <?= $summary['voucher'] ?>)</p>
            <hr>
            <p style="font-size: 18px; color: #28a745;"><strong>Total Bayar: Rp <?= number_format($summary['total_bayar']) ?></strong></p>
            <p><strong>Poin Member:</strong> +<?= $summary['poin'] ?> Poin</p>
        <?php else: ?>
            <p style="color: #888;">Belum ada pesanan.</p>
        <?php endif; ?>
    </div>

    <script>
        const dataMenu = {
            Minuman: [
                { value: "Americano|20000", text: "Americano - Rp20.000" },
                { value: "Latte|25000", text: "Latte - Rp25.000" },
                { value: "Matcha|25000", text: "Matcha - Rp25.000" },
                { value: "Chocolate|30000", text: "Chocolate - Rp30.000" }
            ],
            Makanan: [
                { value: "Croissant|18000", text: "Croissant - Rp18.000" },
                { value: "Brownies|15000", text: "Brownies - Rp15.000" },
                { value: "French Fries|22000", text: "French Fries - Rp22.000" },
                { value: "Sandwich|25000", text: "Sandwich - Rp25.000" }
            ]
        };

        function updateMenu() {
            const jenisSelect = document.getElementById('jenis');
            const menuSelect = document.getElementById('menu');
            const jenisTerpilih = jenisSelect.value;

            menuSelect.innerHTML = "";

            const daftarMenu = dataMenu[jenisTerpilih];
   
            daftarMenu.forEach(item => {
                const opsi = document.createElement('option');
                opsi.value = item.value;
                opsi.text = item.text;
                menuSelect.appendChild(opsi);
            });
        }      
        window.onload = updateMenu;
    </script>
</body>
</html>