<?php
require 'config/koneksi.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Ambil no_ref dari URL
$no_ref = $_GET['no_ref'];

// Ambil data slip
$q = mysqli_query($conn, "
    SELECT 
        s.*, 
        k.nama AS nama_karyawan, 
        k.jabatan,
        p.nama AS nama_perusahaan,
        p.alamat
    FROM slip_gaji s
    JOIN karyawan k ON s.id_karyawan = k.id
    JOIN perusahaan p ON k.id_perusahaan = p.id
    WHERE s.no_ref = '$no_ref'
");
$slip = mysqli_fetch_assoc($q);

// Ambil detail gaji
$detail = mysqli_query($conn, "
    SELECT dg.nominal, kg.keterangan, kg.tipe
    FROM detail_gaji dg
    JOIN keterangan_gaji kg ON dg.id_keterangan_gaji = kg.id
    WHERE dg.no_ref = '$no_ref'
");

// HTML template
ob_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        color: #333;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .company-name {
        font-size: 20px;
        font-weight: bold;
    }

    .company-address {
        font-size: 12px;
    }

    .info-table {
        width: 100%;
        margin-top: 15px;
        margin-bottom: 20px;
    }

    .info-table td {
        padding: 5px 0;
    }

    .section-title {
        background: #f2f2f2;
        padding: 8px;
        font-weight: bold;
        margin-top: 15px;
        border-left: 3px solid #4CAF50;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
    }

    table th {
        background: #4CAF50;
        color: white;
        padding: 8px;
        border: 1px solid #ddd;
        text-align: left;
    }

    table td {
        padding: 8px;
        border: 1px solid #ddd;
    }

    .total-row {
        font-weight: bold;
        background: #f9f9f9;
    }

    .footer {
        text-align: right;
        margin-top: 30px;
        font-style: italic;
    }

    .split-table {
        width: 100%;
        display: table;
    }

    .split-col {
        display: table-cell;
        width: 50%;
        vertical-align: top;
        padding-right: 10px;
    }
    </style>
</head>

<body>

    <div class="header">
        <div class="company-name"><?= $slip['nama_perusahaan'] ?></div>
        <div class="company-address"><?= $slip['alamat'] ?></div>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>No. Referensi</strong></td>
            <td>: <?= $slip['no_ref'] ?></td>
        </tr>
        <tr>
            <td><strong>Tanggal</strong></td>
            <td>: <?= date('d F Y', strtotime($slip['tanggal'])) ?></td>
        </tr>
        <tr>
            <td><strong>Nama Karyawan</strong></td>
            <td>: <?= $slip['nama_karyawan'] ?></td>
        </tr>
        <tr>
            <td><strong>Jabatan</strong></td>
            <td>: <?= $slip['jabatan'] ?></td>
        </tr>
    </table>

    <div class="section-title">Rincian Penghasilan & Potongan</div>

    <div class="split-table">
        <div class="split-col">
            <h4>Penghasilan</h4>
            <table>
                <thead>
                    <tr>
                        <th>Keterangan</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    mysqli_data_seek($detail, 0);
                    $total_pemasukan = 0;
                    while($row = mysqli_fetch_assoc($detail)):
                        if ($row['tipe'] == 'pemasukan'):
                            $total_pemasukan += $row['nominal'];
                    ?>
                    <tr>
                        <td><?= ucfirst($row['keterangan']) ?></td>
                        <td>Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; endwhile; ?>
                    <tr class="total-row">
                        <td>Total Pemasukan</td>
                        <td>Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="split-col">
            <h4>Potongan</h4>
            <table>
                <thead>
                    <tr>
                        <th>Keterangan</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    mysqli_data_seek($detail, 0);
                    $total_potongan = 0;
                    while($row = mysqli_fetch_assoc($detail)):
                        if ($row['tipe'] == 'potongan'):
                            $total_potongan += $row['nominal'];
                    ?>
                    <tr>
                        <td><?= ucfirst($row['keterangan']) ?></td>
                        <td>Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; endwhile; ?>
                    <tr class="total-row">
                        <td>Total Potongan</td>
                        <td>Rp <?= number_format($total_potongan, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <br>

    <div class="section-title">Total Gaji Bersih</div>

    <table>
        <tr class="total-row">
            <td><strong>Gaji Diterima</strong></td>
            <td>
                <strong>
                    Rp <?= number_format($total_pemasukan - $total_potongan, 0, ',', '.') ?>
                </strong>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dicetak pada: <?= date('d-m-Y H:i') ?>
    </div>

</body>

</html>


<?php
$html = ob_get_clean();

// Dompdf options
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output PDF ke browser
$dompdf->stream("slip-gaji-$no_ref.pdf", array("Attachment" => false));
?>