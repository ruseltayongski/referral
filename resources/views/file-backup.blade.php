<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>File Backup</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .thumb { width: 44px; height: 44px; object-fit: cover; border-radius: 4px; background: #eee; }
        .fname { word-break: break-all; font-size: 13px; }
        .part-btn { margin: 0 6px 6px 0; }
        .loader-overlay {
            position: fixed; top: 0; right: 0; bottom: 0; left: 0; z-index: 2000;
            background: rgba(255, 255, 255, .85);
            display: none; align-items: center; justify-content: center; text-align: center;
        }
        .loader-box { max-width: 360px; padding: 24px; }
        .loader-box .spinner-border { width: 3rem; height: 3rem; }
    </style>
</head>
<body class="bg-light">
@php
    $fmtSize = function ($b) {
        if ($b >= 1073741824) return number_format($b / 1073741824, 2) . ' GB';
        if ($b >= 1048576)    return number_format($b / 1048576, 2) . ' MB';
        if ($b >= 1024)       return number_format($b / 1024, 1) . ' KB';
        return $b . ' B';
    };
    $pageUrl = function ($p) use ($from, $to) {
        return url()->current() . '?' . http_build_query(['from' => $from, 'to' => $to, 'page' => $p]);
    };
    $encodePath = function ($path) {
        return implode('/', array_map('rawurlencode', explode('/', $path)));
    };
@endphp

<div id="loader" class="loader-overlay">
    <div class="loader-box">
        <div class="spinner-border text-primary" role="status"></div>
        <div id="loader-text" class="mt-3 font-weight-bold">Loading...</div>
        <div id="loader-sub" class="text-muted small mt-1"></div>
    </div>
</div>

<div class="container py-4">
    <h4 class="mb-3">File Backup</h4>

    @if($error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body">
            <form id="filter-form" method="GET" action="{{ route('file-backup.index') }}" class="form-row align-items-end">
                <div class="col-md-3 mb-2">
                    <label for="from">From</label>
                    <input type="date" id="from" name="from" class="form-control" value="{{ $from }}" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="to">To</label>
                    <input type="date" id="to" name="to" class="form-control" value="{{ $to }}" required>
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                </div>
                <div class="col-md-4 mb-2 text-md-right">
                    <button type="button" class="btn btn-outline-secondary btn-sm preset" data-days="0">Today</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm preset" data-days="7">Last 7 days</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm preset" data-days="30">Last 30 days</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm preset" data-days="month">This month</button>
                </div>
            </form>
        </div>
    </div>

    @if(!$error)
        {{-- Summary + downloads --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                    <div>
                        <strong>{{ number_format($total) }}</strong> file(s) from
                        <strong>{{ \Carbon\Carbon::parse($from)->format('d M Y') }}</strong> to
                        <strong>{{ \Carbon\Carbon::parse($to)->format('d M Y') }}</strong>
                        &middot; {{ $fmtSize($totalSize) }} total
                    </div>
                </div>

                @if($total > 0)
                    <div class="mb-1 text-muted small">
                        Downloads are split into ZIP parts of up to {{ $perPart }} files so large backups don't time out.
                    </div>
                    @for($i = 1; $i <= $parts; $i++)
                        @php
                            $startNo = (($i - 1) * $perPart) + 1;
                            $endNo   = min($i * $perPart, $total);
                        @endphp
                        <a class="btn btn-success btn-sm part-btn dl-btn"
                           href="{{ route('file-backup.download', ['from' => $from, 'to' => $to, 'part' => $i]) }}">
                            Download part {{ $i }} of {{ $parts }}
                            <small>({{ $startNo }}&ndash;{{ $endNo }})</small>
                        </a>
                    @endfor
                @else
                    <div class="text-muted">No files modified in this date range.</div>
                @endif
            </div>
        </div>

        {{-- File table --}}
        @if($total > 0)
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-light">
                        <tr>
                            <th style="width:60px"></th>
                            <th>File</th>
                            <th style="width:100px">Size</th>
                            <th style="width:190px">Last modified</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($files as $f)
                            @php
                                $ext = strtolower(pathinfo($f['path'], PATHINFO_EXTENSION));
                                $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $url = $baseUrl . '/' . $encodePath($f['path']);
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge badge-secondary">{{ strtoupper($ext ?: 'file') }}</span>
                                </td>
                                <td class="fname">
                                    <a href="{{ $url }}" target="_blank" rel="noopener">{{ $f['path'] }}</a>
                                </td>
                                <td>{{ $fmtSize($f['size']) }}</td>
                                <td>{{ \Carbon\Carbon::createFromTimestamp($f['mtime'])->format('d M Y h:i A') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if($pages > 1)
                <nav class="mt-3">
                    <ul class="pagination justify-content-center">
                        <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $pageUrl(max(1, $page - 1)) }}">Previous</a>
                        </li>
                        <li class="page-item disabled">
                            <span class="page-link">Page {{ $page }} of {{ $pages }}</span>
                        </li>
                        <li class="page-item {{ $page >= $pages ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $pageUrl(min($pages, $page + 1)) }}">Next</a>
                        </li>
                    </ul>
                </nav>
            @endif
        @endif
    @endif
</div>

<script>
    (function () {
        var loader = document.getElementById('loader');
        var loaderText = document.getElementById('loader-text');
        var loaderSub = document.getElementById('loader-sub');
        function showLoader(msg, sub) {
            loaderText.textContent = msg || 'Loading...';
            loaderSub.textContent = sub || '';
            loader.style.display = 'flex';
        }
        function hideLoader() { loader.style.display = 'none'; }
        function clearDlCookie() {
            document.cookie = 'backup_dl=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
        }
        // back/forward cache: never leave the overlay stuck
        window.addEventListener('pageshow', hideLoader);

        var from = document.getElementById('from');
        var to = document.getElementById('to');
        function iso(d) {
            var m = ('0' + (d.getMonth() + 1)).slice(-2), day = ('0' + d.getDate()).slice(-2);
            return d.getFullYear() + '-' + m + '-' + day;
        }
        Array.prototype.forEach.call(document.querySelectorAll('.preset'), function (btn) {
            btn.addEventListener('click', function () {
                var now = new Date(), start = new Date();
                var v = btn.getAttribute('data-days');
                if (v === 'month') { start = new Date(now.getFullYear(), now.getMonth(), 1); }
                else { start.setDate(now.getDate() - parseInt(v, 10)); }
                from.value = iso(start);
                to.value = iso(now);
                showLoader('Loading files...', 'Scanning the file server, this can take a moment.');
                btn.form.submit();
            });
        });

        // Filter button
        document.getElementById('filter-form').addEventListener('submit', function () {
            showLoader('Loading files...', 'Scanning the file server, this can take a moment.');
        });

        // Pagination links
        Array.prototype.forEach.call(document.querySelectorAll('.pagination a.page-link'), function (a) {
            a.addEventListener('click', function () { showLoader('Loading files...'); });
        });

        // ZIP download buttons: the server builds the ZIP first, so show progress until the download starts
        Array.prototype.forEach.call(document.querySelectorAll('.dl-btn'), function (a) {
            a.addEventListener('click', function () {
                clearDlCookie();
                showLoader('Preparing your ZIP...', 'Large parts can take a minute or more. Please keep this page open.');
                var timer = setInterval(function () {
                    if (document.cookie.indexOf('backup_dl=') !== -1) { // set by the server when the download begins
                        clearInterval(timer);
                        clearDlCookie();
                        hideLoader();
                    }
                }, 500);
                setTimeout(function () { clearInterval(timer); hideLoader(); }, 15 * 60 * 1000);
            });
        });
    })();
</script>
</body>
</html>