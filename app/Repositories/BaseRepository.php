<?php

namespace App\Repositories;

use App\Repositories\Interfaces\BaseRepositoryInterface;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * EloquentRepository constructor.
     */
    public function __construct()
    {
        $this->model = app()->make($this->model());
    }

    /**
     * get model
     * @return string
     */
    abstract public function model();

    /**
     * Retrieve all data of repository
     */
    public function all($columns = ['*'])
    {
        return $this->model->all($columns);
    }

    /**
     * Retrieve all data of repository with condition
     */
    public function list($condition = [], $columns = ['*'])
    {
        $builder = $this->model;
        $builder = $builder->filter($condition);
        $builder = $builder->orderBy('created_at', 'DESC');
        $builder = $builder->select($columns);
        return $builder->appends($condition);
    }

    /**
     * Find data by id
     */
    public function find($id, $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * Save a new entity in repository
     */
    public function create(array $input)
    {
        return $this->model->create($input);
    }

    /**
     * Update a entity in repository by id
     */
    public function update(array $input, $id)
    {
        //print_r($input);
        $model = $this->model->findOrFail($id);
        $model->fill($input);
        $model->save();

        return $this->model;
    }

    /**
     * Delete a entity in repository by id
     *
     * @param $id
     *
     * @return int
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}
