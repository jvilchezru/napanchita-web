<?php
/**
 * Controlador de Autenticación
 * Maneja login, logout y registro de usuarios
 */
// La sesión se inicia en index.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $db;
    private $usuario;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
    }

    // Mostrar formulario de login
    public function mostrarLogin() {
        if (isset($_SESSION['usuario_id'])) {
            header("Location: index.php?action=dashboard");
            exit();
        }
        require_once __DIR__ . '/../views/login.php';
    }

    // Procesar login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->usuario->email = $_POST['email'] ?? '';
            $this->usuario->password = $_POST['password'] ?? '';

            if ($this->usuario->login()) {
                $_SESSION['usuario_id'] = $this->usuario->id;
                $_SESSION['usuario_nombre'] = $this->usuario->nombre;
                $_SESSION['usuario_rol'] = $this->usuario->rol;
                
                // Forzar guardado de sesión
                session_write_close();
                session_start();
                
                // Redirect directo desde PHP
                header("Location: index.php?action=dashboard");
                exit();
            } else {
                // Si las credenciales son incorrectas, volver al login con error
                $_SESSION['login_error'] = 'Credenciales incorrectas. Intente nuevamente.';
                header("Location: index.php?action=login");
                exit();
            }
        }
    }

    // Mostrar formulario de registro
    public function mostrarRegistro() {
        require_once __DIR__ . '/../views/registro.php';
    }

    // Procesar registro
    public function registro() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->usuario->nombre = $_POST['nombre'] ?? '';
            $this->usuario->email = $_POST['email'] ?? '';
            $this->usuario->password = $_POST['password'] ?? '';
            $this->usuario->telefono = $_POST['telefono'] ?? '';
            $this->usuario->direccion = $_POST['direccion'] ?? '';
            $this->usuario->rol = 'cliente';

            if ($this->usuario->crear()) {
                echo json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al registrar usuario']);
            }
        }
    }

    // Cerrar sesión
    public function logout() {
        session_destroy();
        header("Location: index.php");
        exit();
    }

    // Verificar si hay sesión activa
    public static function verificarSesion() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?action=login");
            exit();
        }
    }

    // Verificar si es administrador
    public static function verificarAdmin() {
        self::verificarSesion();
        if ($_SESSION['usuario_rol'] !== 'admin') {
            header("Location: index.php?action=dashboard");
            exit();
        }
    }
}
?>
