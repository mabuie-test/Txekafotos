<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\OrderService;
use App\Services\PaymentService;

class OrderController extends Controller
{
    public function create(): void
    {
        $this->view('orders/create', [
            'serviceTypes' => OrderService::SERVICE_TYPES,
            'errors' => flash_message('errors', []),
            'maxExtraImages' => (int) config('services.upload.max_extra_images', 5),
            'maxUploadMb' => (int) config('services.upload.max_upload_mb', 5),
        ]);
    }

    public function store(): void
    {
        $data = [
            'client_name' => $this->request->string('client_name'),
            'client_phone' => $this->request->string('client_phone'),
            'service_type' => $this->request->string('service_type'),
            'description' => $this->request->string('description'),
            'terms' => $this->request->input('terms'),
        ];

        $this->session()->flash('old', $data);

        try {
            $paymentService = new PaymentService();
            $normalizedPhone = $paymentService->normalizePhone($data['client_phone']);
            if (!$paymentService->validateMozambiqueMpesaNumber($normalizedPhone)) {
                throw new \RuntimeException('Use um número M-Pesa válido de Moçambique para prosseguir.');
            }
            $data['client_phone'] = $normalizedPhone;

            if (!$this->request->files('primary_image')) {
                throw new \RuntimeException('A foto principal é obrigatória.');
            }

            $orderId = (new OrderService())->createOrder($data, $this->request->files('primary_image'), $this->request->files('extra_images'));
            $this->redirectWithFlash("/pedido/{$orderId}/pagamento", [
                'success' => 'Pedido criado com sucesso. Continue para o pagamento M-Pesa.',
            ]);
        } catch (\Throwable $exception) {
            $this->redirectWithFlash('/pedido/criar', [
                'errors' => ['general' => [$exception->getMessage()]],
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
