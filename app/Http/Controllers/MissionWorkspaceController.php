<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\MissionMessage;
use App\Models\MissionTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Espace projet d'une mission (doc §18-20) : équipe, tâches, messagerie et
 * fichiers. Servi à la fois côté admin (staff avec le module « missions »)
 * et côté partenaire (membres retenus de l'équipe uniquement) — le staff
 * étant redirigé hors de l'espace client, chaque côté a ses propres routes.
 */
class MissionWorkspaceController extends Controller
{
    public function show(Mission $mission)
    {
        $this->authorizeAccess($mission);

        $mission->load([
            'teamApplications.user.professionalProfile',
            'tasks.assignee',
            'messages' => fn ($q) => $q->with('user')->oldest(),
        ]);

        $isStaff = $this->isAdminContext();
        $prefix = $isStaff ? 'admin.missions.workspace' : 'dashboard.partner.missions.workspace';
        $view = $isStaff ? 'admin.missions.workspace' : 'pages.shop.partner.mission_workspace';

        return view($view, compact('mission', 'isStaff', 'prefix'));
    }

    public function storeMessage(Request $request, Mission $mission)
    {
        $this->authorizeAccess($mission);

        $validated = $request->validate([
            'body' => 'nullable|string|max:5000|required_without:attachment',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip,jpg,jpeg,png,webp',
        ]);

        $data = ['user_id' => Auth::id(), 'body' => $validated['body'] ?? null];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $data['attachment_path'] = $file->store("missions/{$mission->id}", 'local');
            $data['attachment_name'] = $file->getClientOriginalName();
        }

        $mission->messages()->create($data);

        return back()->with('success', 'Message envoyé.');
    }

    public function storeTask(Request $request, Mission $mission)
    {
        $this->authorizeAccess($mission);
        abort_unless($this->isAdminContext(), 403);

        $teamIds = $mission->teamApplications()->pluck('user_id')->all();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'assigned_to' => ['nullable', 'integer', 'in:' . implode(',', $teamIds ?: [0])],
            'due_date' => 'nullable|date',
        ]);

        $mission->tasks()->create($validated + ['status' => 'todo', 'created_by' => Auth::id()]);

        return back()->with('success', 'Tâche ajoutée.');
    }

    /**
     * Le staff modifie toute tâche ; un membre de l'équipe ne change que le
     * statut des tâches qui lui sont assignées (doc §19 : permissions minimales).
     */
    public function updateTask(Request $request, MissionTask $task)
    {
        $mission = $task->mission;
        $this->authorizeAccess($mission);

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(MissionTask::STATUSES)),
        ]);

        if (!$this->isAdminContext() && $task->assigned_to !== Auth::id()) {
            abort(403, 'Vous ne pouvez modifier que les tâches qui vous sont assignées.');
        }

        $task->update($validated);

        return back()->with('success', 'Tâche mise à jour.');
    }

    public function downloadAttachment(MissionMessage $message)
    {
        $this->authorizeAccess($message->mission);
        abort_unless($message->attachment_path && Storage::disk('local')->exists($message->attachment_path), 404);

        return Storage::disk('local')->download($message->attachment_path, $message->attachment_name);
    }

    private function isAdminContext(): bool
    {
        return request()->routeIs('admin.*');
    }

    private function authorizeAccess(Mission $mission): void
    {
        $user = Auth::user();

        if ($this->isAdminContext()) {
            // Les routes admin sont déjà protégées par le module « missions ».
            return;
        }

        abort_unless($mission->hasWorkspace() && $mission->isTeamMember($user), 403, "Cet espace projet est réservé à l'équipe de la mission.");
    }
}
