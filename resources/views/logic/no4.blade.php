<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No. 4</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .container {
            max-width: 700px;
        }

        input,
        button {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

        .result {
            border: 1px solid #ccc;
            padding: 16px;
            margin-top: 20px;
            white-space: pre-line;
            font-family: monospace;
        }

        a {
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="/">Kembali</a>
        <h1>No. 4</h1>

        <form method="GET">
            <label>Masukkan jumlah baris</label>
            <input type="number" name="baris" min="1" value="<?= htmlspecialchars(request('baris', '')) ?>">
            <button type="submit">Proses</button>
        </form>

        <?php
$baris = (int) request('baris', 0);
$hasil = '';

if ($baris > 0) {
    for ($i = 1; $i <= $baris; $i++) {
        $hasil .= str_repeat('*', $i) . PHP_EOL;
    }
}
        ?>

        <?php if ($baris > 0): ?>
        <div class="result"><?= htmlspecialchars($hasil) ?></div>
        <?php endif; ?>
    </div>
</body>

</html>