<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Validator;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function create(): void
    {
        $this->view('orders/create', [
            'serviceTypes' => ['restauracao','montagem','inserir_pessoa','juntar_familiares','melhorar_qualidade','remover_fundo','colorizacao','outro'],
            'flash' => App::getInstance()?->session()->getFlash('message'),
            'errors' => App::getInstance()?->session()->getFlash('errors', []),
        ]);
    }

    public function store(): void
    {
        $validator = new Validator();
        $data = [
            'client_name' => (string) $this->request->input('client_name'),
            'client_phone' => (string) $this->request->input('client_phone'),
            'service_type' => (string) $this->request->input('service_type'),
            'description' => (string) $this->request->input('description'),
            'terms' => $this->request->input('terms'),
        ];

        $valid = $validator->validate($data, [
            'client_name' => ['required', 'string'],
            'client_phone' => ['required', 'string'],
            'description' => ['required', 'string'],
            'terms' => ['accepted'],
        ]);

        if (!$this->request->files('primary_image')) {
            $valid = false;
            $errors = $validator->errors();
            $errors['primary_image'][] = 'A foto principal é obrigatória.';
            App::getInstance()?->session()->flash('errors', $errors);
        }

        if (!$valid) {
            App::getInstance()?->session()->flash('errors', $validator->errors());
            $this->redirect('/pedido/criar');
        }

        try {
            $orderId = (new OrderService())->createOrder($data, $this->request->files('primary_image'), $this->request->files('extra_images'));
            App::getInstance()?->session()->flash('message', 'Pedido criado com sucesso. Continue para o pagamento.');
            $this->redirect("/pedido/{$orderId}/pagamento");
        } catch (\Throwable $exception) {
            App::getInstance()?->session()->flash('errors', ['general' => [$exception->getMessage()]]);
            $this->redirect('/pedido/criar');
        }
    }
}
