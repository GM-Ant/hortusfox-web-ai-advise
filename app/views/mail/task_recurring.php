<!doctype html>
<html lang="{{ getLocale() }}">
    <head>
        <meta charset="utf-8"/>

		<title>{{ __('app.mail_info_task_recurring') }}</title>

        @if (ThemeModule::hasMailStyles())
        <style>
            {{ ThemeModule::getMailStyles() }}
        </style>
        @endif
    </head>

    <body>
        <h1>{{ __('app.mail_info_task_recurring') }}</h1>

        <p>
            {!! __('app.mail_info_task_recurring_hint', ['name' => $task->get('title'), 'date' => date('Y-m-d', strtotime($task->get('due_date'))), 'time' => $task->get('recurring_time'), 'url' => workspace_url('/tasks')]) !!}
        </p>

        <p>
            <small>Powered by {{ env('APP_NAME') }}</small>
        </p>
    </body>
</html>