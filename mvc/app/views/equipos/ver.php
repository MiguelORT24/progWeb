<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-camera"></i> Detalles del Equipo</h2>
        <div>
            <a href="<?php echo URLROOT; ?>/equipos" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <a href="<?php echo URLROOT; ?>/equipos/editar/<?php echo $data['equipo']['id_equipo']; ?>" 
               class="btn btn-warning <?php echo !puedeGestionarMaestros() ? 'disabled' : ''; ?>"
               <?php echo !puedeGestionarMaestros() ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
                <i class="bi bi-pencil"></i> Editar
            </a>
        </div>
    </div>

    <!-- Información del Equipo -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Información General</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong><i class="bi bi-upc-scan"></i> SKU:</strong>
                    <span class="badge bg-secondary ms-2"><?php echo $data['equipo']['sku']; ?></span>
                </div>
                <div class="col-md-6 mb-3">
                    <strong><i class="bi bi-tag"></i> Tipo:</strong>
                    <?php if($data['equipo']['tipo'] == 'CAMARA'): ?>
                        <span class="badge bg-info ms-2"><i class="bi bi-camera-video"></i> Cámara</span>
                    <?php elseif($data['equipo']['tipo'] == 'SENSOR'): ?>
                        <span class="badge bg-success ms-2"><i class="bi bi-wifi"></i> Sensor</span>
                    <?php else: ?>
                        <span class="badge bg-secondary ms-2"><?php echo $data['equipo']['tipo']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong><i class="bi bi-bookmark"></i> Marca:</strong>
                    <span class="ms-2"><?php echo $data['equipo']['marca_nombre'] ?? 'Sin marca'; ?></span>
                </div>
                <div class="col-md-6 mb-3">
                    <strong><i class="bi bi-folder"></i> Categoría:</strong>
                    <span class="ms-2"><?php echo $data['equipo']['categoria_nombre'] ?? 'Sin categoría'; ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-3">
                    <strong><i class="bi bi-file-text"></i> Descripción:</strong>
                    <p class="mt-2 mb-0"><?php echo $data['equipo']['descripcion']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Total -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-box-seam"></i> Stock Total</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="stat-box">
                        <h3 class="text-success"><?php echo $data['stock_total']['total_disponible'] ?? 0; ?></h3>
                        <p class="text-muted mb-0">Disponible</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <h3 class="text-warning"><?php echo $data['stock_total']['total_reservado'] ?? 0; ?></h3>
                        <p class="text-muted mb-0">Reservado</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <h3 class="text-primary"><?php echo ($data['stock_total']['total_disponible'] ?? 0) + ($data['stock_total']['total_reservado'] ?? 0); ?></h3>
                        <p class="text-muted mb-0">Total</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-lightning"></i> Acciones Rápidas</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <a href="<?php echo URLROOT; ?>/inventario/verLotes/<?php echo $data['equipo']['id_equipo']; ?>" class="btn btn-outline-primary w-100">
                        <i class="bi bi-boxes"></i> Ver Todos los Lotes
                    </a>
                </div>
                <div class="col-md-6 mb-3">
                    <a href="<?php echo URLROOT; ?>/inventario/crear?equipo=<?php echo $data['equipo']['id_equipo']; ?>" 
                       class="btn btn-outline-success w-100 <?php echo !puedeCrear() ? 'disabled' : ''; ?>"
                       <?php echo !puedeCrear() ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
                        <i class="bi bi-plus-circle"></i> Crear Nuevo Lote
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-box {
        padding: 20px;
        border-radius: 8px;
        background-color: #f8f9fa;
        margin: 10px 0;
    }
    .stat-box h3 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
</style>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
