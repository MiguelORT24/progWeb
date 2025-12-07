<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-camera"></i> Catálogo de Equipos</h1>
        <a href="<?php echo URLROOT; ?>/equipos/crear" 
           class="btn btn-success <?php echo !puedeGestionarMaestros() ? 'disabled' : ''; ?>"
           <?php echo !puedeGestionarMaestros() ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
            <i class="bi bi-plus-circle"></i> Nuevo Equipo
        </a>
    </div>

    <?php flash('mensaje'); ?>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="<?php echo URLROOT; ?>/equipos" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por SKU o descripción..." value="<?php echo $data['termino']; ?>">
                </div>
                <div class="col-md-3">
                    <select name="marca" class="form-select">
                        <option value="">Todas las Marcas</option>
                        <?php foreach($data['marcas'] as $marca): ?>
                            <option value="<?php echo $marca['id_marca']; ?>" <?php echo ($data['filtros']['id_marca'] == $marca['id_marca']) ? 'selected' : ''; ?>>
                                <?php echo $marca['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="categoria" class="form-select">
                        <option value="">Todas las Categorías</option>
                        <?php foreach($data['categorias'] as $cat): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>" <?php echo ($data['filtros']['id_categoria'] == $cat['id_categoria']) ? 'selected' : ''; ?>>
                                <?php echo $cat['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Grid de Equipos -->
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if(empty($data['equipos'])): ?>
            <div class="col-12 text-center py-5">
                <h4 class="text-muted">No se encontraron equipos</h4>
            </div>
        <?php else: ?>
            <?php foreach($data['equipos'] as $equipo): ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0"><?php echo $equipo['sku']; ?></h5>
                                <span class="badge bg-info"><?php echo $equipo['tipo']; ?></span>
                            </div>
                            <p class="card-text text-muted small mb-2">
                                <?php echo $equipo['marca_nombre']; ?> | <?php echo $equipo['categoria_nombre']; ?>
                            </p>
                            <p class="card-text"><?php echo $equipo['descripcion']; ?></p>
                        </div>
                        <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center">
                            <small class="text-muted">ID: <?php echo $equipo['id_equipo']; ?></small>
                            <div class="btn-group">
                                <a href="<?php echo URLROOT; ?>/equipos/ver/<?php echo $equipo['id_equipo']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo URLROOT; ?>/equipos/editar/<?php echo $equipo['id_equipo']; ?>" 
                                   class="btn btn-sm btn-outline-warning <?php echo !puedeGestionarMaestros() ? 'disabled' : ''; ?>"
                                   <?php echo !puedeGestionarMaestros() ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
