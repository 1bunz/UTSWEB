<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Simulasi Tabungan Berjangka</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 40px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .container {
            width: 500px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        form, .hasil {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="number"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 15px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .hasil h3 {
            margin-top: 0;
            text-align: center;
            color: #007BFF;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <P>Nama: Subani Maulana
        <br>NIM: 202404023
    </P>
    <h2> Simulasi Perhitungan Tabungan Berjangka</h2>
    <div class="container">
        <form action="" method="GET">
            <label for="target_nominal">1. Nominal Target Tabungan (Rp):</label>
            <input type="number" name="target_nominal" id="target_nominal" min="100000">
            
            <label for="mode">2. Pilih Mode Perhitungan:</label>
            <select name="mode" id="mode" onchange="toggleModeInput()">
                <option value="bulan">Target Bulan</option>
                <option value="setoran">Jumlah Setoran Tetap</option>
            </select>

            <div id="input_bulan">
                <label for="bulan_input">Jangka Waktu Tabungan (Bulan):</label>
                <input type="number" name="bulan_input" id="bulan_input" min="1">
            </div>

            <div id="input_setoran" style="display:none;">
                <label for="setoran_input">Jumlah Setoran Tetap (Rp/Bulan):</label>
                <input type="number" name="setoran_input" id="setoran_input" min="12501">
            </div>

            <button type="submit">Hitung Simulasi</button>
        </form>

        <div class="hasil">
            <?php

            $BUNGA_PER_BULAN = 0.0335; 
            $BIAYA_ADMIN = 12500;       

            $TargetNominal = (INT)($_GET["target_nominal"] ?? 0); 
            $Mode = $_GET["mode"] ?? "bulan"; 
            $InputBulan = (INT)($_GET["bulan_input"] ?? 0);
            $InputSetoran = (INT)($_GET["setoran_input"] ?? 0);

            if ($TargetNominal > 0 && ($Mode == "bulan" && $InputBulan > 0) || ($Mode == "setoran" && $InputSetoran > 0)) {
                
                if ($Mode == "bulan") {
                    if ($InputBulan > 0) {

                        $BiayaKumulatif = $BIAYA_ADMIN * $InputBulan;
                        
                        $FaktorBunga = ($BUNGA_PER_BULAN / 2) * $InputBulan; 
                        
                        $SetoranWajib = ceil(($TargetNominal + $BiayaKumulatif) / ($InputBulan * (1 + $FaktorBunga)));
                        
                        $SetoranWajib = ceil($SetoranWajib / 100) * 100;

                        $SetoranTetap = $SetoranWajib;
                        $JangkaWaktu = $InputBulan;
                        echo "<h3> Hasil Perhitungan Simulasi</h3>";
                        echo "<p>Untuk mencapai **Rp " . number_format($TargetNominal, 0, ',', '.') . "** dalam **$InputBulan bulan**, Anda harus menyetor:</p>";
                        echo "<h3>Rp " . number_format($SetoranWajib, 0, ',', '.') . " / Bulan</h3>";

                        goto RincianSimulasi; 
                    }
                } 
                
                elseif ($Mode == "setoran") {
                    if ($InputSetoran > $BIAYA_ADMIN) {
                        $SaldoAkhir = 0;
                        $BulanDibutuhkan = 0;
                        
                        while ($SaldoAkhir < $TargetNominal && $BulanDibutuhkan < 500) { 
                            $BulanDibutuhkan++;
                            
                            $SaldoAkhir += $InputSetoran;
                            
                            $BungaDidapat = $SaldoAkhir * $BUNGA_PER_BULAN;
                            $SaldoAkhir += $BungaDidapat;
                            
                            $SaldoAkhir -= $BIAYA_ADMIN;
                        }

                        if ($SaldoAkhir >= $TargetNominal) {
                            $SetoranTetap = $InputSetoran;
                            $JangkaWaktu = $BulanDibutuhkan;
                            echo "<h3> Hasil Perhitungan Simulasi</h3>";
                            echo "<p>Dengan setoran **Rp " . number_format($InputSetoran, 0, ',', '.') . " / bulan**, Anda akan mencapai **Rp " . number_format($TargetNominal, 0, ',', '.') . "** dalam:</p>";
                            echo "<h3>$BulanDibutuhkan Bulan</h3>";

                            goto RincianSimulasi; 
                        } else {
                            echo "<p class='error'>Target Nominal terlalu tinggi atau Setoran terlalu kecil. Simulasi melebihi 500 bulan.</p>";
                        }
                    } else {
                        echo "<p class='error'>Setoran Tetap minimal harus di atas Biaya Admin (Rp " . number_format($BIAYA_ADMIN, 0, ',', '.') . ")</p>";
                    }
                }

                RincianSimulasi:
                if (isset($SetoranTetap) && isset($JangkaWaktu)) {
                    $CurrentSaldo = 0;
                    echo "<h4>Rincian Data Setoran Perbulan:</h4>";
                    echo "<table>";
                    echo "<tr><th>Bulan ke-</th><th>Setoran (Rp)</th><th>Bunga (Rp)</th><th>Admin (Rp)</th><th>Saldo Akhir (Rp)</th></tr>";

                    for ($i = 1; $i <= $JangkaWaktu; $i++) {
                        
                        $CurrentSaldo += $SetoranTetap;
                        
                        
                        $BungaDidapat = $CurrentSaldo * $BUNGA_PER_BULAN;
                        $CurrentSaldo += $BungaDidapat;

                        
                        $CurrentSaldo -= $BIAYA_ADMIN;

                         
                        echo "<tr>";
                        echo "<td>$i</td>";
                        echo "<td>" . number_format($SetoranTetap, 0, ',', '.') . "</td>";
                        echo "<td>" . number_format(round($BungaDidapat), 0, ',', '.') . "</td>";
                        echo "<td>" . number_format($BIAYA_ADMIN, 0, ',', '.') . "</td>";
                        echo "<td>" . number_format(round($CurrentSaldo), 0, ',', '.') . "</td>";
                        echo "</tr>";

                        if ($Mode == "setoran" && $i == $JangkaWaktu) break; 
                    }
                    echo "</table>";
                }

            } elseif (isset($_GET["target_nominal"])) {
                echo "<p class='error'>Mohon isi semua input yang diperlukan.</p>";
            }
            ?>
        </div>
    </div>

    <script>
        function toggleModeInput() {
            const mode = document.getElementById('mode').value;
            const inputBulan = document.getElementById('input_bulan');
            const inputSetoran = document.getElementById('input_setoran');
            const setoranInput = document.getElementById('setoran_input');
            const bulanInput = document.getElementById('bulan_input');

            if (mode === 'bulan') {
                inputBulan.style.display = 'block';
                inputSetoran.style.display = 'none';
            } else {
                inputBulan.style.display = 'none';
                inputSetoran.style.display = 'block';
            }
        }
        
        toggleModeInput();
    </script>
</body>
</html>