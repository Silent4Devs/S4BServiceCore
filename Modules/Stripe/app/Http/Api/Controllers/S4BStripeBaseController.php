<?php

namespace Modules\Stripe\App\Http\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class S4BStripeBaseController extends Controller
{
    /**
     * S4BSendResponse
     *
     * Envía una respuesta JSON exitosa con datos y un mensaje.
     *
     * @param  mixed  $result  Datos a incluir en la respuesta.
     * @param  string  $message  Mensaje descriptivo de la operación.
     * @param  int  $status  Código HTTP (por defecto 200).
     * @return JsonResponse Respuesta JSON formateada.
     */
    protected function S4BSendResponse($result, $message, $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => $message,
        ], $status);
    }

    /**
     * S4BSendError
     *
     * Envía una respuesta JSON de error con un mensaje y opcionalmente datos adicionales.
     *
     * @param  string  $error  Mensaje de error principal.
     * @param  array  $errorMessages  Lista de mensajes de error adicionales (opcional).
     * @param  int  $status  Código HTTP (por defecto 400).
     * @return JsonResponse Respuesta JSON formateada.
     */
    protected function S4BSendError($error, $errorMessages = [], $status = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (! empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $status);
    }
}
