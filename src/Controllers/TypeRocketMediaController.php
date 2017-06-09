<?php

namespace TypeRocket\Controllers;

use TypeRocket\MediaProcesses\LocalStorage;
use TypeRocket\MediaProcesses\Setup;
use TypeRocket\TypeRocketMedia;
use TypeRocket\Form;
use TypeRocket\MediaProcesses\MediaProcess;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TypeRocketMediaController extends Controller
{

    /**
     * Runs top to bottom
     *
     * @var array
     */
    protected $processors;

    public function __construct()
    {
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
        $query = TypeRocketMedia::orderBy('id', 'desc');

        if ($filters['type']) {
            $ext = $filters['type'] == 'pdf' ? ['pdf'] : ['jpg', 'png', 'gif', 'jpeg'];
            $query = $query->whereIn('ext', $ext);
        }

        if ($filters['search']) {
            $query = $query->where('caption', 'like', '%' . $filters['search'] . '%');
        }

        $media = $query->paginate(35);

        return view('typerocket::media.index', compact('media', 'filters'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function jfeed()
    {

        if(!empty($_GET['search'])) {
            $media = TypeRocketMedia::orderBy('id', 'desc')->where('caption', 'like', '%' . $_GET['search'] . '%')->paginate(35);
        } else {
            $media = TypeRocketMedia::orderBy('id', 'desc')->paginate(35);
        }

        return $media;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $form = new Form( TypeRocketMedia::class , 'create', null, '/media');
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
            $media = new TypeRocketMedia();
            foreach($this->processors as $class) {
                /** @var $imageProcess MediaProcess */
                $imageProcess = new $class;
                $imageProcess->run($file, $media);
            }
            $media->save();
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
        $form = new Form(TypeRocketMedia::class, 'update', $id, '/media/' . $id);
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

        $media = TypeRocketMedia::findOrFail($id);

        if( !empty($file) ) {
            (new LocalStorage)->down($media);
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
        $media = TypeRocketMedia::findOrFail($id);
        foreach($this->processors as $class) {
            /** @var $imageProcess MediaProcess */
            $imageProcess = new $class();
            $imageProcess->down($media);
        }
        $media->delete();

        return redirect()->route('media.index');
    }
}
