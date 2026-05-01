<div class="container mt-4">
    <h2 class="mb-4">Listado de Pedidos</h2>

    <label for="selectFiltradoEstados" class="form-label">Estados</label>
    <select id="selectFiltradoEstados" name="nombre_estado" class="form-select" required>
        <option value="0" selected> Todos </option>
        <?php foreach ($estados as $id => $nombre): ?>
            <option value="<?= $id ?>" data-nombre="<?= esc($nombre) ?>">
            <?= esc($nombre) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select id="selectFiltradoServicios" name="nombre_servicio" class="form-select" required>
        <option value="0" selected> Todos </option>
        <?php foreach ($servicios as $id => $nombre): ?>
            <option value="<?= $id ?>" data-nombre="<?= esc($nombre) ?>">
            <?= esc($nombre) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID Pedido</th>
                <th>Fecha</th>
                <th>Servicio Médico</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody id="tablaPedidos">
            <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td><?= esc($pedido['id_pedido']) ?></td>
                    <td><?= esc($pedido['fecha']) ?></td>
                    <td><?= esc($pedido['servicio_medico']) ?></td>
                    <td><?= esc($pedido['estado']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>