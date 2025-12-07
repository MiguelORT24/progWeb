<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning">
                    <h4 class="mb-0">Editar Equipo: <?php echo $data['equipo']['sku']; ?></h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/equipos/editar/<?php echo $data['equipo']['id_equipo']; ?>" method="POST">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">SKU *</label>
                                <input type="text" name="sku" class="form-control" value="<?php echo $data['equipo']['sku']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo *</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="CAMARA" <?php echo ($data['equipo']['tipo'] == 'CAMARA') ? 'selected' : ''; ?>>Cámara</option>
                                    <option value="SENSOR" <?php echo ($data['equipo']['tipo'] == 'SENSOR') ? 'selected' : ''; ?>>Sensor</option>
                                    <option value="COMPONENTE" <?php echo ($data['equipo']['tipo'] == 'COMPONENTE') ? 'selected' : ''; ?>>Componente</option>
                                    <option value="OTRO" <?php echo ($data['equipo']['tipo'] == 'OTRO') ? 'selected' : ''; ?>>Otro</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción *</label>
                            <textarea name="descripcion" class="form-control" rows="3" required><?php echo $data['equipo']['descripcion']; ?></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Marca</label>
                                <select name="id_marca" class="form-select">
                                    <option value="">Seleccione...</option>
                                    <?php foreach($data['marcas'] as $marca): ?>
                                        <option value="<?php echo $marca['id_marca']; ?>" <?php echo ($data['equipo']['id_marca'] == $marca['id_marca']) ? 'selected' : ''; ?>>
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
                                        <option value="<?php echo $cat['id_categoria']; ?>" <?php echo ($data['equipo']['id_categoria'] == $cat['id_categoria']) ? 'selected' : ''; ?>>
                                            <?php echo $cat['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo URLROOT; ?>/equipos" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-warning">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
