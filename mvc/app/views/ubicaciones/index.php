<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Ubicaciones</h1>
        <a href="<?php echo URLROOT; ?>/ubicaciones/crear" 
           class="btn btn-primary <?php echo !puedeGestionarMaestros() ? 'disabled' : ''; ?>"
           <?php echo !puedeGestionarMaestros() ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>Nueva Ubicación</a>
    </div>

    <?php flash('mensaje'); ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['ubicaciones'] as $ubicacion): ?>
                        <tr>
                            <td><?php echo $ubicacion['id_ubicacion']; ?></td>
                            <td><?php echo $ubicacion['nombre']; ?></td>
                            <td>
                                <a href="<?php echo URLROOT; ?>/ubicaciones/editar/<?php echo $ubicacion['id_ubicacion']; ?>" 
                                   class="btn btn-sm btn-warning <?php echo !puedeGestionarMaestros() ? 'disabled' : ''; ?>"
                                   <?php echo !puedeGestionarMaestros() ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?php echo URLROOT; ?>/ubicaciones/eliminar/<?php echo $ubicacion['id_ubicacion']; ?>" 
                                   class="btn btn-sm btn-danger <?php echo !puedeGestionarMaestros() ? 'disabled' : ''; ?>" 
                                   onclick="return confirm('¿Eliminar?');"
                                   <?php echo !puedeGestionarMaestros() ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
