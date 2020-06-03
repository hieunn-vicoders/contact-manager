<?php

namespace VCComponent\Laravel\Contact\Transformers;

use League\Fractal\TransformerAbstract;

class ContactTransformer extends TransformerAbstract
{
    protected $availableIncludes = [];

    public function __construct($includes = [])
    {
        $this->setDefaultIncludes($includes);
    }

    public function transform($model)
    {
        return [
            'id'         => (int) $model->id,
            'email'      => $model->email,
            'full_name'  => $model->full_name,
            'first_name' => $model->first_name,
            'last_name'  => $model->last_name,
            'address'    => $model->address,
            'status'     => $model->status,
            'note'       => $model->note,
            'type'       => (int) $model->type,
            'timestamps' => [
                'created_at' => $model->created_at,
                'updated_at' => $model->updated_at,
            ],
        ];
    }
}
