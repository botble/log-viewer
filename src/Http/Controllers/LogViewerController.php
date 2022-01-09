<?php

namespace Botble\LogViewer\Http\Controllers;

use Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\LogViewer\Entities\Log;
use Botble\LogViewer\Exceptions\FilesystemException;
use Botble\LogViewer\Exceptions\LogNotFoundException;
use Botble\LogViewer\LogViewer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LogViewerController extends BaseController
{

    /**
     * @var int|mixed
     */
    protected $perPage = 30;

    /**
     * @var string
     */
    protected $showRoute = 'log-viewer::logs.show';

    /**
     * @var LogViewer
     */
    protected $logViewer;

    /**
     * LogViewerController constructor.
     * @param LogViewer $logViewer
     */
    public function __construct(LogViewer $logViewer)
    {
        $this->logViewer = $logViewer;
        $this->perPage = config('plugins.log-viewer.general.per-page', $this->perPage);
        Assets::addScripts(['moment', 'datetimepicker'])
            ->addStyles(['datetimepicker']);
    }

    /**
     * List all logs.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|View
     */
    public function listLogs(Request $request)
    {
        page_title()->setTitle(trans('plugins/log-viewer::general.name'));

        Assets::addStylesDirectly(['/vendor/core/plugins/log-viewer/css/log-viewer.css']);

        $stats = $this->logViewer->statsTable();
        $headers = $stats->header();
        $rows = $this->paginate($stats->rows(), $request);

        return view('plugins/log-viewer::logs', compact('headers', 'rows'));
    }

    /**
     * Paginate logs.
     *
     * @param array $data
     * @param Request $request
     * @return LengthAwarePaginator
     */
    protected function paginate(array $data, Request $request)
    {
        $page = $request->get('page', 1);
        $offset = ($page * $this->perPage) - $this->perPage;
        $items = array_slice($data, $offset, $this->perPage, true);
        $rows = new LengthAwarePaginator($items, count($data), $this->perPage, $page);

        $rows->setPath($request->url());

        return $rows;
    }

    /**
     * Show the log.
     *
     * @param string $date
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|View
     */
    public function show($date)
    {
        page_title()->setTitle(trans('plugins/log-viewer::general.name') . ' ' . $date);

        $log = $this->getLogOrFail($date);
        $levels = $this->logViewer->levelsNames();
        $entries = $log->entries()->paginate($this->perPage);

        return view('plugins/log-viewer::show', compact('log', 'levels', 'entries'));
    }

    /**
     * @param string|null $date
     * @return Log|null
     */
    protected function getLogOrFail($date)
    {
        try {
            return $this->logViewer->get($date);
        } catch (LogNotFoundException $ex) {
            abort(404, $ex->getMessage());
        }
    }

    /**
     * Filter the log entries by level.
     *
     * @param string $date
     * @param string $level
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|RedirectResponse|View
     */
    public function showByLevel($date, $level)
    {
        page_title()->setTitle(trans('plugins/log-viewer::general.name') . ' ' . $date);

        $log = $this->getLogOrFail($date);

        if ($level === 'all') {
            return redirect()->route($this->showRoute, [$date]);
        }

        $levels = $this->logViewer->levelsNames();
        $entries = $this->logViewer->entries($date, $level)->paginate($this->perPage);

        return view('plugins/log-viewer::show', compact('log', 'levels', 'entries'));
    }

    /**
     * Download the log
     *
     * @param string $date
     * @return BinaryFileResponse
     */
    public function download($date)
    {
        return $this->logViewer->download($date);
    }

    /**
     * Delete a log.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws FilesystemException
     */
    public function delete(Request $request)
    {
        if (!$request->ajax()) {
            abort(405, 'Method Not Allowed');
        }

        $date = $request->get('date');

        return response()->json([
            'result' => $this->logViewer->delete($date) ? 'success' : 'error',
        ]);
    }
}
