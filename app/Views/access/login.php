<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>"> 


<style>
/* VARIABLES DEL SIDEBAR */
:root {
  --first-color: #031530;
  --white-color: #f7f6fb;
  --body-font: "Nunito", sans-serif;
}

/* RESET */
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
}

body{
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  background: var(--first-color);
  font-family: var(--body-font);
}

/* CAJA LOGIN */
.caja-cb{
  width:100%;
  max-width:420px;
  background:white;
  border:1px solid #ddd;
  border-radius:12px;
  padding:2rem;
  box-shadow:0 0 20px rgba(0,0,0,0.15);
}

/* ICONO */
.login-icon{
  font-size:3rem;
  color: var(--first-color);
  display:block;
  text-align:center;
  margin-bottom:1.5rem;
}

/* LABELS */
.form-label{
  font-weight:600;
  margin-bottom:.35rem;
}

/* INPUTS */
.form-control{
  padding:.7rem .9rem;
  border-radius:8px;
}

/* BOTON */
.btn-primary{
  background: var(--first-color);
  border:none;
}

.btn-primary:hover{
  background:#06224b;
}
</style>
</head>

<body>
<?php
//Para el manejo de errores
$errores = session()->getFlashdata('errores') ?? [];
?>
<div class="caja-cb">

  <i class="bi bi-hospital nav_logo-icon login-icon"></i>

  <form method="post" action="<?= base_url('access/iniciar_sesion') ?>">

    <div class="mb-3">
          <label for="emailUserCreate" class="form-label">Email</label>
          <input id="emailUserCreate" name="email_usuario" type="text" placeholder="Ingrese su email"
                class="form-control <?= isset($errores['email_usuario']) ? 'is-invalid' : '' ?>"
                value="<?= old('email_usuario') ?>">
          <?php if(isset($errores['email_usuario'])): ?>
            <div class="invalid-feedback">
              <?= $errores['email_usuario'] ?>
            </div>
          <?php endif; ?>
    </div>

    <div class="mb-3">
          <label for="passwordUserCreate" class="form-label">Contraseña</label>

            <div class="input-group">
                <input id="passwordUserCreate" name="password_usuario" type="password" placeholder="Ingrese su contraseña"
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

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-primary px-4">Iniciar Sesion</button>
    </div>

  </form>
      <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
</div>

</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/usuarioLogin.js?v=' . time()) ?>"></script>
