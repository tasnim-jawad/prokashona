<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Account\Account;
use App\Models\Account\AccountCategory;
use App\Models\Account\AccountLog;
use App\Models\Account\AccountLogAttachment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BranchPaymentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'amount' => ['required'],
            'description' => ['required'],
            'user_id' => ['required'],
            'account_id' => ['required'],
            'trx_id' => Rule::requiredIf($request->account_id > 1),
            'payment_method' => Rule::requiredIf($request->account_id > 1),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // dd(request()->all());

        $payment_method = (object) [
            "account_id" => 1, // cash
        ];
        try {
            $payment_method = json_decode(request()->payment_method);
        } catch (\Throwable $th) {
        }

        $account_log = new AccountLog();
        $account_log->date = Carbon::now()->toDateTimeString();
        $account_log->category_id = 1; // ponno bikri ay

        $account_log->name = $request->name;
        $account_log->customer_id = $request->user_id;
        $account_log->related_table = "users";

        $account_log->receipt_no = $request->receipt_no;
        $account_log->account_id = $request->account_id;

        $account_log->account_id = $payment_method->account_id ?? 1;
        $account_log->account_number_id = $payment_method->id ?? 1;
        $account_log->trx_id = $request->trx_id;

        $account_log->is_expense = 0;
        $account_log->is_income = 1;

        $account_log->amount = $request->amount;
        $account_log->amount_in_text = $request->amount_in_text;
        $account_log->description = request()->description;

        $account_log->creator = Auth::user()->id;
        $account_log->save();

        $account = Account::where('id', $account_log->account_id)->first();

        credit_user_payment([
            'user_id' => $account_log->customer_id,
            'date' => $account_log->date,
            'amount' => $account_log->amount,
            'type' => 'credit',
            'transaction_type' => 'customer_advance',

            'account_log_id' => $account_log->id,
            'payment_method' => $account->name,
            'account_id' => $account->id,
            'account_number_id' => $account_log->account_number_id,
            'trx_id' => $account_log->trx_id,
        ]);

        calc_user_payment([
            "user_id" => $account_log->customer_id,
        ]);

        if (request()->hasFile('attachments')) {
            foreach (request()->file('attachments') as $file) {
                try {
                    AccountLogAttachment::create([
                        'account_log_id' => $account_log->id,
                        'attachment' => Storage::put('uploads/account_logs', $file),
                    ]);
                } catch (\Throwable $th) {
                }
            }
        }

        return response()->json($account_log, 200);
    }
}
