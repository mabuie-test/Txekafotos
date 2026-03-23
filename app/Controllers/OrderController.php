<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Services\OrderService;
use App\Services\PaymentService;

class OrderController extends Controller
{
    public function create(): void
    {
        $this->view('orders/create', [
            'serviceTypes' => OrderService::SERVICE_TYPES,
            'flash' => App::getInstance()?->session()->getFlash('message'),
            'errors' => App::getInstance()?->session()->getFlash('errors', []),
            'old' => App::getInstance()?->session()->getFlash('old', []),
            'maxExtraImages' => (int) config('services.upload.max_extra_images', 5),
            'maxUploadMb' => (int) config('services.upload.max_upload_mb', 5),
        ]);
    }

    public function store(): void
    {
        $data = [
            'client_name' => (string) $this->request->input('client_name'),
            'client_phone' => (string) $this->request->input('client_phone'),
            'service_type' => (string) $this->request->input('service_type'),
            'description' => (string) $this->request->input('description'),
            'terms' => $this->request->input('terms'),
        ];

        App::getInstance()?->session()->flash('old', $data);

        try {
            $normalizedPhone = (new PaymentService())->normalizePhone($data['client_phone']);
            if (!(new PaymentService())->validateMozambiqueMpesaNumber($normalizedPhone)) {
                throw new \RuntimeException('Use um número M-Pesa válido de Moçambique para prosseguir.');
            }
            $data['client_phone'] = $normalizedPhone;

            if (!$this->request->files('primary_image')) {
                throw new \RuntimeException('A foto principal é obrigatória.');
            }

            $orderId = (new OrderService())->createOrder($data, $this->request->files('primary_image'), $this->request->files('extra_images'));
            App::getInstance()?->session()->flash('message', 'Pedido criado com sucesso. Continue para o pagamento M-Pesa.');
            $this->redirect("/pedido/{$orderId}/pagamento");
        } catch (\Throwable $exception) {
            App::getInstance()?->session()->flash('errors', ['general' => [$exception->getMessage()]]);
            $this->redirect('/pedido/criar');
        }
    }
}
