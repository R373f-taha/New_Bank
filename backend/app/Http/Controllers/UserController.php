<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all(['id', 'name', 'email']);
        return response()->json($users);
    }
    public function getLatestTransactions()
    {

        $user = Auth::user();


        $customer = $user->customer;


        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'This user does not have an associated customer account.',
                'data' => []
            ], 404);
        }


        $transactions = Transaction::where('customer_id', $customer->id)
            ->with(['transfer.sender', 'transfer.receiver'])
            ->latest()
            ->take(6)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Latest 6 transactions retrieved successfully with details.',
            'data' => $transactions
        ], 200);
    }
}
