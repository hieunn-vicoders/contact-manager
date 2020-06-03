<?php

namespace VCComponent\Laravel\Contact\Test\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Contact\Entities\Contact;
use VCComponent\Laravel\Contact\Test\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_create_contact()
    {
        $data = [
            'email'     => 'test@gmail.com',
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
    public function can_update_contact()
    {
        $contact = factory(Contact::class)->create();
        $data    = [
            'email'     => 'test@gmail.com',
            'full_name' => 'Full Name',
        ];

        $response = $this->json('PUT', 'api/contact-management/admin/contacts/' . $contact->id, $data);

        $response->assertOk();
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('contacts', $data);
    }

    /**
     * @test
     */
    public function can_show_contact()
    {
        $contact = factory(Contact::class)->create();

        $response = $this->get('api/contact-management/admin/contacts/' . $contact->id);

        $response->assertOk();
        $response->assertJson(['data' => [
            'id'        => $contact->id,
            'email'     => $contact->email,
            'full_name' => $contact->full_name,
        ]]);
    }

    /**
     * @test
     */
    public function can_delete_contact()
    {
        $contact = factory(Contact::class)->create();

        $response = $this->delete('api/contact-management/admin/contacts/' . $contact->id);

        $response->assertOk();
        $response->assertJson(['success' => true]);
    }
}
