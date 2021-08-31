<?php
declare(strict_types=1);

/**
 * Clase que permite hacer una busqueda sobre un modelo y sobre modelos asociados
 * 
 */

namespace App\Utilidades;

class Buscador
{
    /**
     * @param object $modelo Objeto donde se hará la búsqueda
     * @param array $paginador Objeto de paginación
     * @param string $cadenaAbuscar Cadena a buscar
     * @param array $arrayWhere Arreglo para establecer criterios de búsqueda
     * @param array $camposExcluidos Arreglo con los campos excluidos de la búsqueda
     * @return bool
     */
    private function generarArregloCondicion(object $modelo, array &$paginador, string $cadenaAbuscar, array &$arrayWhere, array $camposExcluidos): bool
    {
        // Validar si la cadena a buscar está vacía
        if ($cadenaAbuscar === null || $cadenaAbuscar === '') {
            return true;
        }

        try {
            $tableAlias = $modelo->getAlias();

            $columnsDefinition = $modelo->getSchema()->typeMap();

            $where = [];

            // Buscar sobre los campos
            foreach ($columnsDefinition as $key => $value) {
                if (!in_array($key, $camposExcluidos)) {
                    if ($value == 'string') {
                        $where[] = ["$tableAlias.$key LIKE " => "%$cadenaAbuscar%"];
                    }
                }
            }

            // Buscar sobre foreing keys de las tablas
            foreach ($paginador['contain'] as $key => $table) {
                $where[] = ["$table.name LIKE " => "%$cadenaAbuscar%"];
            }

            $arrayWhere = ['OR' => $where];

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param object $modelo Modelo donde se hará la búsqueda
     * @param array $paginador Objeto de paginación
     * @param string $cadenaAbuscar Cadena a buscar
     * @param array $condicionesExtra Arreglo para establecer criterios de búsqueda
     * @param array $camposExcluidos Arreglo con los campos excluidos de la búsqueda
     * @return mixed
     */
    public function generarObjetoQueryDinamico(object $modelo, array $paginador, string $cadenaAbuscar, array $condicionesExtra, array $camposExcluidos)
    {
        $cadenaAbuscar = $cadenaAbuscar ?? '';

        $condicionesExtra = $condicionesExtra ?? [];

        $camposExcluidos = $camposExcluidos ?? [];

        $query = $modelo->find('all')->where($condicionesExtra);

        // Almacena las condiciones que serán usadas para la búsqueda
        $arrayWhere = [];

        if ($cadenaAbuscar !== '') {
            // Llamar a buildArrayFinder
            $resultado = $this->generarArregloCondicion($modelo, $paginador, $cadenaAbuscar, $arrayWhere, $camposExcluidos);

            if ($resultado) {
                $query = $modelo->find('all')->where([$condicionesExtra, $arrayWhere]);
            }
        }

        return $query;
    }
}
