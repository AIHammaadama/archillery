<?php

namespace App\Http\Controllers;

use App\Jobs\SendMessageJob;
use App\Models\Message;
use Illuminate\Http\Request;
use Pusher\Pusher;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MessageController extends Controller
{

    public function index()
    {
        $chats = Message::orderBy('id', 'asc')
            ->take(50)
            ->get();

        $chats->load('user');
        
        $chats->transform(fn ($chat) => [
            'id'            => $chat->id,
            'user_id'       => $chat->user_id,
            'message'       => auth()->user()->is_admin ? '<a href="/admin/message/'.$chat->id.'/delete">Delete</a><br><b style="font-size:16px;">'.$chat->user->firstname.' '.$chat->user->lastname.'</b> ('.$chat->user->email.'):<br> '.$chat->message.'<br><i style="font-size:12px;">'.$chat->created_at->diffForHumans().'</i>' : ('<b style="font-size:16px;">'.$chat->user->firstname.' '.$chat->user->lastname.'</b>:<br> '.$chat->message.'<br><i style="font-size:12px;">'.$chat->created_at->diffForHumans().'</i>'),
            'status'        => $chat->status,
            'side'          => ($chat->user_id == Auth::user()->id) ? 'left' : 'right',
            'avatar'        => $chat->user->photo,
            'created_at'    => $chat->created_at
        ]);
        return response()->json($chats);
    }

    public function create(Request $req)
    {
        $req->validate([
            'message' => 'required'
        ]);

        $sendMessage = Message::create([
            'user_id' => auth()->user()->id,
            'message' => $req->message,
        ]);

        if ($sendMessage) {
            // SendMessageJob::dispatch('MessageEvent', $sendMessage);
            $sendMessage->load('user');
            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                [
                    'cluster' => env('PUSHER_APP_CLUSTER'),
                    'useTLS' => false,
                    'encrypted' => true,
                ]
            );

            $pusher->trigger('users-chat', 'MessageEvent', $sendMessage);

            return response()->json(['success' => true, 'payload' => $sendMessage]);
        }
        return response()->json(['success' => false, 'payload' => $sendMessage]);
    }
}
