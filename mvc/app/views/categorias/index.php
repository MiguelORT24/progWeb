<?php require_once APPROOT . '/views/layouts/header.inc.php'; ?>

<h2><i class="fa fa-tags"></i> Categorías</h2>

<?php if(!estaLogueado()){ ?>
    <div class="alert alert-info">Usted no está autorizado!!!</div>
<?php } else { ?>

<div class="row mb-3">
    <div class="col-10"><h4>Lista de Categorías</h4></div>
    <div class="col-2">
        <a href="<?= URLROOT ?>/categorias/create" class="btn btn-primary btn-sm">
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
                <th>Descripción</th>
                <th>Total Productos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data as $categoria): ?>
            <tr>
                <td><?= $categoria['id'] ?></td>
                <td><?= $categoria['categoria_nombre'] ?></td>
                <td><?= $categoria['categoria_descripcion'] ?? '-' ?></td>
                <td><span class="badge bg-info"><?= $categoria['total_productos'] ?></span></td>
                <td>
                    <a href="<?= URLROOT ?>/categorias/edit/<?= $categoria['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="<?= URLROOT ?>/categorias/destroy/<?= $categoria['id'] ?>" class="btn btn-danger btn-sm" 
                       onclick="return confirm('¿Está seguro de eliminar esta categoría?')">
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
