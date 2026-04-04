    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <p>Built with PHP, PDO, and love for clean shopping experiences.</p>
            <p>&copy; <?= date('Y') ?> <?= e((string) App\Core\Config::get('app_name')) ?></p>
        </div>
    </footer>

    <script src="<?= e(asset_url('assets/js/app.js')) ?>"></script>
    </body>

    </html>