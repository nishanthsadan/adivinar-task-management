<?php
use Illuminate\Http\Request;
use App\Http\Controllers\Api\TaskApiController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;


Route::post('/login', function (Request $request) {
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user'  => [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role->value,
        ],
    ]);
});

// ── Public: logout ────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out successfully.']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks',                 [TaskApiController::class, 'index']);
    Route::post('/tasks',                [TaskApiController::class, 'store']);
    Route::patch('/tasks/{id}/status',   [TaskApiController::class, 'updateStatus']);
    Route::get('/tasks/{id}/ai-summary', [TaskApiController::class, 'aiSummary']);
});
