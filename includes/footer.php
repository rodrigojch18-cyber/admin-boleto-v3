    <!-- Footer -->
    <footer class="border-t border-white/5 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-gray-500">
                    &copy; <?= date('Y') ?> <?= APP_NAME ?> — <?= APP_AUTHOR ?>
                </p>
                <div class="flex items-center gap-4">
                    <span class="text-xs text-gray-600">Powered by Supabase</span>
                    <span class="text-xs text-gray-600">&bull;</span>
                    <span class="text-xs text-gray-600">PHP <?= phpversion() ?></span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Supabase JS Client -->
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

    <!-- Application JS -->
    <script>
        // Pass PHP config to JS
        window.SUPABASE_URL  = '<?= SUPABASE_URL ?>';
        window.SUPABASE_KEY  = '<?= SUPABASE_ANON_KEY ?>';
        window.SUPABASE_TABLE = '<?= SUPABASE_TABLE ?>';
    </script>
    <script src="assets/js/app.js"></script>
</body>
</html>
