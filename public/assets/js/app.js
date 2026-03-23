document.querySelectorAll('.image-input').forEach((input) => {
  input.addEventListener('change', (event) => {
    const target = document.querySelector(input.dataset.previewTarget);
    if (!target) return;
    target.innerHTML = '';
    [...event.target.files].forEach((file) => {
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
