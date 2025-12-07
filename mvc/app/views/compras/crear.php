<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Registrar Nueva Compra</h4>
        </div>
        <div class="card-body">
            <form action="<?php echo URLROOT; ?>/compras/crear" method="POST" id="formCompra">
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Proveedor *</label>
                        <select name="id_proveedor" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach($data['proveedores'] as $prov): ?>
                                <option value="<?php echo $prov['id_proveedor']; ?>">
                                    <?php echo $prov['nombre']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha *</label>
                        <input type="date" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>

                <h5 class="mb-3 border-bottom pb-2">Detalle de Productos</h5>
                
                <div id="detalles-container">
                    <div class="row mb-2 detalle-row">
                        <div class="col-md-5">
                            <select name="equipos[]" class="form-select" required>
                                <option value="">Seleccione equipo...</option>
                                <?php foreach($data['equipos'] as $equipo): ?>
                                    <option value="<?php echo $equipo['id_equipo']; ?>">
                                        <?php echo $equipo['sku'] . ' - ' . $equipo['descripcion']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="cantidades[]" class="form-control" placeholder="Cantidad" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="costos[]" class="form-control" placeholder="Costo Unitario" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-row">
                        <i class="bi bi-plus"></i> Agregar Producto
                    </button>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="<?php echo URLROOT; ?>/compras" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Compra</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('detalles-container');
    const btnAdd = document.getElementById('add-row');

    btnAdd.addEventListener('click', function() {
        const row = container.querySelector('.detalle-row').cloneNode(true);
        row.querySelectorAll('input').forEach(input => input.value = '');
        container.appendChild(row);
        
        // Re-attach event listener for remove button
        attachRemoveEvent(row.querySelector('.remove-row'));
    });

    function attachRemoveEvent(btn) {
        btn.addEventListener('click', function() {
            if (container.querySelectorAll('.detalle-row').length > 1) {
                this.closest('.detalle-row').remove();
            }
        });
    }

    // Attach initial remove event
    document.querySelectorAll('.remove-row').forEach(btn => attachRemoveEvent(btn));
});
</script>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
