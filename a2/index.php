<?php

require_once __DIR__ . '/includes/db_connect.inc';

$sql = "SELECT book_id, title, author, price, status, image_path
        FROM books
        ORDER BY book_id DESC
        LIMIT 4";

$result = mysqli_query($conn, $sql);

require_once __DIR__ . '/includes/header.inc';

?>

<!-- Hero Section -->
<section class="hero-section">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-6">

                <span class="hero-label">
                    Welcome to BookVerse
                </span>

                <h1>
                    Discover Your Next Great Read
                </h1>

                <p>
                    Explore our collection of books and find
                    something worth reading.
                </p>

                <div class="hero-buttons">

                    <a
                        href="/wp-fork/a2/books.php"
                        class="btn btn-primary-custom"
                    >
                        Browse Books
                    </a>

                    <a
                        href="/wp-fork/a2/gallery.php"
                        class="btn btn-outline-custom"
                    >
                        View Gallery
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- Carousel -->
<section class="carousel-section">

    <div class="container">

        <div
            id="bookVerseCarousel"
            class="carousel slide"
            data-bs-ride="carousel"
        >

            <div class="carousel-indicators">

                <button
                    type="button"
                    data-bs-target="#bookVerseCarousel"
                    data-bs-slide-to="0"
                    class="active"
                    aria-current="true"
                    aria-label="Slide 1"
                ></button>

                <button
                    type="button"
                    data-bs-target="#bookVerseCarousel"
                    data-bs-slide-to="1"
                    aria-label="Slide 2"
                ></button>

                <button
                    type="button"
                    data-bs-target="#bookVerseCarousel"
                    data-bs-slide-to="2"
                    aria-label="Slide 3"
                ></button>

                <button
                    type="button"
                    data-bs-target="#bookVerseCarousel"
                    data-bs-slide-to="3"
                    aria-label="Slide 4"
                ></button>

            </div>


            <div class="carousel-inner">

                <div class="carousel-item active">

                    <img
                        src="/wp-fork/a2/assets/images/banner1.jpg"
                        class="d-block w-100"
                        alt="BookVerse banner"
                    >

                </div>


                <div class="carousel-item">

                    <img
                        src="/wp-fork/a2/assets/images/banner2.jpg"
                        class="d-block w-100"
                        alt="Books banner"
                    >

                </div>


                <div class="carousel-item">

                    <img
                        src="/wp-fork/a2/assets/images/banner3.jpg"
                        class="d-block w-100"
                        alt="Reading banner"
                    >

                </div>


                <div class="carousel-item">

                    <img
                        src="/wp-fork/a2/assets/images/banner4.jpg"
                        class="d-block w-100"
                        alt="Book collection banner"
                    >

                </div>

            </div>


            <button
                class="carousel-control-prev"
                type="button"
                data-bs-target="#bookVerseCarousel"
                data-bs-slide="prev"
            >

                <span
                    class="carousel-control-prev-icon"
                    aria-hidden="true"
                ></span>

                <span class="visually-hidden">
                    Previous
                </span>

            </button>


            <button
                class="carousel-control-next"
                type="button"
                data-bs-target="#bookVerseCarousel"
                data-bs-slide="next"
            >

                <span
                    class="carousel-control-next-icon"
                    aria-hidden="true"
                ></span>

                <span class="visually-hidden">
                    Next
                </span>

            </button>

        </div>

    </div>

</section>


<!-- Latest Books -->
<section class="latest-books-section">

    <div class="container">

        <div class="section-heading">

            <span>
                Recently Added
            </span>

            <h2>
                Latest Books
            </h2>

        </div>


        <div class="row g-4">

            <?php if (mysqli_num_rows($result) > 0): ?>

                <?php while ($book = mysqli_fetch_assoc($result)): ?>

                    <div class="col-md-6 col-lg-3">

                        <div class="book-card">

                            <div class="book-card-image">

                                <img
                                    src="/wp-fork/a2/<?= htmlspecialchars($book['image_path']) ?>"
                                    alt="<?= htmlspecialchars($book['title']) ?>"
                                >

                            </div>


                            <div class="book-card-content">

                                <h3>
                                    <?= htmlspecialchars($book['title']) ?>
                                </h3>

                                <p class="book-author">
                                    <?= htmlspecialchars($book['author']) ?>
                                </p>

                                <p class="book-price">
                                    $<?= number_format((float)$book['price'], 2) ?>
                                </p>

                                <span class="book-status">
                                    <?= htmlspecialchars($book['status']) ?>
                                </span>

                                <a
                                    href="/wp-fork/a2/details.php?id=<?= (int)$book['book_id'] ?>"
                                    class="btn btn-primary-custom btn-sm"
                                >
                                    View Details
                                </a>

                            </div>

                        </div>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="col-12">

                    <p class="text-center">
                        No books are currently available.
                    </p>

                </div>

            <?php endif; ?>

        </div>


        <div class="text-center mt-5">

            <a
                href="/wp-fork/a2/books.php"
                class="btn btn-primary-custom"
            >
                View All Books
            </a>

        </div>

    </div>

</section>


<?php

require_once __DIR__ . '/includes/footer.inc';

?>