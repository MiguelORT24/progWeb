<?php require_once APPROOT . '/views/layouts/header.inc.php'; ?>

<h2><i class="fa fa-truck"></i> <?= $data['accion'] ?? 'Crear' ?> Proveedor</h2>

<?php if(!empty($data['error'])): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach($data['error'] as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="id" value="<?= $data['id'] ?? '' ?>">
    
    <div class="mb-3">
        <label for="proveedor_nombre" class="form-label">Nombre del Proveedor *</label>
        <input type="text" class="form-control" id="proveedor_nombre" name="proveedor_nombre" 
               value="<?= $data['proveedor_nombre'] ?? '' ?>" required>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="proveedor_contacto" class="form-label">Persona de Contacto</label>
                <input type="text" class="form-control" id="proveedor_contacto" name="proveedor_contacto" 
                       value="<?= $data['proveedor_contacto'] ?? '' ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="proveedor_telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="proveedor_telefono" name="proveedor_telefono" 
                       value="<?= $data['proveedor_telefono'] ?? '' ?>">
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="proveedor_email" class="form-label">Email</label>
        <input type="email" class="form-control" id="proveedor_email" name="proveedor_email" 
               value="<?= $data['proveedor_email'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label for="proveedor_direccion" class="form-label">Dirección</label>
        <textarea class="form-control" id="proveedor_direccion" name="proveedor_direccion" rows="3"><?= $data['proveedor_direccion'] ?? '' ?></textarea>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
        <a href="<?= URLROOT ?>/proveedores" class="btn btn-secondary"><i class="fa fa-times"></i> Cancelar</a>
    </div>
</form>

<?php require_once APPROOT . '/views/layouts/footer.inc.php'; ?>
