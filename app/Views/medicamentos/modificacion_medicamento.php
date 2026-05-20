<div class="caja-cb">
  <form method="post" action="<?= base_url('medicamentos/modificacion') ?>">
    <div class="mb-4">
      <h4 class="border-bottom pb-2 mb-3">Elija lo que quiere modificar</h4>

      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label for="medSelectUpdate" class="form-label">Medicamento</label>
          <select id="medSelectUpdate" name="id_medicamento" class="form-select">
            <option value="-1" selected disabled hidden>Seleccione...</option>
              <?php foreach ($medicamentos as $id => $nombre): ?>
                <option value="<?= $id ?>" data-nombre="<?= esc($nombre) ?>">
                  <?= esc($nombre) ?>
                </option>
              <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Descripción farmacéutica</label>
          
          <select id="descriptionSelectUpdate" name="id_producto" class="form-select" disabled>
            <option value="-1">
            -- Ninguna / No modificar descripción --
            </option>
          </select>
        </div>
      </div>
    </div>

    <div class="mb-4">
      <h4 class="border-bottom pb-2 mb-3">Medicamento</h4>

      <div class="row g-3">

        <div class="col-12 col-md-6">
          <label for="nameInputUpdate" class="form-label">Nombre del medicamento</label>
          <input id="nameInputUpdate" name="nombre_medicamento" type="text" class="form-control" placeholder="Ej: Ibuprofeno" disabled>
        </div>

      </div>
    </div>


    <div class="mb-4">
      <h4 class="border-bottom pb-2 mb-3">Descripción farmacéutica</h4>

      <div class="row g-3">

        <div class="col-12 col-md-4">
          <label for="typeInputUpdate" class="form-label">Tipo de medicamento</label>
          <select id="typeInputUpdate" name="id_tipo_producto" class="form-select" disabled>
            <option value="" selected disabled hidden>Seleccione...</option>
              <?php foreach ($tiposProducto as $id => $nombre): ?>
                  <option value="<?= $id ?>"><?= $nombre ?></option>
              <?php endforeach; ?>
          </select>
        </div>

        <div class="col-12 col-md-4">
          <label for="dosageInputUpdate" class="form-label">Dosis recomendada</label>
          <input id="dosageInputUpdate" name="dosis_producto" type="text" class="form-control" placeholder="Ej: 400" disabled>
        </div>

        <div class="col-12 col-md-4">
          <label for="measurementInputUpdate" class="form-label">Unidad de medida utilizada</label>
          <select id="measurementInputUpdate" name="id_medida_producto" class="form-select" disabled>
            <option value="" selected disabled hidden>Seleccione...</option>
              <?php foreach ($unidadesMedida as $id => $nombre): ?>
                  <option value="<?= $id ?>"><?= $nombre ?></option>
              <?php endforeach; ?>
          </select>
        </div>       
      </div>
    </div>

    <div class="mb-4">
      <label for="descripcionInputUpdate" class="form-label mb-1">Descripción</label>
      <textarea class="form-control" id="descripcionInputUpdate" name="descripcion_producto" rows="3"
        placeholder="Deje una descripción de la utilización del producto" disabled></textarea>
    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-primary px-4">Modificar</button>
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

    <?php if (session()->getFlashdata('info')): ?>
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('info') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    
  </form>
</div>