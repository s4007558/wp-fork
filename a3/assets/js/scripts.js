/*
 * BookVerse - scripts.js
 * The ONE custom JavaScript file for every page. It contains no page-specific
 * <script> tags and no inline onclick="" handlers: each feature below looks for
 * its own elements and does nothing if they are not on the current page.
 *
 * Features
 *   1. Image upload: extension/size validation + live preview   (add.php, edit.php)
 *   2. Status filter (pure JavaScript, uses data-status)         (books.php)
 *   3. Gallery modal with Previous / Next                        (gallery.php)
 *   4. Delete confirmation modal                                 (details.php)
 *   5. Password confirmation check                               (register.php)
 *   6. Bootstrap-style form validation messages                  (all forms)
 */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    /* ------------------------------------------------------------------
     * 1. Image upload validation + preview
     * ------------------------------------------------------------------ */
    var ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    var MAX_BYTES = 5 * 1024 * 1024; // 5 MB, same limit as the server (MAX_IMAGE_BYTES in functions.inc)

    var imageInput = document.querySelector('input[name="image_path"]');
    var imageError = document.getElementById('imageError');
    var imagePreview = document.getElementById('imagePreview');

    function getExtension(filename) {
        var parts = filename.toLowerCase().split('.');
        return parts.length > 1 ? parts.pop() : '';
    }

    function showImageError(message) {
        if (imageError) {
            imageError.textContent = message;
            imageError.hidden = (message === '');
        }
        imageInput.classList.toggle('is-invalid', message !== '');
    }

    function clearPreview() {
        if (imagePreview) {
            imagePreview.hidden = true;
            imagePreview.removeAttribute('src');
        }
    }

    if (imageInput) {
        imageInput.addEventListener('change', function () {
            showImageError('');
            clearPreview();

            var file = imageInput.files[0];
            if (!file) {
                return;                                   // user cancelled: nothing chosen
            }

            if (ALLOWED_EXTENSIONS.indexOf(getExtension(file.name)) === -1) {
                showImageError('Only jpg, jpeg, png, gif and webp images are allowed.');
                imageInput.value = '';                    // throw the bad file away (nothing is uploaded)
                return;
            }
            if (file.size > MAX_BYTES) {
                showImageError('The image is too large (maximum 5 MB).');
                imageInput.value = '';
                return;
            }

            // Valid: show a preview before the form is submitted.
            if (imagePreview) {
                var reader = new FileReader();
                reader.onload = function (event) {
                    imagePreview.src = event.target.result;
                    imagePreview.hidden = false;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    /* ------------------------------------------------------------------
     * 2. Status filter on books.php (show/hide rows, no page reload)
     * ------------------------------------------------------------------ */
    var statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        var rows = document.querySelectorAll('#booksTable tbody tr[data-status]');
        var noResults = document.getElementById('noResults');

        statusFilter.addEventListener('change', function () {
            var selected = statusFilter.value;            // all | available | reserved | sold
            var visible = 0;

            rows.forEach(function (row) {
                var show = (selected === 'all' || row.dataset.status === selected);
                row.hidden = !show;
                if (show) {
                    visible++;
                }
            });

            if (noResults) {
                noResults.hidden = (visible !== 0);
            }
        });
    }

    /* ------------------------------------------------------------------
     * 3. Gallery modal (Bootstrap modal filled from data- attributes)
     * ------------------------------------------------------------------ */
    var imageModalEl = document.getElementById('imageModal');
    if (imageModalEl) {
        var items = Array.prototype.slice.call(document.querySelectorAll('.gallery-item'));
        var modalImage = document.getElementById('modalImage');
        var modalTitle = document.getElementById('imageModalLabel');
        var prevButton = document.getElementById('prevImage');
        var nextButton = document.getElementById('nextImage');
        var current = 0;

        var showItem = function (index) {
            if (items.length === 0) {
                return;
            }
            current = (index + items.length) % items.length;   // wrap around
            var item = items[current];
            modalImage.src = item.dataset.full;
            modalImage.alt = 'Cover of ' + item.dataset.title;
            modalTitle.textContent = item.dataset.title + ' by ' + item.dataset.author;
        };

        items.forEach(function (item, index) {
            item.addEventListener('click', function () {
                showItem(index);                              // Bootstrap opens the modal itself
            });
        });

        prevButton.addEventListener('click', function () { showItem(current - 1); });
        nextButton.addEventListener('click', function () { showItem(current + 1); });

        // Keyboard arrows while the modal is open
        imageModalEl.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowLeft') { showItem(current - 1); }
            if (event.key === 'ArrowRight') { showItem(current + 1); }
        });
    }

    /* ------------------------------------------------------------------
     * 4. Delete confirmation (details.php)
     *    The owner's Delete button does NOT delete anything: it only opens the
     *    Bootstrap confirmation modal. The record is removed only when the
     *    "Delete book" button inside the modal submits the form to process_delete.php.
     * ------------------------------------------------------------------ */
    var deleteButton = document.getElementById('deleteBtn');
    var deleteModalEl = document.getElementById('deleteModal');
    if (deleteButton && deleteModalEl && window.bootstrap) {
        var deleteModal = window.bootstrap.Modal.getOrCreateInstance(deleteModalEl);
        deleteButton.addEventListener('click', function () {
            deleteModal.show();
        });
    }

    var deleteForm = document.getElementById('deleteForm');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function () {
            var confirmButton = deleteForm.querySelector('button[type="submit"]');
            if (confirmButton) {
                // stop an accidental double submit (disabled in the next tick so the click still posts)
                window.setTimeout(function () { confirmButton.disabled = true; }, 0);
            }
        });
    }

    /* ------------------------------------------------------------------
     * 5. Register form: password and confirmation must match
     * ------------------------------------------------------------------ */
    var password = document.getElementById('password');
    var confirmPassword = document.getElementById('confirm_password');
    if (password && confirmPassword) {
        var checkMatch = function () {
            confirmPassword.setCustomValidity(
                confirmPassword.value !== password.value ? 'Passwords do not match.' : ''
            );
        };
        password.addEventListener('input', checkMatch);
        confirmPassword.addEventListener('input', checkMatch);
    }

    /* ------------------------------------------------------------------
     * 6. Form validation messages
     *    The forms use novalidate so we control the messages: when something is
     *    invalid, stop the submit and let Bootstrap show the .invalid-feedback text.
     *    (The server validates everything again - this is only for the user's comfort.)
     * ------------------------------------------------------------------ */
    document.querySelectorAll('form.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
});
