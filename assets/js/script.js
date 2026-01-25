/* =====================
   SWEETALERT POPUP
===================== */
function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: message,
        timer: 2000,
        showConfirmButton: false
    });
}

/* =====================
   KONFIRMASI HAPUS
===================== */
function konfirmasiHapus(url) {
    Swal.fire({
        title: 'Yakin?',
        text: 'Data akan dihapus!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
