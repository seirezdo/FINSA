<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CalendarioPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PenalizarFallas extends Command
{
    protected $signature = 'pagos:penalizar-fallas';
    protected $description = 'Aplica una semana extra a las cuotas en falla que no se recuperaron a tiempo';

    public function handle()
    {
        $cuotasVencidas = CalendarioPago::where('estado', 'falla')->get();

        $this->info('Iniciando revisión. Cuotas a penalizar: ' . $cuotasVencidas->count());

        foreach ($cuotasVencidas as $cuota) {
            
            DB::transaction(function () use ($cuota) {
                
                $prestamo = $cuota->prestamo;
                $ultimaSemana = $prestamo->calendarioPagos()->max('numero_semana');
                
                // 🔥 CANDADO DE ORO: Solo generamos semana extra si la última es menor a 13 🔥
                if ($ultimaSemana < 13) {
                    $fechaUltimaSemana = $prestamo->calendarioPagos()
                                                  ->where('numero_semana', $ultimaSemana)
                                                  ->value('fecha_vencimiento');
                    
                    $valorCuota = $prestamo->calendarioPagos()->where('numero_semana', 1)->value('monto_esperado');

                    // Generamos la SEMANA 13 empujándola al final
                    $prestamo->calendarioPagos()->create([
                        'numero_semana'     => $ultimaSemana + 1,
                        'fecha_vencimiento' => Carbon::parse($fechaUltimaSemana)->addWeek(), 
                        'monto_esperado'    => $valorCuota,
                        'estado'            => 'pendiente',
                    ]);
                }

                // Sin importar si se creó la 13 o no, la cuota morosa se desactiva
                $cuota->update(['estado' => 'falla_penalizada']); 
                
                // El Juez de Cartera Vencida
                if ($ultimaSemana >= 12) {
                    $prestamo->update(['estado' => 'vencido']); 
                }
                
            });
        }

        $this->info('El proceso de penalización ha finalizado con éxito.');
    }    
}