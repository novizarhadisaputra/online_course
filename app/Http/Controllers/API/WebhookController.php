<?php

namespace App\Http\Controllers\API;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ThirdPartyLog;
use App\Traits\ResponseTrait;
use App\Services\XenditService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Notifications\PaymentCallbackNotification;

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
        ThirdPartyLog::create([
            'name' => $gateway,
            'event_name' => 'webhook receive data',
            'ip_address' => $request->ip(),
            'data' => $request->input(),
        ]);

        try {
            DB::beginTransaction();
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

            $data = [
                'id' => $transaction->id,
                'status' => $transaction->status,
            ];

            $transaction->user->notify(new PaymentCallbackNotification($data)->afterCommit());
            DB::commit();
            return $this->success(data: new TransactionResource($transaction));
        } catch (\Throwable $th) {
            DB::rollBack();
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
