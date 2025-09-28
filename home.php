<?php
session_start();
include 'config.php';

$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JobMate - Find Skilled Workers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="Assets/CSS/hero.css">
</head>
<body>

<?php include 'header.php'; ?>

<!-- Hero Section -->
<div class="hero-section">
  <div class="container">
    <h1 class="display-3 fw-bold">Find Skilled Workers Near You</h1>
    <p class="lead mt-3">JobMate connects you with trusted carpenters, plumbers, painters, and other professionals — rated and reviewed by real users. Hire with confidence in just a few clicks!</p>
    <a href="service.php" class="btn">Explore Services</a>
  </div>  
</div>

<!-- Service Categories -->
<div class="container my-5" id="services">
  <h2 class="text-center mb-4">Popular Categories</h2>
  <div class="row text-center">
    <div class="col-md-3 mb-4 category-card">
      <img src="Assets/IMG/plumber.jpeg" class="img-fluid rounded" alt="Plumber">
      <h5 class="mt-2">Plumber</h5>
    </div>
    <div class="col-md-3 mb-4 category-card">
      <img src="carpenter.jpeg" class="img-fluid rounded" alt="Carpenter">
      <h5 class="mt-2">Carpenter</h5>
    </div>
    <div class="col-md-3 mb-4 category-card">
      <img src="Assets/IMG/electrician.jpeg" class="img-fluid rounded" alt="Electrician">
      <h5 class="mt-2">Electrician</h5>
    </div>
    <div class="col-md-3 mb-4 category-card">
      <img src="Assets/IMG/painter.jpeg" class="img-fluid rounded" alt="Painter">
      <h5 class="mt-2">Painter</h5>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>