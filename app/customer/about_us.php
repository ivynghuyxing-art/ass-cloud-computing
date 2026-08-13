<?php
require_once '../_base.php';
$title = 'About Us';
$_title = '';
include 'customer_header.php';
?>

<div class="title">
    <h2>About Us</h2>
</div>

<section class="about-page">

    <!-- Hero Banner -->
    <div class="about-hero">
        <h1>Welcome to Book Nest</h1>
        <p>Your gateway to a world of knowledge, stories, and discovery.</p>
    </div>

    <!-- Brand Story -->
    <div class="about-section">
        <div class="about-text">
            <h2>Our Story</h2>
            <p>Book Nest was founded with a simple mission — to make reading accessible to everyone. From a small community library in Penang, we have grown into a digital library system serving readers across Malaysia.</p>
            <p>We believe every book can inspire and transform. At Book Nest, we are committed to bringing a diverse collection of books to readers everywhere.</p>
        </div>
        <img src ="/images/favicon.png" alt = "About Book Nest" style = "width :100%;border-radius:16px ">
    </div>

    <!-- Values -->
    <div class="about-values">
        <h2>What We Stand For</h2>
        <div class="values-grid">
            <div class="value-card">
                <span class="value-icon">📚</span>
                <h3>Extensive Collection</h3>
                <p>A diverse range of books, journals, and academic resources across all disciplines.</p>
            </div>
            <div class="value-card">
                <span class="value-icon">⏰</span>
                <h3>Easy Borrowing</h3>
                <p>Simple search, reserve, and checkout processes with flexible loan periods.</p>
            </div>
            <div class="value-card">
                <span class="value-icon">🌐</span>
                <h3>Accessibility</h3>
                <p>Our digital library system makes it easy for anyone, anywhere in Malaysia, to borrow books anytime, anywhere. Reading has never been more convenient.</p>
            </div>
            <div class="value-card">
                <span class="value-icon">❤️</span>
                <h3>Community Focus</h3>
                <p>Dedicated to supporting readers, researchers, and lifelong learners.</p>
            </div>
        </div>
    </div>

</section>

<?php include '../footer.php'; ?>