<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class NotificacionController extends Controller
{
    // Devuelve las cuentas pendientes de aprobación para la campana.
    public function pendientes(): JsonResponse
    {
        // Total de cuentas que esperan aprobación.
        $total = User::where('aprobado', false)->count();

        // Solo mostramos las 5 más recientes en el desplegable.
        $recientes = User::where('aprobado', false)
            ->latest('id')
            ->take(5)
            ->get(['id', 'name', 'email']);

        // Sin pendientes: campana tranquila.
        if ($total === 0) {
            return response()->json([
                'label' => 0,
                'label_color' => 'secondary',
                'icon_color' => 'warning',
                'dropdown' => '<span class="dropdown-item text-muted text-center">'
                    . 'No hay cuentas pendientes</span>',
            ]);
        }

        // Armamos el HTML del desplegable (escapando los datos).
        $html = '';

        foreach ($recientes as $usuario) {
            $html .= '<a href="' . e(route('usuarios.index')) . '" class="dropdown-item">'
                . '<i class="bi bi-person-exclamation me-2 text-warning"></i>'
                . e($usuario->name)
                . '<span class="d-block small text-muted">' . e($usuario->email) . '</span>'
                . '</a>';
        }

        // Si hay más de 5, lo indicamos al final.
        if ($total > $recientes->count()) {
            $html .= '<span class="dropdown-item text-muted small text-center">'
                . 'y ' . ($total - $recientes->count()) . ' más…</span>';
        }

        return response()->json([
            'label' => $total,
            'label_color' => 'danger',
            'icon_color' => 'warning',
            'dropdown' => $html,
        ]);
    }
}
