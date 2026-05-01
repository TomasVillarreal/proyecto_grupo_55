
<!-- Vista intermedia que contiene los datos de la tabla de pedidos.
 Se ha creado especificamente para pode realizar el filtrado de los pedidos de forma dinamica
 a través de la escucha de los cambios en los selects que estan en lista.php.
 Se hizo de esa manera para evitar tener que estar recargando la página entera, y solo tener que 
 recargar la tabla especificamente.-->
<?php if (!empty($pedidos)): ?>
    <?php foreach ($pedidos as $pedido): ?>
        <?php
            // En base al estado del pedido le asignamos un color especial al badge. 
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
            }
        ?>
        <tr>
            <td><?= esc($pedido['id_pedido']) ?></td>
            <td><?= esc($pedido['fecha']) ?></td>
            <td><?= esc($pedido['servicio_medico']) ?></td>
            <td> 
                <!-- Aca es donde se llama a la clase que contiene el color particular del estado-->
                <span class="badge rounded-pill <?= $claseBadge ?>">
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