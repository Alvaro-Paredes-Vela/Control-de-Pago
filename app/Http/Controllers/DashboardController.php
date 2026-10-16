<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\Actividad;
use App\Models\Inscripcion;
use App\Models\Pago;
use App\Models\MovimientoCaja;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEstudiantes = Estudiante::count();

        $totalActividades = Actividad::count();

        $totalInscripciones = Inscripcion::where(
            'estado',
            'inscrito'
        )->count();

        $totalPagos = Pago::where(
            'estado',
            'confirmado'
        )->sum('monto');

        $otrosIngresos = MovimientoCaja::where('tipo', 'ingreso')
            ->where('estado', 'confirmado')
            ->sum('monto');

        $totalEgresos = MovimientoCaja::where('tipo', 'egreso')
            ->where('estado', 'confirmado')
            ->sum('monto');

        $totalIngresos = $totalPagos + $otrosIngresos;

        $saldoDisponible = $totalIngresos - $totalEgresos;

        return view('welcome', compact(
            'totalEstudiantes',
            'totalActividades',
            'totalInscripciones',
            'totalPagos',
            'totalIngresos',
            'totalEgresos',
            'saldoDisponible'
        ));
    }
}
