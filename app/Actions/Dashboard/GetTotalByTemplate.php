<?php

namespace App\Actions\Dashboard;

use App\Models\SurveyEntries;
use App\Models\SurveyResearcher;
use App\Models\SurveyTemplate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetTotalByTemplate
{
    /**
     * Execute the action
     *
     * @param  array  $data
     * @return Collection
     */
    public function execute(array $filters, $userId = false)
    {
        $surveyEntriesMdl = new SurveyEntries();
        $surveyResearcherMdl = new SurveyResearcher();

        $start = $filters["start"] ?? false;
        $end = $filters["end"] ?? false;

        return SurveyEntries::with("surveyTemplate")
            ->join(
                $surveyResearcherMdl->getTable(),
                $surveyResearcherMdl->qualifyColumn('id'),
                "=",
                $surveyEntriesMdl->qualifyColumn("survey_researcher_id")
            )
            ->when($start, function (Builder $query, string $start) {
                return $query->where(DB::raw("DATE(survey_entries.created_at)"), ">=", $start);
            })
            ->when($end, function (Builder $query, string $end) {
                return $query->where(DB::raw("DATE(survey_entries.created_at)"), "<=", $end);
            })
            ->when($userId, function (Builder $query, int $userId) use ($surveyResearcherMdl) {
                return $query->where($surveyResearcherMdl->qualifyColumn("user_id"), $userId);
            })
            ->groupBy($surveyEntriesMdl->qualifyColumn("survey_template_id"))
            ->get([$surveyEntriesMdl->qualifyColumn("survey_template_id"), DB::raw("count(survey_entries.survey_template_id) as total")]);
    }

    public function formatToChart(Collection $data)
    {
        $arrLabel = [];
        $arrData = [];

        $arrTemplate = SurveyTemplate::all();

        foreach ($arrTemplate as $template) {
            $arrLabel[] = $template->name;

            $totalEntries = $data->firstWhere("survey_template_id", $template->id);

            $arrData[] = optional($totalEntries)->total ?? 0;
        }

        return [
            "labels" => $arrLabel,
            "datasets" => [
                [
                    "label" => "Total Survey",
                    "backgroundColor" => ['#269FDA', "#ED6325"],
                    "data" => $arrData
                ],
            ]
        ];
    }
}
