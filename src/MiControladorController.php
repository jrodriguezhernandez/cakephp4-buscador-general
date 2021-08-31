<?php
declare(strict_types=1);

namespace App\Controller;
use App\Utilidades\Buscador;

class MiControladorController extends AppController
{
    /**
    * Index method
    *
    * @return \Cake\Http\Response|null|void Renders view
    */
    public function indexTable()
    {
        $this->viewBuilder()->disableAutoLayout();

        $this->paginate = [
            'maxLimit' => $this->numeroElementosPorPagina,
            'contain' => [], -- Arreglo con los modelos asociados
        ];

        // Inicio buscador dinámico
        $buscador = new Buscador();
        $cadenaAbuscar = $this->request->getQuery('search') ?? '';
        $condicionesExtra = [];
        $camposExcluidos = [];

        $query = $buscador->generarObjetoQueryDinamico(
            $this->MiModelo, // Modelo donde se debe hacer la búsqueda
            $this->paginate,     // Objeto paginador
            $cadenaAbuscar,      // Parámetro a buscar
            $condicionesExtra,   // Condiciones extra
            $camposExcluidos     // Campos a excluir
        );
        // Fin buscador dinámico

        $data = $this->paginate($query);

        $this->set(compact('data'));
    }
}
