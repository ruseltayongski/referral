<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Feedback;
use App\Events\SocketReco;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FeedbackController extends Controller
{
    private function getfeedbackData($code)
    {
        return Feedback::select(
            'feedback.id as id',
            'feedback.sender as sender',
            'feedback.message',
            'feedback.filename',
            'feedback.guest_name',
            'users.fname as fname',
            'users.lname as lname',
            'facility.name as facility',
            'facility.abbr as abbr',
            'feedback.created_at as date',
            'feedback.code'
        )
            ->leftJoin('users', 'users.id', '=', 'feedback.sender')
            ->leftJoin('facility', 'facility.id', '=', 'users.facility_id')
            ->where('code', $code)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($row) {
                if ((int) $row->sender === 0) {
                    // Guest — no user_id to resolve, use what they typed at send time
                    $row->sender_name   = $row->guest_name ?: 'Patient';
                    $row->facility_name = 'Patient';
                } else {
                    // Real user — resolved via the sender (user_id) join above
                    $row->sender_name = trim(
                        ucwords(mb_strtolower($row->fname)) . ' ' . ucwords(mb_strtolower($row->lname))
                    ) ?: 'Unknown';
                    $row->facility_name = $row->facility ?: 'Patient';
                }
                return $row;
            });
    }

    public function guestVueFeedback(Request $request, $code)
    {
        $data = $this->getfeedbackData($code);

        return response()->json([
            'messages' => $data,
            'code' => $code,
        ]);
    }

    public function guestSaveFeedback(Request $request, $code, $sender_id)
    {
        Log::info('guestSaveFeedback request: ' . $request);
        $displayName = trim($request->input('display_name', 'Patient')) ?: 'Patient';

        $files = $request->file('file_upload');
        $file_paths = [];

        if ($files && is_array($files)) {
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $extension = $file->getClientOriginalExtension();
                    $uniqueName = 'guest_' . Str::random(10) . '_' . time() . '.' . $extension;
                    $path = $file->storeAs('RecoChat/guest', $uniqueName, 'public');
                    $file_paths[] = '/public' . Storage::url($path);
                }
            }
        }

        $files_pathname = implode('|', $file_paths);

        $feedback = Feedback::create([
            'code'       => $code,
            'sender'     => $sender_id,
            'receiver'   => 0,
            'guest_name' => $displayName,
            'filename'   => $files_pathname,
            'message'    => $request->input('message'),
        ]);

        // ParamCtrl::feedbackContent likely resolves name/facility by user id lookup —
        // it needs a guest-aware overload/param since sender=0 has no user row.
        $reco_json = ParamCtrl::feedbackContent(
            $code,
            $sender_id,
            $request->input('message'),
            $files_pathname,
            $displayName // <-- add this param to feedbackContent's signature
        );

        broadcast(new SocketReco($reco_json));

        return response()->json([
            'success'  => true,
            'id'       => $feedback->id,
            'filename' => $files_pathname,
        ]);
    }
}
