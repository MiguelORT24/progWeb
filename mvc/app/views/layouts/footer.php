    <footer class="bg-white text-center py-3 mt-5 border-top">
        <div class="container">
            <p class="mb-0 text-muted">
                &copy; <?php echo date('Y'); ?> Sistema de Inventario - Cámaras y Sensores
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-ocultar alertas después de 5 segundos
        setTimeout(function() {
            let alert = document.getElementById('msg-flash');
            if (alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    </script>
</body>
</html>
