<?php

namespace App\Http\Controllers\Api;

use App\Enums\Errors\CommonError;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogsRequest;
use App\Http\Requests\UpdateBlogsRequest;
use App\Services\BlogServices;
use Illuminate\Http\Request;


class BlogController extends Controller
{
    /**
     * Constructor for the class.
     *
     * @param BlogService $BlogService
     */
    public function __construct(private readonly BlogServices $blogService = new BlogServices())
    {
        $this->middleware(['ability:' . \App\Enums\Tokens\TokenAbility::BLOG_VIEW->value])->only(['index', 'show']);
        $this->middleware(['ability:' . \App\Enums\Tokens\TokenAbility::BLOG_CREATE->value])->only(['Store']);
        $this->middleware(['ability:' . \App\Enums\Tokens\TokenAbility::BLOG_UPDATE->value])->only(['update']);
        $this->middleware(['ability:' . \App\Enums\Tokens\TokenAbility::BLOG_DELETE->value])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = $this->blogService->get(
                limit: $request->query('limit', 10),
                page: $request->query('page', 1)
            );
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage());
        }

        return response()->json([
            'true' => true,
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $error = CommonError::ERR_FORBIDDEN_ACCESS;

        return response()->json($error->toMap(), $error->httpStatusCode());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogsRequest $request)
    {
        try {
            $data = $this->blogService->create(
                data: $request->validated()
            );
        } catch (\Exception $exception) {
            return response()->jsonFailed($exception->getMessage());
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = $this->blogService->view(
                modelKey: $id
            );
        } catch (\Exception $exception) {
            return response()->jsonFailed($exception->getMessage());
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $error = CommonError::ERR_FORBIDDEN_ACCESS;

        return response()->json($error->toMap(), $error->httpStatusCode());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogsRequest $request, string $id)
    {
        try {
            $data = $this->blogService->update(
                modelKey: $id,
                data: $request->validated()
            );
        } catch (\Exception $exception) {
            return response()->jsonFailed($exception->getMessage());
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $data = $this->blogService->delete(
                modelKey: $id
            );
        } catch (\Exception $exception) {
            return response()->jsonFailed($exception->getMessage());
        }

        return response()->json([
            'status' => true,
            'message' => "success delete blogs",
            'data'  => []
        ]);
    }
}
