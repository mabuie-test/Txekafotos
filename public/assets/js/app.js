document.querySelectorAll('.image-input').forEach((input) => {
  input.addEventListener('change', (event) => {
    const target = document.querySelector(input.dataset.previewTarget);
    const counterTarget = input.dataset.counterTarget ? document.querySelector(input.dataset.counterTarget) : null;
    const files = [...event.target.files];

    if (counterTarget) {
      counterTarget.textContent = String(files.length);
    }

    if (!target) return;
    target.innerHTML = '';

    files.forEach((file) => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const image = document.createElement('img');
        image.src = e.target?.result;
        image.alt = file.name;
        image.className = 'me-2 mb-2';
        target.appendChild(image);
      };
      reader.readAsDataURL(file);
    });
  });
});
