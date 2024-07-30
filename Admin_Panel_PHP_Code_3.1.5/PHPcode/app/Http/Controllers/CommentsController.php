<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\CommentsFlag;
use Illuminate\Http\Request;
use Exception;

class CommentsController extends Controller
{
    public function index()
    {
        try {
            return view('comments');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function index1()
    {
        try {
            return view('comments-flag');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');

        $sql = Comments::with('user', 'news');
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $sql = $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                    ->orwhere('message', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($q1) use ($search) {
                        $q1->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('news', function ($q2) use ($search) {
                        $q2->where('title', 'LIKE', "%{$search}%");
                    });
            });
        }
        $total = $sql->count('id');
        $sql = $sql->skip($offset)->take($limit)->orderBy($sort, $order);
        $rows = $sql->get()->map(function ($row) {
            $operate = '';
            $operate .=
                '<a data-url="' .
                url('comments', $row->id) .
                '" class="btn btn-primary me-4 text-white delete-form" data-id="' .
                $row->id .
                '" title="' .
                __('delete') .
                '">
                <span class="fa fa-trash"></span>
            </a>';
            return [
                'id' => $row->id,
                'user_id' => $row->user_id,
                'name' => $row->user->name ?? '',
                'title' => $row->news->title ?? '',
                'message' => $row->message,
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
        // for remove sub comment data
        $sub_comment = Comments::select('id')->where('parent_id', $id)->get();
        if (!$sub_comment->isEmpty()) {
            foreach ($sub_comment as $row) {
                Comments::find($row->id)->delete();
            }
        }
        Comments::find($id)->delete();
        $response = [
            'error' => false,
            'message' => __('deleted_success'),
        ];
        return response()->json($response);
    }

    public function comment_delete(string $id)
    {
        CommentsFlag::find($id)->delete();
        $response = [
            'error' => false,
            'message' => __('deleted_success'),
        ];
        return response()->json($response);
    }

    public function comment_flag(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');

        $sql = CommentsFlag::with('user', 'news', 'comment');
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $sql = $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%");
            });
        }
        $total = $sql->count('id');
        $sql = $sql->skip($offset)->take($limit)->orderBy($sort, $order);
        $rows = $sql->get()->map(function ($row) {
            $operate = '';
            $operate .=
                '
            <a data-url="' .
                url('comments-delete/' . $row->id) .
                '" class="btn btn-primary me-4 text-white delete-form" data-id="' .
                $row->id .
                '" title="' .
                __('delete') .
                ' Comment Flag">
                <span class="fa fa-trash"></span>
            </a>';
            return [
                'id' => $row->id,
                'comment_id' => $row->comment_id,
                'user_id' => $row->user_id ?? '',
                'news_id' => $row->news_id ?? '',
                'name' => $row->user->name ?? '',
                'message' => $row->message ?? '',
                'comment' => $row->comment->message ?? '',
                'title' => $row->news->title ?? '',
                'created_at' => $row->created_at ?? '',
                'updated_at' => $row->updated_at ?? '',
                'operate' => $operate,
            ];
        });
        return response()->json([
            'total' => $total,
            'rows' => $rows,
        ]);
    }

    public function bulk_comment_delete(Request $request)
    {
        try {
            $request_ids = $request->request_ids;
            foreach ($request_ids as $row) {
                $comment = Comments::find($row);
                if ($comment) {
                    // Delete related SurveyOptions
                    $comment->comment_flag()->delete();
                    // Delete the SurveyQuestion
                    $comment->delete();
                }
            }
            $response = [
                'error' => false,
                'message' => __('deleted_success'),
            ];
            return response()->json($response);
        } catch (\Exception $th) {
            throw $th;
        }
    }
}
