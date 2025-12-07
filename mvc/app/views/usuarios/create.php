<?php require_once APPROOT . '/views/layouts/header.inc.php'; ?>

<?php
    // Lógica para determinar la acción y la URL (del estilo visual preferido)
    $esEdicion = (isset($data['accion']) && $data['accion'] == 'editar');
    $titulo = $esEdicion ? 'Editar Usuario' : 'Agregar Usuario';
    $accionUrl = $esEdicion ? (URLROOT . '/usuarios/edit/' . $data['id']) : (URLROOT . '/usuarios/create');
?>

<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-md-10 col-lg-8 mx-auto">

            <!-- Título del estilo visual -->
            <h2><?php echo $titulo; ?></h2>
            <hr>
            
            <!-- Bloque de error de la nueva lógica -->
            <div class="alert alert-danger <?= (!empty($data['error']) ? 'd-block' : 'd-none'); ?> ">
                <?= (!empty($data['error']) ? implode(', ',$data['error']) : ''); ?>
            </div>

            <!-- Layout del estilo visual (row g-3) -->
            <form class="row g-3" action="<?php echo $accionUrl; ?>" method="POST" enctype="multipart/form-data">
                
                <!-- ID de la nueva lógica -->
                <input type="hidden" name="id" value="<?= htmlspecialchars($data['id'] ?? '') ?>">

                <!-- Campo Nombre: Layout visual, lógica nueva (name, id) -->
                <div class="col-md-6">
                    <label for="usuario_nombre" class="form-label">Nombre: <sup>*</sup></label>
                    <input type="text" 
                            class="form-control" 
                            id="usuario_nombre" 
                            name="usuario_nombre" 
                            value="<?= htmlspecialchars($data['usuario_nombre'] ?? '') ?>" 
                            placeholder="Ingrese el nombre"
                            required>
                </div>

                <!-- Campo Correo: Layout visual, lógica nueva (name, id) -->
                <div class="col-md-6">
                    <label for="usuario_email" class="form-label">Correo: <sup>*</sup></label>
                    <input type="email" 
                            class="form-control" 
                            id="usuario_email" 
                            name="usuario_email" 
                            value="<?= htmlspecialchars($data['usuario_email'] ?? '') ?>" 
                            placeholder="Ingrese el correo electrónico"
                            required>
                </div>

                <!-- Campo Password: Layout visual, lógica nueva (name, id, required) -->
                <div class="col-md-6">
                    <label for="usuario_password" class="form-label">Password: <sup>*</sup></label>
                    <input type="password" 
                            class="form-control" 
                            id="usuario_password" 
                            name="usuario_password" 
                            value="" 
                            placeholder="Ingrese la contraseña"
                            aria-describedby="passwordHelp"
                            <?= ($data['accion'] ?? '')== 'editar' ? '' : 'required' ?>> <!-- Lógica required de la nueva versión -->
                    <?php if ($esEdicion): ?>
                        <div id="passwordHelp" class="form-text">
                            Deje en blanco para no cambiar la contraseña actual.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Campo Confirmar Password: Layout visual, lógica nueva (name, id, required) -->
                <div class="col-md-6">
                    <label for="conf_password" class="form-label">Confirmar Password: <sup>*</sup></label>
                    <input type="password" 
                            class="form-control" 
                            id="conf_password" 
                            name="conf_password" 
                            value="" 
                            placeholder="Confirme la contraseña"
                            <?= ($data['accion'] ?? '')== 'editar' ? '' : 'required' ?> > <!-- Lógica required de la nueva versión -->
                </div>

                <!-- Campo Nivel: Layout visual, lógica nueva (name, id, options) -->
                <div class="col-md-6">
                    <label for="usuario_nivel" class="form-label">Nivel: <sup>*</sup></label>
                    <select class="form-select" 
                            id="usuario_nivel" 
                            name="usuario_nivel" 
                            required>
                        <!-- Opciones de la nueva lógica -->
                        <option value="">Seleccione un nivel</option>
                        <option value="A" <?= ($data['usuario_nivel'] ?? '')== 'A' ? 'selected' : '' ?>>Administrador</option>
                        <option value="G" <?= ($data['usuario_nivel'] ?? '')== 'G' ? 'selected' : '' ?>>Gerente</option>
                        <option value="O" <?= ($data['usuario_nivel'] ?? '')== 'O' ? 'selected' : '' ?>>Operador</option>
                    </select>
                </div>

                <!-- Campo Foto: Layout visual, lógica nueva (name, id, accept) -->
                <div class="col-md-12">
                    <label for="usuario_foto" class="form-label">Foto:</label>
                    
                    <!-- Lógica para mostrar foto actual (del estilo visual) -->
                    <?php if ($esEdicion && !empty($data['foto'])): ?>
                        <div class="mb-2">
                            <p class="mb-1">Foto Actual:</p>
                            <img src="<?php echo URLROOT; ?>/img/uploads/<?php echo $data['foto']; ?>" 
                                    alt="Foto de <?php echo $data['usuario_nombre']; ?>" 
                                    width="100" 
                                    class="img-thumbnail">
                        </div>
                    <?php endif; ?>

                    <input class="form-control" 
                            type="file" 
                            id="usuario_foto" 
                            name="usuario_foto" 
                            accept="image/*"> <!-- Accept de la nueva lógica -->
                </div>

                <!-- Botón: Layout visual, lógica nueva (texto) -->
                <div class="col-12 mt-4">
                    <button class="btn btn-primary" type="submit">
                        <?= ($data['accion'] ?? '') == 'editar' ? 'Actualizar' : 'Guardar' ?>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.inc.php'; ?>