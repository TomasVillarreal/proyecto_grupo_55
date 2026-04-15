<div class="caja-cb">
  <form>

    <!-- Sección 1 -->
    <div class="mb-4">
      <h4 class="border-bottom pb-2 mb-3">Opciones del medicamento</h4>

      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label for="medSelect" class="form-label">Tipo</label>
          <select id="medSelect" class="form-select">
            <option value="" selected disabled hidden>Seleccione...</option>
            <option value="new"> + Crear un nuevo medicamento </option>
            <option value="1">One</option>
            <option value="2">Two</option>
          </select>
        </div>

        <div class="col-12 col-md-6">
          <label for="medInput" class="form-label">Nombre del medicamento</label>
          <input id="medInput" type="text" class="form-control" placeholder="Ej: Ibuprofeno" disabled>
        </div>
      </div>
    </div>

    <!-- Sección 2 -->
    <div class="mb-4">
      <h4 class="border-bottom pb-2 mb-3">Descripción farmacéutica</h4>

      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label for="measurementInput" class="form-label">Medida utilizada</label>
          <select id="measurementInput" class="form-select">
            <option value="" selected disabled>Seleccione...</option>
            <option value="1">One</option>
            <option value="2">Two</option>
          </select>
        </div>

        <div class="col-12 col-md-6">
          <label for="dosageInput" class="form-label">Dosis recomendada</label>
          <input id="dosageInput" type="text" class="form-control" placeholder="Ej: 400mg">
        </div>
      </div>
    </div>

    <!-- Botón -->
    <div class="text-center mt-4">
      <button type="submit" class="btn btn-primary px-4">Crear</button>
    </div>

  </form>
</div>