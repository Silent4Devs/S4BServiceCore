<?php

namespace Modules\Stripe\App\Http\Api\Services;

use Exception;
use Stripe\Stripe;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use Stripe\Product;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\SetupIntent;

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

    public function S4BGetStripeClient(): StripeClient
    {
        return $this->S4BStripeClient;
    }

    public function S4BGetCustomerById(string $S4BCustomerId): Customer
    {
        try {
            return $this->S4BStripeClient->customers->retrieve($S4BCustomerId);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener el cliente: ' . $e->getMessage());
        }
    }

    public function S4BGetCustomerSubscriptions(string $S4BCustomerId)
    {
        try {
            return $this->S4BStripeClient->subscriptions->all(['customer' => $S4BCustomerId]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener las suscripciones del cliente: ' . $e->getMessage());
        }
    }

    public function S4BGetProductDetailsById(string $S4BProductId): Product
    {
        try {
            return $this->S4BStripeClient->products->retrieve($S4BProductId);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener el producto: ' . $e->getMessage());
        }
    }

    public function S4BPostInactiveSubscriptionsByCustomer(string $S4BCustomerId): array
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
                if (!in_array($product->id, $subscribedProductIds)) {
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

    public function S4BPostProductsByCustomer(string $S4BCustomerId): array
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

    public function S4BPostSubscriptionsActiveInactive(string $S4BCustomerId): array
    {
        try {
            \Stripe\Customer::retrieve($S4BCustomerId);

            $S4BSubscriptions = $this->S4BGetCustomerSubscriptions($S4BCustomerId);
            $subscribedProductIds = [];
            $subscribedProducts = [];

            foreach ($S4BSubscriptions->data as $S4BSubscription) {
                foreach ($S4BSubscription->items->data as $S4BItem) {
                    $product = \Stripe\Product::retrieve($S4BItem->price->product);
                    $prices = \Stripe\Price::all(['product' => $product->id]);

                    $formattedPrices = array_map(function ($price) {
                        return [
                            'id' => $price->id,
                            'amount' => $price->unit_amount / 100,
                            'currency' => strtoupper($price->currency),
                            'interval' => $price->recurring->interval ?? null,
                        ];
                    }, $prices->data);

                    $subscribedProductIds[] = $product->id;
                    $subscribedProducts[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->description,
                        'active' => $product->active,
                        'images' => $product->images,
                        'img' => $product->metadata['img'] ?? null,
                        'prices' => $formattedPrices,
                        'subscription_start' => $S4BSubscription->start_date ? date('Y-m-d', $S4BSubscription->start_date) : null,
                        'subscription_end' => $S4BSubscription->current_period_end ? date('Y-m-d', $S4BSubscription->current_period_end) : null,
                    ];
                }
            }

            $S4BProducts = \Stripe\Product::all(['active' => true]);
            $unsubscribedProducts = [];

            foreach ($S4BProducts->data as $product) {
                if (!in_array($product->id, $subscribedProductIds)) {
                    $prices = \Stripe\Price::all(['product' => $product->id]);

                    $formattedPrices = array_map(function ($price) {
                        return [
                            'id' => $price->id,
                            'amount' => $price->unit_amount / 100,
                            'currency' => strtoupper($price->currency),
                            'interval' => $price->recurring->interval ?? null,
                        ];
                    }, $prices->data);

                    $unsubscribedProducts[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->description,
                        'active' => $product->active,
                        'images' => $product->images,
                        'img' => $product->metadata['img'] ?? null,
                        'prices' => $formattedPrices,
                    ];
                }
            }

            $S4BProductsAll = [
                'subscribed_products' => $subscribedProducts,
                'unsubscribed_products' => $unsubscribedProducts,
            ];

            return $S4BProductsAll;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener los productos del cliente: ' . $e->getMessage());
        }
    }

    public function S4BGetAllActiveProducts(): array
    {
        try {
            $allProducts = \Stripe\Product::all(['active' => true]);
            $products = [];

            foreach ($allProducts->data as $product) {
                $prices = \Stripe\Price::all(['product' => $product->id]);

                $formattedPrices = array_map(function ($price) {
                    return [
                        'id' => $price->id,
                        'amount' => $price->unit_amount / 100,
                        'currency' => strtoupper($price->currency),
                        'interval' => $price->recurring->interval ?? null,
                    ];
                }, $prices->data);

                $products[] = [
                    'active' => $product->active,
                    'id' => $product->id,
                    'images' => $product->images,
                    'name' => $product->name,
                    'description' => $product->description,
                    'img' => $product->metadata['img'] ?? null,
                    'metadata' => $product->metadata,
                    'prices' => $formattedPrices,
                ];
            }

            return $products;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener los productos activos: ' . $e->getMessage());
        }
    }

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

    public function S4BTenantSubscriptionStatus($S4BSuscripciones, $S4BModulosValidos)
    {
        try {
            if (!empty($S4BSuscripciones) && is_array($S4BSuscripciones)) {
                foreach ($S4BSuscripciones as $S4BSuscripcion) {
                    if (in_array($S4BSuscripcion['name'], $S4BModulosValidos) && $S4BSuscripcion['active'] === true) {
                        return true;
                    }
                }
            }
            return false;
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
            curl_setopt($ch, CURLOPT_URL, $clientKeyApi);
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

    public function S4BAddCard(string $S4BCustomerId, string $cardNumber, int $expMonth, int $expYear, string $cvc)
    {
        try {
            $paymentMethod = $this->S4BStripeClient->paymentMethods->create([
                'type' => 'card',
                'card' => [
                    'number' => $cardNumber,
                    'exp_month' => $expMonth,
                    'exp_year' => $expYear,
                    'cvc' => $cvc,
                ],
            ]);

            $this->S4BStripeClient->paymentMethods->attach($paymentMethod->id, [
                'customer' => $S4BCustomerId,
            ]);

            return $this->S4BStripeClient->paymentMethods->retrieve($paymentMethod->id);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al agregar la tarjeta: ' . $e->getMessage());
        }
    }

    public function S4BRemovePaymentMethod(string $S4BPaymentMethodId)
    {
        try {
            $this->S4BStripeClient->paymentMethods->detach($S4BPaymentMethodId);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al eliminar el método de pago: ' . $e->getMessage());
        }
    }

    public function S4BGeS4BillingAddress(string $S4BCustomerId)
    {
        try {
            $S4BCustomer = $this->S4BGetCustomerById($S4BCustomerId);

            return $S4BCustomer->address ?: [];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al obtener la dirección de facturación: ' . $e->getMessage());
        }
    }

    public function S4BAddBillingAddress(string $S4BCustomerId, array $S4BBillingAddress): Customer
    {
        return $this->S4BUpdateBillingAddress($S4BCustomerId, $S4BBillingAddress);
    }

    public function S4BRemoveBillingAddress(string $S4BCustomerId): Customer
    {
        try {
            return $this->S4BStripeClient->customers->update($S4BCustomerId, ['address' => null]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al eliminar la dirección de facturación: ' . $e->getMessage());
        }
    }

    public function S4BUpdateBillingAddress(string $S4BCustomerId, array $S4BBillingAddress): Customer
    {
        try {
            return $this->S4BStripeClient->customers->update($S4BCustomerId, ['address' => $S4BBillingAddress]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Error al actualizar la dirección de facturación: ' . $e->getMessage());
        }
    }

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

    //pago
    public function S4BCreateSetupIntent(string $S4BCustomerId)
    {
        try {
            $setupIntent = SetupIntent::create([
                'customer' => $S4BCustomerId,
            ]);

            return ['clientSecret' => $setupIntent->client_secret];
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function S4BProcessPayment(string $S4BCustomerId, string $paymentMethodId, int $amount, string $currency, string $priceId)
    {
        try {
            if ($currency === 'mxn' && $amount * 100 < 1000) {
                throw new Exception('El monto es menor 10.00', $amount);
            }

            $customer = $this->S4BStripeClient->customers->retrieve($S4BCustomerId);
            $defaultPaymentMethod = $customer->invoice_settings->default_payment_method;

            if ($defaultPaymentMethod !== $paymentMethodId) {
                $this->S4BStripeClient->paymentMethods->attach($paymentMethodId, [
                    'customer' => $S4BCustomerId,
                ]);

                $this->S4BStripeClient->customers->update($S4BCustomerId, [
                    'invoice_settings' => ['default_payment_method' => $paymentMethodId],
                ]);
            }

            $paymentIntent = $this->S4BStripeClient->paymentIntents->create([
                'customer' => $S4BCustomerId,
                'amount' => $amount * 100,
                'currency' => $currency,
                'payment_method' => $paymentMethodId,
                'confirm' => true,
                'off_session' => true,
            ]);

            $subscription = $this->S4BStripeClient->subscriptions->create([
                'customer' => $S4BCustomerId,
                'items' => [
                    ['price' => $priceId],
                ],
                'default_payment_method' => $paymentMethodId,
            ]);

            return [
                'clientSecret' => $paymentIntent->client_secret,
                'status' => $paymentIntent->status,
                'subscription' => $subscription,
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
