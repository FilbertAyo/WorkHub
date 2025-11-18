<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(NotificationService $notificationService)
    {
        $smsInfo = $notificationService->getSmsAccountInfo();

        return view('notifications.index', compact('smsInfo'));
    }
}

