<div class="caja-cb">

    <div class="mb-4">
        <h4 class="border-bottom pb-2 mb-3">Eliminar</h4>

        <div class="row g-3">
            <div class="col-12 col-md-6">

                <label for="medSelectDelete" class="form-label">
                    Medicamento
                </label>

                <select id="medSelectDelete" class="form-select">

                    <option value="" selected disabled hidden>
                        Seleccione...
                    </option>

                    <?php foreach ($medicamentos as $med): ?>
                        <option value="<?= $med->id_medicamento ?>">
                            <?= $med->nombre_medicamento ?>
                        </option>
                    <?php endforeach; ?>

                </select>

            </div>
        </div>
    </div>

    <!-- Cards dinámicas -->
    <div id="contenedorCards" class="row g-3 mt-2"></div>

    <!-- Botón eliminar medicamento -->
    <div id="contenedorDeleteMedicamento" class="mt-4"></div>

</div>