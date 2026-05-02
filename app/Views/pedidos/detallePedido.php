<div class="container mt-4">

    <!-- ===================== -->
    <!-- 🧾 DATOS DEL PEDIDO -->
    <!-- ===================== -->

    <?php $p = $pedido; ?>

    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5>Detalle del Pedido #<?= $p->id_pedido ?></h5>
        </div>

        <div class="card-body">
            <p><strong>Fecha:</strong> <?= $p->fecha_solicitud_pedido ?></p>
            <p><strong>Servicio Médico:</strong> <?= $p->nombre_servicio_medico ?></p>
            <p><strong>Comentario:</strong> <?= $p->comentario_pedido ?: 'Sin comentarios' ?></p>
        </div>
    </div>


    <!-- ===================== -->
    <!-- 💊 TABLA DE DETALLES -->
    <!-- ===================== -->

    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5>Productos del Pedido</h5>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Medicamento</th>
                        <th>Tipo Producto</th>
                        <th>Dosis</th>
                        <th>Proveedor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $d): ?>
                        <tr>
                            <td><?= $d->nombre_medicamento ?></td>
                            <td><?= $d->nombre_tipo_producto ?></td>
                            <td>
                                <?= $d->dosis_producto . ' ' . $d->nombre_medida ?>
                            </td>
                            <td><?= $d->nombre_proveedor ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>


    <!-- ===================== -->
    <!-- 🔘 ACCIONES -->
    <!-- ===================== -->

    <div id="acciones">

        <!-- Aprobar -->
        <form method="post" action="<?= base_url('pedidos/aprobar') ?>" style="display:inline;">
            <input type="hidden" name="idPedido" value="<?= $p->id_pedido ?>">
            <button type="submit" class="btn btn-primary">
                Aprobar
            </button>
        </form>

        <!-- Botón inicial Rechazar -->
        <button class="btn btn-danger" onclick="mostrarRechazo()">
            Rechazar
        </button>

    </div>


    <!-- ===================== -->
    <!-- ❌ FORMULARIO RECHAZO -->
    <!-- ===================== -->

    <div id="formRechazo" style="display:none;" class="mt-3">

        <form method="post" action="<?= base_url('pedidos/rechazar') ?>">

            <input type="hidden" name="idPedido" value="<?= $p->id_pedido ?>">

            <div class="mb-3">
                <label for="comentario" class="form-label">
                    Motivo del rechazo (opcional)
                </label>
                <textarea name="comentario" id="comentario" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-danger">
                Confirmar Rechazo
            </button>

        </form>

    </div>

</div>


<!-- ===================== -->
<!-- ⚙️ SCRIPT -->
<!-- ===================== -->

<script>
function mostrarRechazo() {
    document.getElementById('acciones').style.display = 'none';
    document.getElementById('formRechazo').style.display = 'block';
}
</script>