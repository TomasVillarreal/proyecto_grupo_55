<div class="caja-cb">
  <form>
    <div class="mb-4">
      <h4 class="border-bottom pb-2 mb-3">Elija lo que quiere modificar</h4>

      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label for="medSelectUpdate" class="form-label">Medicamento</label>
          <select id="medSelectUpdate" class="form-select">
            <option value="-1" selected disabled hidden>Seleccione...</option>
            <option value="1">Prueba1...</option>
            <?php /* foreach ($medicamentos as $medicamento): ?>
              <option value="<?= $medicamento['id_medicamento']?>"
              data-nombre="<?= $medicamento['nombre_medicamento'] ?>"
              >
              <?= $medicamento['nombre_medicamento'] ?></option>
            <?php endforeach; */?>
          </select>
        </div>

        <div class="col-12 col-md-6">
          <label for="descriptionSelectUpdate" class="form-label">Descripciones farmaceuticas</label>
          <select id="descriptionSelectUpdate" class="form-select" disabled>
            <option value="-1">-- Ninguna / No modificar descripción --</option>
            <option value="1">Prueba1...</option>
            <?php /* foreach ($descriptions as $desc): ?>
              <option 
                value="<?= $desc['id_producto'] ?>"
                data-dosis="<?= $desc['dosis_producto'] ?>"
                data-tipo="<?= $desc['nombre_tipo_producto'] ?>"
                data-unidad="<?= $desc['nombre_unidad_medida'] ?>"
                data-descripcion="<?= $desc['descripcion_producto'] ?>"

                data-tipo="<?= $desc['id_tipo_producto'] ?>"
                data-unidad="<?= $desc['id_medida_producto'] ?>"
              >
              <?= $desc['dosis_producto'] . " " . $desc['medida_producto'] . " - " .  data-tipo="<?= $desc['nombre_tipo_producto'] ?></option>
            <?php endforeach; */?>
          </select>
        </div>
      </div>
    </div>

    <div class="mb-4">
      <h4 class="border-bottom pb-2 mb-3">Medicamento</h4>

      <div class="row g-3">

        <div class="col-12 col-md-6">
          <label for="nameInputUpdate" class="form-label">Nombre del medicamento</label>
          <input id="nameInputUpdate" type="text" class="form-control" placeholder="Ej: Ibuprofeno" disabled>
        </div>

      </div>
    </div>


    <div class="mb-4">
      <h4 class="border-bottom pb-2 mb-3">Descripción farmacéutica</h4>

      <div class="row g-3">

        <div class="col-12 col-md-4">
          <label for="typeInputUpdate" class="form-label">Tipo de medicamento</label>
          <select id="typeInputUpdate" class="form-select" disabled>
            <option value="" selected disabled hidden>Seleccione...</option>
            <?php /* foreach ($tipoMedicamentos as $tipo): ?>
              <option value="<?= $tipo['id_tipo_producto'] ?>"><?= $tipo['nombre_tipo_producto'] ?></option>
            <?php endforeach; */?>
          </select>
        </div>

        <div class="col-12 col-md-4">
          <label for="dosageInputUpdate" class="form-label">Dosis recomendada</label>
          <input id="dosageInputUpdate" type="text" class="form-control" placeholder="Ej: 400" disabled>
        </div>

        <div class="col-12 col-md-4">
          <label for="measurementInputUpdate" class="form-label">Unidad de medida utilizada</label>
          <select id="measurementInputUpdate" class="form-select" disabled>
            <option value="" selected disabled hidden>Seleccione...</option>
            <?php /* foreach ($unidadMedida as $unidad): ?>
              <option value="<?= $tipo['id_unidad'] ?>"><?= $tipo['nombre_unidad'] ?></option>
            <?php endforeach; */?>
          </select>
        </div>       
      </div>
    </div>

    <div class="mb-4">
      <label for="descripcionInputUpdate" class="form-label mb-1">Descripción</label>
      <textarea class="form-control" id="descripcionInputCreate" rows="3"
        placeholder="Deje una descripción de la utilización del producto" disabled></textarea>
    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-primary px-4">Modificar</button>
    </div>

  </form>
</div>