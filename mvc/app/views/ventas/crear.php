<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <h2><i class="bi bi-box-arrow-right"></i> Nueva Salida</h2>
    
    <div class="card">
        <div class="card-body">
            <form action="<?php echo URLROOT; ?>/ventas/crear" method="POST" id="formVenta">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Fecha y Hora</label>
                        <input type="datetime-local" name="fecha" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>" readonly>
                    </div>
                </div>

                <hr>
                <h5>Productos</h5>
                <div id="productosContainer">
                    <div class="row mb-2 producto-item">
                        <div class="col-md-6">
                            <label class="form-label">Producto</label>
                            <select name="equipos[]" class="form-select producto-select" required>
                                <option value="">Seleccione...</option>
                                <?php foreach($data['productos'] as $prod): ?>
                                    <option value="<?php echo $prod['id_equipo']; ?>" 
                                            data-disponible="<?php echo $prod['cantidad_disponible']; ?>">
                                        <?php echo $prod['sku']; ?> - <?php echo $prod['descripcion']; ?> 
                                        (Disponible: <?php echo $prod['cantidad_disponible']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cantidad</label>
                            <input type="number" name="cantidades[]" class="form-control cantidad-input" min="1" required>
                        </div>
                        <input type="hidden" name="precios[]" value="0">
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm w-100" onclick="eliminarProducto(this)" disabled>
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="agregarProducto()">
                    <i class="bi bi-plus"></i> Agregar Producto
                </button>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">Crear Salida</button>
                    <a href="<?php echo URLROOT; ?>/ventas" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let productoTemplate = document.querySelector('.producto-item').cloneNode(true);

function agregarProducto() {
    let container = document.getElementById('productosContainer');
    let nuevoProducto = productoTemplate.cloneNode(true);
    nuevoProducto.querySelectorAll('input, select').forEach(input => input.value = '');
    nuevoProducto.querySelector('.btn-danger').disabled = false;
    container.appendChild(nuevoProducto);
    actualizarValidacionStock();
}

function eliminarProducto(btn) {
    btn.closest('.producto-item').remove();
}

// Validar stock disponible
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('producto-select') || e.target.classList.contains('cantidad-input')) {
        actualizarValidacionStock();
    }
});

function actualizarValidacionStock() {
    document.querySelectorAll('.producto-item').forEach(item => {
        let select = item.querySelector('.producto-select');
        let cantidad = item.querySelector('.cantidad-input');
        let option = select.options[select.selectedIndex];
        
        if (option && cantidad.value) {
            let disponible = parseInt(option.dataset.disponible || 0);
            let solicitado = parseInt(cantidad.value || 0);
            
            if (solicitado > disponible) {
                cantidad.setCustomValidity('Stock insuficiente. Disponible: ' + disponible);
                cantidad.classList.add('is-invalid');
            } else {
                cantidad.setCustomValidity('');
                cantidad.classList.remove('is-invalid');
            }
        }
    });
}
</script>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
