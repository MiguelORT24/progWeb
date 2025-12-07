<?php require_once APPROOT . '/views/layouts/header.inc.php'; ?>

<?php if(!estaLogueado()){ ?>
    <div class="alert alert-info">Debe iniciar sesión para acceder al sistema de inventarios</div>
<?php } else { ?>

<h2 class="mb-4"><i class="fa fa-dashboard"></i> Dashboard de Inventarios</h2>

<!-- Estadísticas Principales -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title"><i class="fa fa-boxes"></i> Total Productos</h5>
                <h2><?= number_format($data['valorInventario']['total_productos'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title"><i class="fa fa-cubes"></i> Total Unidades</h5>
                <h2><?= number_format($data['valorInventario']['total_unidades'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title"><i class="fa fa-dollar-sign"></i> Valor Compra</h5>
                <h2>$<?= number_format($data['valorInventario']['valor_compra'] ?? 0, 2) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title"><i class="fa fa-money-bill"></i> Valor Venta</h5>
                <h2>$<?= number_format($data['valorInventario']['valor_venta'] ?? 0, 2) ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Productos con Stock Bajo -->
<?php if(!empty($data['productosStockBajo'])){ ?>
<div class="alert alert-danger">
    <h4><i class="fa fa-exclamation-triangle"></i> Alerta: Productos con Stock Bajo</h4>
</div>

<div class="card mb-4">
    <div class="card-header bg-danger text-white">
        <h5><i class="fa fa-warning"></i> Productos con Stock Bajo</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Stock Actual</th>
                        <th>Stock Mínimo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['productosStockBajo'] as $producto): ?>
                    <tr>
                        <td><?= $producto['producto_codigo'] ?></td>
                        <td><?= $producto['producto_nombre'] ?></td>
                        <td><?= $producto['categoria_nombre'] ?? 'Sin categoría' ?></td>
                        <td><span class="badge bg-danger"><?= $producto['producto_stock'] ?></span></td>
                        <td><?= $producto['producto_stock_minimo'] ?></td>
                        <td>
                            <a href="<?= URLROOT ?>/movimientos/entrada" class="btn btn-sm btn-success">
                                <i class="fa fa-plus"></i> Reabastecer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>

<!-- Últimos Movimientos -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5><i class="fa fa-exchange-alt"></i> Últimos Movimientos</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Usuario</th>
                        <th>Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['ultimosMovimientos'])): ?>
                        <?php foreach($data['ultimosMovimientos'] as $mov): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($mov['created_at'])) ?></td>
                            <td><?= $mov['producto_nombre'] ?></td>
                            <td>
                                <?php if($mov['movimiento_tipo'] == 'entrada'): ?>
                                    <span class="badge bg-success">Entrada</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Salida</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $mov['movimiento_cantidad'] ?></td>
                            <td><?= $mov['usuario_nombre'] ?? 'Sistema' ?></td>
                            <td><?= $mov['movimiento_motivo'] ?? '-' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay movimientos registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Accesos Rápidos -->
<div class="row">
    <div class="col-md-3">
        <a href="<?= URLROOT ?>/productos/create" class="btn btn-primary btn-lg w-100 mb-3">
            <i class="fa fa-plus-circle"></i><br>Nuevo Producto
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= URLROOT ?>/movimientos/entrada" class="btn btn-success btn-lg w-100 mb-3">
            <i class="fa fa-arrow-down"></i><br>Registrar Entrada
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= URLROOT ?>/movimientos/salida" class="btn btn-warning btn-lg w-100 mb-3">
            <i class="fa fa-arrow-up"></i><br>Registrar Salida
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= URLROOT ?>/productos" class="btn btn-info btn-lg w-100 mb-3">
            <i class="fa fa-list"></i><br>Ver Inventario
        </a>
    </div>
</div>

<?php } ?>

<?php require_once APPROOT . '/views/layouts/footer.inc.php'; ?>