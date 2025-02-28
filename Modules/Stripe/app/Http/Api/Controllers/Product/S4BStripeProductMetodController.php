<?php

namespace Modules\Stripe\App\Http\Api\Controllers\Product;

use App\Http\Controllers\S4BBaseController;
use Modules\Stripe\App\Http\Api\Services\S4BStripeService;
use Illuminate\Http\Request;

class S4BStripeProductMetodController extends S4BBaseController
{
    protected $S4BStripeService;

    public function __construct(S4BStripeService $stripeService)
    {
        $this->S4BStripeService = $stripeService;
    }

    public function S4BGetProductMethod(Request $request)
    {
        try {
            $S4BIdProduct = $request->productId;
            $S4BProduct = $this->S4BStripeService->S4BGetProductDetailsById($S4BIdProduct);

            return $this->S4BSendResponse($S4BProduct, 'Producto obtenido correctamente.');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
    }

    public function S4BGetAllActiveProducts(Request $request)
    {
        try {
            $S4BProduct = $this->S4BStripeService->S4BGetAllActiveProducts();

            return $this->S4BSendResponse($S4BProduct, 'Lista de todos los productos activos.');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
    }

    public function S4BPostProductsByCustomer(Request $request)
    {
        try {
            $S4BCustomerId = $request->customerId;
            $S4BProduct = $this->S4BStripeService->S4BPostProductsByCustomer($S4BCustomerId);

            return $this->S4BSendResponse($S4BProduct, 'Lista de productos contratados por el cliente.');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
    }

    public function S4BPostProductsActiveInactive(Request $request)
    {
        try {
            $S4BCustomerId = $request->customerId;
            $S4BProduct = $this->S4BStripeService->S4BPostSubscriptionsActiveInactive($S4BCustomerId);

            return $this->S4BSendResponse($S4BProduct, 'Lista de productos activos e inactivos su sucription');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
    }

    public function S4BGetUnpurchasedProducts(Request $request)
    {
        try {
            $S4BProduct = $this->S4BStripeService->S4BGetUnpurchasedProducts();

            return $this->S4BSendResponse($S4BProduct, 'Lista de productos no comprados por el usuario.');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
    }

    public function S4BPostInactiveSubscriptionsByCustomer(Request $request)
    {
        try {
            $S4BCustomerId = $request->customerId;
            $S4BProduct = $this->S4BStripeService->S4BPostInactiveSubscriptionsByCustomer($S4BCustomerId);

            return $this->S4BSendResponse($S4BProduct, 'Lista de suscripciones inactivas del cliente.');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
    }

    public function S4BPostCreateSetupIntent(Request $request)
    {
        try {
            $S4BCustomerId = $request->customerId;
            $S4BProduct = $this->S4BStripeService->S4BCreateSetupIntent($S4BCustomerId);

            return $this->S4BSendResponse($S4BProduct, 'SetupIntent creado correctamente.');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
    }
}
