<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Inicio</h1>
            <p class="text-muted">Bienvenido al Sistema de Gestión de Inventario</p>
        </div>
        <a href="<?php echo URLROOT; ?>/reportes/diario" class="btn btn-danger">
            <i class="bi bi-file-pdf"></i> Generar Reporte Diario
        </a>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row">
        <div class="col-md-3">
            <div class="stat-card bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Total Equipos</h6>
                        <h2 class="mb-0"><?php echo $data['total_equipos']; ?></h2>
                    </div>
                    <i class="bi bi-camera stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Lotes Disponibles</h6>
                        <h2 class="mb-0"><?php echo $data['lotes_disponibles']; ?></h2>
                    </div>
                    <i class="bi bi-check-circle stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card bg-warning text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Stock Bajo</h6>
                        <h2 class="mb-0"><?php echo $data['stock_bajo']; ?></h2>
                    </div>
                    <i class="bi bi-exclamation-triangle stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card bg-info text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Órdenes Pendientes</h6>
                        <h2 class="mb-0"><?php echo $data['ordenes_pendientes']; ?></h2>
                    </div>
                    <i class="bi bi-clipboard-check stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos Rápidos -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Accesos Rápidos</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo URLROOT; ?>/inventario" class="btn btn-outline-primary w-100">
                                <i class="bi bi-boxes"></i><br>Ver Inventario
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo URLROOT; ?>/inventario/crear" class="btn btn-outline-success w-100">
                                <i class="bi bi-plus-circle"></i><br>Nuevo Lote
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo URLROOT; ?>/compras/crear" class="btn btn-outline-info w-100">
                                <i class="bi bi-cart-plus"></i><br>Nueva Compra
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo URLROOT; ?>/ordenes/crear" class="btn btn-outline-warning w-100">
                                <i class="bi bi-clipboard-plus"></i><br>Nueva Orden
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }
</style>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
