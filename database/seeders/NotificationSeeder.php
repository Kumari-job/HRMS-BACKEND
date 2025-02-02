<?php

namespace Database\Seeders;

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
        DB::table('notifications')->insert([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\LeaveNotification',
            'data' =>'{"title":"Employee Leave","description":"Tushar Luitelllll has requested Paternity on 2025-01-21"}',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => 1,

        ]);
    }
}
