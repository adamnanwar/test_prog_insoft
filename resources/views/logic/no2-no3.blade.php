<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No. 2 dan 3</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .container {
            max-width: 700px;
        }

        textarea,
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
        <h1>No. 2 dan 3</h1>

        <form method="GET">
            <label>Masukkan kalimat</label>
            <textarea name="text" rows="4"><?= htmlspecialchars(request('text', '')) ?></textarea>

            <label>Masukkan satu kata baru</label>
            <input type="text" name="kata_baru" value="<?= htmlspecialchars(request('kata_baru', '')) ?>">

            <button type="submit">Proses</button>
        </form>

        <?php
$text = trim((string) request('text', ''));
$kataBaru = trim((string) request('kata_baru', ''));

$jumlahAwal = 0;
$textBaru = '';
$jumlahAkhir = 0;

if ($text !== '' || $kataBaru !== '') {
    $jumlahAwal = $text === '' ? 0 : count(preg_split('/\s+/', $text));

    if ($text === '') {
        $textBaru = $kataBaru;
    } elseif ($kataBaru === '') {
        $textBaru = $text;
    } else {
        $textBaru = $text . ' ' . $kataBaru;
    }

    $jumlahAkhir = $textBaru === '' ? 0 : count(preg_split('/\s+/', trim($textBaru)));
}
        ?>

        <?php if ($text !== '' || $kataBaru !== ''): ?>
        <div class="result">
            <p><strong>Kalimat awal:</strong> <?= htmlspecialchars($text) ?></p>
            <p><strong>Jumlah kata awal:</strong> <?= $jumlahAwal ?></p>
            <p><strong>Kata yang disisipkan:</strong> <?= htmlspecialchars($kataBaru) ?></p>
            <p><strong>Hasil setelah disisipkan:</strong> <?= htmlspecialchars($textBaru) ?></p>
            <p><strong>Jumlah kata setelah disisipkan:</strong> <?= $jumlahAkhir ?></p>
        </div>
        <?php endif; ?>
    </div>
</body>

</html>