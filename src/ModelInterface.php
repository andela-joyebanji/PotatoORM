<?php 

namespace Pyjac\ORM;

interface ModelInterface {
    /**
     * Get all models from database.
     *
     * @return array
     */
    static function getAll();

    /**
     * Find model with the specified id.
     */
    static function find($id);

    /**
     * Delete model with the specified id.
     * 
     */
    static function destroy($id);
}