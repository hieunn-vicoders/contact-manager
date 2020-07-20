<?php
namespace VCComponent\Laravel\Contact\Contacts;

use VCComponent\Laravel\Contact\Contacts\Contracts\Contact as ContractsContact;
use VCComponent\Laravel\Contact\Entities\Contact as EntitiesContact;

class Contact implements ContractsContact
{

    public $entity;
    protected $limit;
    protected $column;
    protected $value;
    protected $id;
    protected $attributes = [];
    protected $direction;
    protected $relations;

    public function __construct()
    {
        if (isset(config('contact.models')['contact'])) {
            $model        = config('contact.models.contact');
            $this->entity = new $model;
        } else {
            $this->entity = new EntitiesContact;
        }
    }

    public function ofType($type)
    {
        return $this->entity->where('type', $type);
    }

    public function where($column, $value)
    {
        $query = $this->entity->where($column, $value)->get();

        return $query;
    }

    public function findOrFail($id)
    {
        return $this->entity->findOrFail($id);
    }

    public function toSql()
    {
        return $this->entity->toSql();
    }

    public function get()
    {
        return $this->entity->get();
    }

    public function paginate($perPage)
    {
        return $this->entity->paginate($perPage);
    }

    public function limit($value)
    {

        return $this->entity->limit($value);
    }

    public function orderBy($column, $direction = 'asc')
    {
        return $this->entity->orderBy($column, $direction);
    }

    public function with($relations)
    {
        $this->entity->with($relations);

        return $this;
    }

    public function first()
    {
        return $this->entity->first();
    }

    public function create(array $attributes = [])
    {
        return $this->entity->create($attributes);
    }

    public function firstOrCreate(array $attributes, array $values = [])
    {
        return $this->entity->firstOrCreate($attributes, $values);
    }

    public function update(array $values)
    {
        return $this->entity->update($values);
    }

    public function delete()
    {
        return $this->entity->delete();
    }

}
