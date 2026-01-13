    </div> <!-- /.container-fluid -->
    <?php
    $base = rtrim(BASE_URL, '/');
    $adminBase = $base;
    if (substr($base, -7) === '/public') {
        $adminBase = substr($base, 0, -7);
    }
    $assetBase = $adminBase . '/assets';
    ?>
    <script src="<?php echo $assetBase; ?>/js/bootstrap.bundle.min.js"></script>
</body>
</html>
