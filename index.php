<?php
include 'koneksi.php';

// Inisialisasi variabel
$unused_count = 0;
$issued_count = 0; // Set this from a query if needed
$total_Issued_nominal = 0; // Set this from a query if needed
$total_used_nominal = 0; // Set this from a query if needed
$total_issued_due_soon_nominal = 0; // Set this from a query if needed

// Query untuk menghitung jumlah giro yang belum digunakan
$sql = "SELECT COUNT(*) AS unused_count FROM data_giro WHERE statusgiro='Unused'";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $unused_count = $row['unused_count'];
} else {
    $unused_count = 0;
}

// Query untuk menghitung jumlah giro yang sudah diterbitkan
$sql = "SELECT COUNT(*) AS issued_count FROM detail_giro WHERE statgiro='Issued'";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $issued_count = $row['issued_count'];
} else {
    $issued_count = 0;
}

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Utama</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional: link to an external CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        header {
            text-align: center;
            padding: 20px;
            background-color: #343a40;
            color: white;
        }
        nav {
            margin: 20px 0;
            text-align: center;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
        }
        nav ul li {
            display: inline;
            margin: 0 15px;
        }
        nav ul li a {
            text-decoration: none;
            color: #007bff;
        }
        section {
            background: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        section h2 {
            color: #343a40;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #6c757d;
        }
        .statistic {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <header>
        <h1>Selamat Datang di Aplikasi Giro</h1>
        <p>Silakan pilih salah satu opsi di bawah ini:</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="CreateGiro.php">Input Data Giro</a></li>
            <li><a href="TulisGiro.php">Tulis Giro</a></li>
            <!-- Tambahkan link lain jika perlu -->
        </ul>
    </nav>

    <section>
        <h2>Statistik Giro</h2>
        <p class="statistic">
            Jumlah Giro Unused: 
            <a href="UnusedGiroList.php"><?php echo htmlspecialchars($unused_count); ?></a>
        </p>
        <p class="statistic">
            Jumlah Giro Issued: 
            <a href="IssuedGiroList.php"><?php echo htmlspecialchars($issued_count); ?></a>
        </p>
        <!-- Uncomment and implement these sections as needed
        <p class="statistic">
            Total Nominal Giro Issued: 
            <a href="IssuedGiroList.php"><?php echo htmlspecialchars(number_format($total_Issued_nominal)); ?></a>
        </p>
        <p class="statistic">
            Total Nominal Giro Used: <?php echo htmlspecialchars(number_format($total_used_nominal)); ?>
        </p>
        <p class="statistic">
            Total Nominal Giro Issued yang Jatuh Tempo dalam 3 Hari: 
        </p>
        -->
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Aplikasi Giro. All rights reserved.</p>
    </footer>
</body>
</html>
