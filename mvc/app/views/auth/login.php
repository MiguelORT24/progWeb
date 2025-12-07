<?php require_once APPROOT . '/views/layouts/header.inc.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center"> <!-- Justificado -->
        <div class="col-12 col-md-8 col-lg-5 col-xl-4"> <!-- Tamaño Tar -->
            <div class="card border-0 shadow-sm"> <!-- Tarjeta -->
                <div class="card-body p-4 p-md-5"> <!-- Espaciado -->
                    <div class="text-center mb-4"> <!-- Centrado -->

                    <div class="alert alert-warning <?= (!empty($data['error']) ? 'd-block' : 'd-none'); ?> ">
                        <?= (!empty($data['error']) ? implode(', ',$data['error']) : ''); ?>
                    </div>

                        <h2 class="fw-bold">Iniciar sesión</h2>
                    </div>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="usuario_email" class="form-label">Correo</label>
                            <input
                                type="email"
                                name="usuario_email"
                                id="usuario_email"
                                class="form-control form-control-lg"
                                placeholder="correo@ejemplo.com"
                                required
                            />
                        </div>
                        <div class="mb-4">
                            <label for="usuario_password" class="form-label">Contraseña</label>
                            <input
                                type="password"
                                name="usuario_password"
                                id="usuario_password"
                                class="form-control form-control-lg"
                                placeholder="*"
                                required
                            />
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-info btn-lg">
                                Entrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.inc.php'; ?>