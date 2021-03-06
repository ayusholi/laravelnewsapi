<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsApi;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function home(Request $request)
    {
        $response = $this->determineMethodHandler($request);
        $apiModel = new NewsApi();
        $response['news'] = $apiModel->fetchNewsFromSource($response['sourceId']);
        $response['newsSources'] = $this->fetchAllNewsSources();
        return view('welcome', $response);
    }
    /**
     * @param $request
     * @return mixed
     */
    protected function determineMethodHandler($request)
    {
        if ($request->isMethod('get')) {
            $response['sourceName'] = config('app.default_news_source');
            $response['sourceId'] = config('app.default_news_source_id');
        } else {
            $request->validate([
                'source' => 'required|string',
            ]);
            $split_input = explode(':', $request->source);
            $response['sourceId'] = trim($split_input[0]);
            $response['sourceName'] = trim($split_input[1]);
        }
        return $response;
    }
    /**
     * @return mixed
     */
    public function fetchAllNewsSources()
    {
        $response = Cache::remember('allNewsSources', 22 * 60, function () {
            $api = new NewsApi;
            return $api->getAllSources();
        });
        return $response;
    }

    /**
     * return then news for API
     */
    public function sendNews(Request $request)
    {
        $response = $this->determineMethodHandler($request);
        $apiModel = new NewsApi();
        $response['news'] = $apiModel->fetchNewsFromSource($response['sourceId']);
        $response['newsSources'] = $this->fetchAllNewsSources();
        return json_encode($response);
    }


    /**
     * Get News from Country
     */
    public function getNewsFromCountry(Request $request)
    {
        $response = $this->determineMethodHandler($request);
        $apiModel = new NewsApi();
        $response['news'] = $apiModel->fetchNewsFromSource($response['country']);
        $response['newsSources'] = $this->fetchAllNewsSources();
        return json_encode($response);
    }

    /**
     * Fetch News
     */
    public function fetchNews()
    {
        return view('news');
    }
}
