@extends('layouts.app')

@section('title', 'Feedback Demo')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h3>Feedback Widget Demo</h3>
            </div>
            <div class="card-body">
                <p>Below is the feedback widget embedded in an iframe:</p>

                <iframe src="{{ env('APP_URL', 'http://localhost:8103') }}/feedback-widget" width="100%" height="550px"
                    frameborder="0" title="Feedback" sandbox="allow-scripts allow-forms" loading="lazy"></iframe>

                <p class="mt-3">To embed the feedback widget in your own page, use the following code:</p>

                <pre class="bg-light p-3 rounded">
                    &lt;iframe
                        src="{{ env('APP_URL', 'http://localhost:8103') }}/feedback-widget"
                        width="100%"
                        height="550px"
                        frameborder="0"
                        title="Feedback"
                        sandbox="allow-scripts allow-forms"
                        loading="lazy"&gt;
                    &lt;/iframe&gt;
                </pre>
            </div>
        </div>
    </div>
@endsection
