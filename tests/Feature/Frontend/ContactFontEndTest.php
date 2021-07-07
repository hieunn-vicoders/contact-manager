<?php

namespace VCComponent\Laravel\Contact\Test\Feature\Frontend;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Contact\Entities\Contact;
use VCComponent\Laravel\Contact\Test\TestCase;

class ContactFontEndTest extends TestCase
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
        $response = $this->json('POST', 'api/contact-management/contacts', $data);
        $this->assertValidation($response, 'email', "The email must be a valid email address.");
        $data = [
            'email' => 'test@gmail.com',
            'full_name' => 'Full Name',
        ];

        $response = $this->json('POST', 'api/contact-management/contacts', $data);

        $response->assertOk();
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('contacts', $data);
    }

    /**
     * @test
     */
    public function should_show_contact()
    {
        $contact = factory(Contact::class)->create();

        $response = $this->get('api/contact-management/contacts/' . $contact->id);

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
    public function should_get_contact_all()
    {
        $listContact = [];
        for ($i = 0; $i < 5; $i++) {
            $contact = factory(Contact::class)->create()->toArray();
            unset($contact['updated_at']);
            unset($contact['created_at']);
            array_push($listContact, $contact);
        }

        $response = $this->call('GET', 'api/contact-management/contacts');
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
}
