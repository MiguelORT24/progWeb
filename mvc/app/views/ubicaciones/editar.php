<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Editar Ubicación</div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/ubicaciones/editar/<?php echo $data['ubicacion']['id_ubicacion']; ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="<?php echo $data['ubicacion']['nombre']; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                        <a href="<?php echo URLROOT; ?>/ubicaciones" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
