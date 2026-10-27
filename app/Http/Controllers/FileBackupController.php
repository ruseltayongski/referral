<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Http\Controllers\ApiController; // <-- change to the class that holds fileUploadUrl()

class FileBackupController extends Controller
{
    const PER_PART = 500;   // files per ZIP part
    const PER_PAGE = 50;    // rows per page in the table

    public function index(Request $request)
    {
        $from = $request->input('from', Carbon::now()->subDays(7)->toDateString());
        $to   = $request->input('to', Carbon::now()->toDateString());
        $page = max(1, (int) $request->input('page', 1));

        $data = ['files' => [], 'total' => 0, 'total_size' => 0];
        $error = null;

        $v = $this->validator($from, $to);
        if ($v->fails()) {
            $error = $v->errors()->first();
        } else {
            list($status, $body) = $this->callApi([
                'action'   => 'list',
                'from'     => $from,
                'to'       => $to,
                'page'     => $page,
                'per_page' => self::PER_PAGE,
            ]);
            $json = json_decode($body, true);
            if ($status !== 200 || !is_array($json)) {
                $error = 'File server returned an error (HTTP ' . $status . '): '
                    . mb_substr(strip_tags((string) $body), 0, 300);
            } else {
                $data = $json;
            }
        }

        return view('file-backup', [
            'from'      => $from,
            'to'        => $to,
            'page'      => $page,
            'perPage'   => self::PER_PAGE,
            'perPart'   => self::PER_PART,
            'files'     => $data['files'],
            'total'     => $data['total'],
            'totalSize' => $data['total_size'],
            'pages'     => (int) ceil($data['total'] / self::PER_PAGE),
            'parts'     => (int) ceil($data['total'] / self::PER_PART),
            'baseUrl'   => rtrim(ApiController::fileUploadUrl(), '/'),
            'error'     => $error,
        ]);
    }

    public function download(Request $request)
    {
        $from = $request->input('from');
        $to   = $request->input('to');
        $part = max(1, (int) $request->input('part', 1));

        $v = $this->validator($from, $to);
        if ($v->fails()) {
            return redirect()->route('file-backup.index')->withErrors($v);
        }

        // Make sure this part exists before we start streaming a ZIP
        list($status, $body) = $this->callApi([
            'action' => 'list', 'from' => $from, 'to' => $to, 'page' => 1, 'per_page' => 1,
        ]);
        $json = json_decode($body, true);
        if ($status !== 200 || !is_array($json) || $json['total'] < ((($part - 1) * self::PER_PART) + 1)) {
            return redirect()->route('file-backup.index', compact('from', 'to'))
                ->with('error', 'Nothing to download for that range/part.');
        }

        $url   = $this->endpoint([
            'action'   => 'download',
            'from'     => $from,
            'to'       => $to,
            'part'     => $part,
            'per_part' => self::PER_PART,
        ]);
        $token = config('services.file_backup.token');

        // Fetch the ZIP into a temp file first so we can verify it before sending it
        @set_time_limit(0);
        $tmp = tempnam(sys_get_temp_dir(), 'bkzip_');
        $fh  = fopen($tmp, 'wb');
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => ['X-Backup-Token: ' . $token],
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_FILE           => $fh,
        ]);
        curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);
        fclose($fh);

        $head = (string) file_get_contents($tmp, false, null, 0, 2);
        if ($code !== 200 || $head !== 'PK') {
            $msg = strip_tags((string) file_get_contents($tmp, false, null, 0, 300));
            @unlink($tmp);
            return redirect()->route('file-backup.index', compact('from', 'to'))
                ->with('error', 'Download failed (HTTP ' . $code . ') ' . $err . ' ' . $msg);
        }

        $response = response()->download(
            $tmp,
            'backup_' . $from . '_to_' . $to . '_part' . $part . '.zip',
            ['Content-Type' => 'application/zip']
        )->deleteFileAfterSend(true);

        // Tells the page's JavaScript that the download has started, so it can hide the loader
        $response->headers->setCookie(cookie('backup_dl', '1', 1, '/', null, false, false));

        return $response;
    }

    // ---------------------------------------------------------------- helpers

    private function validator($from, $to)
    {
        return Validator::make(
            ['from' => $from, 'to' => $to],
            [
                'from' => 'required|date_format:Y-m-d',
                'to'   => 'required|date_format:Y-m-d|after_or_equal:from',
            ]
        );
    }

    private function endpoint(array $params)
    {
        return rtrim(ApiController::fileUploadUrl(), '/') . '/backup_api.php?' . http_build_query($params);
    }

    private function callApi(array $params)
    {
        $ch = curl_init($this->endpoint($params));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['X-Backup-Token: ' . config('services.file_backup.token')],
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT        => 120,
        ]);
        $body   = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [$status, $body];
    }
}