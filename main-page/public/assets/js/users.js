$(function () {
  $form = $("#formUser");
  const mdlUserEl = document.getElementById("mdlUser");
  const mdl = new bootstrap.Modal(mdlUserEl);

  // cache del campo password
  const $password = $form.find('[name="password"]');

  // Reset del dialog al cerrar
  mdlUserEl.addEventListener("hidden.bs.modal", () => {
    $form[0].reset();
    $form.find("[name=id]").val("");

    // quitar required siempre y limpiar el campo
    $password.prop("required", false).val("");
  });

  // Botón "Nuevo usuario"
  $(".btn-new-user").on("click", function () {
    $form[0].reset();
    $form.find("[name=id]").val(""); // <-- limpiar id
    $form.find("[name=role]").val("user"); // o 'admin', como prefieras por defecto
    $password.prop("required", true); // <-- requerido al crear
    mdl.show();
  });

  $(".btn-edit").on("click", function () {
    const $tr = $(this).closest("tr");
    $form[0].reset();
    $form.find("[name=id]").val($tr.data("id"));
    $form.find("[name=email]").val($tr.find("td:eq(1)").text().trim());
    const name = $tr.find("td:eq(2)").text().trim().split(" ");
    $form.find("[name=first_name]").val(name.shift());
    $form.find("[name=last_name]").val(name.join(" "));
    $form.find("[name=role]").val($tr.find(".badge").text().trim());

    $password.prop("required", false).val(""); // quitar el require al editar
    mdl.show();
  });

  $form.on("submit", function (e) {
    e.preventDefault();
    const id = $form.find("[name=id]").val();
    const route = id ? "/?r=users/update" : "/?r=users/store";
    $.post(route, $form.serialize())
      .done(() => {
        showToast("Usuario guardado", "success");
        setTimeout(() => {
          location.reload();
        }, 500);
      })
      .fail((x) =>
        showToast(x.responseJSON?.error || "Error guardando", "danger")
      );
  });

  $(".btn-del").on("click", function () {
    Swal.fire({
      title: "¿Eliminar usuario?",
      text: "Esta acción no se puede deshacer.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        const id = $(this).closest("tr").data("id");
        $.post("/?r=users/destroy", {
          id,
          csrf: $("[name=csrf]").first().val(),
        })
          .done(() => {
            showToast("Usuario eliminado", "success");
            setTimeout(() => {
              location.reload();
            }, 500);
          })
          .fail((x) =>
            showToast(x.responseJSON?.error || "Error eliminando", "danger")
          );
      }
    });
  });
});
