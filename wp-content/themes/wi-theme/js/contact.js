document.addEventListener('DOMContentLoaded', function () {
  // ===============================
  // TEXT-, EMAIL-, TEXTAREA-Felder
  // ===============================
  const fields = document.querySelectorAll('.wpcf7 input[type="text"], .wpcf7 input[type="email"], .wpcf7 textarea');

  fields.forEach(field => {
    const fieldWrapper = field.closest('.wpcf7-form-control-wrap')?.closest('div');
    const label = fieldWrapper?.querySelector('.control-label');

    const toggleLabel = () => {
      if (!label) return;
      label.style.opacity = (document.activeElement === field || field.value.trim() !== '') ? '0' : '1';
    };

    field.addEventListener('input', toggleLabel);
    field.addEventListener('focus', toggleLabel);
    field.addEventListener('blur', toggleLabel);
    toggleLabel();
  });

  // ===============================
  // CHECKBOXEN: is-checked toggle auf .wpcf7-list-item
  // ===============================
  // Labels selektieren
  const labels = document.querySelectorAll('.wpcf7-list-item-label');

  labels.forEach(label => {
    label.addEventListener('click', function (e) {
      const listItem = label.closest('.wpcf7-list-item');
      const checkbox = listItem.querySelector('input[type="checkbox"]');

      if (!checkbox) return;

      // Toggle checkbox
      checkbox.checked = !checkbox.checked;

      // Manuell change-Event auslösen, damit andere Listener reagieren
      checkbox.dispatchEvent(new Event('change', { bubbles: true }));
    });
  });

  // Eventlistener für Änderung setzen
  const checkboxes = document.querySelectorAll('.wpcf7 input[type="checkbox"]');
  checkboxes.forEach(checkbox => {
    const listItem = checkbox.closest('.wpcf7-list-item');
    const label = listItem.querySelector('.wpcf7-list-item-label');

    const updateState = () => {
      if (checkbox.checked) {
        listItem.classList.add('is-checked');
        label.classList.add('is-selected'); // optional
      } else {
        listItem.classList.remove('is-checked');
        label.classList.remove('is-selected'); // optional
      }
    };

    checkbox.addEventListener('change', updateState);
    updateState(); // initial
  });
});