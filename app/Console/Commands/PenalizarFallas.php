<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CalendarioPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PenalizarFallas extends Command
{
    // El nombre del comando que usaremos en la terminal
    protected $signature = 'pagos:penalizar-fallas';

    // Descripción para saber qué hace
    protected $description = 'Aplica una semana extra a las cuotas en falla que no se recuperaron a tiempo';

    public function handle()
    {
        // Buscamos todas las cuotas que están en falla y su fecha de vencimiento es HOY o antes.
        $cuotasVencidas = CalendarioPago::where('estado', 'falla')
                            ->whereDate('fecha_vencimiento', '<=', now()) 
                            ->get();

        $this->info('Iniciando revisión. Cuotas a penalizar: ' . $cuotasVencidas->count());

        foreach ($cuotasVencidas as $cuota) {
            
            // Iniciamos la transacción para ESTE cliente específico
            \Illuminate\Support\Facades\DB::transaction(function () use ($cuota) {
                
                $prestamo = $cuota->prestamo;
                
                // Obtenemos la última semana registrada actualmente
                $ultimaSemana = $prestamo->calendarioPagos()->max('numero_semana');
                
                // 🔥 TRUCO INFALIBLE: Buscamos cuánto se le cobró exactamente en su semana 1
                $valorCuota = $prestamo->calendarioPagos()->where('numero_semana', 1)->value('monto_esperado');

                // Generamos la SEMANA EXTRA para el próximo sábado
                $prestamo->calendarioPagos()->create([
                    'numero_semana'     => $ultimaSemana + 1,
                    'fecha_vencimiento' => \Carbon\Carbon::parse($cuota->fecha_vencimiento)->addWeek(),
                    'monto_esperado'    => $valorCuota, // <-- ¡Corregido! Aquí usamos la variable del truco
                    'estado'            => 'pendiente',
                ]);

                // Desactivamos la cuota anterior para que no se cobre doble castigo
                $cuota->update(['estado' => 'falla_penalizada']); 
                
                // 🔥 EL JUEZ DE CARTERA VENCIDA (Adentro del ciclo de cada cliente) 🔥
                // Si la falla ocurrió en la semana 12 o posterior, mandamos el crédito a vencido.
                if ($ultimaSemana >= 12) {
                    $prestamo->update(['estado' => 'vencido']); 
                }
                
            }); // Fin de la transacción del cliente
        }

        $this->info('El proceso de penalización ha finalizado con éxito.');
    }    
}