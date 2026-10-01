document.addEventListener("DOMContentLoaded", () => {
    
    // 1. Status Filter Logic (books.php)
    const filterSelect = document.getElementById('statusFilter');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const selectedStatus = this.value.toLowerCase();
            const rows = document.querySelectorAll('tr[data-status]');
            
            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status').toLowerCase();
                if (selectedStatus === 'all' || rowStatus === selectedStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // 2. Image Validation & Live Preview (add.php)
    const imageInput = document.getElementById('imagePath');
    const imagePreview = document.getElementById('imagePreview');
    
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (file) {
                const extension = file.name.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(extension)) {
                    alert('Invalid file format. Only JPG, JPEG, PNG, GIF, and WEBP are accepted.');
                    this.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
            }
        });
    }

    // 3. Gallery Modal Preview Logic (gallery.php)
    const imageModal = document.getElementById('imageModal');
    if (imageModal) {
        imageModal.addEventListener('show.bs.modal', function(event) {
            const triggerEl = event.relatedTarget;
            
            // Extract metadata from data-* attributes
            const imageSrc = triggerEl.getAttribute('data-image');
            const title = triggerEl.getAttribute('data-title');
            const author = triggerEl.getAttribute('data-author');
            const year = triggerEl.getAttribute('data-year');
            
            // Target modal DOM elements
            const modalTitleHeader = imageModal.querySelector('#imageModalLabel');
            const modalImg = imageModal.querySelector('#modalImage');
            const modalTitle = imageModal.querySelector('#modalTitle');
            const modalAuthor = imageModal.querySelector('#modalAuthor');
            const modalYear = imageModal.querySelector('#modalYear');
            
            // Inject attributes
            if (modalTitleHeader) modalTitleHeader.textContent = title;
            if (modalImg) {
                modalImg.src = imageSrc;
                modalImg.alt = title;
            }
            if (modalTitle) modalTitle.textContent = title;
            if (modalAuthor) modalAuthor.textContent = author ? 'Author: ' + author : '';
            if (modalYear) modalYear.textContent = year ? 'Publication Year: ' + year : '';
        });
    }
});