<?php
$pageTitle = 'Dashboard Mesero';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-concierge-bell me-2"></i> Dashboard Mesero</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<!-- Accesos Rápidos -->
<div class="row mb-4">
    <div class="col-md-3">
        <a href="<?php echo BASE_URL; ?>index.php?action=pedidos_crear" class="text-decoration-none">
            <div class="stat-card blue">
                <i class="fas fa-plus-circle"></i>
                <p>Nuevo Pedido</p>
                <h3><i class="fas fa-shopping-cart"></i></h3>
                <small>Crear pedido rápido</small>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="<?php echo BASE_URL; ?>index.php?action=reservas_crear" class="text-decoration-none">
            <div class="stat-card green">
                <i class="fas fa-calendar-plus"></i>
                <p>Nueva Reserva</p>
                <h3><i class="fas fa-calendar-check"></i></h3>
                <small>Reservar mesa</small>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <div class="stat-card orange">
            <i class="fas fa-table"></i>
            <p>Mesas Activas</p>
            <h3 id="mesasActivas">0</h3>
            <small>En servicio</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card red">
            <i class="fas fa-clock"></i>
            <p>Mis Pedidos</p>
            <h3 id="misPedidos">0</h3>
            <small>En proceso</small>
        </div>
    </div>
</div>

<!-- Mesas del Restaurante -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chair me-2"></i> Estado de Mesas</span>
                <div>
                    <span class="badge bg-success me-2"><i class="fas fa-circle"></i> Disponible</span>
                    <span class="badge bg-danger me-2"><i class="fas fa-circle"></i> Ocupada</span>
                    <span class="badge bg-warning"><i class="fas fa-circle"></i> Reservada</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row" id="mesasGrid">
                    <!-- Las mesas se cargarán dinámicamente -->
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <div class="col-md-2 col-sm-4 mb-3">
                            <div class="card text-center mesa-card" data-mesa="<?php echo $i; ?>">
                                <div class="card-body">
                                    <i class="fas fa-table fa-3x text-success mb-2"></i>
                                    <h5>Mesa <?php echo $i; ?></h5>
                                    <p class="mb-1"><small>4 personas</small></p>
                                    <span class="badge bg-success">Disponible</span>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pedidos Activos -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list-ul me-2"></i> Mis Pedidos Activos
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="tablaPedidosActivos">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Mesa/Cliente</th>
                                <th>Tipo</th>
                                <th>Hora</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No hay pedidos activos</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reservas Pendientes -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-calendar-alt me-2"></i> Reservas del Día
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Mesa</th>
                                <th>Hora</th>
                                <th>Personas</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaReservas">
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                    <p>No hay reservas para hoy</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
    // Actualizar estadísticas
    document.getElementById("mesasActivas").textContent = "3";
    document.getElementById("misPedidos").textContent = "5";
    
    // Click en mesa para ver detalles o asignar pedido
    document.querySelectorAll(".mesa-card").forEach(card => {
        card.style.cursor = "pointer";
        card.addEventListener("click", function() {
            const numMesa = this.dataset.mesa;
            Swal.fire({
                title: "Mesa " + numMesa,
                text: "¿Qué desea hacer?",
                icon: "question",
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: "Nuevo Pedido",
                denyButtonText: "Ver Cuenta",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "index.php?action=pedidos_crear&mesa=" + numMesa;
                } else if (result.isDenied) {
                    window.location.href = "index.php?action=pedidos_ver&mesa=" + numMesa;
                }
            });
        });
    });
    
    // TODO: Cargar datos reales con AJAX
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>