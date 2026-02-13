@extends('layouts.admin')
@section('header', 'Anonymous Staff Feedback')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold kca-navy">Staff Reviews</h4>
    <form action="{{ route('admin.feedback.read-all') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill">
            <i class="fas fa-check-double me-1"></i> Mark All as Read
        </button>
    </form>
</div>

<div class="row">
    @forelse($feedbacks as $item)
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm rounded-4 {{ !$item->is_read ? 'border-start border-4 border-warning' : '' }}">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>{{ $item->created_at->format('d M, h:i A') }}</span>
                        @if(!$item->is_read) <span class="badge bg-warning text-dark">NEW</span> @endif
                    </div>
                    <p class="mb-0">"{{ $item->message }}"</p>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center py-5 text-muted">No feedback yet.</p>
    @endforelse
</div>
@endsection