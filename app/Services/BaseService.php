<?php

namespace App\Services;

abstract class BaseService
{
    protected $model;

    public function findId($id)
    {
        return $this->model->findOrFail($id);
    }

    public function all()
    {
        return $this->model->all();
    }

    public function allAvailable()
    {
        return $this->model
                    ->where('status', 1)
                    ->get();
    }

    public function getAllByStatus()
    {
        return $this->model
                    ->where('status', 1)
                    ->get();
    }

    public function find($name, $condition, $value)
    {
        return $this->model
                    ->where($name, $condition, $value)
                    ->get();
    }

    public function findAvailable($name, $condition, $value)
    {
        return $this->model
                    ->where('status', 1)
                    ->where($name, $condition, $value)
                    ->get();
    }

    public function insert($data)
    {
        $row = $this->model;
        foreach ($data as $key => $value) {
            $row->$key = $value;
        }
        $row->save();

        return $row;
    }

    public function insertMulti($data)
    {
        return $this->model->insert($data);
    }

    public function update($data, $id)
    {
        $row = $this->model->find($id);

        foreach ($data as $key => $value) {
            $row->$key = $value;
        }
        $row->save();

        return $row;
    }

    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }

    public function updateWithCondition($data, $condition, $value)
    {
        $row = $this->model->where($condition, $value)->first();

        foreach ($data as $key => $value) {
            $row->$key = $value;
        }
        $row->save();

        return $row;
    }

    public function paginate($limit)
    {
        return $this->model->paginate($limit);
    }
}
