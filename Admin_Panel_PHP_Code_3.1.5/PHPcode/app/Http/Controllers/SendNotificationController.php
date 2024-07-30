<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Location;
use App\Models\SendNotification;
use Exception;
use Illuminate\Http\Request;

class SendNotificationController extends Controller
{
    public function index()
    {
        try {
            $languageList = Language::where('status', 1)->get();
            $locationList = Location::get();
            return view('notifications', compact('languageList', 'locationList'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $image = '';
        if ($request->hasFile('file')) {
            $image = $request->file('file')->store('notification', 'public');
        }
        $language_id = $request->language;
        $location_id = $request->location_id ?? 0;
        $type = $request->type;
        $category_id = $type == 'category' ? $request->category_id : '0';
        $subcategory_id = $type == 'subcategory_id' ? $request->subcategory_id ?? '0' : '0';
        $news_id = $type == 'news_id' ? $request->news_id : '0';
        $title = $request->title;
        $message = $request->message;

        $data = SendNotification::create([
            'language_id' => $language_id,
            'location_id' => $location_id,
            'type' => $type,
            'category_id' => $category_id,
            'subcategory_id' => $subcategory_id,
            'news_id' => $news_id,
            'title' => $title,
            'message' => $message,
            'image' => $image,
            'date_sent' => now(),
        ]);

        $fcmMsg = [
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'title' => $title,
            'body' => $message,
            'message' => $message,
            'language_id' => $language_id,
            'type' => $type,
            'category_id' => $category_id,
            'subcategory_id' => $subcategory_id,
            'news_id' => $news_id,
            'image' => $data->image,
            'sound' => 'default',
        ];
        send_notification($fcmMsg, $language_id, $location_id);
        $response = [
            'error' => false,
            'message' => __('sent_success'),
        ];
        return response()->json($response);
    }

    public function show(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');
        $sql = SendNotification::with(['language', 'category', 'sub_category'])->orderBy($sort, $order);
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $sql = $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")->orwhere('title', 'LIKE', "%{$search}%");
            });
        }
        $total = $sql->count();
        $sql = $sql->skip($offset)->take($limit);
        $rows = $sql->get()->map(function ($row) {
            $operate =
                '
            <a data-url="' .
                url('notifications', $row->id) .
                '" class="btn  btn-secondary me-4 text-white delete-form" data-id="' .
                $row->id .
                '" title="'.__('delete').'">
                <span class="fa fa-trash"></span>
            </a>';
            return [
                'id' => $row->id,
                'langauge_id' => $row->language_id,
                'langauge_name' => $row->language->language ?? '',
                'category_id' => $row->category_id,
                'category_name' => $row->category->category_name ?? '',
                'subcategory_id' => $row->subcategory_id,
                'subcategory_name' => $row->sub_category->subcategory_name ?? '',
                'news_id' => $row->news_id,
                'news_title' => $row->news->title ?? '',
                'title' => $row->title,
                'message' => $row->message,
                'image' => ($row->image) ? '<a href="' . $row->image . '" data-toggle="lightbox" data-title="Image"><img  class = "images_border" src="' . $row->image . '" height="50" width="50"></a>' : '-',
                'date' => $row->date_sent,
                'operate' => $operate,
            ];
        });
        return response()->json([
            'total' => $total,
            'rows' => $rows,
        ]);
    }

    public function destroy(string $id)
    {
        SendNotification::find($id)->delete();
        $response = [
            'error' => false,
            'message' => __('deleted_success'),
        ];
        return response()->json($response);
    }
}
