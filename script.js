alert("FORM PENDAFTARAN MAHASISWA")
alert("Silahkan Klik Button 'Tambah Data' untuk memasukkan data saudara")

function validateForm() {
    const nim = document.getElementById('nim').value;
    const nama = document.getElementById('nama').value;
    const jurusan = document.getElementById('jurusan').value;
    const foto = document.getElementById('foto');

    if (!nim || !nama || !jurusan) {
        alert("Semua field teks wajib diisi!");
        return false;
    }

    if (foto.files.length > 0) {
        const file = foto.files[0];
        const fileType = file.type;
        const fileSize = file.size / 1024 / 1024; // MB

    if (!['image/jpeg', 'image/jpg', 'image/png'].includes(fileType)) {
        alert("Hanya file JPG/PNG yang diperbolehkan!");
        return false;
    }

    if (fileSize > 2) {
        alert("Ukuran file maksimal 2MB!");
        return false;
    }

    } else if ("<?= $id ?>" === "") {
        alert("Foto wajib diunggah untuk data baru!");
        return false;
        }
    return true;
}