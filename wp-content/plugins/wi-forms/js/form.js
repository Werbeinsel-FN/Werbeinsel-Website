document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('wi-contact-form');
  const responseBox = document.getElementById('wi-form-response');

  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(form);

    try {
      const res = await fetch(WIForms.ajaxUrl, {
        method: 'POST',
        body: new URLSearchParams({
          action: 'wi_handle_form',
          nonce: WIForms.nonce,
          ...Object.fromEntries(formData)
        })
      });

      const json = await res.json();
      responseBox.textContent = json.message;
      responseBox.style.color = json.success ? 'green' : 'red';

      if (json.success) form.reset();
    } catch (error) {
      responseBox.textContent = 'Fehler beim Senden.';
      responseBox.style.color = 'red';
    }
  });
});
