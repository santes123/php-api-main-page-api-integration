const token = localStorage.getItem("jwt");

async function decide() {
  if (!token) return location.replace("login.php");
  try {
    const res = await fetch(BASE_URL + "/api/v1/auth/me", {
      method: "GET",
      headers: { Authorization: "Bearer " + token },
    });

    if (res.ok) return location.replace("tasks.php");
    // 401/403 → token inválido/expirado
    localStorage.removeItem("jwt");
    location.replace("login.php");
  } catch {
    // error de red → llevar a login o mostrar aviso
    location.replace("login.php");
  }
}
decide();
