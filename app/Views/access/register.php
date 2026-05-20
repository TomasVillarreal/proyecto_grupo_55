<div class="caja-cb">
  <form method="post" action="<?= base_url('#') ?>">


    <div class="mb-4">
      <h4 class="border-bottom pb-2 mb-3">Creación de usuarios</h4>

      <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
          <label for="dniUserCreate" class="form-label">DNI</label>
          <input id="dniUserCreate" name="dni_usuario" type="text" class="form-control">
        </div>   

        <div class="col-12 col-md-6">
          <label for="userRoleCreate" class="form-label">Rol</label>
          <select id="userRoleCreate" name="id_role" class="form-select" required>
          <option value="-1" selected disabled hidden>Seleccione...</option>
              <?php /*foreach ($roles as $id => $nombre_rol): ?>
                <option value="<?= $id ?>" data-nombre="<?= esc($nombre_rol) ?>">
                  <?= esc($nombre_rol) ?>
                </option>
              <?php endforeach; */?></select>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
          <label for="nameUserCreate" class="form-label">Nombre</label>
          <input id="nameUserCreate" name="nombre_usuario" type="text" class="form-control">
        </div>

        <div class="col-12 col-md-6">
          <label for="lastnameUserCreate" class="form-label">Apellido</label>
          <input id="lastnameUserCreate" name="apellido_usuario" type="text" class="form-control">
        </div>     
      </div>

      <div class="row g-3 mb-4">

        <div class="col-12 col-md-6">
          <label for="emailUserCreate" class="form-label">Email</label>
          <input id="emailUserCreate" name="email_usuario" type="text" class="form-control">
        </div>
        
        <div class="col-12 col-md-6">
          <label for="passwordUserCreate" class="form-label">Contraseña</label>
          <input id="passwordUserCreate" name="contrasena_usuario" type="text" class="form-control">
        </div> 
      </div>
    </div>


    <div class="text-center mt-4">
      <button type="submit" class="btn btn-primary px-4">Crear</button>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert"">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

  </form>
</div>