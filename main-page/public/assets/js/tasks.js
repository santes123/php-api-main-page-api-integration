$(function () {
  const mdlEl = document.getElementById("mdlTask");
  const mdl = new bootstrap.Modal(mdlEl);
  const $form = $("#formTask");

  // 1) SIEMPRE reset al cerrar el modal
  mdlEl.addEventListener("hidden.bs.modal", () => {
    $form[0].reset();
    $form.find("[name=id]").val(""); // <-- clave: limpiar id
    $("#chkCompleted").prop("checked", false);
  });

  // 2) Botón "Nueva tarea"
  $(".btn-new-task").on("click", function () {
    $form[0].reset();
    $form.find("[name=id]").val(""); // <-- importante
    $("#chkCompleted").prop("checked", false);

    // si eres admin, seleccionar primer usuario o uno por defecto
    const $sel = $form.find("[name=user_id]");
    if ($sel.length) $sel.prop("selectedIndex", 0);
    mdl.show();
  });
  function fillFormFromRow($tr) {
    $form[0].reset();
    $form.find("[name=id]").val($tr.data("id"));
    $form.find("[name=title]").val($tr.find("td:eq(1)").text().trim());
    $form.find("[name=description]").val($tr.data("description") || "");
    const starts = $tr
      .find("td:eq(" + ($('th:contains("Usuario")').length ? 3 : 2) + ")")
      .text()
      .trim();
    const ends = $tr
      .find("td:eq(" + ($('th:contains("Usuario")').length ? 4 : 3) + ")")
      .text()
      .trim();

    // Convertir "YYYY-MM-DD HH:MM:SS" a "YYYY-MM-DDTHH:MM"
    function toLocal(dt) {
      if (!dt) return "";
      return dt.replace(" ", "T").slice(0, 16);
    }
    $form.find("[name=starts_at]").val(toLocal(starts));
    $form.find("[name=ends_at]").val(toLocal(ends));

    const isCompleted = $tr.find(".badge").hasClass("bg-success");
    $form.find("[name=completed]").prop("checked", isCompleted);

    // Usuario (si eres admin)
    const userHeaderExists = $('th:contains("Usuario")').length > 0;
    if (userHeaderExists) {
      const email = $tr.find("td:eq(2)").text().trim();
      $form
        .find("[name=user_id] option")
        .filter(function () {
          return $(this).text().trim() === email;
        })
        .prop("selected", true);
    }
  }

  $(".btn-edit").on("click", function () {
    fillFormFromRow($(this).closest("tr"));
    mdl.show();
  });

  $form.on("submit", function (e) {
    e.preventDefault();
    const id = $form.find("[name=id]").val();
    const route = id ? "/?r=tasks/update" : "/?r=tasks/store";

    const dataArray = $form.serializeArray();
    const data = {};
    dataArray.forEach((i) => (data[i.name] = i.value));
    data.completed = $("#chkCompleted").is(":checked") ? 1 : 0;

    $.post(route, data)
      .done(() => {
        showToast("Tarea guardada", "success");
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
      title: "¿Eliminar tarea?",
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
        $.post("/?r=tasks/destroy", {
          id,
          csrf: $form.find("[name=csrf]").val(),
        })
          .done(() => {
            showToast("Tarea eliminada", "success");
            setTimeout(() => {
              location.reload();
            }, 500);
          })
          .fail((x) => showToast(x.responseJSON?.error, "danger"));
      }
    });
  });
});
