<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function index()
    {
        try {
            $languageList = Language::where('status', 1)->get();
            $subcategoryList = SubCategory::select('id', 'subcategory_name')->orderBy('row_order', 'ASC')->get();
            return view('subcategory', compact('languageList', 'subcategoryList'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function get_subcategory_by_category(Request $request)
    {
        $res = SubCategory::select('id', 'subcategory_name');
        if ($request->has('category_id')) {
            $res = $res->where('category_id', $request->category_id);
        }
        $res = $res->orderBy('row_order', 'ASC')->get();
        if (!empty($res)) {
            if ($request->sortable) {
                $options = '';
                foreach ($res as $row) {
                    $options .= '<li id="' . $row->id . '">' . $row->subcategory_name . '</li>';
                }
            } else {
                $options = '<option value="">' . __('select') . ' ' . __('subcategory') . '</option>';
                foreach ($res as $row) {
                    $options .= '<option value="' . $row->id . '">' . $row->subcategory_name . '</option>';
                }
            }
        }
        return $options;
    }

    public function show(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'row_order');
        $order = $request->input('order', 'ASC');

        $sql = SubCategory::with(['category', 'language']);
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $sql = $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")->orwhere('subcategory_name', 'LIKE', "%{$search}%");
            });
        }
        if ($request->has('language_id') && $request->language_id) {
            $sql = $sql->where('language_id', $request->language_id);
        }
        if ($request->has('category_id') && $request->category_id) {
            $sql = $sql->where('category_id', $request->category_id);
        }
        $total = $sql->count();
        $sql = $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $rows = $sql->get()->map(function ($row) {
            $edit = '<a class="dropdown-item edit-data" data-toggle="modal" data-target="#editDataModal" title="' . __('edit') . '"><i class="fa fa-pen mr-1 text-primary"></i>' . __('edit') . '</a>';
            $delete = '<a data-url="' . url('sub_category', $row->id) . '" class="dropdown-item delete-form" data-id="' . $row->id . '" title="' . __('delete') . '"><i class="fa fa-trash mr-1 text-danger"></i>' . __('delete') . '</a>';
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

            return [
                'id' => $row->id,
                'language_id' => $row->language_id,
                'language' => $row->language->language ?? '',
                'category_id' => $row->category_id,
                'category_name' => $row->category->category_name ?? '',
                'subcategory_name' => $row->subcategory_name,
                'slug' => $row->slug,
                'row_order' => '<span class="btn btn-icon btn-sm btn-warning move" alt="Move" >' . $row->row_order . '</span>',
                'operate' => $operate,
            ];
        });
        return response()->json([
            'total' => $total,
            'rows' => $rows,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'language' => 'required',
            'category' => 'required',
            'name' => 'required',
            'slug' => 'required',
        ]);

        $slug = customSlug($request->slug);
        $existingSlug = SubCategory::where('slug', $slug)->exists();
        if ($existingSlug) {
            $response = [
                'error' => true,
                'message' => __('slug_already_use'),
            ];
            return response()->json($response);
        }

        $sub_category = new SubCategory();
        $sub_category->language_id = $request->language;
        $sub_category->category_id = $request->category;
        $sub_category->subcategory_name = $request->name;
        $sub_category->slug = $slug;
        $sub_category->save();
        $response = [
            'error' => false,
            'message' => __('created_success'),
        ];
        return response()->json($response);
    }

    public function update(Request $request)
    {
        $request->validate([
            'edit_language' => 'required',
            'category_id' => 'required',
            'name' => 'required',
            'slug' => 'required',
        ]);
        $slug = customSlug($request->slug);
        $existingSlug = SubCategory::where('slug', $slug)
            ->where('id', '!=', $request->edit_id)
            ->exists();
        if ($existingSlug) {
            $response = [
                'error' => true,
                'message' => __('slug_already_use'),
            ];
            return response()->json($response);
        }

        $sub_category = SubCategory::find($request->edit_id);
        $sub_category->category_id = $request->category_id;
        $sub_category->subcategory_name = $request->name;
        $sub_category->language_id = $request->edit_language;
        $sub_category->slug = $slug;
        $sub_category->save();
        $response = [
            'error' => false,
            'message' => __('updated_success'),
        ];
        return response()->json($response);
    }

    public function destroy(string $id)
    {
        SubCategory::find($id)->delete();
        $response = [
            'error' => false,
            'message' => __('deleted_success'),
        ];
        return response()->json($response);
    }

    public function update_order(Request $request)
    {
        if ($request->row_order) {
            $row_order = explode(',', $request->row_order);
            foreach ($row_order as $key => $id) {
                SubCategory::where('id', $id)->update(['row_order' => $key + 1]);
            }
        }
        return redirect('sub_category');
    }
}
