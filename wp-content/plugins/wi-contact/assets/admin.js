(function ($) {
  function renumber() {
    $("#wi-forms-rows tr").each(function (i) {
      $(this)
        .find("input,select")
        .each(function () {
          const n = $(this).attr("name");
          if (!n) return;
          $(this).attr(
            "name",
            n
              .replace(/\[\d+\]/, "[" + i + "]")
              .replace(/\[__i__\]/, "[" + i + "]")
          );
        });
    });
  }
  function minGuard() {
    const rows = $("#wi-forms-rows tr");
    rows.find(".wi-row-del").prop("disabled", rows.length <= 4);
  }
  $(document).on("click", "#wi-add-row", function (e) {
    e.preventDefault();
    const $tpl = $("#wi-row-template").clone().removeAttr("id").show();
    $("#wi-forms-rows").append($tpl);
    renumber();
    minGuard();
  });
  $(document).on("click", ".wi-row-del", function (e) {
    e.preventDefault();
    if ($("#wi-forms-rows tr").length <= 4) return;
    $(this).closest("tr").remove();
    renumber();
    minGuard();
  });
  $(document).on("click", ".wi-row-up", function (e) {
    e.preventDefault();
    const $tr = $(this).closest("tr");
    $tr.prev().before($tr);
    renumber();
  });
  $(document).on("click", ".wi-row-down", function (e) {
    e.preventDefault();
    const $tr = $(this).closest("tr");
    $tr.next().after($tr);
    renumber();
  });
  $(function () {
    minGuard();
  });
})(jQuery);
