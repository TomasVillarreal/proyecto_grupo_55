<?php
//Para manejo de los errores
$errores = session()->getFlashdata('errores') ?? [];
?>
<link rel="stylesheet" href="<?= base_url('assets/css/styles.css?v=' . time()) ?>">
<div class="caja-cb">
  <form method="post" action="<?= base_url('/access/crear') ?>">

    <div class="mb-4">
      <h4 class="border-bottom pb-2 mb-3">Creación de usuarios</h4>

      <div class="row g-3 mb-4">

        <div class="col-12 col-md-6">
          <label for="dniUserCreate" class="form-label">DNI</label>
          <input id="dniUserCreate" name="dni_usuario" type="text"
                class="form-control <?= isset($errores['dni_usuario']) ? 'is-invalid' : '' ?>"
                value="<?= old('dni_usuario') ?>">
          <?php if(isset($errores['dni_usuario'])): ?>
            <div class="invalid-feedback">
              <?= $errores['dni_usuario'] ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="col-12 col-md-6">
          <label for="userRoleCreate" class="form-label">Rol</label>
          <select id="userRoleCreate" name="id_rol" class="form-select <?= isset($errores['id_rol']) ? 'is-invalid' : '' ?>">
          <option value="-1" selected disabled hidden>Seleccione...</option>
          <?php foreach ($roles as $id => $nombre_rol): ?>
            <option value="<?= $id ?>" data-nombre="<?= esc($nombre_rol) ?>"
                <?= old('id_rol') == $id ? 'selected' : '' ?>>
                <?= esc($nombre_rol) ?>
            </option>
            <?php endforeach; ?>
          </select>
          <?php if(isset($errores['id_rol'])): ?>
            <div class="invalid-feedback">
              <?= $errores['id_rol'] ?>
            </div>
          <?php endif; ?>
        </div>

      </div>

      <div class="row g-3 mb-4">

        <div class="col-12 col-md-6">
          <label for="nameUserCreate" class="form-label">Nombre</label>
          <input id="nameUserCreate" name="nombre_usuario" type="text"
                class="form-control <?= isset($errores['nombre_usuario']) ? 'is-invalid' : '' ?>"
                value="<?= old('nombre_usuario') ?>">
          <?php if(isset($errores['nombre_usuario'])): ?>
            <div class="invalid-feedback">
              <?= $errores['nombre_usuario'] ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="col-12 col-md-6">
          <label for="lastnameUserCreate" class="form-label">Apellido</label>
          <input id="lastnameUserCreate" name="apellido_usuario" type="text"
                class="form-control <?= isset($errores['apellido_usuario']) ? 'is-invalid' : '' ?>"
                value="<?= old('apellido_usuario') ?>">
          <?php if(isset($errores['apellido_usuario'])): ?>
            <div class="invalid-feedback">
              <?= $errores['apellido_usuario'] ?>
            </div>
          <?php endif; ?>
        </div>

      </div>

      <div class="row g-3 mb-4">

        <div class="col-12 col-md-6">
          <label for="emailUserCreate" class="form-label">Email</label>
          <input id="emailUserCreate" name="email_usuario" type="text"
                class="form-control <?= isset($errores['email_usuario']) ? 'is-invalid' : '' ?>"
                value="<?= old('email_usuario') ?>">
          <?php if(isset($errores['email_usuario'])): ?>
            <div class="invalid-feedback">
              <?= $errores['email_usuario'] ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="col-12 col-md-6">
            <label for="passwordUserCreate" class="form-label">Contraseña</label>

            <div class="input-group">
                <input id="passwordUserCreate" name="password_usuario" type="password"
                      class="form-control <?= isset($errores['password_usuario']) ? 'is-invalid' : '' ?>">

                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                    <i class="bi bi-eye"></i>
                </button>

                <?php if(isset($errores['password_usuario'])): ?>
                    <div class="invalid-feedback">
                        <?= $errores['password_usuario'] ?>
                    </div>
                <?php endif; ?>
            </div>
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
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

  </form>
</div>
<script src="<?= base_url('assets/js/usuarioLogin.js?v=' . time()) ?>"></script>