<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No. 6</title>
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
        <h1>No. 6</h1>

        <?php
$dataValue = request('data', '');

if (is_array($dataValue)) {
    $dataValue = implode(',', $dataValue);
}
        ?>

        <form method="GET">
            <label>Masukkan array angka dipisah koma</label>
            <input type="text" name="data" value="<?= htmlspecialchars($dataValue) ?>"
                placeholder="contoh: 1, 3, 9 , 8, 5, 2 ,4">
            <button type="submit">Proses</button>
        </form>

        <?php
$input = trim($dataValue);
$hasil = [];

if ($input !== '') {
    $arr = explode(',', $input);
    $arr = array_map('trim', $arr);
    $arr = array_filter($arr, fn($item) => $item !== '');
    $arr = array_map('intval', $arr);

    sort($arr);
    $hasil = $arr;
}
        ?>

        <?php if ($input !== ''): ?>
        <div class="result">
            <p><strong>Input:</strong> <?= htmlspecialchars($input) ?></p>
            <p><strong>Hasil urut:</strong> <?= implode(', ', $hasil) ?></p>
        </div>
        <?php endif; ?>
    </div>
</body>

</html>