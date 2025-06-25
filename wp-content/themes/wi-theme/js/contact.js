document.addEventListener('DOMContentLoaded', function () {
  // ===============================
  // TEXT-, EMAIL-, TEXTAREA-Felder: Label verstecken bei Input oder Fokus
  // ===============================
  const fields = document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], textarea');

  fields.forEach(field => {
    const label = field.closest('.control-group')?.querySelector('.control-label');

    const toggleLabel = () => {
      if (!label) return;
      label.style.opacity = (field === document.activeElement || field.value.trim() !== '') ? '0' : '1';
    };

    field.addEventListener('input', toggleLabel);
    field.addEventListener('focus', toggleLabel);
    field.addEventListener('blur', toggleLabel);
    toggleLabel();
  });

  // ===============================
  // CHECKBOXES: is-checked toggle bei Klick aufs Label
  // ===============================
  const labels = document.querySelectorAll('.wi-checkbox-group > label');

  labels.forEach(label => {
    const input = label.querySelector('input[type="checkbox"]');
    if (!input) return;

    const toggle = () => {
      label.classList.toggle('is-checked', input.checked);
    };

    label.addEventListener('click', () => {
      input.checked = !input.checked;
      toggle();
    });

    input.addEventListener('change', toggle);
    toggle(); // initial state
  });
});