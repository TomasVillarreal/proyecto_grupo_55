<div class="caja-cb">

  <form method="GET" action="">
    <div class="mb-4">
      <h4 class="border-bottom pb-2 mb-3">Eliminar</h4>

      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label for="medSelectUpdate" class="form-label">Medicamento</label>
          <select id="medSelectUpdate" class="form-select">
            <option value="-1" selected disabled hidden>Seleccione...</option>
            <option value="1">Prueba1...</option>
            <?php /* foreach ($medicamentos as $medicamento): ?>
              <option value="<?= $medicamento['id_medicamento']?>"
              <?= $medicamento['nombre_medicamento'] ?></option>
            <?php endforeach; */?>
          </select>
        </div>
      </div>
    </div>
  </form>



  <?php /* if (!empty($descripciones)): ?>

    <div class="row g-3 mt-3">

      <?php foreach ($descripciones as $desc): ?>
        <div class="col-md-4">
          <div class="card p-3 shadow-sm">

            <h6><?= $desc['dosis_producto'] . " " . $desc['medida_producto'] ?></h6>
            <p class="text-muted"><?= $desc['nombre_tipo_producto'] ?></p>

            <!-- 🗑️ Eliminar descripción -->
            <form method="POST" action="/descripciones/delete/<?= $desc['id_producto'] ?>">
              <button type="submit" class="btn btn-outline-danger btn-sm">
                🗑️ Eliminar
              </button>
            </form>

          </div>
        </div>
      <?php endforeach; ?>

    </div>

  <?php endif; */?>


  <?php if (empty($descripciones) && !empty($selectedMed)): ?>
    <p class="text-muted mt-3">Este medicamento no tiene descripciones.</p>
  <?php endif; ?>

 

  <?php if (!empty($selectedMed)): ?>
    <form method="POST" action="/medicamentos/delete/<?= $selectedMed ?>">
      <button type="submit" class="btn btn-danger mt-3">
        🗑️ Eliminar medicamento completo
      </button>
    </form>
  <?php endif; ?>

</div>
