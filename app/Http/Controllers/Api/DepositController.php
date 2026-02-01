<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    /**
     * Store a new deposit request
     * 
     * Endpoint: POST /api/v1/deposit
     * Requires: Authentication (auth:api middleware)
     * 
     * Request Body:
     * - user_id (required|integer): ID of the user making the deposit
     * - amount (required|numeric): Deposit amount (minimum: 1)
     * - mobile_no (required|string): Mobile number (max: 15 characters)
     * - payment_mode (required|string): Payment method (max: 50 characters)
     * 
     * Success Response (201):
     * {
     *   "status": "success",
     *   "message": "Deposit request submitted successfully. Awaiting approval.",
     *   "data": { ... deposit record ... }
     * }
     * 
     * Error Response (422):
     * {
     *   "status": "error",
     *   "message": "Validation failed",
     *   "errors": { ... validation errors ... }
     * }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deposit(Request $request)
    {
        try {
            // Validate the request
            $validator = \Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'amount' => 'required|numeric|min:1',
                'mobile_no' => 'required|string|max:15',
                'payment_mode' => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Prepare deposit data
            $depositData = [
                'user_id' => $request->user_id,
                'amount' => $request->amount,
                'mobile_no' => $request->mobile_no,
                'payment_mode' => $request->payment_mode,
                'is_approved' => 0, // Pending approval by default
            ];

            // Create deposit record using the model
            $model = new \App\Models\UserDeposit();
            $response = $model->store($depositData);

            // Return JSON response
            if (in_array($response['status'], [200, 201])) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Deposit request submitted successfully. Awaiting approval.',
                    'data' => $response['data']
                ], $response['status']);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $response['message']
                ], $response['status']);
            }

        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
