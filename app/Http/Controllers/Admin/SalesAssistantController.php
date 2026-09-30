<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Services\MissionMatcher;
use App\Services\SalesAssistant;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Assistant commercial (doc §44) : les questions du cahier des charges,
 * chacune adossée à des données officielles calculées par le système.
 */
class SalesAssistantController extends Controller
{
    public function index(Request $request, SalesAssistant $assistant, MissionMatcher $matcher)
    {
        $days = in_array($request->integer('days'), [30, 90, 365], true) ? $request->integer('days') : 90;

        $missions = Mission::whereIn('status', ['draft', 'open', 'closed'])->latest()->get(['id', 'reference', 'title']);
        $mission = $request->filled('mission_id') ? Mission::find($request->integer('mission_id')) : null;

        return view('admin.crm.assistant', [
            'promotion' => $assistant->promotionData(),
            'ranking' => $assistant->partnerRanking($days),
            'days' => $days,
            'missions' => $missions,
            'mission' => $mission,
            'profiles' => $mission ? $matcher->suggest($mission) : collect(),
            'productFacts' => fn ($p) => $assistant->productFacts($p),
        ]);
    }

    public function promote(SalesAssistant $assistant)
    {
        return $this->run(fn () => $assistant->promotionAdvice($assistant->promotionData()), 'promote');
    }

    public function partners(Request $request, SalesAssistant $assistant)
    {
        $days = in_array($request->integer('days'), [30, 90, 365], true) ? $request->integer('days') : 90;

        return $this->run(fn () => $assistant->partnerAdvice($assistant->partnerRanking($days), $days), 'partners');
    }

    private function run(callable $call, string $section)
    {
        try {
            $text = $call();
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('ai_result', $text)->with('ai_section', $section);
    }
}
