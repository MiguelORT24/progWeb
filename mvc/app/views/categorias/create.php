<?php require_once APPROOT . '/views/layouts/header.inc.php'; ?>

<h2><i class="fa fa-tags"></i> <?= $data['accion'] ?? 'Crear' ?> Categoría</h2>

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
        <label for="categoria_nombre" class="form-label">Nombre de la Categoría *</label>
        <input type="text" class="form-control" id="categoria_nombre" name="categoria_nombre" 
               value="<?= $data['categoria_nombre'] ?? '' ?>" required>
    </div>

    <div class="mb-3">
        <label for="categoria_descripcion" class="form-label">Descripción</label>
        <textarea class="form-control" id="categoria_descripcion" name="categoria_descripcion" rows="3"><?= $data['categoria_descripcion'] ?? '' ?></textarea>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
        <a href="<?= URLROOT ?>/categorias" class="btn btn-secondary"><i class="fa fa-times"></i> Cancelar</a>
    </div>
</form>

<?php require_once APPROOT . '/views/layouts/footer.inc.php'; ?>
