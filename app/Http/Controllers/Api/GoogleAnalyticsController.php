<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class GoogleAnalyticsController extends Controller
{
    /**
     * 获取 uv pv 数据
     *
     * @param Request $request
     * @return mixed
     */
    public function getUvPv(Request $request)
    {
        // type : day month week
        $type = $request->get('type', 'day');
        $prev = $request->get('prev', 8);

        $dateParts = $this->makeDateParts($type, $prev);
        $metrics   = [['ga:users', 'users'], ['ga:pageviews', 'pageviews']];

        return \GoogleAnalytics::getReport($dateParts, $metrics)->format();
    }

    protected function makeDateParts($type, $prev)
    {
        $methods = [
            'day'   => ['subDays', 'startOfDay', 'endOfDay'],
            'month' => ['subMonths', 'startOfMonth', 'endOfMonth'],
            'week'  => ['subWeeks', 'startOfWeek', 'endOfWeek'],
        ];

        list($subMethod, $startDateMethod, $endDateMethod) = Arr::get($methods, $type);

        $dateParts = [];
        for ($i = $prev; $i >= 1; $i--) {
            $startDate   = Carbon::now()->{$subMethod}($i)->{$startDateMethod}()->toDateString();
            $endDate     = Carbon::now()->{$subMethod}($i)->{$endDateMethod}()->toDateString();
            $dateParts[] = [$startDate, $endDate];
        }

        return $dateParts;
    }

}
