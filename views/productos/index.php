<?php
$pageTitle = 'Productos';
include __DIR__ . '/../layouts/header.php';
?>

<style>
    .badge-disponible {
        background-color: #28a745 !important;
        color: white;
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        font-weight: 500;
    }

    .badge-no-disponible {
        background-color: #dc3545 !important;
        color: white;
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        font-weight: 500;
    }

    .producto-imagen {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-fish me-2"></i> Gestión de Productos</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?action=dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Productos</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <!-- Mensajes Flash -->
    <?php if (has_flash_message()): ?>
        <?php $flash = get_flash_message(); ?>
        <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Listado de Productos</h5>
                </div>
                <div class="col-auto">
                    <a href="<?php echo BASE_URL; ?>index.php?action=categorias" class="btn btn-light btn-sm">
                        <i class="fas fa-tags"></i> Categorías
                    </a>
                    <a href="<?php echo BASE_URL; ?>index.php?action=productos_crear" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Producto
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="filtroCategoria">Filtrar por Categoría:</label>
                    <select id="filtroCategoria" class="form-select">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['nombre']); ?>">
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filtroDisponible">Filtrar por Disponibilidad:</label>
                    <select id="filtroDisponible" class="form-select">
                        <option value="">Todos</option>
                        <option value="Disponible">Disponible</option>
                        <option value="No Disponible">No Disponible</option>
                    </select>
                </div>
            </div>

            <table id="tablaProductos" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Disponibilidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod): ?>
                        <tr>
                            <td>
                                <?php if (!empty($prod['imagen_url']) && file_exists($prod['imagen_url'])): ?>
                                    <img src="<?php echo BASE_URL . $prod['imagen_url']; ?>"
                                        alt="<?php echo htmlspecialchars($prod['nombre']); ?>"
                                        class="producto-imagen">
                                <?php else: ?>
                                    <div class="producto-imagen bg-secondary d-flex align-items-center justify-content-center text-white">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $prod['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($prod['nombre']); ?></strong>
                                <br>
                                <small class="text-muted"><?php echo htmlspecialchars(substr($prod['descripcion'] ?? '', 0, 50)); ?></small>
                            </td>
                            <td data-categoria="<?php echo htmlspecialchars($prod['categoria_nombre'] ?? 'Sin categoría'); ?>">
                                <?php echo htmlspecialchars($prod['categoria_nombre'] ?? 'Sin categoría'); ?>
                            </td>
                            <td><strong>S/ <?php echo number_format($prod['precio'], 2); ?></strong></td>
                            <td data-disponible="<?php echo $prod['disponible'] ? 'Disponible' : 'No Disponible'; ?>">
                                <span class="badge <?php echo $prod['disponible'] ? 'badge-disponible' : 'badge-no-disponible'; ?>">
                                    <?php echo $prod['disponible'] ? 'Disponible' : 'No Disponible'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo BASE_URL; ?>index.php?action=productos_editar&id=<?php echo $prod['id']; ?>"
                                        class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="cambiarEstado(<?php echo $prod['id']; ?>, <?php echo $prod['disponible'] ? 0 : 1; ?>)"
                                        class="btn btn-info" title="Cambiar Disponibilidad">
                                        <i class="fas fa-<?php echo $prod['disponible'] ? 'toggle-on' : 'toggle-off'; ?>"></i>
                                    </button>
                                    <button onclick="eliminarProducto(<?php echo $prod['id']; ?>)"
                                        class="btn btn-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let tabla;
    let filtroCategoriaActual = '';
    let filtroDisponibleActual = '';

    $(document).ready(function() {
        // Filtro personalizado global
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex, rowData, counter) {
                // DEBUG: Mostrar valores
                console.log('=== FILTRO EJECUTÁNDOSE ===');
                console.log('DataIndex:', dataIndex);
                console.log('Data Array:', data);
                console.log('Filtro Categoría Actual:', filtroCategoriaActual);
                console.log('Filtro Disponible Actual:', filtroDisponibleActual);

                // Obtener la fila actual del DOM
                const row = tabla.row(dataIndex).node();
                console.log('Row:', row);

                // Verificar filtro de categoría
                if (filtroCategoriaActual !== '') {
                    const categoriaCell = $(row).find('td').eq(3); // Columna de categoría
                    const categoriaValor = categoriaCell.attr('data-categoria') || categoriaCell.text().trim();

                    console.log('Categoría Cell:', categoriaCell);
                    console.log('Categoría Valor obtenido:', categoriaValor);
                    console.log('Comparando:', categoriaValor, '===', filtroCategoriaActual);

                    if (categoriaValor !== filtroCategoriaActual) {
                        console.log('❌ NO PASA filtro de categoría');
                        return false;
                    }
                    console.log('✅ PASA filtro de categoría');
                }

                // Verificar filtro de disponibilidad
                if (filtroDisponibleActual !== '') {
                    const disponibleCell = $(row).find('td').eq(5); // Columna de disponibilidad
                    const disponibleValor = disponibleCell.attr('data-disponible') || disponibleCell.text().trim();

                    console.log('Disponible Cell:', disponibleCell);
                    console.log('Disponible Valor obtenido:', disponibleValor);
                    console.log('Comparando:', disponibleValor, '===', filtroDisponibleActual);

                    if (disponibleValor !== filtroDisponibleActual) {
                        console.log('❌ NO PASA filtro de disponibilidad');
                        return false;
                    }
                    console.log('✅ PASA filtro de disponibilidad');
                }

                console.log('✅ FILA MOSTRADA');
                return true;
            }
        );

        // Inicializar DataTable
        tabla = $('#tablaProductos').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            order: [
                [3, 'asc'], // Ordenar por categoría
                [2, 'asc'] // Luego por nombre
            ]
        });

        // Filtro por categoría
        $('#filtroCategoria').on('change', function() {
            const valorAnterior = filtroCategoriaActual;
            filtroCategoriaActual = this.value;
            console.log('🔍 CAMBIO FILTRO CATEGORÍA');
            console.log('Valor anterior:', valorAnterior);
            console.log('Valor nuevo:', filtroCategoriaActual);
            console.log('Ejecutando tabla.draw()...');
            tabla.draw();
            console.log('tabla.draw() ejecutado');
        });

        // Filtro por disponibilidad
        $('#filtroDisponible').on('change', function() {
            const valorAnterior = filtroDisponibleActual;
            filtroDisponibleActual = this.value;
            console.log('🔍 CAMBIO FILTRO DISPONIBILIDAD');
            console.log('Valor anterior:', valorAnterior);
            console.log('Valor nuevo:', filtroDisponibleActual);
            console.log('Ejecutando tabla.draw()...');
            tabla.draw();
            console.log('tabla.draw() ejecutado');
        });
    });

    function cambiarEstado(id, estado) {
        const mensaje = estado === 1 ? 'marcar como disponible' : 'marcar como no disponible';

        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas ${mensaje} este producto?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo BASE_URL; ?>index.php?action=productos_cambiar_estado',
                    method: 'POST',
                    data: {
                        id: id,
                        estado: estado
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('¡Éxito!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error al cambiar el estado', 'error');
                    }
                });
            }
        });
    }

    function eliminarProducto(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará el producto y su imagen. No se puede revertir.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo BASE_URL; ?>index.php?action=productos_eliminar',
                    method: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('¡Eliminado!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error al eliminar el producto', 'error');
                    }
                });
            }
        });
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>