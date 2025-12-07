<?php require_once APPROOT . '/views/layouts/header.inc.php'; ?>

<h2><i class="fa fa-box"></i> <?= $data['accion'] ?? 'Crear' ?> Producto</h2>

<?php if(!empty($data['error'])): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach($data['error'] as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $data['id'] ?? '' ?>">
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="producto_codigo" class="form-label">Código del Producto *</label>
                <input type="text" class="form-control" id="producto_codigo" name="producto_codigo" 
                       value="<?= $data['producto_codigo'] ?? '' ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="producto_nombre" class="form-label">Nombre del Producto *</label>
                <input type="text" class="form-control" id="producto_nombre" name="producto_nombre" 
                       value="<?= $data['producto_nombre'] ?? '' ?>" required>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="producto_descripcion" class="form-label">Descripción</label>
        <textarea class="form-control" id="producto_descripcion" name="producto_descripcion" rows="3"><?= $data['producto_descripcion'] ?? '' ?></textarea>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="categoria_id" class="form-label">Categoría</label>
                <select class="form-select" id="categoria_id" name="categoria_id">
                    <option value="">Seleccione una categoría</option>
                    <?php foreach($data['categorias'] as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (isset($data['categoria_id']) && $data['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= $cat['categoria_nombre'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="proveedor_id" class="form-label">Proveedor</label>
                <select class="form-select" id="proveedor_id" name="proveedor_id">
                    <option value="">Seleccione un proveedor</option>
                    <?php foreach($data['proveedores'] as $prov): ?>
                        <option value="<?= $prov['id'] ?>" <?= (isset($data['proveedor_id']) && $data['proveedor_id'] == $prov['id']) ? 'selected' : '' ?>>
                            <?= $prov['proveedor_nombre'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="mb-3">
                <label for="producto_precio_compra" class="form-label">Precio de Compra *</label>
                <input type="number" step="0.01" class="form-control" id="producto_precio_compra" name="producto_precio_compra" 
                       value="<?= $data['producto_precio_compra'] ?? '' ?>" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="producto_precio_venta" class="form-label">Precio de Venta *</label>
                <input type="number" step="0.01" class="form-control" id="producto_precio_venta" name="producto_precio_venta" 
                       value="<?= $data['producto_precio_venta'] ?? '' ?>" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="producto_stock" class="form-label">Stock Actual *</label>
                <input type="number" class="form-control" id="producto_stock" name="producto_stock" 
                       value="<?= $data['producto_stock'] ?? 0 ?>" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="producto_stock_minimo" class="form-label">Stock Mínimo *</label>
                <input type="number" class="form-control" id="producto_stock_minimo" name="producto_stock_minimo" 
                       value="<?= $data['producto_stock_minimo'] ?? 0 ?>" required>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="producto_foto" class="form-label">Foto del Producto</label>
        <input type="file" class="form-control" id="producto_foto" name="producto_foto" accept="image/*">
        <?php if(!empty($data['producto_foto'])): ?>
            <img src="data:image/png;base64,<?= base64_encode($data['producto_foto']) ?>" width="100" class="mt-2">
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
        <a href="<?= URLROOT ?>/productos" class="btn btn-secondary"><i class="fa fa-times"></i> Cancelar</a>
    </div>
</form>

<?php require_once APPROOT . '/views/layouts/footer.inc.php'; ?>
