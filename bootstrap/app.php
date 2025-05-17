<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $object = (object) [
            'message' => "",
            'status' => 400,
            'data' => null,
        ];

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($object) {
            if ($request->is('api/*')) {
                $object->message = $e->getMessage();
                return response()->json($object, $object->status);
            }
        });
        $exceptions->render(function (QueryException $e, Request $request) use ($object) {
            if ($request->is('api/*')) {
                $object->message = $e->getMessage();
                return response()->json($object, $object->status);
            }
        });
        $exceptions->render(function (ValidationException $e, Request $request) use ($object) {
            if ($request->is('api/*')) {
                $object->message = $e->getMessage();
                $object->data = $e->errors();
                $object->status = 422;
                return response()->json($object, $object->status);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($object) {
            if ($request->is('api/*')) {
                $object->message = $e->getMessage();
                $object->status = 401;
                return response()->json($object, $object->status);
            }
        });

        $exceptions->render(function (Exception $e, Request $request) use ($object) {
            if ($request->is('api/*')) {
                $object->message = $e->getMessage();
                return response()->json($object, $object->status);
            }
        });
    })->create();
