<div class="caja-cb">

    <div class="mb-4">
        <h4 class="border-bottom pb-2 mb-3">Eliminar</h4>

        <div class="row g-3">
            <div class="col-12 col-md-6">

                <label for="medSelectDelete" class="form-label">
                    Medicamento
                </label>

                <select id="medSelectDelete" name="id_medicamento" class="form-select">

                    <option value="" selected disabled hidden>
                        Seleccione...
                    </option>

                <?php foreach ($medicamentos as $id => $nombre): ?>
                    <option value="<?= $id ?>" data-nombre="<?= esc($nombre) ?>">
                    <?= esc($nombre) ?>
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

</div>