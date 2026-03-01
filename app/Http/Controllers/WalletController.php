<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WalletsController extends Controller
{
    /**
     * Display the specified user's wallet.
     *
     * @param  int  $wallet_id
     * @return \Illuminate\Http\Response
     */
    public function show($wallet_id)
    {
        // Ensure the authenticated user owns the requested wallet
        // $this->authorize('view', Wallet::class);

        // Retrieve the wallet for the specified user_id
        $wallet = Wallet::where('id', $wallet_id)->firstOrFail();

        return response()->json($wallet, 200);
    }

    /**
     * Create a new wallet for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Ensure the authenticated user is creating their own wallet
        // $this->authorize('create', Wallet::class);

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'transaction' => 'required|string|max:255|in:credit,debit',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Get the authenticated user's ID
        $user_id = Auth::id();

        // Calculate the new balance based on the transaction type
        $latestWallet = Wallet::where('user_id', $user_id)->orderBy('created_at', 'desc')->first();
        $newBalance = ($latestWallet ? $latestWallet->balance : 0);

        if (strtolower($request->input('transaction')) === 'credit') {
            $newBalance += $request->input('amount');
        } elseif (strtolower($request->input('transaction')) === 'debit') {
            $newBalance -= $request->input('amount');
        }

        // Create a new wallet
        $wallet = Wallet::create([
            'user_id' => $user_id,
            'amount' => $request->input('amount'),
            'transaction' => $request->input('transaction'),
            'balance' => $newBalance,
        ]);

        return response()->json($wallet, 201);
    }

     /**
     * Update the specified user's wallet.
     *
     * @param  int  $wallet_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $wallet_id)
    {

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'transaction' => 'required|string|max:255|in:credit,debit',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $latestWallet = Wallet::where('user_id', Auth::id())->orderBy('created_at', 'desc')->first();
        $newBalance = ($latestWallet ? $latestWallet->balance : 0);

        if (strtolower($request->input('transaction')) === 'credit') {
            $newBalance += $request->input('amount');
        } elseif (strtolower($request->input('transaction')) === 'debit') {
            $newBalance -= $request->input('amount');
        }

        $wallet = Wallet::where('id', $wallet_id)->firstOrFail();
        $wallet->update([
            'amount' => $request->input('amount'),
            'transaction' => $request->input('transaction'),
            'balance' => $newBalance,
        ]);

        return response()->json($wallet, 200);
    }

    /**
     * Remove the specified user's wallet from storage.
     *
     * @param  int  $wallet_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($wallet_id)
    {
        
        $wallet = Wallet::where('id', $wallet_id)->firstOrFail();
        $wallet->delete();

        return response()->json(null, 204);
    }


    /**
     * Transfer funds from the authenticated user's wallet to another user's wallet.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $recipient_user_id
     * @return \Illuminate\Http\Response
     */
    public function transferFunds(Request $request, $recipient_user_id)
    {
        $sender_user_id = Auth::id();
        $senderWallet = Wallet::where('user_id', $sender_user_id)->latest('created_at')->firstOrFail();

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $transferAmount = $request->input('amount');
        $transactionType = 'debit'; // Funds transfer is always a debit from the sender's wallet

        $senderNewBalance = $senderWallet->balance - $transferAmount;
        $recipientWallet = Wallet::where('user_id', $recipient_user_id)->latest('created_at')->firstOrFail();
        $recipientNewBalance = $recipientWallet->balance + $transferAmount;

        // Ensure the sender has sufficient balance for the transfer
        if ($senderNewBalance < 0) {
            return response()->json(['error' => 'Insufficient balance for the transfer.'], 400);
        }

        // Start a database transaction to update both sender and recipient wallets atomically
        try {
            DB::transaction(function () use ($transferAmount, $transactionType, $senderNewBalance, $recipient_user_id, $recipientNewBalance) {
                // Update the sender's wallet
                Wallet::create([
                    'amount' => $transferAmount,
                    'transaction' => $transactionType,
                    'balance' => $senderNewBalance,
                    'user_id' => Auth::id(),
                ]);

                // Update the recipient's wallet
                Wallet::create([
                    'amount' => $transferAmount,
                    'transaction' => 'credit', // Funds transfer is a credit for the recipient
                    'balance' => $recipientNewBalance,
                    'user_id' => $recipient_user_id,
                ]);
            });

            return response()->json(['message' => 'Funds transfer successful.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing the funds transfer.'], 500);
        }
    }
}
