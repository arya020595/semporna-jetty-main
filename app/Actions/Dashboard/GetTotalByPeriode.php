<?php

namespace App\Actions\Dashboard;

use App\Models\SurveyEntries;
use App\Models\SurveyResearcher;
use App\Models\SurveyTemplate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetTotalByPeriode
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

        $groupType = $this->checkGroup($start, $end);

        $groupFields = $this->getGroupFields($groupType);

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
            ->groupBy(array_merge($groupFields, ["survey_entries.survey_template_id"]))
            ->when($groupFields, function ($query, $groupFields) {
                foreach ($groupFields as $group) {
                    $query->orderBy($group, "ASC");
                }
            })
            ->get(array_merge($groupFields, ["survey_entries.survey_template_id", DB::raw("count(survey_entries.survey_template_id) as total")]));
    }

    public function checkGroup($start, $end)
    {
        if (!$start || !$end) {
            return "years";
        }

        $carbonStart = Carbon::parse($start);
        $carbonEnd = Carbon::parse($end);

        $daysDiff = $carbonStart->diffInDays($carbonEnd);
        $groupBy = "years";

        if ($daysDiff < 365) {
            $groupBy = "months";
        }

        return $groupBy;
    }

    protected function getGroupFields($type)
    {
        if ($type == "years") {
            return ["year"];
        }

        return ["year", "month"];
    }

    public function formatToChart(Collection $data, $type = "years")
    {
        $arrLabel = [];
        $arrData = [];
        $arrColor = ['#269FDA', "#ED6325"];
        $template = SurveyTemplate::all();

        foreach ($template as $key => $itemTemplate) {
            $arrTotal = $data->filter(function ($item) use ($itemTemplate) {
                return $item->survey_template_id == $itemTemplate->id;
            })->pluck("total");

            $arrData[] = [
                "label" => $itemTemplate->name,
                "backgroundColor" => $arrColor[$key],
                "data" => $arrTotal
            ];
        }

        if ($type == "years") {
            $arrLabel = $data->pluck("year")->unique()->toArray();
        } else {
            $arrLabel = $data->map(function ($item) {
                return Carbon::createFromFormat('m', $item->month)->format('M') . " " . $item->year;
            })->unique()->toArray();
        }

        return [
            "labels" => array_values($arrLabel),
            "datasets" => $arrData
        ];
    }
}
