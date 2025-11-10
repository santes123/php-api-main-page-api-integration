const $ = (id) => document.getElementById(id);

$("btn").onclick = async () => {
  $("msg").textContent = "Procesando…";
  try {
    const res = await fetch(BASE_URL + "/api/v1/auth/login", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        email: $("email").value.trim(),
        password: $("password").value,
      }),
    });
    const data = await res.json();
    if (!res.ok) throw data;

    // Esperamos { token: "...", user: {...} } (si tu API devuelve solo token, igual sirve)
    localStorage.setItem("api_token", data.token);
    localStorage.setItem("api_me", JSON.stringify(data.user || {}));

    console.log("data: ", JSON.stringify(data || {}));
    console.log("token: ", JSON.stringify(data.token));

    showToast("success", "Login correcto");
    setTimeout(() => {
      location.href = "tasks.php";
    }, 500);
  } catch (e) {
    $("msg").textContent = "Error: " + (e?.error || "credenciales");
  }
};
