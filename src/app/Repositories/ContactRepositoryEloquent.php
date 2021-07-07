<?php

namespace VCComponent\Laravel\Contact\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use VCComponent\Laravel\Contact\Entities\Contact;
use VCComponent\Laravel\Contact\Repositories\ContactRepository;
use VCComponent\Laravel\Vicoders\Core\Exceptions\NotFoundException;

/**
 * Class AccountantRepositoryEloquent.
 */
class ContactRepositoryEloquent extends BaseRepository implements ContactRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        if (isset(config('contact.models')['contact'])) {
            return config('contact.models.contact');
        } else {
            return Contact::class;
        }
    }

    public function getEntity()
    {
        return $this->model;
    }

    public function updateStatus($request, $id)
    {
        $contact = $this->find($id);

        $contact->status = $request->input('status');
        $contact->save();
    }

    public function bulkUpdateStatus($request)
    {

        $data     = $request->all();
        $contacts = $this->findWhereIn("id", $request->ids);

        if (count($request->ids) > $contacts->count()) {
            throw new NotFoundException("contacts");
        }

        $result = $this->getEntity()->whereIn("id", $request->ids)->update(['status' => $data['status']]);

        return $result;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
