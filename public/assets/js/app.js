const buildPreview = (file, target) => {
  const reader = new FileReader();
  reader.onload = (event) => {
    const image = document.createElement('img');
    image.src = event.target?.result || '';
    image.alt = file.name;
    image.className = 'preview-thumb';
    target.appendChild(image);
  };
  reader.readAsDataURL(file);
};

const syncInputPreview = (input, files) => {
  const target = input.dataset.previewTarget ? document.querySelector(input.dataset.previewTarget) : null;
  const counterTarget = input.dataset.counterTarget ? document.querySelector(input.dataset.counterTarget) : null;

  if (counterTarget) {
    counterTarget.textContent = String(files.length);
  }

  if (!target) {
    return;
  }

  target.innerHTML = '';
  files.forEach((file) => buildPreview(file, target));
};

const attachDropzone = (input) => {
  const dropzone = input.closest('.upload-dropzone');
  if (!dropzone) {
    return;
  }

  ['dragenter', 'dragover'].forEach((eventName) => {
    dropzone.addEventListener(eventName, (event) => {
      event.preventDefault();
      dropzone.classList.add('dragover');
    });
  });

  ['dragleave', 'drop'].forEach((eventName) => {
    dropzone.addEventListener(eventName, (event) => {
      event.preventDefault();
      dropzone.classList.remove('dragover');
    });
  });

  dropzone.addEventListener('drop', (event) => {
    const files = [...(event.dataTransfer?.files || [])];
    if (!files.length) {
      return;
    }

    const dataTransfer = new DataTransfer();
    files.forEach((file) => dataTransfer.items.add(file));
    input.files = dataTransfer.files;
    syncInputPreview(input, files);
  });
};

document.querySelectorAll('.image-input').forEach((input) => {
  attachDropzone(input);

  input.addEventListener('change', (event) => {
    const files = [...(event.target.files || [])];
    syncInputPreview(input, files);
  });
});

document.querySelectorAll('[data-download-card]').forEach((button) => {
  button.addEventListener('click', async () => {
    const targetSelector = button.getAttribute('data-download-card');
    const fileName = button.getAttribute('data-download-name') || 'detalhes-pedido.png';
    const target = targetSelector ? document.querySelector(targetSelector) : null;

    if (!target || typeof window.html2canvas !== 'function') {
      window.alert('Não foi possível gerar o PNG neste navegador agora.');
      return;
    }

    const originalLabel = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>A gerar PNG...';

    try {
      const canvas = await window.html2canvas(target, {
        backgroundColor: '#ffffff',
        scale: 2,
      });

      const link = document.createElement('a');
      link.href = canvas.toDataURL('image/png');
      link.download = fileName;
      link.click();
    } finally {
      button.disabled = false;
      button.innerHTML = originalLabel;
    }
  });
});
