<?php
include 'koneksi.php';

// Prepare the statement to get unused cek records grouped by bank and entity names
$stmt = $conn->prepare("
    SELECT le.nama_entitas, dg.namabank, dg.nocek, dg.ac_number
    FROM data_cek dg 
    JOIN list_entitas le ON dg.id_entitas = le.id_entitas
    WHERE dg.Statuscek = 'Unused'
    ORDER BY le.nama_entitas, dg.namabank
");
if ($stmt === false) {
    die("Preparation failed: " . $conn->error);
}

// Execute the statement
$stmt->execute();
$result = $stmt->get_result();

// Initialize an array to hold the counts and records
$report_data = [];
while ($row = $result->fetch_assoc()) {
    $report_data[$row['nama_entitas']][$row['namabank']][] = [
        'nocek' => $row['nocek'],
        'ac_number' => $row['ac_number'],
    ];
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Jumlah cek Unused</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            color: #333;
            margin: 20px;
            line-height: 1.6;
        }
        h1 {
            color: #4a90e2;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        th, td {
            border: 1px solid #e0e0e0;
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #4a90e2;
            color: white;
            font-weight: bold;
        }
        .bank-header {
            background-color: #cce5ff;
            font-weight: bold;
            cursor: pointer;
        }
        .entity-header {
            background-color: #b3d4fc;
            font-weight: bold;
        }
        .cek-list {
            display: none; /* Initially hide the list */
            padding-left: 20px;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #4a90e2;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            text-align: center;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
        a:hover {
            background-color: #357ab8;
        }
    </style>
    <script>
        function togglecekList(bank) {
            const cekList = document.getElementById(bank);
            cekList.style.display = cekList.style.display === "none" ? "table-row" : "none";
        }

        function sortcekList(cekListId) {
            const cekTable = document.querySelector(`#${cekListId} table tbody`);
            const rows = Array.from(cekTable.rows);
            const acNumberIndex = 2;

            rows.sort((rowA, rowB) => {
                const acNumberA = rowA.cells[acNumberIndex].textContent.trim();
                const acNumberB = rowB.cells[acNumberIndex].textContent.trim();
                return acNumberA.localeCompare(acNumberB);
            });

            rows.forEach(row => cekTable.appendChild(row));
        }
    </script>
</head>
<body>
    <h1>Laporan Jumlah Cek Available</h1>
    
    <?php if (empty($report_data)): ?>
        <p style="text-align: center;">Tidak ada data Cek.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nama Entitas</th>
                    <th>Bank</th>
                    <th>Jumlah Cek</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report_data as $nama_entitas => $banks): ?>
                    <tr class="entity-header">
                        <td colspan="3"><?php echo htmlspecialchars($nama_entitas); ?></td>
                    </tr>
                    <?php foreach ($banks as $bank => $cekList): ?>
                        <tr class="bank-header" onclick="togglecekList('<?php echo htmlspecialchars($bank); ?>')">
                            <td></td>
                            <td><?php echo htmlspecialchars($bank); ?></td>
                            <td><?php echo count($cekList); ?></td> <!-- Count of nocek for this bank -->
                        </tr>
                        <tr class="cek-list" id="<?php echo htmlspecialchars($bank); ?>">
                            <td colspan="3">
                                <table style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>No Urut</th>
                                            <th>No cek</th>
                                            <th onclick="sortcekList('<?php echo htmlspecialchars($bank); ?>')" style="cursor: pointer;">AC Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        

                                        <?php 
                                        usort($cekList, function($a, $b) {
                                            return strcmp($a['ac_number'], $b['ac_number']);
                                        });
                                        
                                        foreach ($cekList as $index => $cek): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td> <!-- Add sequence number -->
                                                <td><?php echo htmlspecialchars($cek['nocek']); ?></td>
                                                <td><?php echo htmlspecialchars($cek['ac_number']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <a href="index.php">Kembali ke Halaman Utama</a>
</body>
</html>