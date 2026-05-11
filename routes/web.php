<?php


use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;


Route::get('/', function () {
    return view('welcome');
});


Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name("user.index"); // List
    Route::get('/search/{identifier}', [UserController::class, 'show'])->name("user.show"); // Read
    Route::get('/create', [UserController::class, 'create'])->name("user.create"); // Create
    Route::post('/create', [UserController::class, 'store'])->name("user.store"); // store
    Route::get('/{identifier}', [UserController::class, 'show'])->name("user.show"); // Read
    Route::put('/update/{id}', [UserController::class, 'update'])->name("user.update");  // Update
    Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name("user.delete"); // Delete
});


Route::get('/redis-test', function () {

    //crc32 = it's return 10 digits of integer value
    //crs64 = it's return 16/18 digits of integer value 
    //String - SET/GET , INCR/DECR, SETEX/PEXPIRE

    
    // ১. সরাসরি রেডিস মেথড ব্যবহার (String)
    Redis::set('moinul:skill', 'Mastering Redis');

    // ২. লারাভেল ক্যাশ মেথড ব্যবহার (এটিও রেডিসে যাবে কারণ আমরা .env তে ক্যাশ রেডিস করেছি)
    Cache::put('project_name', 'PureOlaa - Organic Food', 600); // ১০ মিনিট থাকবে

    // ৩. ইনক্রিমেন্ট টেস্ট (লাইভ কাউন্টার)
    $views = Redis::incr('live_visitor_count');

    // ১. স্ট্রিং ডেটা (এটি দিয়ে তুমি নাম বা ছোট তথ্য সেভ করবে)
    Redis::set('user:1:name', 'Moinul - Developer');

    // ২. সেশন বা টেম্পোরারি ডেটা (৬০ সেকেন্ড পর ভ্যানিশ হয়ে যাবে)
    Redis::setex('session:token', 60, 'ABC-123-XYZ');

    // ৩. ইনক্রিমেন্ট (লাইক বা ভিউ কাউন্টার)
    // প্রতিবার রিফ্রেশ করলে এটি ১ করে বাড়বে
    Redis::incr('post:99:views');

    // ৪. লিস্ট (কিউ বা মেসেজ পাঠানোর জন্য)
    Redis::rpush('notifications', 'আপনার প্রোফাইল কেউ ভিজিট করেছে!');
    Redis::rpush('notifications', 'নতুন একটি মেসেজ এসেছে।');

    // ৫. হ্যাশ (পুরো একটা অবজেক্ট বা ইউজারের ডিটেইলস)
    Redis::hset('pureolaa:product:1', 'name', 'Pure Honey');
    Redis::hset('pureolaa:product:1', 'price', '500');
    Redis::hset('pureolaa:product:1', 'stock', '25');

    return "সফলভাবে ডেটা রেডিসে পাঠানো হয়েছে। বর্তমান ভিউ সংখ্যা: " . $views;
});
Route::get('/get-redis-data', function () {
    // ১. স্ট্রিং ডেটা তুলে আনা
    $skill = Redis::get('moinul:skill');

    // ২. কাউন্টার ভ্যালু দেখা
    $views = Redis::get('live_visitor_count');

    // ৩. লিস্টের সব ডেটা দেখা (০ থেকে -১ মানে শুরু থেকে শেষ পর্যন্ত)
    $notifications = Redis::lrange('notifications', 0, -1);

    return [
        'skill' => $skill,
        'total_views' => $views,
        'notifications' => $notifications
    ];
});