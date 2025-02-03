<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i = 0; $i <= 100; $i++){

        DB::table('notifications')->insert([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\LeaveNotification',
            'data' =>'{"title":"Employee Leave","description":"Tushar Luitelllll has requested Paternity on 2025-01-21"}',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        
        ]);
    }
    }
}
