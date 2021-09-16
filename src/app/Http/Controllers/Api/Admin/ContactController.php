<?php

namespace VCComponent\Laravel\Contact\Http\Controllers\Api\Admin;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use VCComponent\Laravel\Contact\Repositories\ContactRepository;
use VCComponent\Laravel\Contact\Transformers\ContactTransformer;
use VCComponent\Laravel\Contact\Validators\ContactValidator;
use VCComponent\Laravel\Export\Services\Export\Export;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;
use VCComponent\Laravel\Vicoders\Core\Exceptions\PermissionDeniedException;

class ContactController extends ApiController
{
    protected $repository;
    protected $entity;
    protected $transformer;
    protected $validator;

    public function __construct(ContactRepository $repository, ContactValidator $validator)
    {
        $this->repository = $repository;
        $this->entity = $repository->getEntity();
        $this->validator = $validator;

        if (isset(config('contact.transformers')['contact'])) {
            $this->transformer = config('contact.transformers.contact');
        } else {
            $this->transformer = ContactTransformer::class;
        }
        if (!empty(config('contact.auth_middleware.admin'))) {
            $user = $this->getAuthenticatedUser();
            if (Gate::forUser($user)->denies('manage', $this->entity)) {
                throw new PermissionDeniedException();
            }

            foreach (config('contact.auth_middleware.admin') as $middleware) {
                $this->middleware($middleware['middleware'], ['except' => $middleware['except']]);
            }
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
    public function export(Request $request)
    {

        $user = $this->getAuthenticatedUser();
        // if (!$this->entity->ableToViewList($user)) {
        //     throw new PermissionDeniedException();
        // }

        $this->validator->isValid($request, 'RULE_EXPORT');

        $data = $request->all();
        $orders = $this->getReportContacts($request);

        $args = [
            'data' => $orders,
            'label' => $request->label ? $data['label'] : 'Orders',
            'extension' => $request->extension ? $data['extension'] : 'Xlsx',
        ];
        $export = new Export($args);
        $url = $export->export();

        return $this->response->array(['url' => $url]);
    }

    private function getReportContacts(Request $request)
    {
        $fields = [
            'contacts.email as `Email`',
            'contacts.full_name as `Họ và tên`',
            'contacts.first_name as `Tên`',
            'contacts.last_name as `Họ`',
            'contacts.phone_number as `Số điện thoại`',
            'contacts.address as `Địa chỉ chi tiết`',
            // 'contacts.province as `Thành phố`'  ,
            // 'contacts.district as `Quận`',
            // 'contacts.ward as `Phường`',
            'contacts.note as `Ghi chú`',
            // 'orders.status as `Trạng thái đơn hàng`',
            // '(case when status = 1 then "Đã Export"  when export_status = 0 then "Chưa Export" end) as `Trạng Thái Export`',
            // 'users.username as `Người tạo`',

        ];
        $fields = implode(', ', $fields);

        $query = $this->entity->query();
        $query = $query->select(DB::raw($fields));
        $query = $this->getStatus($request, $query);
        $query = $this->getType($request, $query);

        $query = $this->getFromDate($request, $query);
        $query = $this->getToDate($request, $query);

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['email', 'full_name', 'first_name', 'last_name'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $products = $query->get()->toArray();

        return $products;
    }

    public function index(Request $request)
    {
        $query = $this->entity->query();

        if (method_exists($this, 'withQuery')) {
            $query = $this->withQuery($query);
        }

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

        if (method_exists($this, 'withQuery')) {
            $query = $this->withQuery($query);
        }

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
    public function fomatDate($date)
    {

        $fomatDate = Carbon::createFromFormat('Y-m-d', $date);

        return $fomatDate;
    }

    public function field($request)
    {
        if ($request->has('field')) {
            if ($request->field === 'updated') {
                $field = 'contacts.updated_at';
            } elseif ($request->field === 'published') {
                $field = 'contacts.published_date';
            } elseif ($request->field === 'created') {
                $field = 'contacts.created_at';
            }
            return $field;
        } else {
            throw new Exception('field requied');
        }
    }

    public function getFromDate($request, $query)
    {
        if ($request->has('from')) {

            $field = $this->field($request);
            $form_date = $this->fomatDate($request->from);
            $query = $query->whereDate($field, '>=', $form_date);
        }
        return $query;
    }

    public function getToDate($request, $query)
    {
        if ($request->has('to')) {
            $field = $this->field($request);
            $to_date = $this->fomatDate($request->to);
            $query = $query->whereDate($field, '<=', $to_date);
        }
        return $query;
    }
}
