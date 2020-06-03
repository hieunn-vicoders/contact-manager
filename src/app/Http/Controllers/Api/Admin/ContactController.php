<?php

namespace VCComponent\Laravel\Contact\Http\Controllers\Api\Admin;

use Exception;
use Illuminate\Http\Request;
use VCComponent\Laravel\Contact\Repositories\ContactRepository;
use VCComponent\Laravel\Contact\Transformers\ContactTransformer;
use VCComponent\Laravel\Contact\Validators\ContactValidator;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;

class ContactController extends ApiController
{
    protected $repository;
    protected $entity;
    protected $transformer;
    protected $validator;

    public function __construct(ContactRepository $repository, ContactValidator $validator)
    {
        $this->repository = $repository;
        $this->entity     = $repository->getEntity();
        $this->validator  = $validator;

        if (isset(config('contact.transformers')['contact'])) {
            $this->transformer = config('contact.transformers.contact');
        } else {
            $this->transformer = ContactTransformer::class;
        }
    }
    public function getStatus($request, $query)
    {

        if ($request->has('status')) {
            $pattern = '/^\d$/';

            if (!preg_match($pattern, $request->status)) {
                throw new Exception('The input status is incorrect');
            }

            $query = $query->where('status', $request->status);
        }

        return $query;
    }
    public function getType($request, $query)
    {

        if ($request->has('type')) {
            $pattern = '/^\d$/';

            if (!preg_match($pattern, $request->type)) {
                throw new Exception('The input status is incorrect');
            }

            $query = $query->where('type', $request->type);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->entity->query();
        $query = $this->getStatus($request, $query);
        $query = $this->getType($request, $query);

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['email', 'full_name', 'first_name', 'last_name'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $per_page = $request->has('per_page') ? (int) $request->get('per_page') : 15;
        $contacts = $query->paginate($per_page);

        return $this->response->paginator($contacts, new $this->transformer());
    }

    function list(Request $request) {
        $query = $this->entity->query();
        $query = $this->getStatus($request, $query);
        $query = $this->getType($request, $query);


        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['email', 'full_name', 'first_name', 'last_name'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $contacts = $query->get();

        return $this->response->paginator($contacts, new $this->transformer());
    }

    public function show(Request $request, $id)
    {
        $contact = $this->repository->find($id);

        return $this->response->item($contact, new $this->transformer());
    }

    public function store(Request $request)
    {
        $this->validator->isValid($request, 'RULE_ADMIN_CREATE');

        $contact = $this->repository->create($request->all());

        return $this->response->item($contact, new $this->transformer());
    }

    public function update(Request $request, $id)
    {
        $this->validator->isValid($request, 'RULE_ADMIN_UPDATE');
        $this->repository->find($id);
        $contact = $this->repository->update($request->all(), $id);

        return $this->response->item($contact, new $this->transformer());
    }

    public function destroy(Request $request, $id)
    {
        $this->repository->delete($id);

        return $this->success();
    }

    public function bulkUpdateStatus(Request $request)
    {
        $this->repository->bulkUpdateStatus($request);
        return $this->success();
    }

    public function updateStatus(Request $request, $id)
    {
        $this->repository->updateStatus($request, $id);
        return $this->success();
    }
}
