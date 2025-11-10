// Bootstrap 5 toast helper
window.showToast = function (message, type = "info") {
  // type: 'success' | 'danger' | 'warning' | 'info'
  const area = document.getElementById("toastArea");
  if (!area) return alert(message);

  const toastId = "t_" + Math.random().toString(36).slice(2);
  const bgClass =
    {
      success: "text-bg-success",
      danger: "text-bg-danger",
      warning: "text-bg-warning",
      info: "text-bg-info",
    }[type] || "text-bg-secondary";

  const el = document.createElement("div");
  el.className = `toast align-items-center ${bgClass} border-0`;
  el.id = toastId;
  el.role = "alert";
  el.ariaLive = "assertive";
  el.ariaAtomic = "true";
  el.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">${escapeHtml(message)}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
    </div>
  `;
  area.appendChild(el);

  const t = new bootstrap.Toast(el, { delay: 2500 });
  t.show();

  el.addEventListener("hidden.bs.toast", () => el.remove());
};

// escape de caracteres para evitar XSS al imprimir mensajes
function escapeHtml(str) {
  return String(str)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}
