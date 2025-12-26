@extends('layouts.app')

@section('title', 'Feedback Widget')

@section('content')
    <div id="feedback-widget" class="container mt-4">
        <div class="form-container">
            <form action="#" method="POST" class="contact-form" enctype="multipart/form-data" id="multistep-form">
                @csrf
                <h2>Contact Us</h2>
                <p id="form-description">Step 1 of 3: Contact Information</p>
                <div id="error-message" class="alert alert-danger mt-3" style="display: none;"></div>

                <!-- Step 1: Contacts -->
                <div class="step active" data-step="1">
                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="name">Name <span>*</span>:</label>
                            <input type="text" id="name" name="name" required>
                            <div id="name-error" class="error-message"></div>
                        </div>
                        <div class="form-group half-width">
                            <label for="email">Email <span>*</span>:</label>
                            <input type="email" id="email" name="email" required>
                            <div id="email-error" class="error-message"></div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="phone">Phone <span>*</span>:</label>
                            <input type="tel" id="phone" name="phone" required>
                            <div id="phone-error" class="error-message"></div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Details -->
                <div class="step" data-step="2">
                    <div class="form-group">
                        <label for="theme">Theme <span>*</span>:</label>
                        <input type="text" id="theme" name="theme" required>
                        <div id="theme-error" class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="text">Text <span>*</span>:</label>
                        <textarea id="text" name="text" rows="4" required></textarea>
                        <div id="text-error" class="error-message"></div>
                    </div>
                </div>

                <!-- Step 3: Attachments -->
                <div class="step" data-step="3">
                    <div class="form-group">
                        <label>Attachments (optional):</label>
                        <div class="drop-zone" id="drop-zone">
                            <input type="file" id="attachments" name="attachments[]" multiple class="drop-zone-input">
                            <i class="fas fa-paperclip icon-clip"></i>
                            <span class="drop-zone-text">Drag & drop files here or click to browse</span>
                        </div>
                        <div id="file-list"></div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="form-actions">
                    <button type="button" id="back-btn" class="nav-btn" style="display: none;">Back</button>
                    <button type="button" id="next-btn" class="nav-btn">Next</button>
                    <button type="submit" id="submit-btn" style="display: none;">Submit Feedback</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('styles')
    @vite(['resources/css/feedback-widget.css'])
@endsection

@section('scripts')
    @vite(['resources/js/feedback-widget.js'])
@endsection
