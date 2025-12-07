<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Nueva Orden de Instalación</h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/ordenes/crear" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label">Cliente *</label>
                            <select name="id_cliente" class="form-select" required>
                                <option value="">Seleccione cliente...</option>
                                <?php foreach($data['clientes'] as $cliente): ?>
                                    <option value="<?php echo $cliente['id_cliente']; ?>">
                                        <?php echo $cliente['nombre']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fecha Programada *</label>
                            <input type="date" name="fecha_programada" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo URLROOT; ?>/ordenes" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-warning">Crear Orden</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
