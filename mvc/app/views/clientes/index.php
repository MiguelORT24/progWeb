<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Clientes</h1>
        <!-- <a href="< ?php echo URLROOT; ?>/clientes/crear" class="btn btn-primary">Nuevo Cliente</a> -->
    </div>

    <?php flash('mensaje'); ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['clientes'] as $cliente): ?>
                        <tr>
                            <td><?php echo $cliente['id_cliente']; ?></td>
                            <td><?php echo $cliente['nombre']; ?></td>
                            <td><?php echo $cliente['telefono']; ?></td>
                            <td><?php echo $cliente['email']; ?></td>
                            <td>
                                <a href="<?php echo URLROOT; ?>/clientes/editar/<?php echo $cliente['id_cliente']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <a href="<?php echo URLROOT; ?>/clientes/eliminar/<?php echo $cliente['id_cliente']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?');"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
