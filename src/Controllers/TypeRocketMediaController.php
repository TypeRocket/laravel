<?php

namespace TypeRocket\Controllers;

use TypeRocket\MediaProcesses\LocalStorage;
use TypeRocket\MediaProcesses\Setup;
use TypeRocket\TypeRocketMedia;
use TypeRocket\Form;
use TypeRocket\MediaProcesses\MediaProcess;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TypeRocketMediaController extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Runs top to bottom
     *
     * @var array
     */
    protected $processors;
    protected $where_like = 'like';

    public function __construct()
    {
        foreach (config('typerocket.media.controller_middleware', []) as $method => $middleware) {
            $middleware = (array) $middleware;
            foreach ($middleware as $name) {
                $this->middleware($name, ['only' => $method]);
            }
        }

        $this->processors = config('typerocket.media.processors', [
            Setup::class,
            LocalStorage::class
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = [
            'type' => $request->get('type'),
            'search' => $request->get('search'),
        ];
        $media = $this->jfeed($request);

        return view('typerocket::media.index', compact('media', 'filters'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function jfeed(Request $request)
    {
        $filters = [
            'type' => $request->get('type'),
            'search' => $request->get('search'),
        ];
        $query = $this->getMediaModel()->orderBy('id', 'desc');

        if ($filters['type'] && $filters['type'] != 'all') {
            $ext = $filters['type'] == 'pdf' ? ['pdf'] : ['jpg', 'png', 'gif', 'jpeg'];
            $query = $query->whereIn('ext', $ext);
        }

        if ($filters['search']) {
            $query = $query->where('caption', $this->where_like, '%' . $filters['search'] . '%');
        }

        return $query->paginate(35);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $form = new Form( $this->getMediaModelClass() , 'create', null, '/media');
        return view('typerocket::media.create', ['form' => $form]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file = $request->file('file');

        if( !empty($file) ) {
            $media = $this->getMediaModel();
            foreach($this->processors as $class) {
                /** @var $imageProcess MediaProcess */
                $imageProcess = new $class;
                $imageProcess->run($file, $media);
            }
            $media->save();
        }

        if ($request->ajax()) {
            return response(null, Response::HTTP_CREATED);
        }

        return redirect()->route('media.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return string
     */
    public function show($id)
    {
        return 'Nothing to see';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $form = new Form($this->getMediaModelClass(), 'update', $id, '/media/' . $id);
        $form->setRequest($request);
        return view('typerocket::media.edit', ['form' => $form]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $tr = (object) $request->input('tr');
        $file = $request->file('tr.file');

        $media = $this->getMediaModel()->findOrFail($id);

        if( !empty($file) ) {
	        foreach($this->processors as $class) {
		        /** @var $imageProcess MediaProcess */
		        $imageProcess = new $class;
		        if(method_exists($imageProcess, 'down')) {
			        $imageProcess->down($media);
		        }
	        }

            foreach($this->processors as $class) {
                /** @var $imageProcess MediaProcess */
                $imageProcess = new $class;
                $imageProcess->run($file, $media);
            }
        }

        $media->alt = $tr->alt;
        $media->caption = $tr->caption;
        $media->save();

        return redirect()->route('media.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $media = $this->getMediaModel()->findOrFail($id);
        foreach($this->processors as $class) {
            /** @var $imageProcess MediaProcess */
            $imageProcess = new $class();
            $imageProcess->down($media);
        }
        $media->delete();

        return redirect()->route('media.index');
    }

	/**
	 * @return \TypeRocket\TypeRocketMedia
	 */
	public function getMediaModel() {
		return new TypeRocketMedia();
    }

	public function getMediaModelClass() {
		return TypeRocketMedia::class;
	}
}
