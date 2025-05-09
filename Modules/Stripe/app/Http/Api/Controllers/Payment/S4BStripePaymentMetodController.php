<?php

namespace Modules\Stripe\app\Http\Api\Controllers\Payment;

use App\Http\Controllers\S4BBaseController;
use Modules\Stripe\app\Http\Api\Services\S4BStripeService;
use Illuminate\Http\Request;
use Modules\Stripe\app\Http\Api\Controllers\Utilities\S4BStripeUtilities;

class S4BStripePaymentMetodController extends S4BBaseController
{
    protected $S4BStripeService;
    protected $S4BStripeUtilities;


    public function __construct(S4BStripeService $stripeService, S4BStripeUtilities $stripeUtilities)
    {
        $this->S4BStripeService = $stripeService;
        $this->S4BStripeUtilities = $stripeUtilities;
    }

    public function S4BPostPaymentMethod(Request $request)
    {
        try {
            $S4BCustomerId = $request->customerId;
            $S4BPayment = $this->S4BStripeService->S4BGetSavedCards($S4BCustomerId);

            return $this->S4BSendResponse($S4BPayment, 'Metodos de pagos correcto.');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
    }

    public function S4BAddPaymentMethod(Request $request)
    {
        try {
            $S4BCustomerId = $request->customerId;
            $S4BPaymentId = $request->paymentId;

            $S4BPayment = $this->S4BStripeService->S4BAddPaymentMethod($S4BCustomerId, $S4BPaymentId);

            return $this->S4BSendResponse($S4BPayment, 'Metodo de pago agregado y vinculado correctamente.');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
    }

    public function S4BAddCardPaymentMethod(Request $request)
    {
        try {
            $S4BStripeId = 'cus_RB6jvmea5u8gkC'; //costumerId $S4BStripeId

            // Inicializar un arreglo para almacenar errores
            $errors = [];

            // Validar cada campo y almacenar el error si existe
            $validationCardNumber = $this->S4BStripeUtilities->S4BValidateCardNumber($request['card_number']);
            if ($validationCardNumber !== true) {
                $errors['card_number'] = $validationCardNumber;
            }

            $validationExpirationMonth = $this->S4BStripeUtilities->S4BValidateExpirationMonth($request['expiration_month']);
            if ($validationExpirationMonth !== true) {
                $errors['expiration_month'] = $validationExpirationMonth;
            }

            $validationExpirationYear = $this->S4BStripeUtilities->S4BValidateExpirationYear($request['expiration_year']);
            if ($validationExpirationYear !== true) {
                $errors['expiration_year'] = $validationExpirationYear;
            }

            $validationDate = $this->S4BStripeUtilities->S4BValidateExpirationDate($request['expiration_month'], $request['expiration_year']);
            if ($validationDate !== true) {
                $errors['expiration_date'] = $validationDate;
            }

            $validationCVC = $this->S4BStripeUtilities->S4BValidateCVC($request['cvc']);
            if ($validationCVC !== true) {
                $errors['cvc'] = $validationCVC;
            }

            // Verificar si hay errores
            if (! empty($errors)) {
                // Retornar los errores
                return $this->S4BSendError('Validation failed', ['errors' => $errors]);
            }

            // Si no hay errores, proceder con la lógica adicional
            $S4BPayment = $this->S4BStripeService->S4BAddCard($S4BStripeId, $request['card_number'], $request['expiration_month'], $request['expiration_year'], $request['cvc']);

            return $this->S4BSendResponse($S4BPayment, 'Metodo de pago agregado y vinculado correctamente.');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
    }

    public function S4BRemovePaymentMethod(Request $request)
    {
        try {
            $S4BPaymentId = 'pm_1PZ98cLyj74BldhkuDritGlQ';
            // $S4BPaymentId = 'card_1QQa6eLyj74BldhkqErgGNFG'; //paymentId
            $S4BPayment = $this->S4BStripeService->S4BRemovePaymentMethod($S4BPaymentId);

            return $this->S4BSendResponse($S4BPayment, 'Metodo de pago removido correctamente.');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
    }

    public function S4BGeS4BillingAddressMethod(Request $request)
    {
        try {
            $S4BStripeId = 'cus_RB6jvmea5u8gkC'; //costomerId
            $S4BBillingAddress = $this->S4BStripeService->S4BGeS4BillingAddress($S4BStripeId);

            return $this->S4BSendResponse($S4BBillingAddress, 'Dirección de Factura obtenida con éxito.');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
    }

    public function S4BAddBillingAddressMethod(Request $request)
    {
        try {
            $S4BStripeId = 'cus_RB6jvmea5u8gkC'; // Customer ID

            // Obtenemos la dirección del request
            $data = $request->input('address'); // Address contiene la información del formulario
            $billingAddress = collect($data)->toArray();

            // Validamos la dirección utilizando S4BValidateAddress
            $addressValidation = $this->S4BStripeUtilities->S4BValidateAddress($billingAddress);

            if (! empty($addressValidation)) {
                // Enviamos los errores si la validación falla
                return $this->S4BSendError(
                    'Ha habido un error al validar la dirección de facturación',
                    ['errors' => $addressValidation],
                    422
                );
            }

            // Si pasa la validación, llamamos al servicio para agregar la dirección
            $S4BAddBillingAddress = $this->S4BStripeService->S4BAddBillingAddress($S4BStripeId, $billingAddress);

            return $this->S4BSendResponse($S4BAddBillingAddress, 'Dirección de factura agregada exitosamente');
        } catch (\Exception $e) {
            // Manejo de excepciones
            return $this->S4BSendError('Ha ocurrido un error inesperado', ['error' => $e->getMessage()], 500);
        }
    }

    public function S4BUpdateBillingAddressMethod(Request $request)
    {
        try {
            $S4BStripeId = 'cus_RB6jvmea5u8gkC'; // Customer ID

            // Obtenemos la dirección del request
            $data = $request->input('address'); // Address contiene la información del formulario
            $billingAddress = collect($data)->toArray();

            // Validamos la dirección utilizando S4BValidateAddress
            $addressValidation = $this->S4BStripeUtilities->S4BValidateAddress($billingAddress);

            if (! empty($addressValidation)) {
                // Enviamos los errores si la validación falla
                return $this->S4BSendError(
                    'Ha habido un error al validar la dirección de facturación',
                    ['errors' => $addressValidation],
                    422
                );
            }

            // Si pasa la validación, llamamos al servicio para agregar la dirección
            $S4BAddBillingAddress = $this->S4BStripeService->S4BAddBillingAddress($S4BStripeId, $billingAddress);

            return $this->S4BSendResponse($S4BAddBillingAddress, 'Dirección de factura modificada exitosamente');
        } catch (\Exception $e) {
            // Manejo de excepciones
            return $this->S4BSendError('Ha ocurrido un error inesperado', ['error' => $e->getMessage()], 500);
        }
    }

    public function S4BRemoveBillingAddressMethod(Request $request)
    {
        try {
            $S4BStripeId = 'cus_RB6jvmea5u8gkC'; // Customer ID

            $S4BRemoveBillingAddress = $this->S4BStripeService->S4BRemoveBillingAddress($S4BStripeId);
            // S4BRemoveBillingAddress(string $S4BCustomerId)

            return $this->S4BSendResponse($S4BRemoveBillingAddress, 'Dirección de factura removida exitosamente');
        } catch (\Exception $e) {
            // Manejo de excepciones
            return $this->S4BSendError('Ha ocurrido un error inesperado', ['error' => $e->getMessage()], 500);
        }
    }

    public function createSubscriptionForMultipleProducts(Request $request)
    {
        try {
            $S4BCustomerId = $request->customerId;
            $productPriceIds = $request->productPriceIds; // Customer ID

            $S4BCreateSubcription = $this->S4BStripeService->createSubscriptionForMultipleProducts($S4BCustomerId, $productPriceIds);

            return $this->S4BSendResponse($S4BCreateSubcription, 'Pago correcto');
        } catch (\Exception $e) {
            // Manejo de excepciones
            return $this->S4BSendError('Ha ocurrido un error inesperado', ['error' => $e->getMessage()], 500);
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

    public function S4BPostProcessPaymentMethod(Request $request)
    {
        try {
            $S4BCustomerId = $request->customerId;
            $amount = $request->amount;
            $currency = $request->currency;
            $paymentMethodId = $request->paymentMethodId;
            $priceId = $request->priceId;
            $S4BProduct = $this->S4BStripeService->S4BProcessPayment($S4BCustomerId, $paymentMethodId, $amount, $currency, $priceId);

            return $this->S4BSendResponse($S4BProduct, 'Pago correcto.');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
    }
}
