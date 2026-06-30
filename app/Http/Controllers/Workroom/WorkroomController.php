<?php

namespace App\Http\Controllers\Workroom;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Workroom;
use App\Models\Revision;
use App\Models\FactProjectActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WorkroomController extends Controller
{
    public function show(Project $project)
    {
        $user = Auth::user();
        $workroom = $project->workroom;

        if (!$workroom) {
            abort(404, 'Workroom belum tersedia.');
        }

        // Access check
        if ($user->isMahasiswa()) {
            abort_if($workroom->student_id !== $user->student->id, 403);
        } elseif ($user->isClient()) {
            abort_if($workroom->client_id !== $user->client->id, 403);
        }

        $workroom->load('revisions.requester');
        return view('workroom.workroom', compact('project', 'workroom', 'user'));
    }

    public function sendMessage(Request $request, Workroom $workroom)
    {
        $user = Auth::user();
        $request->validate(['message' => 'required|string|max:2000']);

        $messages = $workroom->messages ?? [];
        $messages[] = [
            'id' => count($messages) + 1,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role' => $user->role,
            'message' => $request->message,
            'created_at' => now()->toDateTimeString(),
            'type' => 'text',
        ];

        $workroom->update(['messages' => $messages]);

        return back()->with('success', 'Pesan terkirim!');
    }

    public function uploadDeliverable(Request $request, Workroom $workroom)
    {
        $user = Auth::user();
        abort_if(!$user->isMahasiswa(), 403);

        $request->validate([
            'deliverable_file' => 'required|file|max:20480',
            'notes' => 'nullable|string|max:500',
        ]);

        $path = $request->file('deliverable_file')->store('deliverables/' . $workroom->project_id, 'public');

        $deliverables = $workroom->deliverables ?? [];
        $deliverables[] = [
            'id' => count($deliverables) + 1,
            'filename' => $request->file('deliverable_file')->getClientOriginalName(),
            'path' => $path,
            'notes' => $request->notes,
            'uploaded_at' => now()->toDateTimeString(),
            'uploaded_by' => $user->name,
        ];

        $workroom->update([
            'deliverables' => $deliverables,
            'current_deliverable_notes' => $request->notes,
            'status' => 'submitted',
            'submitted_at' => now(),
            'progress_percentage' => 90,
        ]);

        // Update project status
        $workroom->project->update(['status' => 'in_review']);

        // Add system message to chat
        $messages = $workroom->messages ?? [];
        $messages[] = [
            'id' => count($messages) + 1,
            'user_id' => null,
            'user_name' => 'System',
            'role' => 'system',
            'message' => "📎 {$user->name} telah mengupload deliverable: {$deliverables[count($deliverables)-1]['filename']}",
            'created_at' => now()->toDateTimeString(),
            'type' => 'system',
        ];
        $workroom->update(['messages' => $messages]);

        return back()->with('success', 'Deliverable berhasil diupload! Client sedang mereview pekerjaan Anda.');
    }

    public function updateProgress(Request $request, Workroom $workroom)
    {
        abort_if(!Auth::user()->isMahasiswa(), 403);
        $request->validate(['progress' => 'required|integer|min:0|max:100']);

        $workroom->update(['progress_percentage' => $request->progress]);
        return back()->with('success', 'Progress diperbarui!');
    }

    public function requestRevision(Request $request, Workroom $workroom)
    {
        abort_if(!Auth::user()->isClient(), 403);
        $request->validate([
            'feedback' => 'required|string|min:10',
        ]);

        $project = $workroom->project;
        if ($project->revision_count >= $project->max_revisions) {
            return back()->with('error', 'Sudah mencapai batas maksimum revisi (' . $project->max_revisions . 'x).');
        }

        $revisionNumber = $workroom->revisions()->count() + 1;

        Revision::create([
            'workroom_id' => $workroom->id,
            'requested_by' => Auth::id(),
            'revision_number' => $revisionNumber,
            'feedback' => $request->feedback,
            'specific_changes' => $request->specific_changes ? explode(',', $request->specific_changes) : [],
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        $workroom->update(['status' => 'revision']);
        $project->update([
            'status' => 'revision',
            'revision_count' => $revisionNumber,
        ]);

        // System chat message
        $messages = $workroom->messages ?? [];
        $messages[] = [
            'id' => count($messages) + 1,
            'user_id' => null,
            'user_name' => 'System',
            'role' => 'system',
            'message' => "🔄 Client meminta revisi ke-{$revisionNumber}. Silakan perbaiki pekerjaan Anda.",
            'created_at' => now()->toDateTimeString(),
            'type' => 'revision',
        ];
        $workroom->update(['messages' => $messages]);

        FactProjectActivity::record([
            'project_id' => $project->id,
            'client_id' => $workroom->client_id,
            'student_id' => $workroom->student_id,
            'activity_type' => 'revision_requested',
            'activity_category' => 'project',
            'revision_count' => $revisionNumber,
            'project_title' => $project->title,
            'project_status' => 'revision',
        ]);

        return back()->with('success', 'Permintaan revisi ke-' . $revisionNumber . ' berhasil dikirim.');
    }

    public function approveWork(Request $request, Workroom $workroom)
    {
        abort_if(!Auth::user()->isClient(), 403);

        $workroom->update([
            'status' => 'approved',
            'approved_at' => now(),
            'progress_percentage' => 100,
        ]);

        // System message
        $messages = $workroom->messages ?? [];
        $messages[] = [
            'id' => count($messages) + 1,
            'user_name' => 'System',
            'role' => 'system',
            'message' => '✅ Client telah menyetujui pekerjaan! Silakan berikan rating dan cairkan dana.',
            'created_at' => now()->toDateTimeString(),
            'type' => 'approved',
        ];
        $workroom->update(['messages' => $messages]);

        $workroom->project->update(['status' => 'in_review']);

        return redirect()->route('payment.escrow', $workroom->project_id)
            ->with('success', '✅ Pekerjaan disetujui! Silakan berikan rating dan cairkan dana.');
    }

    public function disputeProject(Request $request, Workroom $workroom)
    {
        $workroom->update(['status' => 'disputed']);
        $workroom->project->update(['status' => 'disputed']);

        return back()->with('info', 'Dispute telah diajukan. Admin akan menyelesaikan dalam 2x24 jam.');
    }
}
