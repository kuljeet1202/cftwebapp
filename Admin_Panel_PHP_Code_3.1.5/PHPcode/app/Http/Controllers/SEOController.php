<?php

namespace App\Http\Controllers;

use App\Models\WebSeoPages;
use Illuminate\Http\Request;

class SEOController extends Controller
{
    public function index()
    {
        $setting = getSetting();
        $options = ['all_categories', 'more_pages', 'news_notifications', 'personal_notifications', 'view_all', 'all_breaking_news', 'live_streaming_news'];
        $pages = WebSeoPages::get()->pluck('page_type');
        return view('seo-setting', compact('setting', 'options', 'pages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'page_type' => 'required',
            'meta_keyword' => 'required',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'og_image' => 'required',
        ]);

        $WebSeoPages = new WebSeoPages();
        if ($request->hasFile('og_image')) {
            $WebSeoPages->og_image = $request->file('og_image')->store('web_seo_pages', 'public');
        } else {
            $WebSeoPages->og_image = '';
        }
        $WebSeoPages->meta_title = $request->meta_title ?? '';
        $WebSeoPages->schema_markup = $request->schema_markup ?? '';
        $WebSeoPages->meta_description = $request->meta_description ?? '';
        $meta_keyword = json_decode($request->meta_keyword, true);
        $WebSeoPages->meta_keyword = get_meta_keyword($meta_keyword);
        $WebSeoPages->page_type = $request->page_type;
        $WebSeoPages->save();
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
        $sql = WebSeoPages::orderBy($sort, $order);
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $sql = $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")->orwhere('page_type', 'LIKE', "%{$search}%");
            });
        }
        $total = $sql->count();
        $sql = $sql->skip($offset)->take($limit);
        $rows = $sql->get()->map(function ($row) {
            $edit = '<a class="dropdown-item edit-data" data-toggle="modal" data-target="#editDataModal" title="' . __('edit') . '"><i class="fa fa-pen mr-1 text-primary"></i>' . __('edit') . '</a>';
            $delete = '<a data-url="' . url('seo-setting', $row->id) . '" class="dropdown-item delete-form" data-id="' . $row->id . '" title="' . __('delete') . '"><i class="fa fa-trash mr-1 text-danger"></i>' . __('delete') . '</a>';
            $operate =
                '<div class="dropdown">
                            <a href="javascript:void(0)" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <button class="btn btn-primary btn-sm px-3"><i class="fas fa-ellipsis-v"></i></button>
                            </a>
                            <div class="dropdown-menu dropdown-scrollbar" aria-labelledby="dropdownMenuButton">
                            ' .
                $edit .
                $delete .
                '
                            </div>
                        </div>';

            json_decode($row->meta_keyword);
            if (json_last_error() === JSON_ERROR_NONE) {
                $meta_keyword = json_decode($row->meta_keyword);
            } else {
                $meta_keyword = $row->meta_keyword;
            }
            return [
                'id' => $row->id,
                'page_type' => $row->page_type,
                'page_type_badge' => page_type($row->page_type),
                'meta_keyword' => $meta_keyword,
                'meta_title' => $row->meta_title,
                'schema_markup' => $row->schema_markup,
                'meta_description' => $row->meta_description,
                'og_image' => !empty($row->og_image) ? '<a href="' . $row->og_image . '" data-toggle="lightbox" data-title="Image"><img  class = "images_border" src="' . $row->og_image . '" height="50" width="50"></a>' : '-',
                'operate' => $operate,
            ];
        });
        return response()->json([
            'total' => $total,
            'rows' => $rows,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'page_type' => 'required',
            'meta_keyword' => 'required',
            'meta_title' => 'required',
            'meta_description' => 'required',
        ]);

        $WebSeoPages = WebSeoPages::find($request->edit_id);
        if ($request->hasFile('og_image')) {
            $WebSeoPages->og_image = $request->file('og_image')->store('web_seo_pages', 'public');
        }
        $WebSeoPages->page_type = $request->page_type;
        $WebSeoPages->meta_title = $request->meta_title ?? '';
        $WebSeoPages->schema_markup = $request->schema_markup ?? '';
        $WebSeoPages->meta_description = $request->meta_description ?? '';
        $meta_keyword = json_decode($request->meta_keyword, true);
        $WebSeoPages->meta_keyword = get_meta_keyword($meta_keyword);
        $WebSeoPages->save();
        $response = [
            'error' => false,
            'message' => __('updated_success'),
        ];
        return response()->json($response);
    }

    public function destroy(string $id)
    {
        WebSeoPages::find($id)->delete();
        $response = [
            'error' => false,
            'message' => __('deleted_success'),
        ];
        return response()->json($response);
    }
}
