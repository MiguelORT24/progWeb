<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-clock-history"></i> Historial del Lote #<?php echo $data['lote']['id_lote']; ?></h3>
        <a href="<?php echo URLROOT; ?>/inventario" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong>Equipo:</strong> <?php echo $data['lote']['sku']; ?>
                </div>
                <div class="col-md-4">
                    <strong>Ubicación Actual:</strong> <?php echo $data['lote']['ubicacion_nombre']; ?>
                </div>
                <div class="col-md-4">
                    <strong>Estado Actual:</strong> 
                    <span class="badge bg-primary"><?php echo $data['lote']['estado']; ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Tipo Movimiento</th>
                            <th>Cantidad</th>
                            <th>Usuario</th>
                            <th>Referencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['movimientos'])): ?>
                            <tr>
                                <td colspan="5" class="text-center">No hay movimientos registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($data['movimientos'] as $mov): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($mov['fecha_hora'])); ?></td>
                                    <td>
                                        <?php 
                                            $tipo = strtolower($mov['tipo']);
                                            if ($tipo === 'entrada') {
                                                $badge = 'bg-success';
                                            } elseif ($tipo === 'salida') {
                                                $badge = 'bg-danger';
                                            } elseif ($tipo === 'edicion') {
                                                $badge = 'bg-info text-dark';
                                            } else {
                                                $badge = 'bg-secondary';
                                            }
                                        ?>
                                        <span class="badge <?php echo $badge; ?>">
                                            <?php echo ucfirst($mov['tipo']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $mov['cantidad']; ?></td>
                                    <td><?php echo $mov['usuario_nombre'] ?? 'Sistema'; ?></td>
                                    <td><?php echo $mov['motivo'] ?? '-'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
