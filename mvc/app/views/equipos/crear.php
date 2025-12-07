<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Nuevo Equipo</h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/equipos/crear" method="POST">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">SKU *</label>
                                <input type="text" name="sku" class="form-control" required placeholder="Ej: CAM-001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo *</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="CAMARA">Cámara</option>
                                    <option value="SENSOR">Sensor</option>
                                    <option value="COMPONENTE">Componente</option>
                                    <option value="OTRO">Otro</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción *</label>
                            <textarea name="descripcion" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Marca</label>
                                <select name="id_marca" class="form-select">
                                    <option value="">Seleccione...</option>
                                    <?php foreach($data['marcas'] as $marca): ?>
                                        <option value="<?php echo $marca['id_marca']; ?>">
                                            <?php echo $marca['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Categoría</label>
                                <select name="id_categoria" class="form-select">
                                    <option value="">Seleccione...</option>
                                    <?php foreach($data['categorias'] as $cat): ?>
                                        <option value="<?php echo $cat['id_categoria']; ?>">
                                            <?php echo $cat['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo URLROOT; ?>/equipos" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-success">Guardar Equipo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
