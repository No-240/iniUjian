<?php
include 'koneksi.php';

// Ambil data berdasarkan ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id == 0) {
    header('Location: index.php');
    exit;
}

$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mahasiswa WHERE id = $id"));
if (!$data) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim          = trim(mysqli_real_escape_string($conn, $_POST['nim']));
    $nama_lengkap = trim(mysqli_real_escape_string($conn, $_POST['nama_lengkap']));
    $jurusan      = trim(mysqli_real_escape_string($conn, $_POST['jurusan']));
    $foto_lama    = $data['foto'];
    $foto_nama    = $foto_lama; // default: pakai foto lama

    // Proses upload foto baru (jika ada)
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $file     = $_FILES['foto'];
        $tipe_ok  = ['image/jpeg', 'image/jpg', 'image/png'];

        if (!in_array($file['type'], $tipe_ok)) {
            $error = 'Format file harus JPG, JPEG, atau PNG.';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $error = 'Ukuran file tidak boleh melebihi 2 MB.';
        } else {
            $ekstensi  = pathinfo($file['name'], PATHINFO_EXTENSION);
            $foto_nama = uniqid('mhs_') . '.' . $ekstensi;

            if (!is_dir('uploads/')) {
                mkdir('uploads/', 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], 'uploads/' . $foto_nama)) {
                // Hapus foto lama jika bukan default
                if ($foto_lama && file_exists('uploads/' . $foto_lama) && $foto_lama != 'default.jpg') {
                    unlink('uploads/' . $foto_lama);
                }
            } else {
                $error = 'Gagal mengunggah foto.';
                $foto_nama = $foto_lama;
            }
        }
    }

    if ($error == '') {
        $sql = "UPDATE mahasiswa SET
                    nim = '$nim',
                    nama_lengkap = '$nama_lengkap',
                    jurusan = '$jurusan',
                    foto = '$foto_nama'
                WHERE id = $id";

        if (mysqli_query($conn, $sql)) {
            header('Location: index.php?pesan=edit');
            exit;
        } else {
            $error = 'Gagal memperbarui data: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0f0f13;
            --surface: #1a1a22;
            --surface2: #22222e;
            --accent: #6c63ff;
            --accent2: #ff6584;
            --text: #e8e8f0;
            --muted: #888899;
            --border: #2e2e3e;
            --danger: #e74c3c;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container { max-width: 600px; margin: 0 auto; }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--muted);
            text-decoration: none;
            font-size: 0.875rem;
            margin-bottom: 28px;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--text); }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px;
        }

        .card-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 6px;
            background: linear-gradient(135deg, #ff6584, #6c63ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-sub { color: var(--muted); font-size: 0.875rem; margin-bottom: 28px; }

        .alert-error {
            background: rgba(231, 76, 60, 0.15);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.875rem;
            margin-bottom: 20px;
        }

        .form-group { margin-bottom: 20px; }

        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        label span { color: var(--accent2); }

        input[type="text"] {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input[type="text"]:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.15);
        }

        /* Foto saat ini */
        .foto-current {
            display: flex;
            align-items: center;
            gap: 14px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 10px;
        }

        .foto-current img {
            width: 52px;
            height: 52px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid var(--border);
        }

        .foto-current-info { font-size: 0.8rem; color: var(--muted); }
        .foto-current-info strong { display: block; color: var(--text); margin-bottom: 2px; }

        .file-input-wrapper { position: relative; }
        .file-input-wrapper input[type="file"] { display: none; }

        .file-label {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--surface2);
            border: 1px dashed var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            font-size: 0.875rem;
            color: var(--muted);
        }

        .file-label:hover {
            border-color: var(--accent);
            background: rgba(108, 99, 255, 0.05);
        }

        #preview-container { margin-top: 12px; display: none; }
        #preview-container img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid var(--border);
        }
        #file-name { font-size: 0.8rem; color: var(--accent); margin-top: 6px; }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: 'Syne', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            background: #5a52e0;
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(108, 99, 255, 0.4);
        }
    </style>
</head>
<body>
<div class="container">

    <a href="index.php" class="back-link">← Kembali ke Daftar</a>

    <div class="card">
        <div class="card-title">Edit Mahasiswa</div>
        <div class="card-sub">Perbarui data mahasiswa. Kosongkan field foto jika tidak ingin mengganti foto.</div>

        <?php if ($error): ?>
            <div class="alert-error">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form id="formEdit" method="POST" enctype="multipart/form-data" novalidate>

            <div class="form-group">
                <label>NIM <span>*</span></label>
                <input type="text" name="nim" id="nim" value="<?= htmlspecialchars($data['nim']) ?>">
            </div>

            <div class="form-group">
                <label>Nama Lengkap <span>*</span></label>
                <input type="text" name="nama_lengkap" id="nama_lengkap" value="<?= htmlspecialchars($data['nama_lengkap']) ?>">
            </div>

            <div class="form-group">
                <label>Jurusan <span>*</span></label>
                <input type="text" name="jurusan" id="jurusan" value="<?= htmlspecialchars($data['jurusan']) ?>">
            </div>

            <div class="form-group">
                <label>Foto Profil</label>

                <!-- Foto saat ini -->
                <?php
                $foto_src = (file_exists('uploads/' . $data['foto']) && $data['foto'] != '') ?
                    'uploads/' . $data['foto'] :
                    'https://ui-avatars.com/api/?name=' . urlencode($data['nama_lengkap']) . '&background=6c63ff&color=fff&size=52';
                ?>
                <div class="foto-current">
                    <img src="<?= htmlspecialchars($foto_src) ?>" alt="Foto Saat Ini">
                    <div class="foto-current-info">
                        <strong>Foto Saat Ini</strong>
                        <?= htmlspecialchars($data['foto']) ?>
                    </div>
                </div>

                <div class="file-input-wrapper">
                    <label class="file-label" for="foto">
                        <span>📷</span>
                        <span id="file-text">Klik untuk mengganti foto (opsional)</span>
                    </label>
                    <input type="file" name="foto" id="foto" accept=".jpg,.jpeg,.png">
                </div>
                <div id="preview-container">
                    <img id="foto-preview" src="" alt="Preview Baru">
                    <div id="file-name"></div>
                </div>
            </div>

            <button type="submit" class="btn-submit">Perbarui Data</button>
        </form>
    </div>

</div>

<script>
    // Preview foto baru
    document.getElementById('foto').addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            document.getElementById('file-text').textContent = file.name;
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('foto-preview').src = e.target.result;
                document.getElementById('file-name').textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                document.getElementById('preview-container').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Validasi JavaScript
    document.getElementById('formEdit').addEventListener('submit', function (e) {
        const nim     = document.getElementById('nim').value.trim();
        const nama    = document.getElementById('nama_lengkap').value.trim();
        const jurusan = document.getElementById('jurusan').value.trim();
        const fotoInput = document.getElementById('foto');
        const file    = fotoInput.files[0];

        if (!nim) {
            alert('NIM tidak boleh kosong!');
            e.preventDefault(); return;
        }
        if (!nama) {
            alert('Nama Lengkap tidak boleh kosong!');
            e.preventDefault(); return;
        }
        if (!jurusan) {
            alert('Jurusan tidak boleh kosong!');
            e.preventDefault(); return;
        }

        // Validasi foto hanya jika ada file baru dipilih
        if (file) {
            const tipeOk = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!tipeOk.includes(file.type)) {
                alert('Format foto harus JPG, JPEG, atau PNG!');
                e.preventDefault(); return;
            }
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran foto tidak boleh melebihi 2 MB!');
                e.preventDefault(); return;
            }
        }
    });
</script>
</body>
</html>
