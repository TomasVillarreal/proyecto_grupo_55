<div class="container mt-4">
    <h2 class="mb-4">Listado de Pedidos</h2>

    <div class="d-flex justify-content-end gap-2 mb-3">

        <select id="selectFiltradoEstados" class="form-select form-select-sm w-auto">
        <!-- Select que contiene todos los estados posibles.
         Se van creando las opciones de forma dinamica a traves de un for del php.-->
            <option value="0" selected>Todos los estados</option>
            <?php foreach ($estados as $id => $nombre): ?>
                <option value="<?= $id ?>">
                    <?= esc($nombre) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select id="selectFiltradoServicios" class="form-select form-select-sm w-auto">
            <option value="0" selected>Todos los servicios</option>
            <!-- Select que contiene todos los servicios medicos que pueden realizar pedidos posibles.
            Se van creando las opciones de forma dinamica a traves de un for del php.-->
            <?php foreach ($servicios as $id => $nombre): ?>
                <option value="<?= $id ?>">
                    <?= esc($nombre) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-dark">
            <tr>
                <th>ID Pedido</th>
                <th>Fecha</th>
                <th>Servicio Médico</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody id="tablaPedidos">
            <!-- Se llama a la tabla intermedia que contiene el cuerpo de la tabla.-->
            <?= view('pedidos/_tabla', ['pedidos' => $pedidos]) ?>
        </tbody>
    </table>
</div>