<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\User;

class ChecklistTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreate()
    {
        $user = User::find(1);

        $response = $this->actingAs($user)
            ->call('POST', '/checklists', [
                'data'=> [
                    "attributes" => [
                        "object_domain"=> "contact",
                        "object_id"=> "1",
                        "due"=> "2019-01-25T07:50:14+00:00",
                        "urgency"=> 1,
                        "description"=> "Need to verify this guy house.",
                        "items"=> [
                            "Visit his house",
                            "Capture a photo",
                            "Meet him on the house"
                        ],
                        "task_id"=> "123"
                    ]
                ]
            ]);

        $this->assertEquals(201, $response->status());
    }

    public function testDelete(){
        $user = User::find(1);

        $response = $this->actingAs($user)->call('DELETE', '/checklists/1');

        $this->assertEquals(204, $response->status());
    }

    public function testIndex()
    {
        $user = User::find(1);

        $response = $this->actingAs($user)->call('GET', '/checklists');

        $this->assertEquals(200, $response->status());
    }

    public function testShow()
    {
        $user = User::find(1);

        $response = $this->actingAs($user)->call('GET', '/checklists/1');

        $this->assertEquals(200, $response->status());
    }

    public function testUpdate(){
        $user = User::find(1);

        $response = $this->actingAs($user)
            ->call('PATCH', '/checklists/1', [
                "data" => [
                    "type"=> "checklists",
                    "id"=> 1,
                    "attributes"=> [
                        "object_domain"=> "contact",
                        "object_id"=> "1",
                        "description"=> "Need to verify this guy house.",
                        "is_completed"=> false,
                        "completed_at"=> null,
                        "created_at"=> "2018-01-25T07:50:14+00:00"
                    ],
                    "links"=> [
                        "self"=> "https://dev-kong.command-api.kw.com/checklists/50127"
                    ]
                ]
            ]);

        $this->assertEquals(200, $response->status());
    }



}
