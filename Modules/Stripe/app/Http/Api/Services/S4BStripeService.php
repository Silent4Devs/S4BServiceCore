<?php

namespace Modules\Stripe\App\Http\Api\Services;

use Exception;
use Stripe\Stripe;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use Stripe\Product;
use Stripe\Customer;

class S4BStripeService
{
    protected $S4BStripeClient;

    public function __construct(Request $request)
    {
        $stripeKey = $request->header('STRIPE_SECRET');

        if (!$stripeKey) {
            throw new \Exception('STRIPE_KEY o STRIPE_SECRET, no se encuentra en los headers');
        }

        Stripe::setApiKey($stripeKey);
        $this->S4BStripeClient = new StripeClient($stripeKey);
    }

    public function createCharge($amount, $currency, $source, $description)
    {
        return $this->S4BStripeClient->charges->create([
            'amount' => $amount,
            'currency' => $currency,
            'source' => $source,
            'description' => $description,
        ]);
    }

    /**
     * Obtiene la instancia de StripeClient.
     */
    public function S4BGetStripeClient(): StripeClient
    {
        return $this->S4BStripeClient;
    }

    /**
     * Obtiene un cliente de Stripe por su ID.
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function S4BGetCustomerById(string $S4BCustomerId): Customer
    {
        try {
            return $this->S4BStripeClient->customers->retrieve($S4BCustomerId);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene las suscripciones de un cliente de Stripe.
     *
     * @return \Stripe\Collection
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function S4BGetCustomerSubscriptions(string $S4BCustomerId)
    {
        try {
            return $this->S4BStripeClient->subscriptions->all(['customer' => $S4BCustomerId]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener las suscripciones del cliente: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene el detalle de un producto de Stripe por su ID.
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function S4BGetProductDetailsById(string $S4BProductId): Product
    {
        try {
            return $this->S4BStripeClient->products->retrieve($S4BProductId);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener el producto: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene las suscripciones que no están activas para un cliente.
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function S4BGetInactiveSubscriptionsByCustomer(string $S4BCustomerId): array
    {
        try {
            \Stripe\Customer::retrieve($S4BCustomerId);

            $S4BSubscriptions = $this->S4BGetCustomerSubscriptions($S4BCustomerId);
            $subscribedProductIds = [];

            foreach ($S4BSubscriptions->data as $S4BSubscription) {
                foreach ($S4BSubscription->items->data as $S4BItem) {
                    $subscribedProductIds[] = $S4BItem->price->product;
                }
            }

            $S4BProducts = \Stripe\Product::all(['active' => true]);

            $unsubscribedProducts = [];

            foreach ($S4BProducts->data as $product) {
                if (! in_array($product->id, $subscribedProductIds)) {
                    $unsubscribedProducts[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->description,
                        'active' => $product->active,
                        'images' => $product->images,
                        'img' => $product->metadata['img'] ?? null,
                    ];
                }
            }

            return $unsubscribedProducts;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener los productos no suscritos activos del cliente: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene todos los productos de un cliente a través de sus suscripciones.
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function S4BGetProductsByCustomer(string $S4BCustomerId): array
    {
        try {
            \Stripe\Customer::retrieve($S4BCustomerId);

            $S4BSubscriptions = $this->S4BGetCustomerSubscriptions($S4BCustomerId);
            $S4BProducts = [];

            foreach ($S4BSubscriptions->data as $S4BSubscription) {
                foreach ($S4BSubscription->items->data as $S4BItem) {

                    $product = \Stripe\Product::retrieve($S4BItem->price->product);
                    $price = $S4BItem->price;

                    $paymentMethod = null;
                    $cardDetails = null;

                    if ($S4BSubscription->default_payment_method) {
                        $paymentMethod = \Stripe\PaymentMethod::retrieve($S4BSubscription->default_payment_method);
                        $cardDetails = $paymentMethod->card ?? null;
                    }

                    $S4BProducts[] = [
                        'active' => $product->active,
                        'id' => $product->id,
                        'images' => $product->images,
                        'name' => $product->name,
                        'description' => $product->description,
                        'img' => $product->metadata['img'] ?? null,
                        'price' => [
                            'amount' => $price->unit_amount / 100,
                            'currency' => strtoupper($price->currency),
                            'interval' => $price->recurring->interval ?? null,
                        ],
                        'payment_method' => $paymentMethod->type ?? 'unknown',
                        'last4' => $cardDetails ? $cardDetails->last4 : null,
                        'subscription_start' => $S4BSubscription->start_date ? date('Y-m-d', $S4BSubscription->start_date) : null,
                        'subscription_end' => $S4BSubscription->current_period_end ? date('Y-m-d', $S4BSubscription->current_period_end) : null,
                    ];
                }
            }

            return $S4BProducts;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener los productos del cliente: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene todos los productos activos de Stripe.
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function S4BGetAllActiveProducts(): array
    {
        try {
            $allProducts = \Stripe\Product::all(['active' => true]);
            $products = [];

            foreach ($allProducts->data as $product) {
                $products[] = [
                    'active' => $product->active,
                    'id' => $product->id,
                    'images' => $product->images,
                    'name' => $product->name,
                    'description' => $product->description,
                    'img' => $product->metadata['img'] ?? null,
                ];
            }

            return $products;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener los productos activos: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene los productos no adquiridos por un cliente a través de sus suscripciones.
     *
     * @param  string  $S4BCustomerId
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function S4BGetUnpurchasedProducts(): array
    {
        try {
            $allProducts = \Stripe\Product::all(['active' => true]);
            $unpurchasedProducts = [];
            $totalMonthlyAmount = 0;
            $totalYearlyAmount = 0;

            foreach ($allProducts->data as $product) {
                $prices = \Stripe\Price::all(['product' => $product->id]);
                $formattedPrices = [];

                foreach ($prices->data as $price) {
                    $formattedPrice = [
                        'id' => $price->id,
                        'amount' => $price->unit_amount / 100,
                        'currency' => strtoupper($price->currency),
                        'interval' => $price->recurring->interval ?? null,
                    ];

                    if ($price->recurring) {
                        if ($price->recurring->interval === 'month') {
                            $totalMonthlyAmount += $formattedPrice['amount'];
                        } elseif ($price->recurring->interval === 'year') {
                            $totalYearlyAmount += $formattedPrice['amount'];
                        }
                    }

                    $formattedPrices[] = $formattedPrice;
                }

                $unpurchasedProducts[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'prices' => $formattedPrices,
                    'img' => $product->metadata['img'] ?? null,
                ];
            }

            return [
                'unpurchased_products' => $unpurchasedProducts,
                'total_monthly_amount' => $totalMonthlyAmount,
                'total_yearly_amount' => $totalYearlyAmount,
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener los productos: ' . $e->getMessage());
        }
    }

    /**
     * Verifica el estado de suscripciones de un cliente para determinar si tiene acceso a los módulos válidos.
     *
     * Este método revisa las suscripciones activas de un cliente y valida si alguna de ellas coincide con los módulos
     * válidos proporcionados. Si una suscripción activa pertenece a un módulo válido, el método devuelve `true`,
     * de lo contrario, devuelve `false`. Si no hay suscripciones o las suscripciones no son válidas, también se devuelve `false`.
     *
     * @param  array  $S4BSuscripciones  Listado de las suscripciones del cliente.
     * @param  array  $S4BModulosValidos  Módulos que se consideran válidos para el acceso.
     * @return bool `true` si el cliente tiene acceso a uno de los módulos válidos, `false` en caso contrario.
     *
     * @throws \Throwable En caso de error inesperado, se aborta con un error 403.
     */
    public function S4BTenantSubscriptionStatus($S4BSuscripciones, $S4BModulosValidos)
    {
        try {
            if (! empty($S4BSuscripciones) && is_array($S4BSuscripciones)) {
                foreach ($S4BSuscripciones as $S4BSuscripcion) {
                    if (in_array($S4BSuscripcion['name'], $S4BModulosValidos) && $S4BSuscripcion['active'] === true) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        } catch (\Throwable $S4BTh) {
            abort(403);
        }
    }

    public function S4BTenantSubscriptionStatusOnPremise($S4BModulosValidos)
    {
        try {
            $clientKey = env('CLIENT_KEY');
            $clientKeyApi = env('CLIENT_KEYAPI');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $clientKeyApi); //"http://192.168.9.113/api/onPremise/clientes");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['uuid' => $clientKey]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                curl_close($ch);
                return false;
                // return response()->json([
                //     'message' => 'Error al obtener los datos de la API externa',
                //     'error' => curl_error($ch),
                // ], 500);
            }

            curl_close($ch);

            $jsonData = json_decode($response, true);
            if (!empty($jsonData) && is_array($jsonData)) {
                foreach ($jsonData as $cliente) {
                    if ($cliente['Estatus'] === true) {
                        if (!empty($cliente['modulos']) && is_array($cliente['modulos'])) {
                            foreach ($cliente['modulos'] as $modulo) {
                                if (in_array($modulo['nombre_catalogo'], $S4BModulosValidos) && $modulo['estatus'] === true) {
                                    return true;
                                }
                            }
                        }
                    }
                }
                return false;
            }
            return false;
        } catch (\Throwable $S4BTh) {
            abort(403, 'Error en la verificación de suscripción');
        }
    }


    /**
     * Obtiene el historial de compras de un cliente.
     *
     * @return \Stripe\Collection
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function S4BGetPurchaseHistory(string $S4BCustomerId)
    {
        try {
            $paymentIntents = $this->S4BStripeClient->paymentIntents->all(['customer' => $S4BCustomerId]);

            $purchaseHistory = [];
            foreach ($paymentIntents->data as $paymentIntent) {
                $charge = $paymentIntent->charges->data[0] ?? null;

                $paymentDetails = $charge->payment_method_details ?? null;
                $last4 = $paymentDetails->card->last4 ?? 'N/A';
                $productName = $paymentIntent->metadata['product_name'] ?? 'Producto desconocido';

                $purchaseHistory[] = [
                    'amount' => $paymentIntent->amount / 100,
                    'currency' => strtoupper($paymentIntent->currency),
                    'date' => date('Y-m-d H:i:s', $paymentIntent->created),
                    'product_name' => $productName,
                    'payment_method' => '**** **** **** ' . $last4,
                ];
            }

            return $purchaseHistory;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener el historial de compras: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene las tarjetas guardadas de un cliente.
     *
     * @return \Stripe\Collection
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function S4BGetSavedCards(string $S4BCustomerId)
    {
        try {
            $cards = $this->S4BStripeClient->paymentMethods->all([
                'customer' => $S4BCustomerId,
                'type' => 'card',
            ]);

            $savedCards = [];
            foreach ($cards->data as $card) {
                $savedCards[] = [
                    'type' => ucfirst($card->card->brand),
                    'last4' => $card->card->last4,
                    'added_date' => date('Y-m-d H:i:s', $card->created),
                    'is_active' => $card->card->checks->cvc_check === 'pass',
                ];
            }

            return $savedCards;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener las tarjetas guardadas: ' . $e->getMessage());
        }
    }

    /**
     * Agrega un nuevo método de pago para un cliente. se necesita agregar primero una tarjeta para asiciarlo
     *
     * @return \Stripe\PaymentMethod
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function S4BAddPaymentMethod(string $S4BCustomerId, string $S4BPaymentMethodId)
    {
        try {
            $this->S4BStripeClient->paymentMethods->attach($S4BPaymentMethodId, [
                'customer' => $S4BCustomerId,
            ]);

            return $this->S4BStripeClient->paymentMethods->retrieve($S4BPaymentMethodId);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al agregar el método de pago: ' . $e->getMessage());
        }
    }

    /**
     * Agrega una tarjeta como método de pago para un cliente. agrega y aoscia metodo de pago
     *
     * @param  string  $S4BCustomerId  El ID del cliente en Stripe.
     * @param  string  $cardNumber  El número de la tarjeta de crédito.
     * @param  int  $expMonth  El mes de expiración de la tarjeta (1-12).
     * @param  int  $expYear  El año de expiración de la tarjeta (4 dígitos).
     * @param  string  $cvc  El código de seguridad de la tarjeta.
     * @return \Stripe\PaymentMethod
     *
     * @throws Exception Si ocurre un error al agregar la tarjeta.
     */
    public function S4BAddCard(string $S4BCustomerId, string $cardNumber, int $expMonth, int $expYear, string $cvc)
    {
        try {
            // Crear el PaymentMethod para la tarjeta
            $paymentMethod = $this->S4BStripeClient->paymentMethods->create([
                'type' => 'card',
                'card' => [
                    'number' => $cardNumber,
                    'exp_month' => $expMonth,
                    'exp_year' => $expYear,
                    'cvc' => $cvc,
                ],
            ]);

            // Asociar el PaymentMethod al cliente
            $this->S4BStripeClient->paymentMethods->attach($paymentMethod->id, [
                'customer' => $S4BCustomerId,
            ]);

            return $this->S4BStripeClient->paymentMethods->retrieve($paymentMethod->id);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al agregar la tarjeta: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un método de pago de un cliente.
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function S4BRemovePaymentMethod(string $S4BPaymentMethodId)
    {
        try {
            $this->S4BStripeClient->paymentMethods->detach($S4BPaymentMethodId);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al eliminar el método de pago: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene la dirección de facturación de un cliente.
     *
     * @return array
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    // : array Se comento ya que la Api retorna un objeto
    public function S4BGeS4BillingAddress(string $S4BCustomerId)
    {
        try {
            $S4BCustomer = $this->S4BGetCustomerById($S4BCustomerId);

            return $S4BCustomer->address ?: [];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener la dirección de facturación: ' . $e->getMessage());
        }
    }

    /**
     * Agrega una nueva dirección de facturación para un cliente.
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function S4BAddBillingAddress(string $S4BCustomerId, array $S4BBillingAddress): Customer
    {
        return $this->S4BUpdateBillingAddress($S4BCustomerId, $S4BBillingAddress);
    }

    /**
     * Elimina la dirección de facturación de un cliente.
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function S4BRemoveBillingAddress(string $S4BCustomerId): Customer
    {
        try {
            return $this->S4BStripeClient->customers->update($S4BCustomerId, ['address' => null]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al eliminar la dirección de facturación: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza la dirección de facturación de un cliente.
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function S4BUpdateBillingAddress(string $S4BCustomerId, array $S4BBillingAddress): Customer
    {
        try {
            return $this->S4BStripeClient->customers->update($S4BCustomerId, ['address' => $S4BBillingAddress]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al actualizar la dirección de facturación: ' . $e->getMessage());
        }
    }

    /**
     * Crea una suscripción para varios productos de un cliente en Stripe.
     *
     * Este método permite crear una suscripción con múltiples productos seleccionados
     * por un cliente en Stripe. Los productos se agregan usando los IDs de precio de
     * cada producto, y la suscripción será configurada según el intervalo de pago
     * definido en los precios de los productos.
     *
     * @param  string  $S4BCustomerId  El ID del cliente en Stripe.
     * @param  array  $productPriceIds  Un arreglo de IDs de precios de los productos a suscribir.
     * @return array Un arreglo con el ID de la sesión de Stripe y la URL para el pago en Stripe.
     *
     * @throws \Stripe\Exception\ApiErrorException Si ocurre un error en la API de Stripe.
     */
    public function createSubscriptionForMultipleProducts(string $S4BCustomerId, array $productPriceIds): array
    {
        try {
            $customer = \Stripe\Customer::retrieve($S4BCustomerId);

            if (! $customer) {
                throw new Exception('Cliente no encontrado en Stripe.');
            }

            $lineItems = [];
            $productDetails = [];
            foreach ($productPriceIds as $priceId) {

                $price = \Stripe\Price::retrieve($priceId);
                $product = \Stripe\Product::retrieve($price->product);

                $lineItems[] = [
                    'price' => $priceId,
                    'quantity' => 1,
                ];

                $productDetails[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $price->unit_amount / 100,
                    'currency' => strtoupper($price->currency),
                    'interval' => $price->recurring->interval ?? null,
                ];
            }

            if (empty($lineItems)) {
                throw new Exception('No se proporcionaron productos para contratar.');
            }

            $session = \Stripe\Checkout\Session::create([
                'customer' => $S4BCustomerId,
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'subscription',
                'success_url' => env('APP_URL') . '/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => env('APP_URL') . '/cancel',
                'payment_intent_data' => [
                    'setup_future_usage' => 'off_session',
                ],
            ]);

            return [
                'session_id' => $session->id,
                'url' => $session->url,
                'product_details' => $productDetails,
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('No se pudo procesar la contratación: ' . $e->getMessage());
        }
    }
}
