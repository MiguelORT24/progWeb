<?php require_once APPROOT . '/views/layouts/header.inc.php'; ?>
    <h2> Usuarios </h2>

    <?php if(!estaLogueado()){?>
        <div class="alert alert-info"> Usted no está autorizado!!!</div>
    <?php }else { ?>
    
    

    <div class="row mb-3" >
        <!-- <div class="col 11"></div> -->
        <div class="col-3">Acciones adicionales: </div>
        <div class="col-8">Exportar a Json: <a href=" <?= URLROOT ?>/usuarios/json" class= "btn btn-primary btn-sm"><i class=" fa fa-file-text" title= "Exportar"></i></a>
                           Imprimir lista: <a href=" <?= URLROOT ?>/usuarios/imprimir" class= "btn btn-success btn-sm"><i class=" fa fa-file-pdf" title= "Imprimir PDF"></i></a>
    </div>
        <div class="col-1"><a href=" <?= URLROOT ?>/usuarios/create" class= "btn btn-primary btn-sm"><i class=" fa fa-user-plus" title= "Agregar"></i></a> </div>
    </div>
    

    <div class="table-responsive">

        <table
            class="table table-primary table-hover table-striped"
        >
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Correro</th>
                    <th scope="col">Nivel</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data as $usuario):
                    extract($usuario);
                    // d($usuario);

                    // $usuario -> usuario_nombre === $usuario ['usuario_nombre'];
                    ?>
                <!-- <tr class="">
                    <td scope="row">< ?php $usuario ['id'] ?></td>
                    <td>< ?= $usuario ['usuario_nombre'] ?></td>
                    <td>< ?= $usuario ['usuario_email'] ?></td>
                    <td>< ?= $usuario ['usuario_nivel'] ?></td>
                    <td>< ?= $usuario ['usuario_foto'] ?></td>
                    <td>Editar Borrar</td>
                    
                </tr> -->

                <tr class="">
                    <td scope="row"><?= $id ?></td>
                    <td><?= $usuario_nombre ?></td>
                    <td><?= $usuario_email ?></td>
                    <td><?= $usuario_nivel ?></td>

                    <?php $foto = (!empty ($usuario_foto)) ? "data:image/png;base64," . base64_encode($usuario_foto) : null?>
                    <!-- <td><img src="data:image/png;base64,< ?= base64_encode($usuario_foto) ?>" width="30px"> </img></td> -->
                    <td><img src="<?= $foto ?>" width="30px" alt=""></td>

                    <td><a href=" <?= URLROOT ?>/usuarios/edit/<?= $id ?>" class="btn btn-warning btn-sm">
                        <i class=" fa fa-edit" title= "Editar"></i>
                    </a> 
                    <a href=" <?= URLROOT ?>/usuarios/destroy/<?= $id ?>" class="btn btn-danger btn-sm">
                        <i class=" fa fa-trash" title= "Eliminar"></i>
                    </a>
                    </td>
                    
                </tr>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php } ?>

    <!-- //Tabla de usuarios -->
<?php require_once APPROOT . '/views/layouts/footer.inc.php';?>
