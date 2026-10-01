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

    // 2. Image Validation & Preview (add.php)
    const imageInput = document.getElementById('imagePath');
    const imagePreview = document.getElementById('imagePreview');
    
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (file) {
                const extension = file.name.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(extension)) {
                    alert('Invalid file format. Only JPG, JPEG, PNG, GIF, and WEBP are accepted.');
                    this.value = ''; // Clear the input
                    imagePreview.style.display = 'none';
                    return;
                }
                
                // Show Preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';

                // 3. Gallery Modal Logic (gallery.php)
    const imageModal = document.getElementById('imageModal');
    if (imageModal) {
        imageModal.addEventListener('show.bs.modal', function (event) {
            const triggerImage = event.relatedTarget;
            
            // Extract info from data-* attributes
            const imageUrl = triggerImage.getAttribute('src');
            const bookTitle = triggerImage.getAttribute('data-title');
            const bookAuthor = triggerImage.getAttribute('data-author');
            const bookYear = triggerImage.getAttribute('data-year');
            
            // Update the modal's content
            imageModal.querySelector('.modal-title').textContent = bookTitle;
            imageModal.querySelector('#modalImage').src = imageUrl;
            imageModal.querySelector('#modalImage').alt = bookTitle;
            imageModal.querySelector('#modalAuthor').textContent = 'By ' + bookAuthor;
            imageModal.querySelector('#modalYear').textContent = 'Published: ' + bookYear;
        });
    }
            }
        });
    }
});