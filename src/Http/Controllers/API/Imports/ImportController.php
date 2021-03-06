<?php

namespace Fusion\Http\Controllers\API\Imports;

use Fusion\Http\Controllers\Controller;
use Fusion\Http\Resources\ImportResource;
use Fusion\Models\Import;
use Fusion\Rules\ImportStrategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return resource
     */
    public function index(Request $request)
    {
        $this->authorize('imports.viewAny');

        $imports = Import::orderBy('name')->paginate(25);

        return ImportResource::collection($imports);
    }

    /**
     * Display a specific resource.
     *
     * @param Import $import
     *
     * @return resource
     */
    public function show(Import $import)
    {
        $this->authorize('imports.view');

        return new ImportResource($import);
    }

    /**
     * Store newly created resource in storage.
     *
     * @param Request $request
     *
     * @return ImportResource
     */
    public function store(Request $request)
    {
        $this->authorize('imports.create');

        $attributes = $request->validate([
            'name'     => 'required',
            'handle'   => 'required|unique:imports,handle',
            'source'   => 'required_without:upload|url',
            'module'   => 'required',
            'group'    => 'required|integer',
            'strategy' => ['required', 'array', new ImportStrategy()],
            'backup'   => 'required|boolean',
            // 'upload'   => 'required_without:source|string'
        ]);

        // Move tmp file created through <p-upload> to more permanent folder..
        // if ($attributes['upload']) {
        //     Storage::move($attributes['upload'], "imports/{$attributes['handle']}.csv");
        // }

        $import = Import::create($attributes);

        return new ImportResource($import);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Import  $import
     *
     * @return resource
     */
    public function update(Request $request, Import $import)
    {
        $this->authorize('imports.update');

        // Validate..
        $attributes = $request->validate([
            'name'     => 'required',
            'handle'   => 'required|unique:imports,id,'.$import->id,
            'source'   => 'required_without:upload|url',
            'module'   => 'required',
            'group'    => 'required|integer',
            'strategy' => ['required', 'array', new ImportStrategy()],
            'backup'   => 'required|boolean',
            // 'upload'   => 'required_without:source|string'
        ]);

        // Move tmp file created through <p-upload> to more permanent folder..
        // if ($attributes['upload']) {
        //     Storage::move($attributes['upload'], "imports/{$attributes['handle']}.csv");
        // }

        $import->update($attributes);

        return new ImportResource($import);
    }

    /**
     * Destroy specific resource from storage.
     *
     * @return Import $import
     */
    public function destroy(Import $import)
    {
        $this->authorize('imports.delete');

        $import->delete();
    }
}
