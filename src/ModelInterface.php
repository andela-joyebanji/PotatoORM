<?php

namespace Pyjac\ORM;

interface ModelInterface
{
    /**
     * Get all models from database.
     *
     * @return array
     */
    public static function getAll();

    /**
     * Find model with the specified id.
     */
    public static function find($id);

    /**
     * Delete model with the specified id.
     */
    public static function destroy($id);
}
