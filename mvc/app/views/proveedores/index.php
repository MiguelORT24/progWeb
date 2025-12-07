<?php require_once APPROOT . '/views/layouts/header.inc.php'; ?>

<h2><i class="fa fa-truck"></i> Proveedores</h2>

<?php if(!estaLogueado()){ ?>
    <div class="alert alert-info">Usted no está autorizado!!!</div>
<?php } else { ?>

<div class="row mb-3">
    <div class="col-10"><h4>Lista de Proveedores</h4></div>
    <div class="col-2">
        <a href="<?= URLROOT ?>/proveedores/create" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Agregar
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Total Productos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data as $proveedor): ?>
            <tr>
                <td><?= $proveedor['id'] ?></td>
                <td><?= $proveedor['proveedor_nombre'] ?></td>
                <td><?= $proveedor['proveedor_contacto'] ?? '-' ?></td>
                <td><?= $proveedor['proveedor_telefono'] ?? '-' ?></td>
                <td><?= $proveedor['proveedor_email'] ?? '-' ?></td>
                <td><span class="badge bg-info"><?= $proveedor['total_productos'] ?></span></td>
                <td>
                    <a href="<?= URLROOT ?>/proveedores/edit/<?= $proveedor['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="<?= URLROOT ?>/proveedores/destroy/<?= $proveedor['id'] ?>" class="btn btn-danger btn-sm" 
                       onclick="return confirm('¿Está seguro de eliminar este proveedor?')">
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
