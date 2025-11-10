const token = localStorage.getItem("api_token");
if (!token) location.href = "login.html";

const me = safeJson(localStorage.getItem("api_me")) || {};
console.log("user info: ", me);
const isAdmin = (me?.role || "").toLowerCase() === "admin";

const $ = (id) => document.getElementById(id);
let editingId = null;

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
$("btnNew").onclick = async () => {
  editingId = null;
  resetForm();
  $("dlgTitle").textContent = "Crear tarea";
  if (isAdmin) {
    await ensureUsersLoaded();
    $("wrap_user").style.display = "";
  } else {
    $("wrap_user").style.display = "none";
  }
  $("dlg").showModal();
};
$("btnCancel").onclick = () => $("dlg").close();

$("btnSave").onclick = async () => {
  const body = {
    title: $("f_title").value.trim(),
    description: $("f_description").value.trim() || null,
    starts_at: $("f_starts").value
      ? $("f_starts").value.replace("T", " ") + ":00"
      : null,
    ends_at: $("f_ends").value
      ? $("f_ends").value.replace("T", " ") + ":00"
      : null,
    completed: $("f_completed").checked ? 1 : 0,
  };
  if (isAdmin && $("wrap_user").style.display !== "none") {
    const sel = $("f_user_id").value;
    if (sel) body.user_id = parseInt(sel, 10);
  }

  try {
    let res;
    if (editingId) {
      res = await authFetch(`/api/v1/tasks/${editingId}`, {
        method: "PUT",
        body: JSON.stringify(body),
      });
    } else {
      res = await authFetch(`/api/v1/tasks`, {
        method: "POST",
        body: JSON.stringify(body),
      });
    }
    const data = await res.json();
    if (!res.ok) throw data;
    $("dlg").close();
    showToast("success", "tarea guardada");
    setTimeout(() => {
      load();
    }, 500);
  } catch (e) {
    showToast("error", e?.error || "Error al guardar");
  }
};

function resetForm() {
  $("f_title").value = "";
  $("f_description").value = "";
  $("f_starts").value = "";
  $("f_ends").value = "";
  $("f_completed").checked = false;
  $("f_user_id").innerHTML = "";
}

async function ensureUsersLoaded() {
  // Carga usuarios para el select (solo admin)
  const res = await authFetch(`/api/v1/users`);
  const data = await res.json();
  const list = Array.isArray(data) ? data : data.data || [];
  $("f_user_id").innerHTML = (list || [])
    .map((u) => `<option value="${u.id}">${escapeHtml(u.email)}</option>`)
    .join("");
}

async function load() {
  $("list").innerHTML = "";
  $("msg").textContent = "Cargando…";

  //verificar si eres admin
  if (!isAdmin) {
    const label = document.getElementById("seeAll")?.closest("label");
    if (label) label.style.display = "none";
  }
  const seeAll = isAdmin && $("seeAll")?.checked;

  console.log("seeall: ", seeAll);
  console.log("isAdmin: " + isAdmin);
  console.log("check: " + $("seeAll")?.checked);

  // filtros
  const q = $("q").value.trim();
  const completed = $("completed").value;
  const p = parseInt($("page").value || "1", 10);
  const per = parseInt($("per").value || "10", 10);

  const qs = new URLSearchParams();
  if (q) qs.set("q", q);
  if (completed !== "") qs.set("completed", completed);
  if (p) qs.set("page", p);
  if (per) qs.set("per_page", per);

  if (seeAll) qs.set("all", "1"); // GET /api/v1/tasks?all=1

  console.log(`/api/v1/tasks${qs.toString() ? "?" + qs.toString() : ""}`);

  try {
    const res = await authFetch(
      `/api/v1/tasks${qs.toString() ? "?" + qs.toString() : ""}`
    );
    const data = await res.json();
    const items = Array.isArray(data) ? data : data.data || data; // tolerante a distintos formatos
    $("msg").textContent = items?.length ? "" : "Sin tareas";
    (items || []).forEach(drawItem);
  } catch {
    $("msg").textContent = "Error al cargar";
  }
}

function drawItem(t) {
  const div = document.createElement("div");
  div.className = "task";
  const estado = t.completed == 1 ? "Completada" : "Pendiente";
  const when = (t.starts_at || "") + (t.ends_at ? " → " + t.ends_at : "");
  div.innerHTML = `
        <div>
          <div><strong>${escapeHtml(
            t.title || ""
          )}</strong> <small class="tag">${estado}</small></div>
          <div class="muted">${escapeHtml(t.description || "")}</div>
          <div class="muted">${escapeHtml(when)}</div>
        </div>
        <div>
          <button class="edit">Editar</button>
          <button class="del">Borrar</button>
        </div>
      `;

  div.querySelector(".edit").onclick = async () => {
    editingId = t.id;
    resetForm();
    $("dlgTitle").textContent = "Editar tarea #" + t.id;
    $("f_title").value = t.title || "";
    $("f_description").value = t.description || "";
    $("f_starts").value = toInputDT(t.starts_at);
    $("f_ends").value = toInputDT(t.ends_at);
    $("f_completed").checked = t.completed == 1;

    if (isAdmin) {
      await ensureUsersLoaded();
      if (t.user_id) $("f_user_id").value = String(t.user_id);
      $("wrap_user").style.display = "";
    } else {
      $("wrap_user").style.display = "none";
    }
    $("dlg").showModal();
  };

  div.querySelector(".del").onclick = async () => {
    const swal = await Swal.fire({
      title: "¿Eliminar tarea?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar",
      background: "#111826",
      color: "#e8eef5",
    });

    console.log("swal.isConfirmed: " + swal.isConfirmed);
    if (!swal.isConfirmed) return;

    try {
      const res = await authFetch(`/api/v1/tasks/${t.id}`, {
        method: "DELETE",
      });
      const data = await res.json();
      if (!res.ok) throw data;
      showToast("success", "Tarea eliminada");
      setTimeout(() => {
        load();
      }, 500);
    } catch (e) {
      showToast("error", e?.error || "Error al eliminar");
    }
  };

  $("list").appendChild(div);
}

function toInputDT(dt) {
  // "YYYY-MM-DD HH:MM:SS" -> "YYYY-MM-DDTHH:MM"
  if (!dt) return "";
  return String(dt).slice(0, 16).replace(" ", "T");
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
function safeJson(s) {
  try {
    return JSON.parse(s);
  } catch {
    return null;
  }
}

load();
