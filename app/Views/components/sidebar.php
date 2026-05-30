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
                    </div>
                </div> 
            </nav>
        </div>
    </div>
</div>