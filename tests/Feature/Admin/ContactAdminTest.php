<?php

namespace VCComponent\Laravel\Contact\Test\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Contact\Entities\Contact;
use VCComponent\Laravel\Contact\Test\TestCase;
use VCComponent\Laravel\User\Entities\User;

class ContactAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_create_contact()
    {
        $data = [
            'email' => 'test',
        ];
        $response = $this->json('POST', 'api/contact-management/admin/contacts', $data);
        $this->assertValidation($response, 'email', "The email must be a valid email address.");
        $data = [
            'email' => 'test@gmail.com',
            'full_name' => 'Full Name',
        ];

        $response = $this->json('POST', 'api/contact-management/admin/contacts', $data);
        $response->assertOk();
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('contacts', $data);
    }

    /**
     * @test
     */
    public function should_update_contact()
    {
        $contact = factory(Contact::class)->create();
        $data = [
            'email' => 'test',
        ];
        $response = $this->json('PUT', 'api/contact-management/admin/contacts/' . $contact->id, $data);
        $this->assertValidation($response, 'email', "The email must be a valid email address.");
        $data = [
            'email' => 'test@gmail.com',
            'full_name' => 'Name update',
        ];

        $response = $this->json('PUT', 'api/contact-management/admin/contacts/' . $contact->id, $data);

        $response->assertOk();
        $response->assertJson([
            'data' => [
                'full_name' => $data['full_name'],
            ],
        ]);
        $this->assertDatabaseHas('contacts', $data);
    }

    /**
     * @test
     */
    public function should_show_contact()
    {
        $contact = factory(Contact::class)->create();

        $response = $this->get('api/contact-management/admin/contacts/' . $contact->id);

        $response->assertOk();
        $response->assertJson(['data' => [
            'id' => $contact->id,
            'email' => $contact->email,
            'full_name' => $contact->full_name,
        ]]);
    }

    /**
     * @test
     */
    public function should_delete_contact()
    {
        $contact = factory(Contact::class)->create()->toArray();
        unset($contact['updated_at']);
        unset($contact['created_at']);
        $response = $this->delete('api/contact-management/admin/contacts/' . $contact['id']);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDeleted('contacts', $contact);
    }

    /**
     * @test
     */
    public function should_get_contact_all_admin()
    {
        $listContact = [];
        for ($i = 0; $i < 5; $i++) {
            $contact = factory(Contact::class)->create()->toArray();
            unset($contact['updated_at']);
            unset($contact['created_at']);
            array_push($listContact, $contact);
        }

        $response = $this->call('GET', 'api/contact-management/admin/contacts');
        $response->assertStatus(200);
        /* sort by id */
        $listIds = array_column($listContact, 'id');
        array_multisort($listIds, SORT_DESC, $listContact);
        $response->assertJson(['data' => $listContact]);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function should_update_status_contact_admin()
    {
        $contact = factory(Contact::class)->create()->toArray();
        unset($contact['updated_at']);
        unset($contact['created_at']);
        $this->assertDatabaseHas('contacts', $contact);
        $data = ['status' => 2];
        $response = $this->json('PUT', 'api/contact-management/admin/contacts/status/' . $contact['id'], $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/contact-management/admin/contacts/' . $contact['id']);

        $response->assertJson(['data' => [
            'status' => 2,
        ]]);

    }

    /**
     * @test
     */
    public function should_bulk_update_status_contact_admin()
    {
        $contacts = factory(Contact::class, 5)->create();

        $contacts = $contacts->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();
        $response = $this->json('GET', 'api/contact-management/admin/contacts');
        $response->assertJsonFragment(['status' => '1']);

        $listIds = array_column($contacts, 'id');

        $data = ['ids' => $listIds, 'status' => 2];
        $response = $this->json('PUT', 'api/contact-management/admin/contacts/status/bulk', $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response = $this->json('GET', 'api/contact-management/admin/contacts');
        $response->assertJsonFragment(['status' => '2']);
    }
    /**
     * @test
     */
    public function should_export_contact_admin()
    {
        $token = $this->loginToken();

        factory(Contact::class)->create();
        $data = [
            'label' => 'contact',
            'extension' => 'mp3',
        ];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->json('GET', 'api/contact-management/admin/contacts/exports', $data);
        $this->assertValidation($response, 'extension', 'The extension format is invalid.');

        $data = [
            'label' => 'contact',
            'extension' => 'xlsx',
        ];
        $response = $this->json('GET', 'api/contact-management/admin/contacts/exports', $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'url',
        ]);
    }
    protected function loginToken()
    {
        $dataLogin = ['username' => 'admin', 'password' => '123456789', 'email' => 'admin@test.com'];
        $user = factory(User::class)->make($dataLogin);
        $user->save();
        $login = $this->json('POST', 'api/user-management/login', $dataLogin);
        $token = $login->Json()['token'];
        return $token;

    }
}
