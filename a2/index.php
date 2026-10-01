<?php

require_once __DIR__ . '/includes/db_connect.inc';


/*
|--------------------------------------------------------------------------
| Get latest four books
|--------------------------------------------------------------------------
*/

$sql = "SELECT book_id, title, author, price, status, image_path
        FROM books
        ORDER BY book_id DESC
        LIMIT 4";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

$books = [];

while ($row = mysqli_fetch_assoc($result)) {
    $books[] = $row;
}


require_once __DIR__ . '/includes/header.inc';

?>


<main>


    <!-- =========================================================
         HERO SECTION
    ========================================================== -->

    <section class="hero-section">

        <div class="container">

            <div class="row align-items-center g-4">


                <!-- HERO TEXT -->

                <div class="col-lg-6">

                    <span class="hero-label">
                        WELCOME TO BOOKVERSE
                    </span>


                    <h1>

                        Discover your next

                        <span class="highlight">
                            great read.
                        </span>

                    </h1>


                    <p class="hero-text">

                        Explore our collection of books, discover new
                        favourites and find your next story.

                    </p>


                    <div class="hero-buttons">


                        <a
                            href="/wp-1/a2/books.php"
                            class="btn btn-primary-custom"
                        >
                            Browse Books
                        </a>


                        <a
                            href="/wp-1/a2/gallery.php"
                            class="btn btn-outline-custom"
                        >
                            View Gallery
                        </a>


                    </div>

                </div>



                <!-- CAROUSEL -->

                <div class="col-lg-6">

                    <div
                        id="bookCarousel"
                        class="carousel slide carousel-fade shadow-lg"
                        data-bs-ride="carousel"
                    >


                        <!-- INDICATORS -->

                        <div class="carousel-indicators">

                            <button
                                type="button"
                                data-bs-target="#bookCarousel"
                                data-bs-slide-to="0"
                                class="active"
                                aria-current="true"
                                aria-label="Slide 1"
                            ></button>


                            <button
                                type="button"
                                data-bs-target="#bookCarousel"
                                data-bs-slide-to="1"
                                aria-label="Slide 2"
                            ></button>


                            <button
                                type="button"
                                data-bs-target="#bookCarousel"
                                data-bs-slide-to="2"
                                aria-label="Slide 3"
                            ></button>


                            <button
                                type="button"
                                data-bs-target="#bookCarousel"
                                data-bs-slide-to="3"
                                aria-label="Slide 4"
                            ></button>

                        </div>



                        <!-- SLIDES -->

                        <div class="carousel-inner rounded-4">


                            <div class="carousel-item active">

                                <img
                                    src="/wp-1/a2/assets/images/banner1.jpg"
                                    class="d-block w-100"
                                    alt="BookVerse collection"
                                >

                            </div>


                            <div class="carousel-item">

                                <img
                                    src="/wp-1/a2/assets/images/banner2.jpg"
                                    class="d-block w-100"
                                    alt="Books collection"
                                >

                            </div>


                            <div class="carousel-item">

                                <img
                                    src="/wp-1/a2/assets/images/banner3.jpg"
                                    class="d-block w-100"
                                    alt="Books on a shelf"
                                >

                            </div>


                            <div class="carousel-item">

                                <img
                                    src="/wp-1/a2/assets/images/banner4.jpg"
                                    class="d-block w-100"
                                    alt="Reading collection"
                                >

                            </div>


                        </div>



                        <!-- PREVIOUS -->

                        <button
                            class="carousel-control-prev"
                            type="button"
                            data-bs-target="#bookCarousel"
                            data-bs-slide="prev"
                            aria-label="Previous slide"
                        >

                            <span class="carousel-control-prev-icon"></span>

                        </button>



                        <!-- NEXT -->

                        <button
                            class="carousel-control-next"
                            type="button"
                            data-bs-target="#bookCarousel"
                            data-bs-slide="next"
                            aria-label="Next slide"
                        >

                            <span class="carousel-control-next-icon"></span>

                        </button>


                    </div>

                </div>


            </div>

        </div>

    </section>



    <!-- =========================================================
         FEATURED BOOKS
    ========================================================== -->

    <section class="section-padding">

        <div class="container">


            <div class="section-heading">

                <span class="section-label">
                    FEATURED COLLECTION
                </span>


                <h2>
                    Explore our books
                </h2>


                <p>
                    Find something interesting from our growing collection.
                </p>

            </div>



            <div class="row g-4">


                <?php if (count($books) > 0): ?>


                    <?php foreach ($books as $book): ?>


                        <div class="col-sm-6 col-lg-3">


                            <article class="book-card">


                                <!-- BOOK IMAGE -->

                                <?php if (!empty($book['image_path'])): ?>

                                    <img
                                        src="/wp-1/a2/<?= htmlspecialchars($book['image_path']) ?>"
                                        alt="<?= htmlspecialchars($book['title']) ?>"
                                        class="book-card-image"
                                    >

                                <?php else: ?>

                                    <div
                                        class="book-card-image d-flex align-items-center justify-content-center"
                                    >
                                        No Cover Image
                                    </div>

                                <?php endif; ?>



                                <!-- BOOK INFORMATION -->

                                <div class="book-card-body">


                                    <span class="status-badge">
                                        <?= htmlspecialchars($book['status']) ?>
                                    </span>


                                    <h3>
                                        <?= htmlspecialchars($book['title']) ?>
                                    </h3>


                                    <p class="book-author">
                                        <?= htmlspecialchars($book['author']) ?>
                                    </p>


                                    <p class="book-price">

                                        $<?= number_format(
                                            (float)$book['price'],
                                            2
                                        ) ?>

                                    </p>


                                    <a
                                        href="/wp-1/a2/details.php?id=<?= (int)$book['book_id'] ?>"
                                        class="btn btn-sm btn-primary-custom"
                                    >
                                        View Details
                                    </a>


                                </div>


                            </article>


                        </div>


                    <?php endforeach; ?>


                <?php else: ?>


                    <div class="col-12">

                        <div class="alert alert-info">

                            No books have been added yet.

                        </div>

                    </div>


                <?php endif; ?>


            </div>

        </div>

    </section>


</main>


<?php

require_once __DIR__ . '/includes/footer.inc';

?>