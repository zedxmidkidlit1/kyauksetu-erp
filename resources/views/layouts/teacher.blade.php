<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Teacher Portal' }} - {{ config('app.name', 'ERP') }}</title>
        <style>
            :root {
                color-scheme: light;
                --bg: #f6f7f9;
                --panel: #ffffff;
                --text: #1f2937;
                --muted: #667085;
                --line: #d9dee7;
                --brand: #0f766e;
                --brand-dark: #115e59;
                --accent: #7c3aed;
                --soft: #ecfdf5;
                --warning: #fff7ed;
                --danger: #b42318;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                background: var(--bg);
                color: var(--text);
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                line-height: 1.5;
            }

            a {
                color: var(--brand);
                text-decoration: none;
            }

            a:hover {
                text-decoration: underline;
            }

            .shell {
                min-height: 100vh;
            }

            .topbar {
                background: var(--panel);
                border-bottom: 1px solid var(--line);
            }

            .topbar-inner,
            .main {
                margin: 0 auto;
                max-width: 1180px;
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .topbar-inner {
                align-items: center;
                display: flex;
                gap: 1rem;
                justify-content: space-between;
                padding-bottom: 1rem;
                padding-top: 1rem;
            }

            .brand {
                color: var(--text);
                font-weight: 800;
            }

            .nav {
                align-items: center;
                display: flex;
                flex-wrap: wrap;
                gap: .7rem;
                justify-content: flex-end;
            }

            .main {
                padding-bottom: 2.5rem;
                padding-top: 1.25rem;
            }

            .page-header {
                align-items: flex-start;
                display: flex;
                gap: 1rem;
                justify-content: space-between;
                margin-bottom: 1rem;
            }

            .grid {
                display: grid;
                gap: 1rem;
            }

            .grid-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .grid-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .stack {
                display: grid;
                gap: 1rem;
            }

            .card {
                background: var(--panel);
                border: 1px solid var(--line);
                border-radius: .5rem;
                padding: 1rem;
            }

            .card-heading {
                margin-bottom: .75rem;
            }

            h1, h2, h3, p {
                margin-top: 0;
            }

            h1 {
                font-size: clamp(1.5rem, 4vw, 2.2rem);
                line-height: 1.15;
                margin-bottom: .35rem;
            }

            h2 {
                font-size: 1.05rem;
                margin-bottom: .25rem;
            }

            h3 {
                font-size: .95rem;
                margin-bottom: .25rem;
            }

            .muted {
                color: var(--muted);
            }

            .button {
                align-items: center;
                background: var(--brand);
                border: 0;
                border-radius: .375rem;
                color: #fff;
                cursor: pointer;
                display: inline-flex;
                font: inherit;
                font-weight: 700;
                justify-content: center;
                min-height: 2.5rem;
                padding: .55rem .9rem;
            }

            .button:hover {
                background: var(--brand-dark);
                text-decoration: none;
            }

            .link-button {
                background: transparent;
                border: 0;
                color: var(--brand);
                cursor: pointer;
                font: inherit;
                padding: 0;
            }

            label {
                display: block;
                font-size: .875rem;
                font-weight: 700;
                margin-bottom: .35rem;
            }

            input {
                background: #fff;
                border: 1px solid var(--line);
                border-radius: .375rem;
                color: var(--text);
                font: inherit;
                padding: .65rem .75rem;
                width: 100%;
            }

            .field {
                margin-bottom: 1rem;
            }

            .error {
                color: var(--danger);
                font-size: .875rem;
                margin-top: .35rem;
            }

            .notice {
                background: var(--soft);
                border: 1px solid #a7f3d0;
                border-radius: .5rem;
                margin-bottom: 1rem;
                padding: .75rem 1rem;
            }

            .table-wrap {
                overflow-x: auto;
            }

            table {
                border-collapse: collapse;
                min-width: 760px;
                width: 100%;
            }

            th, td {
                border-bottom: 1px solid var(--line);
                padding: .75rem;
                text-align: left;
                vertical-align: top;
            }

            th {
                color: var(--muted);
                font-size: .75rem;
                text-transform: uppercase;
            }

            .badge {
                background: #eef2f6;
                border-radius: 999px;
                display: inline-flex;
                font-size: .75rem;
                font-weight: 700;
                padding: .2rem .55rem;
                text-transform: capitalize;
            }

            .stat {
                color: var(--accent);
                font-size: 2rem;
                font-weight: 800;
                line-height: 1;
            }

            .empty-state {
                background: var(--warning);
                border: 1px solid #fed7aa;
                border-radius: .5rem;
                color: #7c2d12;
                padding: .85rem 1rem;
            }

            @media (max-width: 760px) {
                .topbar-inner,
                .page-header {
                    display: block;
                }

                .nav {
                    justify-content: flex-start;
                    margin-top: .75rem;
                }

                .grid-2,
                .grid-3 {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <div class="shell">
            <header class="topbar">
                <div class="topbar-inner">
                    <a class="brand" href="{{ auth()->user()?->hasRole('teacher') ? route('teacher.dashboard') : route('teacher.login') }}">Teacher Portal</a>
                    <nav class="nav">
                        @auth
                            @if (auth()->user()->hasRole('teacher'))
                                <a href="{{ route('teacher.dashboard') }}">Dashboard</a>
                                <a href="{{ route('teacher.profile') }}">Profile</a>
                                <a href="{{ route('teacher.assignments') }}">Assignments</a>
                                <a href="{{ route('teacher.timetable') }}">Timetable</a>
                                <a href="{{ route('teacher.classes') }}">Classes</a>
                                <a href="{{ route('teacher.announcements') }}">Announcements</a>
                                <form method="POST" action="{{ route('teacher.logout') }}">
                                    @csrf
                                    <button class="link-button" type="submit">Logout</button>
                                </form>
                            @else
                                <a href="{{ route('teacher.login') }}">Login</a>
                            @endif
                        @else
                            <a href="{{ route('teacher.login') }}">Login</a>
                        @endauth
                    </nav>
                </div>
            </header>

            <main class="main">
                @if (session('status'))
                    <div class="notice">{{ session('status') }}</div>
                @endif

                @yield('content')
            </main>
        </div>
    </body>
</html>
