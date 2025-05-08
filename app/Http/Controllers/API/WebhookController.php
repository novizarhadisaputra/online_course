<?php

namespace App\Http\Controllers\API;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\XenditService;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Traits\ResponseTrait;

class WebhookController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function receiveFromPayment(Request $request, string $gateway)
    {
        try {
            $input = json_decode(json_encode($request->input()));

            switch ($gateway) {
                case 'xendit':
                    $transaction = Transaction::where('code', $input->data->reference_id)->first();
                    if ($transaction) {
                        $xendit = new XenditService($transaction);
                        $xendit->receiveFromHook($request);
                    }
                    break;
                default:
                    break;
            }
            return $this->success(data:new TransactionResource($transaction));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
