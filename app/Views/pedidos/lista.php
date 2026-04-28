<div class="container mt-4">
    <h2 class="mb-4">Listado de Pedidos</h2>

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID Pedido</th>
                <th>Fecha</th>
                <th>Servicio Médico</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($pedidos)): ?>
                <?php foreach ($pedidos as $pedido): ?>
                    <?php
                        $estado = (string)$pedido['estado'];

                        switch ($estado) {
                            case 'Rechazado':
                                $claseBadge = 'text-bg-danger';
                                break;

                            case 'Pendiente':
                                $claseBadge = 'text-bg-warning';
                                break;

                            case 'Aprobado':
                                $claseBadge = 'text-bg-success';
                                break;

                            default:
                                $claseBadge = 'text-bg-primary';
                                break;
                        }
                    ?>

                    <tr onclick="window.location.href='#';" style="cursor:pointer;">
                        <td><?= esc((string)$pedido['id_pedido']) ?></td>
                        <td><?= esc((string)$pedido['fecha']) ?></td>
                        <td><?= esc((string)$pedido['servicio_medico']) ?></td>
                        <td>
                            <span class="badge <?= $claseBadge ?>">
                                <?= esc($estado) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No hay pedidos registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>