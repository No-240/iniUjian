<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Form Mahasiswa</title>
    <link rel="stylesheet"href="style.css">
</head>
<body>
    <h2>Form Mahasiswa</h2>
    <a href="form.php">Tambah Data</a><br><br>
    <table>
        <tr>
            <th>Foto</th>
            <th>NIM</th>
            <th>Nama</th>
            <th>Jurusan</th>
            <th>Aksi</th>
        </tr>
        <?php
        $res = mysqli_query($conn, "SELECT * FROM mahasiswa");
        while ($row = mysqli_fetch_assoc($res)) { ?>
        <tr>
            <td><img src="uploads/<?= $row['foto'] ?>"></td>
            <td><?= $row['nim'] ?></td>
            <td><?= $row['nama'] ?></td>
            <td><?= $row['jurusan'] ?></td>
            <td>
                <a href="form.php?id=<?= $row['id'] ?>">Edit</a> | 
                <a href="proses.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus data?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
    <script src="script.js"></script>

    <script>
        setTimeout(() => {
            const alert = document.querySelection('.alert')
            if (alert) alert.style.display = 'none';
        }, 3000);
    </script>
</body>
</html>
