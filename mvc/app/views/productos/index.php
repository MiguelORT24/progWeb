<?php require_once APPROOT . '/views/layouts/header.inc.php'; ?>

<h2><i class="fa fa-box"></i> Productos</h2>

<?php if(!estaLogueado()){ ?>
    <div class="alert alert-info">Usted no está autorizado!!!</div>
<?php } else { ?>

<div class="row mb-3">
    <div class="col-10">
        <h4>Lista de Productos</h4>
    </div>
    <div class="col-2">
        <a href="<?= URLROOT ?>/productos/create" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Agregar
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Proveedor</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
                <th>Stock</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data as $producto): ?>
            <tr>
                <td><?= $producto['producto_codigo'] ?></td>
                <td><?= $producto['producto_nombre'] ?></td>
                <td><?= $producto['categoria_nombre'] ?? 'Sin categoría' ?></td>
                <td><?= $producto['proveedor_nombre'] ?? 'Sin proveedor' ?></td>
                <td>$<?= number_format($producto['producto_precio_compra'], 2) ?></td>
                <td>$<?= number_format($producto['producto_precio_venta'], 2) ?></td>
                <td>
                    <?php if($producto['nivel_stock'] == 'BAJO'): ?>
                        <span class="badge bg-danger"><?= $producto['producto_stock'] ?></span>
                    <?php elseif($producto['nivel_stock'] == 'MEDIO'): ?>
                        <span class="badge bg-warning"><?= $producto['producto_stock'] ?></span>
                    <?php else: ?>
                        <span class="badge bg-success"><?= $producto['producto_stock'] ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($producto['nivel_stock'] == 'BAJO'): ?>
                        <span class="badge bg-danger">Stock Bajo</span>
                    <?php elseif($producto['nivel_stock'] == 'MEDIO'): ?>
                        <span class="badge bg-warning">Stock Medio</span>
                    <?php else: ?>
                        <span class="badge bg-success">Normal</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?= URLROOT ?>/productos/edit/<?= $producto['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="<?= URLROOT ?>/productos/destroy/<?= $producto['id'] ?>" class="btn btn-danger btn-sm" 
                       onclick="return confirm('¿Está seguro de eliminar este producto?')">
                        <i class="fa fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php } ?>

<?php require_once APPROOT . '/views/layouts/footer.inc.php'; ?>
