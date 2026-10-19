
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;

// Controladores de autenticación
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Controladores del sistema
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\CategoriaActividadController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\MovimientoCajaController;
use App\Http\Controllers\CampoActividadController;
use App\Http\Controllers\EstadoCuentaController;

/*
|--------------------------------------------------------------------------
| AUTORIZACIONES
|--------------------------------------------------------------------------
*/

// Solo el administrador puede gestionar las cuentas de usuarios.
Gate::define('administrar-usuarios', function ($user) {
    return $user->rol === 'administrador';
});

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    // Mostrar el formulario de inicio de sesión.
    Route::get('/login', [LoginController::class, 'showLogin'])
        ->name('login');

    // Procesar el inicio de sesión.
    Route::post('/login', [LoginController::class, 'login']);

    // Mostrar el formulario de registro.
    Route::get('/register', [RegisterController::class, 'showRegister'])
        ->name('register');

    // Procesar el registro de usuarios.
    Route::post('/register', [RegisterController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Cerrar sesión.
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    /*
    |--------------------------------------------------------------------------
    | USUARIOS: SOLO ADMINISTRADOR
    |--------------------------------------------------------------------------
    */

    Route::middleware('can:administrar-usuarios')->group(function () {

        Route::resource('usuarios', UserController::class)
            ->parameters(['usuarios' => 'usuario']);
    });

    /*
    |--------------------------------------------------------------------------
    | ESTUDIANTES
    |--------------------------------------------------------------------------
    */

    Route::resource('estudiantes', EstudianteController::class);

    /*
    |--------------------------------------------------------------------------
    | CATEGORÍAS
    |--------------------------------------------------------------------------
    */

    Route::resource('categorias', CategoriaActividadController::class)
        ->parameters(['categorias' => 'categoria']);

    /*
    |--------------------------------------------------------------------------
    | ACTIVIDADES
    |--------------------------------------------------------------------------
    */

    Route::resource('actividades', ActividadController::class)
        ->parameters(['actividades' => 'actividade']);

    // Registrar campos personalizados de una actividad.
    Route::post(
        'actividades/{actividad}/campos',
        [CampoActividadController::class, 'store']
    )->name('actividades.campos.store');

    // Eliminar campos personalizados de una actividad.
    Route::delete(
        'actividades/{actividad}/campos/{campo}',
        [CampoActividadController::class, 'destroy']
    )->name('actividades.campos.destroy');

    /*
    |--------------------------------------------------------------------------
    | INSCRIPCIONES
    |--------------------------------------------------------------------------
    */

    Route::resource('inscripciones', InscripcionController::class)
        ->parameters(['inscripciones' => 'inscripcione']);

    /*
    |--------------------------------------------------------------------------
    | FINANZAS
    |--------------------------------------------------------------------------
    */

    // Métodos de pago.
    Route::resource('metodos-pago', MetodoPagoController::class)
        ->parameters(['metodos-pago' => 'metodo_pago']);

    // Pagos.
    Route::resource('pagos', PagoController::class);

    // Movimientos de caja.
    Route::resource('movimientos-caja', MovimientoCajaController::class)
        ->parameters(['movimientos-caja' => 'movimientos_caja']);

    // Estado de cuentas.
    Route::get('/estado-cuentas', [EstadoCuentaController::class, 'index'])
        ->name('estado-cuentas.index');
});

/*
|--------------------------------------------------------------------------
| RUTA PRINCIPAL
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');
