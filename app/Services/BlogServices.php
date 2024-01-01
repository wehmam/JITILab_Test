<?php

namespace App\Services;

use App\Enums\Errors\CommonError;
use Illuminate\Database\Eloquent\Model;
use App\Models\Blog;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * The BlogService class provides methods for managing Blogs.
 *
 * @package App\Services\Reminders
 * @author  Imam Maulana Ashari <https://github.com/wehmam>
 * */
class BlogServices
{
    private array $defaultColumns = [
        'id', 'uuid' , 'title', 'description', 'image', 'created_at', 'updated_at'
    ];

    /**
     * Retrieves an array of Blogs .
     *
     * @param int $limit The maximum number of reminders to retrieve. Defaults to 10.
     * @param int $page The page number of the reminders to retrieve. Defaults to 1.
     * @return array The array of reminders and limit information.
     */
    public function get(int $limit = 10, int $page = 1): array
    {
        $blogs = Blog::query()
            ->select($this->defaultColumns)
            ->orderBy('id', 'desc')
            ->simplePaginate(
                perPage: $limit,
                page: $page
            );

        foreach ($blogs->items() as $blog) {
            $blog->image = Storage::exists($blog->image) ? url(Storage::url($blog->image)) : $blog->image;
        }

        return $blogs->items();
    }

    /**
     * Creates a new blogs.
     *
     * @param array $data An array of data containing the details of the blogs.
     * @return array The details of the created blogs.
     */
    public function create(array $data): array
    {
        if($data["image"]) {
            $data["image"] = FileServices::uploadFile($data["image"]);
        }

        $data['uuid'] = \Str::uuid();
        $data['title'] = $data['title'];
        $data['description'] = $data['description'];
        $blog = Blog::query()->create($data);
        $blog->refresh();

        return $blog->only($this->defaultColumns);
    }

    /**
     * Retrieves an array containing the specified blog model data.
     *
     * @param int|string $modelKey The key of the model.
     * @return array An array containing the model data.
     * @throws \Exception
     */
    public function view(int|string $modelKey): array
    {
        return $this->loadModel($modelKey)->only($this->defaultColumns) ?? [];
    }

    /**
     * Load a model by the given model key.
     *
     * @param int|string $modelKey The model key.
     * @return Model The loaded model.
     * @throws \Exception When the blog is not found.
     */
    private function loadModel(int|string $modelKey): Model
    {
        $reminder = Blog::query()
            ->select($this->defaultColumns)
            ->where('id', $modelKey)
            ->first();

        if (blank($reminder)) {
            throw new \Exception(CommonError::ERR_NOT_FOUND->value, 404);
        }

        return $reminder;
    }

    /**
     * Deletes a Blog .
     *
     * @param int|string $modelKey The model key.
     * @return bool The result of the deletion.
     * @throws \Exception If the blog is not found.
     */
    public function delete(int|string $modelKey): bool
    {
        $blog = $this->loadModel($modelKey);

        if (blank($blog)) {
            throw new \Exception(CommonError::ERR_NOT_FOUND->value, 404);
        }

        Log::info('delete Blog', [
            'user' => request()->user()->only(['id', 'name']),
            'reminder' => $blog->toArray()
        ]);

        // delete blog
        $blog->delete();

        return true;
    }

    /**
     * Update the given blogs model with the provided data.
     *
     * @param int|string $modelKey The key of the model to update.
     * @param array $data The data to update the model with.
     * @return array The updated model data.
     * @throws \Exception
     */
    public function update(int|string $modelKey, array $data): array
    {
        $blog = $this->loadModel($modelKey);
        $data = Arr::whereNotNull($data);
        if (filled($data)) {

            if(isset($data["image"]) && $data["image"] ) {
                // delete old file
                FileServices::deleteFile($blog->image);
                $data["image"] = FileServices::uploadFile($data["image"]);
            }

            $data['title'] = $data['title'] ?? $blog->title;
            $data['description'] = $data['description'] ?? $blog->description;
            $blog->update($data);
            $blog->refresh();
        }

        return $blog->only($this->defaultColumns) ?? [];
    }
}
