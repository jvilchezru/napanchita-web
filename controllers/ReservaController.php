<?php

require_once __DIR__ . '/../models/Reserva.php';
require_once __DIR__ . '/../models/Mesa.php';
require_once __DIR__ . '/../models/Cliente.php';

class ReservaController
{
    private $reserva;
    private $mesa;
    private $cliente;

    public function __construct()
    {
        $this->reserva = new Reserva();
        $this->mesa = new Mesa();
        $this->cliente = new Cliente();
    }

    /**
     * Listar reservas
     */
    public function index()
    {
        $filtros = [
            'estado' => $_GET['estado'] ?? '',
            'fecha' => $_GET['fecha'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? date('Y-m-d'),
            'fecha_hasta' => $_GET['fecha_hasta'] ?? date('Y-m-d', strtotime('+30 days'))
        ];

        $reservas = $this->reserva->listar($filtros);
        $estadisticas = $this->reserva->obtenerEstadisticas();

        include __DIR__ . '/../views/reservas/index.php';
    }

    /**
     * Mostrar formulario de creación
     */
    public function crear()
    {
        $mesas = $this->mesa->listarDisponiblesParaReserva();
        $clientes = $this->cliente->listar();

        include __DIR__ . '/../views/reservas/crear.php';
    }

    /**
     * Guardar nueva reserva
     */
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=reservas');
            return;
        }

        $this->reserva->cliente_id = $_POST['cliente_id'] ?? null;
        $this->reserva->mesa_id = $_POST['mesa_id'] ?? null;
        $this->reserva->fecha = $_POST['fecha'] ?? null;
        $this->reserva->hora = $_POST['hora'] ?? null;
        $this->reserva->personas = $_POST['personas'] ?? null;
        $this->reserva->notas = $_POST['notas'] ?? '';
        $this->reserva->estado = $_POST['estado'] ?? 'pendiente';
        $this->reserva->creado_por_usuario_id = $_SESSION['usuario_id'] ?? null;

        $resultado = $this->reserva->crear();

        if ($resultado['success']) {
            set_flash_message($resultado['message'] . ' Código: ' . $resultado['codigo'], 'success');
        } else {
            set_flash_message($resultado['message'], 'error');
        }

        redirect('index.php?action=reservas');
    }

    /**
     * Mostrar formulario de edición
     */
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect('index.php?action=reservas');
            return;
        }

        $reserva = $this->reserva->obtenerPorId($id);
        if (!$reserva) {
            set_flash_message('Reserva no encontrada', 'error');
            redirect('index.php?action=reservas');
            return;
        }

        // Obtener mesas disponibles para reserva, incluyendo la mesa actual de esta reserva
        $mesas = $this->mesa->listarDisponiblesParaReserva($reserva['mesa_id']);
        $clientes = $this->cliente->listar();

        include __DIR__ . '/../views/reservas/editar.php';
    }

    /**
     * Actualizar reserva
     */
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=reservas');
            return;
        }

        $id = $_POST['id'] ?? null;
        $estadoNuevo = $_POST['estado'] ?? 'pendiente';
        
        // Obtener reserva actual para validaciones
        $reservaActual = $this->reserva->obtenerPorId($id);
        
        if (!$reservaActual) {
            set_flash_message('Reserva no encontrada', 'error');
            redirect('index.php?action=reservas');
            return;
        }
        
        // Validar: canceladas no se pueden editar
        if ($reservaActual['estado'] === 'cancelada') {
            set_flash_message('No se puede modificar una reserva cancelada', 'error');
            redirect('index.php?action=reservas');
            return;
        }
        
        // Validar: completadas no se pueden editar
        if ($reservaActual['estado'] === 'completada') {
            set_flash_message('No se puede modificar una reserva completada', 'error');
            redirect('index.php?action=reservas');
            return;
        }
        
        // Validar: confirmadas solo pueden cambiar mesa, personas o estado (no fecha, hora, cliente, notas)
        if ($reservaActual['estado'] === 'confirmada') {
            // Verificar que no se cambien los campos bloqueados
            // Normalizar la hora para comparación (quitar segundos)
            $horaActual = substr($reservaActual['hora'], 0, 5);
            $horaNueva = substr($_POST['hora'] ?? '', 0, 5);
            
            $clienteIdActual = intval($reservaActual['cliente_id']);
            $clienteIdNuevo = intval($_POST['cliente_id'] ?? 0);
            
            $cambiosNoPermitidos = ($clienteIdNuevo != $clienteIdActual) ||
                                   ($_POST['fecha'] != $reservaActual['fecha']) ||
                                   ($horaNueva != $horaActual);
            
            if ($cambiosNoPermitidos) {
                set_flash_message('Una reserva confirmada solo puede modificar la mesa y cantidad de personas', 'error');
                redirect('index.php?action=reservas_editar&id=' . $id);
                return;
            }
            
            // Si está confirmada y se cambia el estado, solo puede pasar a completada o no_show
            if ($estadoNuevo !== 'confirmada' && !in_array($estadoNuevo, ['completada', 'no_show'])) {
                set_flash_message('Una reserva confirmada solo puede cambiar a completada o no_show', 'error');
                redirect('index.php?action=reservas_editar&id=' . $id);
                return;
            }
        }
        
        // Validar: confirmadas no se pueden cancelar
        if ($reservaActual['estado'] === 'confirmada' && $estadoNuevo === 'cancelada') {
            set_flash_message('No se puede cancelar una reserva confirmada', 'error');
            redirect('index.php?action=reservas_editar&id=' . $id);
            return;
        }
        
        // Validar: confirmadas/completadas no pueden regresar a pendiente
        if (in_array($reservaActual['estado'], ['confirmada', 'completada']) && $estadoNuevo === 'pendiente') {
            set_flash_message('No se puede regresar a pendiente una reserva confirmada o completada', 'error');
            redirect('index.php?action=reservas_editar&id=' . $id);
            return;
        }
        
        // Validar: solo se puede completar después de la fecha/hora (excepto si ya está completada)
        if ($estadoNuevo === 'completada' && $reservaActual['estado'] !== 'completada') {
            $fechaHoraReserva = strtotime($reservaActual['fecha'] . ' ' . $reservaActual['hora']);
            if ($fechaHoraReserva > time()) {
                set_flash_message('Solo puede marcar como completada después de la fecha y hora de la reserva', 'error');
                redirect('index.php?action=reservas_editar&id=' . $id);
                return;
            }
        }
        
        // Validar anticipación de 1 hora (solo si se está modificando fecha/hora y no está completada, cancelada o confirmada)
        if (!in_array($reservaActual['estado'], ['completada', 'cancelada', 'confirmada'])) {
            $fechaNueva = $_POST['fecha'] ?? $reservaActual['fecha'];
            $horaNueva = $_POST['hora'] ?? $reservaActual['hora'];
            
            // Solo validar si cambió la fecha o la hora
            if ($fechaNueva != $reservaActual['fecha'] || $horaNueva != $reservaActual['hora']) {
                if ($fechaNueva === date('Y-m-d')) {
                    $fechaHoraReserva = strtotime($fechaNueva . ' ' . $horaNueva);
                    $diferenciaSegundos = $fechaHoraReserva - time();
                    $diferenciaHoras = $diferenciaSegundos / 3600;
                    
                    if ($diferenciaHoras < 1) {
                        set_flash_message('Las reservas deben hacerse con al menos 1 hora de anticipación', 'error');
                        redirect('index.php?action=reservas_editar&id=' . $id);
                        return;
                    }
                }
            }
        }

        $this->reserva->id = $id;
        $this->reserva->cliente_id = $_POST['cliente_id'] ?? null;
        $this->reserva->mesa_id = $_POST['mesa_id'] ?? null;
        $this->reserva->fecha = $_POST['fecha'] ?? null;
        $this->reserva->hora = $_POST['hora'] ?? null;
        $this->reserva->personas = $_POST['personas'] ?? null;
        $this->reserva->notas = $_POST['notas'] ?? '';
        $this->reserva->estado = $estadoNuevo;

        $resultado = $this->reserva->actualizar();

        if ($resultado['success']) {
            set_flash_message($resultado['message'], 'success');
            
            // Si se completó la reserva, redirigir a crear pedido
            if ($estadoNuevo == 'completada') {
                redirect('index.php?action=pedidos_crear&mesa_id=' . $this->reserva->mesa_id . '&reserva_id=' . $id);
                return;
            }
        } else {
            set_flash_message($resultado['message'], 'error');
        }

        redirect('index.php?action=reservas');
    }

    /**
     * Cambiar estado de reserva (AJAX)
     */
    public function cambiarEstado()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $id = $_POST['id'] ?? null;
        $estado = $_POST['estado'] ?? null;

        if (!$id || !$estado) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        // Obtener reserva actual para validaciones
        $reservaActual = $this->reserva->obtenerPorId($id);
        
        if (!$reservaActual) {
            echo json_encode(['success' => false, 'message' => 'Reserva no encontrada']);
            return;
        }
        
        // Validar: confirmadas no se pueden cancelar
        if ($reservaActual['estado'] === 'confirmada' && $estado === 'cancelada') {
            echo json_encode(['success' => false, 'message' => 'No se puede cancelar una reserva confirmada']);
            return;
        }
        
        // Validar: solo se puede completar después de la fecha/hora
        if ($estado === 'completada') {
            $fechaHoraReserva = strtotime($reservaActual['fecha'] . ' ' . $reservaActual['hora']);
            if ($fechaHoraReserva > time()) {
                $tiempoRestante = $fechaHoraReserva - time();
                $minutosRestantes = ceil($tiempoRestante / 60);
                $horaReserva = date('H:i', $fechaHoraReserva);
                echo json_encode([
                    'success' => false, 
                    'message' => "La reserva no puede completarse aún. Disponible en $minutosRestantes minutos (a las $horaReserva)"
                ]);
                return;
            }
        }

        $resultado = $this->reserva->cambiarEstado($id, $estado);
        
        // Si se completó exitosamente, incluir URL para crear pedido
        if ($resultado['success'] && $estado === 'completada') {
            $resultado['redirect'] = BASE_URL . 'index.php?action=pedidos_crear&mesa_id=' . $reservaActual['mesa_id'] . '&reserva_id=' . $id;
        }
        
        echo json_encode($resultado);
    }

    /**
     * Cancelar reserva
     */
    public function cancelar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=reservas');
            return;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            set_flash_message('ID no proporcionado', 'error');
            redirect('index.php?action=reservas');
            return;
        }

        $resultado = $this->reserva->cancelar($id);

        if ($resultado['success']) {
            set_flash_message($resultado['message'], 'success');
        } else {
            set_flash_message($resultado['message'], 'error');
        }

        redirect('index.php?action=reservas');
    }

    /**
     * Verificar disponibilidad (AJAX)
     */
    public function verificarDisponibilidad()
    {
        header('Content-Type: application/json');

        $mesa_id = $_GET['mesa_id'] ?? null;
        $fecha = $_GET['fecha'] ?? null;
        $hora = $_GET['hora'] ?? null;
        $reserva_id = $_GET['reserva_id'] ?? null;

        if (!$mesa_id || !$fecha || !$hora) {
            echo json_encode(['disponible' => false, 'message' => 'Datos incompletos']);
            return;
        }

        $disponible = $this->reserva->verificarDisponibilidad($mesa_id, $fecha, $hora, $reserva_id);
        
        echo json_encode([
            'disponible' => $disponible,
            'message' => $disponible ? 'Mesa disponible' : 'Mesa no disponible para esa fecha y hora'
        ]);
    }

    /**
     * Calendario de reservas
     */
    public function calendario()
    {
        $mes = $_GET['mes'] ?? date('m');
        $anio = $_GET['anio'] ?? date('Y');

        $fecha_desde = "$anio-$mes-01";
        $fecha_hasta = date('Y-m-t', strtotime($fecha_desde));

        $reservas = $this->reserva->listar([
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta
        ]);

        include __DIR__ . '/../views/reservas/calendario.php';
    }

    /**
     * Buscar reserva por código (AJAX)
     */
    public function buscarPorCodigo()
    {
        header('Content-Type: application/json');

        $codigo = $_GET['codigo'] ?? '';

        if (empty($codigo)) {
            echo json_encode(['success' => false, 'message' => 'Código no proporcionado']);
            return;
        }

        $reserva = $this->reserva->obtenerPorCodigo($codigo);

        if ($reserva) {
            echo json_encode(['success' => true, 'data' => $reserva]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Reserva no encontrada']);
        }
    }

    /**
     * Obtener reservas del día (AJAX)
     */
    public function obtenerReservasDelDia()
    {
        header('Content-Type: application/json');

        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $reservas = $this->reserva->obtenerReservasDelDia($fecha);

        echo json_encode(['success' => true, 'data' => $reservas]);
    }

    /**
     * Marcar como no presentado
     */
    public function marcarNoShow()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?action=reservas');
            return;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            set_flash_message('ID no proporcionado', 'error');
            redirect('index.php?action=reservas');
            return;
        }

        $resultado = $this->reserva->cambiarEstado($id, 'no_show');

        if ($resultado['success']) {
            set_flash_message($resultado['message'], 'success');
        } else {
            set_flash_message($resultado['message'], 'error');
        }

        redirect('index.php?action=reservas');
    }
}
