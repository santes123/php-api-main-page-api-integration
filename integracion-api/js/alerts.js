const Toast = Swal.mixin({
  toast: true,
  position: "top-end",
  background: "#111826",
  color: "#e8eef5",
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
});

function showToast(icon, title) {
  Toast.fire({ icon, title });
}

function showAlert(icon, title, text) {
  Swal.fire({
    icon,
    title,
    text,
    background: "#111826",
    color: "#e8eef5",
    confirmButtonColor: "#2b5cff",
  });
}
