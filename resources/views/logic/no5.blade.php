<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No. 5</title>
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
        <h1>No. 5</h1>

        <form method="GET">
            <label>Masukkan kata</label>
            <input type="text" name="text" value="<?= htmlspecialchars(request('text', '')) ?>">
            <button type="submit">Proses</button>
        </form>

        <?php
$text = strtolower(trim((string) request('text', '')));
$hurufTerbanyak = '';

if ($text !== '') {
    $text = str_replace(' ', '', $text);
    $huruf = str_split($text);
    $jumlah = array_count_values($huruf);
    arsort($jumlah);
    $hurufTerbanyak = array_key_first($jumlah);
}
        ?>

        <?php if ($text !== ''): ?>
        <div class="result">
            <p><strong>Input:</strong> <?= htmlspecialchars(request('text')) ?></p>
            <p><strong>Huruf yang paling sering muncul:</strong> <?= htmlspecialchars($hurufTerbanyak) ?></p>
        </div>
        <?php endif; ?>
    </div>
</body>

</html>