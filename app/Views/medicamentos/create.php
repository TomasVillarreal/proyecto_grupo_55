<div class="caja-cb">
  <form>

    <!-- Sección 1 -->
    <div class="mb-4">
      <h4 class="border-bottom pb-2 mb-3">Opciones del medicamento</h4>

      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label for="medSelectCreate" class="form-label">Medicamento</label>
          <select id="medSelectCreate" class="form-select" required>
            <option value="" selected disabled hidden>Seleccione...</option>
            <option value="new"> + Crear un nuevo medicamento </option>
            <option value="1">One</option>
            <option value="2">Two</option>
          </select>
        </div>

        <div class="col-12 col-md-6">
          <label for="nameInputCreate" class="form-label">Nombre del medicamento</label>
          <input id="nameInputCreate" type="text" class="form-control" placeholder="Ej: Ibuprofeno" disabled>
        </div>
      </div>
    </div>

    <!-- Sección 2 -->
    <div class="mb-4">
      <h4 class="border-bottom pb-2 mb-3">Descripción farmacéutica</h4>

      <div class="row g-3">

        <div class="col-12 col-md-4">
          <label for="typeInputCreate" class="form-label">Tipo de medicamento</label>
          <select id="typeInputCreate" class="form-select" required>
            <option value="" selected disabled hidden>Seleccione...</option>
            <option value="1">One</option>
            <option value="2">Two</option>
          </select>
        </div>

        <div class="col-12 col-md-4">
          <label for="dosageInputCreate" class="form-label">Dosis recomendada</label>
          <input id="dosageInputCreate" type="text" class="form-control" placeholder="Ej: 400" required>
        </div>

        <div class="col-12 col-md-4">
          <label for="measurementInputCreate" class="form-label">Unidad de medida utilizada</label>
          <select id="measurementInputCreate" class="form-select" required>
            <option value="" selected disabled hidden>Seleccione...</option>
            <option value="1">One</option>
            <option value="2">Two</option>
          </select>
        </div>       
      </div>
    </div>

    <div class="mb-4">
      <label for="descripcionInputCreate" class="form-label mb-1">Descripción</label>
      <textarea class="form-control" id="descripcionInputCreate" rows="3"
        placeholder="Deje una descripción de la utilización del producto"></textarea>
    </div>

    

    <!-- Botón -->
    <div class="text-center mt-4">
      <button type="submit" class="btn btn-primary px-4">Crear</button>
    </div>

  </form>
</div>