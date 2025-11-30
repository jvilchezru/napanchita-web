<?php
$pageTitle = 'Dashboard Administrador';
include __DIR__ . '/../layouts/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-chart-line me-2"></i> Dashboard Administrador</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<!-- Tarjetas de Estadísticas -->
<div class="row">
    <div class="col-md-3">
        <div class="stat-card blue">
            <i class="fas fa-shopping-cart"></i>
            <p>Pedidos Hoy</p>
            <h3 id="pedidosHoy">0</h3>
            <small>+15% vs ayer</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card green">
            <i class="fas fa-dollar-sign"></i>
            <p>Ventas Hoy</p>
            <h3 id="ventasHoy">S/ 0.00</h3>
            <small>+8% vs ayer</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card orange">
            <i class="fas fa-users"></i>
            <p>Clientes Nuevos</p>
            <h3 id="clientesNuevos">0</h3>
            <small>Esta semana</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card red">
            <i class="fas fa-clock"></i>
            <p>Pedidos Pendientes</p>
            <h3 id="pedidosPendientes">0</h3>
            <small>Requieren atención</small>
        </div>
    </div>
</div>

<!-- Gráficos y Tablas -->
<div class="row mt-4">
    <!-- Gráfico de Ventas -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-area me-2"></i> Ventas de los Últimos 7 Días
            </div>
            <div class="card-body">
                <canvas id="ventasChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- Platos Más Vendidos -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-fire me-2"></i> Platos Top
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Ceviche Clásico
                        <span class="badge bg-primary rounded-pill">45</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Chicharrón de Pescado
                        <span class="badge bg-primary rounded-pill">38</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Arroz con Mariscos
                        <span class="badge bg-primary rounded-pill">32</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Jalea Mixta
                        <span class="badge bg-primary rounded-pill">28</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Leche de Tigre
                        <span class="badge bg-primary rounded-pill">25</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Pedidos Recientes -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list me-2"></i> Pedidos Recientes</span>
                <a href="<?php echo BASE_URL; ?>index.php?action=pedidos" class="btn btn-sm btn-primary">
                    Ver Todos
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="pedidosRecientes">
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Cargando pedidos...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Estado de Mesas -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-table me-2"></i> Estado de Mesas
            </div>
            <div class="card-body">
                <canvas id="mesasChart"></canvas>

                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle text-success me-2"></i> Disponibles</span>
                        <strong id="mesasDisponibles">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle text-danger me-2"></i> Ocupadas</span>
                        <strong id="mesasOcupadas">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle text-warning me-2"></i> Reservadas</span>
                        <strong id="mesasReservadas">0</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actividad Reciente -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-history me-2"></i> Actividad Reciente del Sistema
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush" id="actividadReciente">
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-spinner fa-spin me-2"></i> Cargando actividad...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$extraScripts = '
<script>
    // Gráfico de Ventas
    const ventasCtx = document.getElementById("ventasChart").getContext("2d");
    const ventasChart = new Chart(ventasCtx, {
        type: "line",
        data: {
            labels: ["Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"],
            datasets: [{
                label: "Ventas (S/)",
                data: [1200, 1900, 1500, 2100, 2400, 2800, 2200],
                borderColor: "rgb(102, 126, 234)",
                backgroundColor: "rgba(102, 126, 234, 0.1)",
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Gráfico de Mesas
    const mesasCtx = document.getElementById("mesasChart").getContext("2d");
    const mesasChart = new Chart(mesasCtx, {
        type: "doughnut",
        data: {
            labels: ["Disponibles", "Ocupadas", "Reservadas"],
            datasets: [{
                data: [5, 3, 2],
                backgroundColor: [
                    "rgb(40, 167, 69)",
                    "rgb(220, 53, 69)",
                    "rgb(255, 193, 7)"
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    
    // Actualizar estadísticas (simulado)
    document.getElementById("pedidosHoy").textContent = "24";
    document.getElementById("ventasHoy").textContent = "S/ 2,450.00";
    document.getElementById("clientesNuevos").textContent = "12";
    document.getElementById("pedidosPendientes").textContent = "3";
    
    document.getElementById("mesasDisponibles").textContent = "5";
    document.getElementById("mesasOcupadas").textContent = "3";
    document.getElementById("mesasReservadas").textContent = "2";
    
    // Cargar datos reales con AJAX (para implementar en Sprint 2+)
    // TODO: Implementar llamadas AJAX a los controladores correspondientes
</script>
';

include __DIR__ . '/../layouts/footer.php';
?>