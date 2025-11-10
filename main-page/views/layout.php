<?php require __DIR__ . '/partials/head.php'; ?>
<?php require __DIR__ . '/partials/flash.php'; ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<body>
    <?php if (is_logged_in()) require __DIR__ . '/partials/navbar.php'; ?>
    <main class="container py-4">
        <?php require __DIR__ . '/partials/flash.php'; ?>
        <?php echo $content ?? ''; ?>
    </main>
    <?php require __DIR__ . '/partials/footer.php'; ?>
    <script src="/assets/js/app.js"></script>
</body>

</html>