<!DOCTYPE html>
<html lang="en" class="h-100" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Astro v5.13.2">
    <title>Sistema de Inventarios</title>
    <link href="<?= URLROOT ?>/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= URLROOT ?>/assets/css/style.css" rel="stylesheet">
    <link href="<?= URLROOT ?>/assets/fontawesome/css/all.min.css" rel="stylesheet">
    <meta name="theme-color" content="#712cf9">
    <link href="sticky-footer-navbar.css" rel="stylesheet">
</head>

<body class="d-flex flex-column h-100">
    <header> <!-- Fixed navbar -->
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <div class="container-fluid"> 
                <a class="navbar-brand" href="<?= URLROOT ?>"><i class="fa fa-boxes"></i> Sistema de Inventarios</a> 
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation"> 
                    <span class="navbar-toggler-icon"></span> 
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav me-auto mb-2 mb-md-0">
                        <li class="nav-item"> <a class="nav-link" href="<?= URLROOT ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                        <li class="nav-item"> <a class="nav-link" href="<?= URLROOT ?>/inventario"><i class="fa fa-box"></i> Inventario</a> </li>
                        <li class="nav-item"> <a class="nav-link" href="<?= URLROOT ?>/equipos"><i class="fa fa-boxes"></i> Equipos</a> </li>
                        <li class="nav-item"> <a class="nav-link" href="<?= URLROOT ?>/marcas"><i class="fa fa-industry"></i> Marcas</a> </li>
                        <li class="nav-item"> <a class="nav-link" href="<?= URLROOT ?>/ubicaciones"><i class="fa fa-map-marker-alt"></i> Ubicaciones</a> </li>
                        <li class="nav-item"> <a class="nav-link" href="<?= URLROOT ?>/movimientos"><i class="fa fa-exchange-alt"></i> Salidas</a> </li>
                    </ul>
                            
                    <!-- logueo -->
                    <?php if(!estaLogueado()) { ?>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"> 
                            <a class="nav-link active" href="<?= URLROOT ?>/usuarios/login">Login</a> 
                        </li>
                    </ul>
                    <!-- fin logueo  -->
                    <?php } else { ?>
                        <div class="dropdown">
                            <a class="btn btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-user"></i> <?= $_SESSION['usuario_nombre']?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= URLROOT?>/usuarios/logout"><i class="fa fa-sign-out-alt"></i> Salir</a></li>
                            </ul>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </nav>
    </header> <!-- Begin page content -->
    <main class="flex-shrink-0">
        <div class="container">
