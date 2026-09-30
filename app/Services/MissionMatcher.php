<?php

namespace App\Services;

use App\Models\Mission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MissionMatcher
{
    /**
     * Suggère les professionnels les plus adaptés à une mission (doc §17).
     * Score explicable sur 100 : compétences 40, ville 20, vérification 20,
     * badges 10, disponibilité 10. Le système recommande, l'admin décide.
     *
     * @return Collection<int, array{user: User, score: int, reasons: array<int, string>, applied: bool}>
     */
    public function suggest(Mission $mission, int $limit = 10): Collection
    {
        $appliedIds = $mission->applications()->pluck('user_id')->all();
        $missionSkills = collect($mission->required_skills ?? [])->map(fn ($s) => Str::lower(trim($s)))->filter();

        return User::where('role', 'partner')
            ->where('partner_status', 'approved')
            ->whereIn('partner_type', $mission->targetTypes())
            ->with(['professionalProfile'])
            ->withCount(['userBadges as valid_badges_count' => fn ($q) => $q->valid()])
            ->get()
            ->map(function (User $user) use ($mission, $missionSkills, $appliedIds) {
                $profile = $user->professionalProfile;
                $score = 0;
                $reasons = [];

                $userSkills = collect($profile?->skills ?? [])->map(fn ($s) => Str::lower(trim($s)));
                if ($missionSkills->isNotEmpty()) {
                    $matched = $missionSkills->filter(fn ($ms) => $userSkills->contains(fn ($us) => Str::contains($us, $ms) || Str::contains($ms, $us)));
                    if ($matched->isNotEmpty()) {
                        $score += (int) round(40 * $matched->count() / $missionSkills->count());
                        $reasons[] = 'Compétences : ' . $matched->count() . '/' . $missionSkills->count();
                    }
                }

                if ($mission->city && $profile?->city && Str::lower(trim($profile->city)) === Str::lower(trim($mission->city))) {
                    $score += 20;
                    $reasons[] = 'Basé à ' . $profile->city;
                }

                $level = $profile?->verification_level ?? 1;
                $score += $level * 4;
                $reasons[] = "Vérification {$level}/5";

                if ($user->valid_badges_count > 0) {
                    $score += min($user->valid_badges_count * 5, 10);
                    $reasons[] = $user->valid_badges_count . ' badge(s)';
                }

                $availability = $profile?->availability;
                if ($availability === 'available') {
                    $score += 10;
                    $reasons[] = 'Disponible';
                } elseif ($availability === 'busy') {
                    $score += 3;
                }

                return [
                    'user' => $user,
                    'score' => min($score, 100),
                    'reasons' => $reasons,
                    'applied' => in_array($user->id, $appliedIds, true),
                ];
            })
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }
}
