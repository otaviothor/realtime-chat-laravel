<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function listMessages(User $user)
    {
//        $userFrom = Auth::user();
        $userFrom = 1;
        $userTo = $user->id;

        $messages = Message::where(
                function ($query) use ($userFrom, $userTo) {
                    $query->where([
                        'from' => $userFrom,
                        'to' => $userTo,
                    ]);
                }
            )->orWhere(
                function ($query) use ($userFrom, $userTo) {
                    $query->where([
                        'to' => $userFrom,
                        'from' => $userTo,
                    ]);
                }
            )->orderBy('created_at', 'ASC')
            ->get();

        return response()->json([
            'messages' => $messages
        ], Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $message = new Message();
        $message->from = 1;
//        $message->from = Auth::user()->id;
        $message->to = $request->to;
        $message->content = filter_var($request->content, FILTER_SANITIZE_STRIPPED);
        $message->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
