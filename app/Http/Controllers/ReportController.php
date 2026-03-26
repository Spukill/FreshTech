<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Review;
use App\Models\ReportReview;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    public function show() {
        Gate::authorize('manageReport', Report::class);
        $reports = Report::orderByRaw("
            CASE 
                WHEN status = 'pending' THEN 1 
                WHEN status = 'accepted' THEN 2 
                WHEN status = 'rejected' THEN 3 
                ELSE 4 
            END
        ")->get();
        return view('pages.reports.reports', compact('reports'));
    }

    public function get($reportId) {
        Gate::authorize('manageReport', Report::class);
        $report = Report::find($reportId);
        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }
        $report->load('repReview.review');
        return response()->json($report);
    }

    public function create(Request $request) {
        $request->validate([
            'description' => 'required|string|min:10|max:1000',
        ]);
        
        $review = Review::findOrFail($request->reviewId);
        Gate::authorize('createReport', [Report::class, $review]);
        $report = Report::create([
            'id_buyer' => Auth::user()->buyer->id,
            'description' => $request->description,
            'status' => "pending",
        ]);
        
        $repReview = ReportReview::create([
            'id_report' => $report->id,
            'id_review' => $review->id,
        ]);

        return response()->json([
            'success' => 'Report Created!',
            'promotion' => $report
        ]);
    }

    public function update(Request $request) {
        Gate::authorize('manageReport', Report::class);
        $request->validate([
            'status' => 'required|string|in:accept,reject',
        ]);
        $reports = null;
        $report = Report::findOrFail($request->reportId);
        if ($request->status == "reject") {
            $report->update([
                'status' => "rejected",
            ]);
        } else {
            $reports = $report->repReview->review->reports;
            $reports = $reports->map(function ($item) {
                $item->report->update(['status' => 'accepted']);
                return $item->report; 
            });
            $report->repReview->review->delete();
        }

        return response()->json([
            'success' => 'Report updated!',
            'report' => $report,
            'reports' => $reports
        ]);
    }
}
