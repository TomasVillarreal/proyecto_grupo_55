<?php /*Se toma el rol del usuario en sesion para determinar si se le muestran los botones correspondientes
Ademas de la informaciond el usuario que se muestra en el sidebar*/
$esResponsable = session()->get('nombre_rol') === 'Responsable';
$nombreCompleto = session()->get('nombre_completo');
$dniUsuario = session()->get('dni_usuario');
?>
<div class="sidebar-layout" id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle">
            <i class='bi bi-list' id="header-toggle"></i>
        </div>
    </header>

    <div class="sidebar-container">
        <div class="l-navbar" id="nav-bar">
            <nav class="nav">
                <div> 
                    <a href="<?= site_url('/') ?>" class="nav_logo">
                        <i class='bi bi-hospital nav_logo-icon'></i>
                        <span class="nav_logo-name">Clinicks</span>
                    </a>

                    <div class="nav_list"> 
                        <a href="<?= site_url('/') ?>" class="nav_link active">
                            <i class='bi bi-plus-circle nav_icon'></i>
                            <span class="nav_name">Agregar medicamento</span>
                        </a> 
                        <a href="<?= site_url('/update') ?>" class="nav_link">
                            <i class='bi bi-pencil-square nav_icon'></i>
                            <span class="nav_name">Modificar medicamento</span>
                        </a> 
                        <a href="<?= site_url('/delete') ?>" class="nav_link">
                            <i class='bi bi-file-x nav_icon'></i>
                            <span class="nav_name">Eliminar medicamento</span>
                        </a>
                        <?php //Se verifica el rol del usuario para mostrar el boton de crear usuario
                            if($esResponsable): ?>
                            <a href="<?= site_url('/access/registrar') ?>" class="nav_link">
                                <i class="bi bi-person-plus nav_icon"></i>
                                <span class="nav_name">Crear usuario</span>
                            </a>
                        <?php endif; ?>
                        <a href="<?= site_url('/access/logout') ?>" class="nav_link" style="margin-top: auto; margin-bottom: 2rem;">
                            <i class='bi bi-box-arrow-left nav_icon'></i>
                            <span class="nav_name">Cerrar sesión</span>
                        </a>
                        <div class="sidebar-user">
                            <i class="bi bi-person-circle"></i>

                            <div class="sidebar-user-info">
                                <span><?= session()->get('nombre_completo') ?></span>
                                <small><?= session()->get('nombre_rol') ?></small>
                            </div>
                        </div>
                    </div>
                </div> 
            </nav>
        </div>
    </div>
</div>