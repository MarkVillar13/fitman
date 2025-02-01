<?php
$products = [];
$services = [];

$productQuery = mysqli_query($db, "SELECT * FROM products WHERE picture != ''");
if ($productQuery) {
    $products = mysqli_fetch_all($productQuery, MYSQLI_ASSOC);
}

$serviceQuery = mysqli_query($db, "SELECT * FROM services");
if ($serviceQuery) {
    $services = mysqli_fetch_all($serviceQuery, MYSQLI_ASSOC);
}

$products = $products ?? [];
$services = $services ?? [];
?>

<section class="py-5 bg-light">
    <div class="container">
        <h1 class="display-4 text-center mb-5">Our Products & Services</h1>
        
        <div class="row g-4 justify-content-center">
            <!-- Products -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-lg h-100 rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h2 class="h3 fw-bold ml-4">Featured Products</h2>
                    </div>
                    <div class="card-body p-0">
                        <div id="productCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php foreach ($products as $key => $product): ?>
                                <div class="carousel-item <?= $key === 0 ? 'active' : '' ?>" data-bs-interval="5000">
                                    <div class="p-4">
                                        <div class="row align-items-center">
                                            <div class="col-4">
                                                <img src="assets/products/<?= htmlspecialchars($product['picture']) ?>" 
                                                     class="img-fluid rounded-3 shadow-sm" 
                                                     alt="<?= htmlspecialchars($product['name']) ?>">
                                            </div>
                                            <div class="col-8">
                                                <h3 class="h5 mb-3 text-dark"><?= htmlspecialchars($product['name']) ?></h3>
                                                <h4 class="text-muted small mb-3"><?= htmlspecialchars($product['description']) ?></h4>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge px-3 py-2 rounded-pill" style="background: #e3d3f5; color: #5719a8">
                                                        ₱<?= number_format($product['price'], 2) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if (count($products) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon bg-dark rounded-circle"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon bg-dark rounded-circle"></span>
                            </button>
                            
                            <div class="carousel-indicators position-relative mt-3">
                                <?php foreach ($products as $key => $product): ?>
                                <button type="button" 
                                        data-bs-target="#productCarousel" 
                                        data-bs-slide-to="<?= $key ?>" 
                                        class="<?= $key === 0 ? 'active' : '' ?> bg-dark">
                                </button>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-lg h-100 rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h2 class="h3 fw-bold ml-4">Our Services</h2>
                    </div>
                    <div class="card-body p-0">
                        <div id="serviceCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php foreach ($services as $key => $service): ?>
                                <div class="carousel-item <?= $key === 0 ? 'active' : '' ?>" data-bs-interval="5000">
                                    <div class="p-4">
                                        <div class="row align-items-center">
                                            <div class="col-4">
                                                <img src="assets/services/<?= htmlspecialchars($service['picture']) ?>" 
                                                     class="img-fluid rounded-3 shadow-sm" 
                                                     alt="<?= htmlspecialchars($service['name']) ?>">
                                            </div>
                                            <div class="col-8">
                                                <h3 class="h5 mb-3 text-dark"><?= htmlspecialchars($service['name']) ?></h3>
                                                <h4 class="text-muted small mb-3"><?= htmlspecialchars($service['description']) ?></h4>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge px-3 py-2 rounded-pill" style="background: #e3d3f5; color: #5719a8">
                                                        ₱<?= number_format($service['price'], 2) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if (count($services) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#serviceCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon bg-dark rounded-circle"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#serviceCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon bg-dark rounded-circle"></span>
                            </button>
                            
                            <div class="carousel-indicators position-relative mt-3">
                                <?php foreach ($services as $key => $service): ?>
                                <button type="button" 
                                        data-bs-target="#serviceCarousel" 
                                        data-bs-slide-to="<?= $key ?>" 
                                        class="<?= $key === 0 ? 'active' : '' ?> bg-dark">
                                </button>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
  h1.display-4 {
    font-size: 2.5rem; /* Smaller size */
    font-weight: 700;  /* Bold text */
    letter-spacing: 1px; /* Slight letter spacing for emphasis */
}
.carousel-item {
    transition: transform .6s ease-in-out;
}

.carousel-control-prev,
.carousel-control-next {
    width: 60px;
    height: 40px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.8;
    padding: 0 15px;
    border-radius: 50%;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    width: 20px;
    height: 20px;
}

.carousel-indicators {
    margin-bottom: 0;
}

.carousel-indicators button {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin: 0 4px;
}

.card {
    border-radius: 15px;
    transition: all 0.3s ease-in-out;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.card-body {
    padding: 0;
}

h4 {
    font-size: 1rem;
    font-weight: 400;
    color: #6c757d;
}

h3 {
    font-size: 1.25rem;
    font-weight: 600;
}

.badge {
    font-weight: 500;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .carousel-item .row {
        flex-direction: column;
    }
    
    .carousel-item .col-4,
    .carousel-item .col-8 {
        width: 100%;
    }
    
    .carousel-item img {
        margin-bottom: 1rem;
    }

    .carousel-control-prev,
    .carousel-control-next {
        left: -20px;
    }
}
</style>