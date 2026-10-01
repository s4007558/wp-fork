document.addEventListener("DOMContentLoaded", function () {

    /*
     * Image upload validation
     */

    const imageInput = document.getElementById("image");

    if (imageInput) {

        imageInput.addEventListener("change", function () {

            const file = this.files[0];

            if (!file) {
                return;
            }

            const allowedTypes = [
                "image/jpeg",
                "image/png",
                "image/gif",
                "image/webp"
            ];

            if (!allowedTypes.includes(file.type)) {

                alert(
                    "Please upload a JPG, JPEG, PNG, GIF or WEBP image."
                );

                this.value = "";

            }

        });

    }


    /*
     * Gallery modal
     */

    const galleryImages =
        document.querySelectorAll("[data-gallery-image]");

    const modalImage =
        document.getElementById("modalImage");

    galleryImages.forEach(function (image) {

        image.addEventListener("click", function () {

            if (modalImage) {

                modalImage.src =
                    this.getAttribute("data-gallery-image");

                modalImage.alt =
                    this.getAttribute("data-gallery-title") || "Book";

            }

        });

    });


    /*
     * Client-side book filtering
     */

    const filterButtons =
        document.querySelectorAll("[data-filter]");

    const bookItems =
        document.querySelectorAll("[data-book-status]");

    filterButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            const filter =
                this.getAttribute("data-filter");

            bookItems.forEach(function (book) {

                const status =
                    book.getAttribute("data-book-status");

                if (
                    filter === "all" ||
                    filter === status
                ) {

                    book.style.display = "";

                } else {

                    book.style.display = "none";

                }

            });


            filterButtons.forEach(function (item) {
                item.classList.remove("active");
            });

            this.classList.add("active");

        });

    });

});