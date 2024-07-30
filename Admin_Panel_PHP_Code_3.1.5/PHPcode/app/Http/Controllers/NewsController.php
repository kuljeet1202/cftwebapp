<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use App\Models\Location;
use App\Models\News;
use App\Models\News_image;
use App\Models\News_like;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function get_news_by_category(Request $request)
    {
        $category_id = $request->category_id;
        $res = News::where('status', 1)->where('category_id', $category_id)->get();
        $option = '<option value="">' . __('select') . ' ' . __('news') . '</option>';
        if (!empty($res)) {
            foreach ($res as $value) {
                $option .= '<option value="' . $value['id'] . '">' . $value['title'] . '</option>';
            }
        }
        return $option;
    }

    public function get_news_by_subcategory(Request $request)
    {
        $subcategory_id = $request->subcategory_id;
        $res = News::where('status', 1)->where('subcategory_id', $subcategory_id)->get();
        $option = '<option value="">' . __('select') . ' ' . __('news') . '</option>';
        if (!empty($res)) {
            foreach ($res as $value) {
                $option .= '<option value="' . $value['id'] . '">' . $value['title'] . '</option>';
            }
        }
        return $option;
    }

    public function index()
    {
        try {   
            $languageList = Language::where('status', 1)->get();
            $tagList = Tag::get();
            $locationList = Location::get();
            $userList = User::select('id', 'name')->where('status', 1)->get();
            $roleList = Role::get();
            return view('news', compact('languageList', 'tagList', 'locationList', 'userList', 'roleList'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'language' => 'required',
            'title' => 'required',
            'slug' => 'required',
            'content_type' => 'required',
            // 'des' => 'required|string',
            'meta_title' => 'required',
            'file' => 'image',
            'meta_keyword' => 'required',
            'meta_description' => 'required',
            'video_file' => $request->content_type == 'video_upload' ? 'required|mimes:mp4,mov,avi|max:20480' : '',
            'youtube_url' => $request->content_type == 'video_youtube' ? 'required|youtube_url' : '',
            'other_url' => $request->content_type == 'video_other' ? 'required' : '',
        ];
        if (is_category_enabled() == 1) {
            $rules['category_id'] = 'required';
        }
        if (is_location_news_enabled() == 1) {
            $rules['location_id'] = 'required';
        }
        $request->validate($rules);

        $slug = customSlug($request->slug);
        $existingSlug = News::where('slug', $slug)->exists();
        if ($existingSlug) {
            $response = [
                'error' => true,
                'message' => __('slug_already_use'),
            ];
            return response()->json($response);
        }

        $news = new News();
        $content_value = '';
        $content_type = $request->content_type;
        if ($content_type == 'video_youtube') {
            $content_value = $request->input('youtube_url');
        } elseif ($content_type == 'video_other') {
            $content_value = $request->input('other_url');
        } elseif ($content_type == 'video_upload' && $request->file('video_file')->isValid()) {
            $content_value = $request->file('video_file')->store('news_video', 'public');
        }

        $language_id = $request->language;
        $news->language_id = $language_id;
        $news->category_id = $request->category_id ?? 0;
        $news->subcategory_id = $request->subcategory_id ?? 0;
        $news->tag_id = implode(',', $request->tag_id ?? []);
        $news->title = $request->title;
        $news->slug = $slug;
        $news->date = now();
        $news->content_type = $content_type;
        $news->content_value = $content_value;
        $news->description = $request->des ?? '';
        $news->user_id = 0;
        $news->status = 1;
        $news->show_till = $request->show_till ?? '';
        $news->location_id = $request->location_id ?? 0;
        $news->admin_id = 0;
        $meta_keyword = json_decode($request->meta_keyword, true);
        $news->schema_markup = $request->schema_markup ?? '';
        $news->meta_description = $request->meta_description ?? '';
        $news->meta_title = $request->meta_title ?? '';
        $news->meta_keyword = get_meta_keyword($meta_keyword);
        if ($request->hasFile('file')) {
            $news->image = $request->file('file')->store('news', 'public');
        } else {
            $news->image = '';
        }
        $news->save();
        $id = $news->id;
        if ($request->file('ofile')) {
            foreach ($request->file('ofile') as $file) {
                $newFile = new News_image();
                $newFile->news_id = $id;
                $filePath = $file->store("news/{$id}", 'public');
                $fileName = basename($filePath);
                $newFile->other_image = $fileName;
                $newFile->save();
            }
        }
        if ($request->notification) {
            $fcmMsg = [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'message' => $request->title,
                'body' => $request->title,
                'news_id' => $id,
                'language_id' => $language_id,
                'type' => 'newlyadded',
                'location_id' => $request->location_id,
            ];
            $location = $request->location_id ?? 0;
            send_notification($fcmMsg, $language_id, $location);
        }
        $response = [
            'error' => false,
            'message' => __('created_success'),
        ];
        return response()->json($response);
    }

    public function show(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');

        $sql = News::with('language', 'category', 'sub_category', 'location')->withCount('newsview');
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $sql = $sql->where(function ($q) use ($search) {
                $q->orWhere('id', 'LIKE', "%{$search}%")->orWhere('title', 'LIKE', "%{$search}%");
            });
        }
        if ($request->has('language_id') && $request->language_id) {
            $sql = $sql->where('language_id', $request->language_id);
        }
        if ($request->has('category_id') && $request->category_id) {
            $sql->where('category_id', $request->category_id);
        }
        if ($request->has('subcategory_id') && $request->subcategory_id) {
            $sql->where('subcategory_id', $request->subcategory_id);
        }
        if ($request->has('location_id') && $request->location_id) {
            $sql->where('location_id', $request->location_id);
        }
        if ($request->has('user_id') && $request->user_id) {
            $sql->where('user_id', $request->user_id);
        }
        if ($request->has('status') && $request->status != '') {
            $sql->where('status', $request->status);
        }
        $total = $sql->count();
        $sql = $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $rows = $sql->get()->map(function ($row) {
            $con_value = $con_v = $videos = '';
            if ($row->content_type != 'standard_post') {
                if ($row->content_type == 'video_upload') {
                    $filename = $row->content_value;
                    $con_value = Storage::url($filename);
                    $videos = '  <a class="btn btn-icon  " data-toggle="lightbox" data-title="Video" data-type="video" href="' . $con_value . '" title="view video"><i class="fa fa-eye mr-1 text-primary"></i>View Video</a>';
                } else {
                    $con_value = $row->content_value;
                    $videos = '<a class="btn btn-icon" data-toggle="lightbox" data-title="Video" data-type="youtube" href="' . $row->content_value . '" title="view video"><i class="fa fa-eye mr-1 text-primary"></i>View Video</a>';
                }
            }
            $clone_data = '  <a class="btn btn-icon clone-data" data-id="' . $row->id . '" data-cvalue="' . $con_v . '" title="Clone News"><i class="fa fa-clone mr-1 text-primary"></i>Clone News</a>';
            $edit_des = '<a class="btn btn-icon edit-data-des" data-toggle="modal" data-target="#editDataDesModal" title="' . __('edit') . '"><i class="fa fa-pen mr-1 text-primary"></i> ' . __('edit') . ' ' . __('description') . '</a>';
            $edit = '<a class="dropdown-item edit-data" data-toggle="modal" data-target="#editDataModal" title="' . __('edit') . '"><i class="fa fa-pen mr-1 text-primary"></i>' . __('edit') . '</a>';
            $delete = '<a data-url="' . url('news', $row->id) . '" class="dropdown-item delete-form" data-id="' . $row->id . '" title="' . __('delete') . '"><i class="fa fa-trash mr-1 text-danger"></i>' . __('delete') . '</a>';
            $operate =
                '<div class="dropdown">
                            <a href="javascript:void(0)" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <button class="btn btn-primary btn-sm px-3"><i class="fas fa-ellipsis-v"></i></button>
                            </a>
                            <div class="dropdown-menu dropdown-scrollbar" aria-labelledby="dropdownMenuButton">
                            ' .
                $edit .
                $edit_des .
                $delete .
                $videos .
                $clone_data .
                '
                            </div>
                        </div>';

            $total_like = News_like::where('news_id', $row->id)
                ->where('status', 1)
                ->count();
            $total_dislike = News_like::where('news_id', $row->id)
                ->where('status', 2)
                ->count();

            $totalImage = DB::table('tbl_news_image')
                ->where('news_id', $row->id)
                ->count();

            json_decode($row->meta_keyword);
            if (json_last_error() === JSON_ERROR_NONE) {
                $meta_keyword = json_decode($row->meta_keyword);
            } else {
                $meta_keyword = $row->meta_keyword;
            }
            if (isset($row->tag_id) && !empty($row->tag_id)) {
                $tagNames = Tag::whereIn('id', explode(',', $row->tag_id))
                    ->distinct()
                    ->pluck('tag_name')
                    ->implode(',');
                $row->tag_name = $tagNames;
                $row->tag_id = !empty($res2) ? $res2[0]->tag_id : $row->tag_id;
            }
            $is_expire = '-';
            if($row->show_till && $row->show_till != '0000-00-00') {
                $is_expire = (date('Y-m-d') > $row->show_till) ? '<div class="badge badge-danger">Expired</div>' : '-';
            }

            $status = [
                0 => '<span class="badge badge-danger">' . __('deactive') . '</span>',
                1 => '<span class="badge badge-success">' . __('active') . '</span>',
            ];

            return [
                'id' => $row->id,
                'language_id' => $row->language_id,
                'language_name' => $row->language->language ?? '',
                'category_id' => $row->category_id,
                'category_name' => $row->category->category_name ?? '',
                'subcategory_id' => $row->subcategory_id ?? '',
                'subcategory_name' => $row->sub_category->subcategory_name ?? '',
                'tag_id' => $row->tag_id ?? '',
                'tag_name' => $row->tag_name ?? '',
                'title' => $row->title ?? '',
                'date' => $row->date ?? '',
                'content_type' => str_replace('_', ' ', $row->content_type) ?? '',
                'content_type1' => $row->content_type,
                'content_value' => $row->content_value,
                'image' => !empty($row->image) ? '<a href="' . $row->image . '" data-toggle="lightbox" data-title="Image"><img class="images_border" src="' . $row->image . '" height="50" width="50"></a>' : '-',
                'description' => $row->description ?? '',
                'show_till' => $row->show_till ?? '',
                'status' => $row->status ?? '',
                'is_clone' => $row->is_clone ?? '',
                'views' => $row->newsview_count,
                'likes' => $total_like,
                'dislikes' => $total_dislike,
                'operate' => $operate,
                'created_at' => $row->created_at ?? '',
                'updated_at' => $row->updated_at ?? '',
                'location_id' => $row->location_id ?? '',
                'location' => $row->location->location_name ?? '',
                'status_badge' => $status[$row->status],
                'total_image' => '<a href="' . url('news-image/' . $row->id) . '" class="btn btn-icon btn-sm btn-warning" title="other image">' . $totalImage . '</a>',
                'is_expire' => $is_expire,
                'short_description' => $row->description ? mb_strimwidth($row->description, 0, 70, '...') : '',
                'slug' => $row->slug,
                'meta_keyword' => $meta_keyword,
                'schema_markup' => $row->schema_markup,
                'meta_title' => $row->meta_title,
                'meta_description' => $row->meta_description,
            ];
        });
        return response()->json([
            'total' => $total,
            'rows' => $rows,
        ]);
    }

    public function update(Request $request, News $news)
    {
        $rules = [
            'language' => 'required',
            'title' => 'required',
            'slug' => 'required',
            'content_type' => 'required',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'meta_keyword' => 'required',
        ];
        if (is_category_enabled() == 1) {
            $rules['category_id'] = 'required';
        }
        if (is_location_news_enabled() == 1) {
            $rules['location_id'] = 'required';
        }

        $request->validate($rules);

        $slug = customSlug($request->slug);
        $existingSlug = News::where('slug', $slug)
            ->where('id', '!=', $request->edit_id)
            ->exists();
        if ($existingSlug) {
            $response = [
                'error' => true,
                'message' => __('slug_already_use'),
            ];
            return response()->json($response);
        }

        $location = $request->location_id ?? 0;
        $news = News::find($request->edit_id);
        $content_value = '';
        $content_type = $request->content_type;
        if ($content_type == 'video_youtube') {
            $content_value = $request->input('youtube_url');
            if (preg_match('/(youtube.com|youtu.be)\/(watch)?(\?v=)?(\S+)?/', $content_value, $match)) {
            } else {
                $response = [
                    'error' => true,
                    'message' => __('invalid_link'),
                ];
                return response()->json($response);
            }
        } elseif ($content_type == 'video_other') {
            $content_value = $request->input('other_url');
        } elseif ($content_type == 'video_upload') {
            if ($request->file('video_file')) {
                $content_value = $request->file('video_file')->store('news_video', 'public');
            } else {
                $content_value = $news->content_value;
            }
        }
        if ($request->hasFile('file')) {
            $news->image = $request->file('file')->store('news', 'public');
        }

        $news->category_id = $request->category_id ?? 0;
        $news->subcategory_id = $request->subcategory_id ?? 0;
        $news->tag_id = implode(',', $request->tag_id ?? []);
        $news->title = $request->title;
        $news->date = now();
        $news->content_type = $content_type;
        $news->content_value = $content_value;
        $news->slug = $slug;
        if ($request->des) {
            $news->description = $request->des;
        }
        $news->status = $request->status;
        $news->show_till = $request->show_till ?? '';
        $news->language_id = $request->language;
        $news->location_id = $location;
        $news->schema_markup = $request->schema_markup ?? '';
        $news->meta_title = $request->meta_title ?? '';
        $news->meta_description = $request->meta_description ?? '';
        $meta_keyword = json_decode($request->meta_keyword, true);
        $news->meta_keyword = get_meta_keyword($meta_keyword);
        $news->save();
        $id = $news->id;
        if ($request->notification) {
            $fcmMsg = [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'message' => $request->title,
                'body' => $request->title,
                'news_id' => $id,
                'language_id' => $request->language,
                'type' => 'newlyadded',
            ];
            send_notification($fcmMsg, $request->language, $location);
        }
        $response = [
            'error' => false,
            'message' => __('updated_success'),
        ];
        return response()->json($response);
    }

    public function destroy(string $id)
    {
        $res = News::find($id);
        $newsPath = 'news/' . $id;
        if ($res) {
            $filePath = 'news/' . $res->getRawOriginal('image');
            if (!is_null($res->image) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            //gallery image remove
            $res1 = News_image::select('other_image')->where('news_id', $id)->get();
            foreach ($res1 as $row) {
                $filePath1 = $newsPath . '/' . $row->getRawOriginal('other_image');
                if (!is_null($row->other_image) && Storage::disk('public')->exists($filePath1)) {
                    Storage::disk('public')->delete($filePath1);
                }
                $filePath2 = $row->getRawOriginal('other_image');
                if (!is_null($row->other_image) && Storage::disk('public')->exists($filePath2)) {
                    Storage::disk('public')->delete($filePath2);
                }
            }
            News_image::where('news_id', $id)->delete();
        }
        $res->delete();
        if (Storage::disk('public')->exists($newsPath)) {
            Storage::disk('public')->deleteDirectory($newsPath);
        }
        $response = [
            'error' => false,
            'message' => __('deleted_success'),
        ];
        return response()->json($response);
    }

    public function upload_img(Request $request)
    {
        $fileType = $request->input('filetype');
        $validExtensions = $fileType === 'image' ? ['png', 'jpeg', 'jpg'] : ['mp4', 'mp3'];
        if (!$request->file('file')->isValid()) {
            return response('Invalid file', 400);
        }
        $file = $request->file('file');
        if (!in_array($file->getClientOriginalExtension(), $validExtensions)) {
            return response('Invalid file extension', 400);
        }
        return $file->store('news', 'public');
    }

    public function update_description(Request $request)
    {
        $id = $request->edit_id;
        $news = News::find($id);
        $news->description = $request->des;
        $news->save();
        $response = [
            'error' => false,
            'message' => __('updated_success'),
        ];
        return response()->json($response);
    }

    public function newsImage($id)
    {
        $news = News::find($id);
        return view('news_image', compact('news'));
    }

    public function showImage(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');

        $news_id = $request->news_id;
        $sql = News_image::with('news')->where('news_id', $news_id);
        $total = $sql->count('id');
        $sql = $sql->skip($offset)->take($limit)->orderBy($sort, $order);
        $rows = $sql->get()->map(function ($row) {
            $operate =
                '<a data-url="' .
                route('deleteImage', $row->id) .
                '" class="btn btn-primary me-4 text-white delete-form" data-id="' .
                $row->id .
                '" title="' .
                __('delete') .
                '">
               <span class="fa fa-trash"></span>
            </a>';
            return [
                'id' => $row->id,
                'image' => !empty($row->other_image) ? '<a href="' . $row->other_image . '" data-toggle="lightbox" data-title="Image"><img  class = "images_border" src="' . $row->other_image . '" height="50" width="50"></a>' : '-',
                'operate' => $operate,
            ];
        });
        return response()->json([
            'total' => $total,
            'rows' => $rows,
        ]);
    }

    public function storeImage(Request $request)
    {
        $news_id = $request->news_id;
        foreach ($request->file('file') as $file) {
            $newFile = new News_image();
            $newFile->news_id = $news_id;
            $filePath = $file->store("news/{$news_id}", 'public');
            $fileName = basename($filePath);
            $newFile->other_image = $fileName;
            $newFile->save();
        }
        $response = [
            'error' => false,
            'message' => __('created_success'),
        ];
        return response()->json($response);
    }

    public function deleteImage(Request $request)
    {
        $res = News_image::find($request->id);
        if ($res) {
            $filePath = 'news/' . $res->news_id . '/' . $res->getRawOriginal('other_image');
            if (!is_null($res->other_image) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath2 = $res->getRawOriginal('other_image');
            if (!is_null($res->other_image) && Storage::disk('public')->exists($filePath2)) {
                Storage::disk('public')->delete($filePath2);
            }
        }
        $res->delete();
        $response = [
            'error' => false,
            'message' => __('deleted_success'),
        ];
        return response()->json($response);
    }

    public function bulk_news_delete(Request $request)
    {
        try {
            $request_ids = $request->request_ids;
            foreach ($request_ids as $row) {
                $news = News::find($row);
                if ($news) {
                    $news->images()->delete();
                    $news->delete();
                }
            }
            $response = [
                'error' => false,
                'message' => __('deleted_success'),
            ];
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function clone_news(Request $request)
    {
        $news = News::find($request->id);
        if ($news) {
            $clonedNews = $news->replicate();
            $clonedNews->status = 0;
            $clonedNews->is_clone = 1;
            $clonedNews->slug = 'cloned-' . $news->slug;
            $clonedNews->date = now();
            $clonedNews->save();
            $newsImages = $news->images;
            foreach ($newsImages as $image) {
                $clonedImage = $image->replicate();
                $clonedImage->news_id = $clonedNews->id;
                $clonedImage->other_image = $image->other_image;
                $clonedImage->save();
            }
            $response = [
                'error' => false,
                'message' => __('updated_success'),
            ];
            return response()->json($response);
        }
    }
}
