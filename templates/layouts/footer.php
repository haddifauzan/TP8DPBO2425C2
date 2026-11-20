<!-- PENUTUP CONTAINER KONTEN UTAMA -->
</main>

<!-- FOOTER APLIKASI (STICKY FOOTER) -->
<!-- mt-auto bekerja sama dengan body flexbox untuk menjaga posisi footer selalu di batas bawah layar -->
<footer class="bg-light border-top py-3 mt-auto">
    <div class="container text-center">
        <small class="text-muted">
            <!-- Menampilkan tahun saat ini secara otomatis dan nama aplikasi -->
            &copy; <?= date('Y') ?> <?= APP_NAME ?>. All Rights Reserved.
        </small>
    </div>
</footer>

<!-- PEMUATAN ASSET JAVASCRIPT BOOTSTRAP LOKAL -->
<!-- Memuat bootstrap.bundle.min.js yang sudah mencakup Popper.js untuk komponen dropdown & alert -->
<script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
