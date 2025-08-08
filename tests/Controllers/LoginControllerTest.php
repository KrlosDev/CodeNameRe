<?php

namespace Tests\Controllers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\KitTestCase;

class LoginControllerTest extends KitTestCase
{
    use DatabaseTransactions;

    /********* CONSTRUCTORA *********/

    public function test_it_a_constructora_can_successfully_log_in()
    {
        $user = $this->getUserConstructora();

        $data = [
            'name' => $user->name,
            'password' => $user->password,
        ];

        $this->post('login', $data)
            ->seeStatusCode(302)
            ->assertRedirectedTo('');
    }

    public function test_it_a_constructora_cannot_successfully_log_in_on_wrong_credentials()
    {
        $user = $this->getUserConstructora();

        $data = [
            'name' => $user->name,
            'password' => 'q12e21rt3113t3',
        ];

        $response = $this->post('login', $data)
            ->seeStatusCode(302);

        $this->assertStringEndsWith('login', $response->currentUri);
    }

    /********* BROKER *********/

    public function test_it_a_broker_can_successfully_log_in()
    {
        $user = $this->getUserBroker();

        $data = [
            'name' => $user->name,
            'password' => $user->password,
        ];

        $this->post('login', $data)
            ->seeStatusCode(302)
            ->assertRedirectedTo('');
    }

    public function test_it_a_broker_cannot_successfully_log_in_on_wrong_credentials()
    {
        $user = $this->getUserBroker();

        $data = [
            'name' => $user->name,
            'password' => 'q12e21rt3113t3',
        ];

        $response = $this->post('login', $data)
            ->seeStatusCode(302);

        $this->assertStringEndsWith('login', $response->currentUri);
    }

    /********* EJECUTIVO VENTAS *********/

    public function test_it_a_ej_ventas_can_successfully_log_in()
    {
        $user = $this->getUserEjVentas();

        $data = [
            'name' => $user->name,
            'password' => $user->password,
        ];

        $this->post('login', $data)
            ->seeStatusCode(302)
            ->assertRedirectedTo('');
    }

    public function test_it_a_ej_ventas_cannot_successfully_log_in_on_wrong_credentials()
    {
        $user = $this->getUserEjVentas();

        $data = [
            'name' => $user->name,
            'password' => 'q12e21rt3113t3',
        ];

        $response = $this->post('login', $data)
            ->seeStatusCode(302);

        $this->assertStringEndsWith('login', $response->currentUri);
    }

    /********* EJECUTIVO BANCOS *********/

    public function test_it_a_ej_bancos_can_successfully_log_in()
    {
        $user = $this->getUserEjBancos();

        $data = [
            'name' => $user->name,
            'password' => $user->password,
        ];

        $this->post('login', $data)
            ->seeStatusCode(302)
            ->assertRedirectedTo('');
    }

    public function test_it_a_ej_bancos_cannot_successfully_log_in_on_wrong_credentials()
    {
        $user = $this->getUserEjBancos();

        $data = [
            'name' => $user->name,
            'password' => 'q12e21rt3113t3',
        ];

        $response = $this->post('login', $data)
            ->seeStatusCode(302);

        $this->assertStringEndsWith('login', $response->currentUri);
    }

    /********* CLIENTE *********/

    public function test_it_a_cliente_can_successfully_log_in()
    {
        $user = $this->getUserCliente();

        $data = [
            'name' => $user->name,
            'password' => $user->password,
        ];

        $this->post('login', $data)
            ->seeStatusCode(302)
            ->assertRedirectedTo('');
    }

    public function test_it_a_cliente_cannot_successfully_log_in_on_wrong_credentials()
    {
        $user = $this->getUserCliente();

        $data = [
            'name' => $user->name,
            'password' => 'q12e21rt3113t3',
        ];

        $response = $this->post('login', $data)
            ->seeStatusCode(302);

        $this->assertStringEndsWith('login', $response->currentUri);
    }
}
