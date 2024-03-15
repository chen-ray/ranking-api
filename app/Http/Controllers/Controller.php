<?php

namespace App\Http\Controllers;

use App\Http\Resources\BreakdownCollection;
use App\Http\Resources\RankingCollection;
use App\Jobs\BadmintonCrawler;
use App\Jobs\Breakdown;
use App\Jobs\CrawlerWeeks;
use App\Jobs\HandleCountryImg;
use App\Models\Country;
use App\Models\Ranking;
use App\Models\Week;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Octane\Facades\Octane;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        $model = Ranking::orderBy('id', 'desc')->first();
        $publicationId = $model->ranking_publication_id;

        [$men, $women, $manDoubles, $womanDoubles, $mixedDoubles] = Octane::concurrently([
            fn() => Ranking::where('ranking_publication_id', $publicationId)->where('ranking_category_id', 6)->limit(100)->get(),
            fn() => Ranking::where('ranking_publication_id', $publicationId)->where('ranking_category_id', 7)->limit(100)->get(),
            fn() => Ranking::where('ranking_publication_id', $publicationId)->where('ranking_category_id', 8)->limit(100)->get(),
            fn() => Ranking::where('ranking_publication_id', $publicationId)->where('ranking_category_id', 9)->limit(100)->get(),
            fn() => Ranking::where('ranking_publication_id', $publicationId)->where('ranking_category_id', 10)->limit(100)->get(),
        ]);

        $data = [
            'publicationId' => $publicationId,
            'men' => $men,
            'women' => $women,
            'manDoubles' => $manDoubles,
            'womanDoubles' => $womanDoubles,
            'mixedDoubles' => $mixedDoubles,
        ];

        return view('ranking', $data);
        //return view('welcome');
    }

    public function test(Request $request)
    {
        //$badminton = new BadmintonCrawler();
        //$badminton->rankingbreakdown();
        if (!$request->has('action')) {
            echo 'no action';
        }
        $action = $request->get('action');

        if ($action == 'crawler') {
            dispatch(new BadmintonCrawler());
        } else if ($action == 'breakdown' && $request->has('id')) {
            dispatch(new Breakdown(Ranking::find($request->get('id'))));
        } else if ($action == 'hello') {
            echo 'hello<br>';
        }

        //echo '<br>finish';
    }

    //https://extranet.bwf.sport/docs/flags-svg/china.svg

    public function api(Request $request): RankingCollection
    {

        $categoryId = $request->get('category_id', 6);
        $p1Country = $request->get('p1_country', 0);
        $publicationId = $request->get('publication_id');
        if (!$publicationId) {
            $model = Ranking::orderBy('id', 'desc')->first();
            $publicationId = $model->ranking_publication_id;
        }

        $ranking = Ranking::where('ranking_publication_id', $publicationId)
            ->where('ranking_category_id', $categoryId);

        if ($p1Country) {
            $ranking = $ranking->where('p1_country', $p1Country);
        }

        $rankings = $ranking->paginate(20);

        return new RankingCollection($rankings);
    }

    public function breakdowns(Request $request, int $rankingId): BreakdownCollection
    {
        $model = \App\Models\Breakdown::where('ranking_id', $rankingId)->get();
        if ($model->count() == 0) {
            Log::info('no breakdown, sync');
            $ranking = Ranking::find($rankingId);
            Breakdown::dispatchSync($ranking);
            $model = \App\Models\Breakdown::where('ranking_id', $rankingId)->get();
        }
        return new BreakdownCollection($model);
    }


    public function publicationIds()
    {
        $result = Ranking::distinct('ranking_publication_id')->orderBy('ranking_publication_id', 'desc')->pluck('ranking_publication_id');
        return $result->toArray();
    }

    /**
     * 分发爬虫任务
     * @param Request $request
     * @param string $action ranking or weeks
     * @return JsonResponse
     */
    public function crawler(Request $request, string $action): JsonResponse
    {
        if ($action == 'ranking') {
            $page = $request->get('page', 1);
            $catId = $request->get('catId', 6);
            $publicationId = $request->get('publicationId', 0);
            dispatch(new BadmintonCrawler($catId, $page, $publicationId));
        } elseif ($action == 'weeks') {
            CrawlerWeeks::dispatch();
        }

        return response()->json(['msg' => 'ok'], 200);
    }

    public function countryUrl(): JsonResponse
    {
        $countries = Country::where('flag_name_svg', 'not like', 'http%')->get();
        log::info('要处理的数据条数=>' . $countries->count());
        foreach ($countries as $country) {
            if (Str::startsWith($country->flag_url, '/')) {
                dispatch(new HandleCountryImg($country));
            }
        }
        return response()->json(['msg' => 'ok'], 200);
    }

    public function weeks(): JsonResponse
    {
        $weeks = Week::orderBy('id', 'desc')->limit('10')->get();
        return response()->json($weeks->toArray(), 200);
    }
}
