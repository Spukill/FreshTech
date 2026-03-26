@extends('layouts.app')

@section('title', 'View Reports | ' . config('app.name'))

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}" class="text-decoration-none text-primary">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ url('/dashboard') }}" class="text-decoration-none text-primary">Dashboard</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Reports</li>
    </ol>
</nav>

<div class="container mt-4">
    <h1 class="fs-4 mb-4">User Reports</h1>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Report Type</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="reportTableBody">
                @forelse($reports as $report)
                <tr id="row-{{ $report->id }}">
                    {{-- Report Type --}}
                    <td>
                        @if ($report->repReview != NULL)
                            <span class="fw-bold text-uppercase small">Review</span>
                        @else
                            <span class="fw-bold text-uppercase small">Product</span>
                        @endif
                    </td>

                    {{-- Description - Truncated for better table layout --}}
                    <td class="text-muted">
                        {{ Str::limit($report->description, 50) }}
                    </td>

                    {{-- Status Badge --}}
                    <td>
                        @php
                            $statusClass = match($report->status) {
                                'pending'  => 'bg-warning text-dark', // Yellow
                                'accepted' => 'bg-success',          // Green
                                'rejected' => 'bg-danger',           // Red
                                default    => 'bg-secondary',        // Gray fallback
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">
                            {{ ucfirst($report->status) }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="text-center">
                        @if($report->status == "pending")
                            <button class="btn btn-primary btn-sm botao-handle-report mt-2 botao" data-id="{{ $report->id }}">
                                <i class="bi bi-eye"></i> View Full Report
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">No reports found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="handleReportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Handle Report</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="handleReportForm">
                    <input type="hidden" id="report_id">

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">User's Review</label>
                        <div id="comment" class="form-control-plaintext border-bottom pb-2"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Report Reason</label>
                        <div id="description" class="form-control-plaintext border-bottom pb-2">
                            </div>
                    </div>

                    <div class="d-flex flex-column gap-3 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reportAction" id="accept" value="accept" checked>
                            <label class="form-check-label text-success fw-bold" for="accept">
                                Accept Report
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reportAction" id="reject" value="reject">
                            <label class="form-check-label text-danger fw-bold" for="reject">
                                Reject Report
                            </label>
                        </div>
                    </div>

                    <button type="submit" id="submit-report-decision" class="btn btn-primary w-100">
                        Submit Decision
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
