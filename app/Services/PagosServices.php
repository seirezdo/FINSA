<?php 

namespace App\Services;

use App\Models\Pago;
use App\Models\Prestamo; // <-- Esta es la línea que te falta
use App\Models\CalendarioPago;
use Illuminate\Support\Facades\DB;

class PagosServices
{
    /**
     * Registra un pago, aplica reglas de mora y verifica liquidación.
     * Todo se ejecuta en una transacción para asegurar la integridad [1, 2].
     */
 public function registrarPago(array $datos)
    {
        return DB::transaction(function () use ($datos) {
            
            $montoRestante = $datos['monto_pagado'];
            $prestamo = Prestamo::findOrFail($datos['prestamo_id']);

            // 1. Obtenemos todas las cuotas que NO están pagadas por completo
            $cuotasPendientes = CalendarioPago::where('prestamo_id', $prestamo->id)
                ->where('estado', '!=', 'pagado')
                ->orderBy('numero_semana', 'asc') // Siempre empezamos por la más atrasada
                ->get();

            if ($cuotasPendientes->isEmpty()) {
                throw new \Exception('Este préstamo ya está totalmente liquidado.');
            }

            // 2. Bucle de Cascada: Repartimos el dinero semana por semana
            foreach ($cuotasPendientes as $cuota) {
                if ($montoRestante <= 0) {
                    break; // Si ya no nos queda dinero, detenemos el reparto
                }

                // Calculamos cuánto le falta a ESTA semana específica para llenarse
                $recuperadoHastaAhora = Pago::where('calendario_pago_id', $cuota->id)->sum('monto_pagado');
                $deudaDeEstaSemana = $cuota->monto_esperado - $recuperadoHastaAhora;

                if ($deudaDeEstaSemana > 0) {
                    // Tomamos lo que alcance: el total del dinero restante o solo lo que debe esta semana
                    $montoAAplicar = min($montoRestante, $deudaDeEstaSemana);

                    // Guardamos el abono específicamente amarrado a esta semana
                    Pago::create([
                        'calendario_pago_id' => $cuota->id,
                        'monto_pagado'       => $montoAAplicar,
                        'fecha_pago'         => $datos['fecha_pago'],
                        'metodo_pago'        => $datos['metodo_pago'],
                        'registrado_por'     => auth()->id(),
                    ]);

                    // Le restamos al fajo de dinero lo que acabamos de aplicar
                    $montoRestante -= $montoAAplicar;
                    
                    // Verificamos cómo quedó esta semana después del abono
                    $recuperadoTotal = $recuperadoHastaAhora + $montoAAplicar;

                    if ($recuperadoTotal >= $cuota->monto_esperado) {
                        $cuota->update(['estado' => 'pagado']); // ¡Se completó la cuota!
                    } else {
                        $cuota->update(['estado' => 'parcial']); // Quedó a la mitad
                    }
                }
            }

            // 3. Evaluar Liquidación Automática del Préstamo [4]
            $totalEsperado = CalendarioPago::where('prestamo_id', $prestamo->id)->sum('monto_esperado');
            $totalRecuperado = Pago::whereIn('calendario_pago_id', 
                CalendarioPago::where('prestamo_id', $prestamo->id)->pluck('id')
            )->sum('monto_pagado');

            if ($totalRecuperado >= $totalEsperado) {
                $prestamo->update(['estado' => 'liquidado']);
            }

            return [
                'mensaje' => 'Abono procesado y distribuido correctamente en cascada.'
            ];
        });
    }

    /**
     * Verifica si todas las cuotas están pagadas y actualiza el préstamo.
     */
    private function verificarYLiquidarPrestamo($prestamo)
    {
        // Contamos cuotas pendientes usando Eloquent para mayor precisión [3]
        $pendientes = $prestamo->calendarioPagos()
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->count();

        if ($pendientes === 0) {
            // Si no hay más deuda, el préstamo pasa a estado 'liquidado' automáticamente
            $prestamo->update(['estado' => 'liquidado']);
        }
    }
}