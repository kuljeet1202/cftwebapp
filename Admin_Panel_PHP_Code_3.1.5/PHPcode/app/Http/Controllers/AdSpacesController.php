<?php

namespace App\Http\Controllers;

use App\Models\AdSpaces;
use App\Models\FeaturedSections;
use App\Models\Language;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdSpacesController extends Controller
{
    public function index()
    {
        try {
            $languageList = Language::where('status', 1)->get();
            return view('ad-spaces', compact('languageList'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'language_id' => 'required',
        ]);

        $adSpaces = new AdSpaces();
        if ($request->hasFile('ad_image')) {
            $adSpaces->ad_image = $request->file('ad_image')->store('ad_spaces', 'public');
        } else {
            $adSpaces->ad_image = '';
        }
        if ($request->hasFile('web_ad_image')) {
            $adSpaces->web_ad_image = $request->file('web_ad_image')->store('ad_spaces', 'public');
        } else {
            $adSpaces->web_ad_image = '';
        }
        $ad_space = $request->ad_space;
        $string = explode('-', $ad_space);
        if ($string[0] == 'featuredsection') {
            $ad_featured_section_id = $string[1];
        } else {
            $ad_featured_section_id = 0;
        }
        $adSpaces->language_id = $request->language_id;
        $adSpaces->ad_space = $request->ad_space;
        $adSpaces->ad_featured_section_id = $ad_featured_section_id;
        $adSpaces->ad_url = $request->ad_url;
        $adSpaces->save();
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

        $sql = AdSpaces::with(['language', 'feature_section']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $sql = $sql->where(function ($q) use ($search) {
                $q->orWhere('id', 'LIKE', "%{$search}%")->orWhere('ad_space', 'LIKE', "%{$search}%");
            });
        }
        if ($request->has('language_id') && $request->language_id) {
            $sql = $sql->where('language_id', $request->language_id);
        }
        if ($request->has('status') && $request->status != '') {
            $sql->where('status', $request->status);
        }
        $total = $sql->count('id');
        $sql = $sql->skip($offset)->take($limit)->orderBy($sort, $order);
        $rows = $sql->get()->map(function ($row) {
            $edit = '<a class="dropdown-item edit-data" data-toggle="modal" data-target="#editDataModal" title="' . __('edit') . '"><i class="fa fa-pen mr-1 text-primary"></i>' . __('edit') . '</a>';
            $delete = '<a data-url="' . url('ad_spaces', $row->id) . '" class="dropdown-item delete-form" data-id="' . $row->id . '" title="' . __('delete') . '"><i class="fa fa-trash mr-1 text-danger"></i>' . __('delete') . '</a>';
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

            $ad_image = $row->ad_image ? $row->ad_image : '';
            $web_ad_image = $row->web_ad_image ? $row->web_ad_image : '';
            return [
                'id' => $row->id,
                'language_id' => $row->language_id,
                'language' => $row->language->language ?? '',
                'date' => date('d-M-Y', strtotime($row->created_at)),
                'ad_space' => $row->ad_space ?? '',
                'ad_featured_section_id' => $row->ad_featured_section_id ?? '',
                'ad_featured_section' => !empty($row->feature_section->title) ? 'Above ' . $row->feature_section->title : '',
                'ad_image' => !empty($row->ad_image) ? '<a href=' . $ad_image . '  data-toggle="lightbox" data-title="Image"><img class = "images_border" src=' . $ad_image . ' height=75, width=300 >' : '-',
                'web_ad_image' => !empty($row->web_ad_image) ? '<a href=' . $web_ad_image . '  data-toggle="lightbox" data-title="Image"><img class = "images_border" src=' . $web_ad_image . ' height=75, width=300 >' : '-',
                'ad_url' => $row->ad_url,
                'status1' => $row->status ? "<span class='badge badge-success'>Enable</span>" : "<span class='badge badge-danger'>Disable</span>",
                'status' => $row->status,
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
            'language_id' => 'required',
        ]);
        $adSpaces = AdSpaces::find($request->edit_id);
        if ($request->hasFile('ad_image')) {
            Storage::disk('public')->delete($adSpaces->getRawOriginal('ad_image'));
            $adSpaces->ad_image = $request->file('ad_image')->store('ad_spaces', 'public');
        }
        if ($request->hasFile('web_ad_image')) {
            Storage::disk('public')->delete($adSpaces->getRawOriginal('web_ad_image'));
            $adSpaces->web_ad_image = $request->file('web_ad_image')->store('ad_spaces', 'public');
        }
        $ad_space = $request->ad_space;
        $string = explode('-', $ad_space);
        if ($string[0] == 'featuredsection') {
            $ad_featured_section_id = $string[1];
        } else {
            $ad_featured_section_id = 0;
        }
        $adSpaces->language_id = $request->language_id;
        $adSpaces->ad_space = $request->ad_space;
        $adSpaces->ad_featured_section_id = $ad_featured_section_id;
        $adSpaces->ad_url = $request->ad_url;
        $adSpaces->status = $request->status;
        $adSpaces->save();
        $response = [
            'error' => false,
            'message' => __('updated_success'),
        ];
        return response()->json($response);
    }

    public function destroy(string $id)
    {
        AdSpaces::find($id)->delete();
        $response = [
            'error' => false,
            'message' => __('deleted_success'),
        ];
        return response()->json($response);
    }

    public function getFeaturedSectionsByLanguage(Request $request)
    {
        $language_id = $request->language_id;
        $res = FeaturedSections::where('language_id', $language_id)->get();
        $option = '';
        if (!empty($res)) {
            foreach ($res as $value) {
                if ($value['style_app'] == 'style_6' || $value['style_web'] == 'style_web') {
                    $option .= '<option value="featuredsection-' . $value['id'] . '" >Above ' . $value['title'] . '.  (Not applicable)</option>';
                } else {
                    $option .= '<option value="featuredsection-' . $value['id'] . '">Above ' . $value['title'] . '</option>';
                }
            }
        }
        $option .= '<option value="news_details-top">News Details (Top)</option>';
        $option .= '<option value="news_details-bottom">News Details (Bottom)</option>';
        return $option;
    }
}
