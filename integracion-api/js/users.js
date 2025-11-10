const token = localStorage.getItem("api_token");
if (!token) location.href = "login.html";
const $ = (id) => document.getElementById(id);

function authFetch(path, opts = {}) {
  const headers = Object.assign(
    { "Content-Type": "application/json", Authorization: "Bearer " + token },
    opts.headers || {}
  );
  return fetch(BASE_URL + path, { ...opts, headers });
}

$("btnLogout").onclick = () => {
  localStorage.removeItem("api_token");
  localStorage.removeItem("api_me");
  location.href = "login.php";
};
$("btnReload").onclick = load;

async function load() {
  $("tbody").innerHTML = "";
  $("msg").textContent = "Cargando…";
  const p = parseInt($("page").value || "1", 10);
  const per = parseInt($("per").value || "50", 10);

  const qs = new URLSearchParams();
  if (p) qs.set("page", p);
  if (per) qs.set("per_page", per);

  try {
    const res = await authFetch(
      `/api/v1/users${qs.toString() ? "?" + qs.toString() : ""}`
    );
    const data = await res.json();
    const items = Array.isArray(data) ? data : data.data || data;
    $("msg").textContent = "";
    (items || []).forEach((u) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `<td>${u.id}</td><td>${escapeHtml(u.email || "")}</td>
             <td>${escapeHtml(
               [u.first_name || "", u.last_name || ""].join(" ").trim()
             )}</td>
             <td class="muted">${escapeHtml(u.created_at || "")}</td>`;
      $("tbody").appendChild(tr);
    });
    $("totals").textContent = `Mostrando ${(items || []).length}`;
  } catch {
    $("msg").textContent = "Error al cargar usuarios";
  }
}

function escapeHtml(s) {
  return (s || "").replace(
    /[&<>"']/g,
    (m) =>
      ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[
        m
      ])
  );
}

load();
