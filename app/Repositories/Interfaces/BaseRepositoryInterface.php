<?php

namespace App\Repositories\Interfaces;

interface BaseRepositoryInterface
{
    /**
     * Retrieve all data of repository
     */
    public function all($columns = ['*']);

    /**
     * Find data by id
     */
    public function find($id, $columns = ['*']);

    /**
     * Save a new entity in repository
     */
    public function create(array $input);

    /**
     * Update a entity in repository
     */
    public function update(array $input, $id);

    /**
     * Delete a entity in repository
     */
    public function delete($id);
}
