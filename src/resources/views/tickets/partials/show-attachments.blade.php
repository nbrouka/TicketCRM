<!-- Attachments Section -->
@if ($ticket->media->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <h4>Attachments</h4>
            <div class="row">
                @foreach ($ticket->media as $media)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-file fa-3x text-muted mb-2"></i>
                                <h6 class="card-title">{{ $media->file_name }} </h6>
                                <p class="card-text text-muted small">
                                    {{ number_format($media->size / 1024, 2) }} KB
                                </p>
                                @php
                                    $fileName = $media->file_name;
                                    // Ensure the file name has an extension
                                    if (!pathinfo($fileName, PATHINFO_EXTENSION)) {
                                        // If no extension, try to get it from the mime type or use a generic one
                                        $extension = $media->mime_type
                                            ? explode('/', $media->mime_type)[1] ?? 'file'
                                            : 'file';
                                        $fileName = $fileName . '.' . $extension;
                                    }
                                @endphp
                                <a href="{{ route('tickets.download', [$ticket->id, $media->id]) }}"
                                    class="btn btn-sm btn-outline-primary" download="{{ $fileName }}">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
