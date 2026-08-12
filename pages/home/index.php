<?php
/** @var \WebbyCMS\Request $request */
?>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Starway Travel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#destinations">Destinations</a></li>
                    <li class="nav-item"><a class="nav-link" href="#gallery">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="container my-5">
    <div class="p-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-3">
            <h1 class="display-5 fw-bold">Welcome to Starway Travel!</h1>
            <p class="col-md-8 fs-5">Come travel with us. <?= e(initWebbyCMS()) ?></p>
        </div>
    </div>

    <div class="row g-4" id="destinations">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="card-title h5">Heading A</h2>
                    <p class="card-text">Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh.</p>
                    <a href="#" class="btn btn-primary">View details »</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="card-title h5">Heading B</h2>
                    <p class="card-text">Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. Aenean lacinia bibendum nulla sed consectetur.</p>
                    <a href="#" class="btn btn-primary">View details »</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="card-title h5">Heading C</h2>
                    <p class="card-text">Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                    <a href="#" class="btn btn-primary">View details »</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-5" id="contact">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title h5">Form post handling sample</h2>
                    <p class="text-muted">Submitting this form POSTs to <code>/pages/home/forms/sample.php</code> and redirects back here.</p>
                    <form method="post">
                        <input type="hidden" name="form" value="sample">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="send_something" class="form-label">Your message</label>
                            <input type="text" class="form-control" name="send_something" id="send_something" value="<?= e((string) $request->input('send_something', '')) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
