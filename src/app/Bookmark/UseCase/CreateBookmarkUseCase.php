<?php

namespace App\Bookmark\UseCase;

use App\Models\Bookmark;
use Dusterio\LinkPreview\Client;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

final class CreateBookmarkUseCase
{
  /**
   * ブックマーク作成処理
   *
   * @param string $url
   * @param string $category
   * @param string $comment
   * @return void
   * @throws ValidationException
   */
  public function handle(string $url, string $category, string $comment)
  {
    $previewClient = new Client($url);
    try {
      $preview = $previewClient->getPreview('general')->toArray();

      $model = new Bookmark();
      $model->url = $url;
      $model->category_id = $category;
      $model->user_id = Auth::id();
      $model->comment = $comment;
      $model->page_title = $preview['title'];
      $model->page_description = $preview['description'];
      $model->page_thumbnail_url = $preview['cover'];
      $model->save();
    } catch (Exception $e) {
      Log::error($e->getMessage());
      throw ValidationException::withMessages([
        'url' => 'URLが存在しない等の理由で読み込めませんでした。変更して再度投稿してください'
      ]);
    }
  }
}