<?php

use App\Events\NewInquiryReceivedEvent;
use App\Events\NewMessageEvent;
use App\Events\NewQueuedMessageEvent;
use App\Jobs\ProcessInquiryJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('form');
});

Route::get('/form', function () {
    return view('form');
});

Route::get('/admin', function () {
    return view('admin');
});

Route::get('/demo', function () {
    return view('demo');
});

Route::post('/submit-inquiry', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'required|string|max:255',
        'department' => 'nullable|string|max:100',
        'priority' => 'required|string|in:normal,high,urgent',
        'message' => 'required|string|max:2000',
    ]);

    $ticketId = 'TK-' . strtoupper(substr(uniqid(), 7));
    $inquiry = array_merge($validated, [
        'ticket_id' => $ticketId,
        'created_at' => now()->toDateTimeString(),
    ]);

    // 1. Instant Pusher broadcast to Admin page
    broadcast(new NewInquiryReceivedEvent($inquiry));

    // 2. Dispatch email job to Redis Queue
    ProcessInquiryJob::dispatch($inquiry);

    return response()->json([
        'success' => true,
        'message' => 'Inquiry submitted, broadcasted to Admin, and queued in Redis for email dispatch!',
        'ticket_id' => $ticketId,
        'inquiry' => $inquiry,
    ]);
});

Route::get('/api/inquiries', function () {
    try {
        $items = Redis::lrange('inquiries:processed_list', 0, 19);
        $decoded = array_filter(array_map(function ($item) {
            return json_decode($item, true);
        }, $items));
        return response()->json(array_values($decoded));
    } catch (\Throwable $e) {
        return response()->json([]);
    }
});

// Backward-compatible test routes
Route::post('/test-redis', function (Request $request) {
    $value = $request->input('value', 'Hello from Redis at ' . now());
    
    Redis::set('test_key', $value);
    $redisValue = Redis::get('test_key');
    
    Cache::put('cache_test_key', 'Cache facade stored: ' . $value, now()->addMinutes(10));
    $cacheValue = Cache::get('cache_test_key');

    return response()->json([
        'status' => 'success',
        'message' => 'Redis is connected and functioning properly!',
        'redis_direct_get' => $redisValue,
        'laravel_cache_get' => $cacheValue,
        'ping' => Redis::ping(),
        'timestamp' => now()->toDateTimeString()
    ]);
});

Route::post('/send-message', function (Request $request) {
    $message = $request->input('message', 'Hello via Pusher direct broadcast!');
    broadcast(new NewMessageEvent($message));

    return response()->json([
        'status' => 'Direct event broadcasted successfully!',
        'message' => $message
    ]);
});

Route::post('/send-queued-message', function (Request $request) {
    $message = $request->input('message', 'Hello via Redis-queued Pusher broadcast!');
    broadcast(new NewQueuedMessageEvent($message));

    return response()->json([
        'status' => 'Event dispatched to Redis queue! (Run "php artisan queue:work" to process)',
        'message' => $message
    ]);
});
