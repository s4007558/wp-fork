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
            }
        });
    }
});