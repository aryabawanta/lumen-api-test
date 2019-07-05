<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ChecklistItem;

class ChecklistItemController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function bulk_updates(Request $request, $checklistId){
        $post = $request->input('data');

        $responses  = [];
        foreach ($post as $item) {
            $status = 200;

            $checklist_item = ChecklistItem::whereChecklistId($checklistId)->find($item['id']);

            if (empty($checklist_item)){
                $responses[] = [
                    'id' => $item['id'],
                    'action' => $item['action'],
                    'status' => 404
                ];
                continue;
            }

            if ($item['action']=='update')
                $updated_checklist_item = $this->_updateChecklist($item, $checklist_item);

            if (!$updated_checklist_item)
                $status = 500;

            $responses[] = [
                'id' => $item['id'],
                'action' => $item['action'],
                'status' => $status
            ];
        }
        return response($responses, 200);
    }

    public function complete(Request $request){
        $post = $request->input('data');

        $completed_items  = [];
        foreach ($post as $item) {
            $checklist_item = ChecklistItem::find($item['item_id']);
            $checklist_item->is_completed = true;
            $checklist_item->save();

            $completed_items[] = [
                'id' => $checklist_item->id,
                'item_id' => $checklist_item->id,
                'is_completed' => true,
                'checklist_id' => $checklist_item->checklist_id
            ];
        }
        return response($completed_items, 400);
    }

    public function create(Request $request, $checklistId){
        $post = $request->input('data');

        $checklist = $this->_createChecklistItem($post, $checklistId);

        if (empty($checklist))
            return response('Failed to Create Checklist Item', 500);

        return response($checklist, 201);
    }

    public function delete($checklistId, $itemId){
        $deleted = ChecklistItem::whereChecklistId($checklistId)->find($itemId)->delete();

        if (!$deleted)
            return response("Delete Failed!", 400);

        return response("Success to Delete this Checklist Item!", 204);
    }

    public function incomplete(Request $request)
    {
        $post = $request->input('data');

        $completed_items  = [];
        foreach ($post as $item) {
            $checklist_item = ChecklistItem::find($item['item_id']);
            $checklist_item->is_completed = false;
            $checklist_item->save();

            $completed_items[] = [
                'id' => $checklist_item->id,
                'item_id' => $checklist_item->id,
                'is_completed' => true,
                'checklist_id' => $checklist_item->checklist_id
            ];
        }
        return response($completed_items, 400);
    }

    public function index(){
        $checklist_items = ChecklistItem::all();

        if (empty($checklist_items))
            return response('Data not Found!', 404);

        return response($checklist_items, 200);
    }

    public function update(Request $request, $checklistId, $itemId){
        $post = $request->input('data');

        $checklist_item = ChecklistItem::whereChecklistId($checklistId)->find($itemId);

        $updated_checklist_item = $this->_updateChecklist($post, $checklist_item);
        if (!$updated_checklist_item)
            return response('Failed to Update!', 400);

        return response($updated_checklist_item, 201);
    }

    public function show($checklistId, $itemId){
        $checklist_item = ChecklistItem::whereChecklistId($checklistId)->find($itemId);

        if (empty($checklist_item))
            return response('Data not Found!', 404);

        return response($checklist_item, 200);
    }

    // not done yet
    public function summaries(){
        $checklist_items = ChecklistItem::all();
        $summaries = [
            "today" => 0,
            "past_due" => 0,
            "this_week" => 0,
            "past_week" => 0,
            "this_month" => 0,
            "past_month" => 0,
            "total" => 0
        ];
        foreach ($checklist_items as $checklist_item) {
            if ($checklist_item->created_at->isToday()){
                $summaries['today']++;
                $summaries['this_week']++;
                $summaries['this_month']++;
            }
            $summaries['total']++;
        }
        return response($summaries, 200);
    }

    private function _createChecklistItem($post, $checklistId){
        $record = $post['attribute'];
        $record['checklist_id'] = $checklistId;
        $record['created_by'] = Auth::user()->id;

        $inserted_checklist_item =  ChecklistItem::create($record);
        if (empty($inserted_checklist_item))
            return false;

        return $this->_formatChecklistItem($inserted_checklist_item);
    }

    private function _formatChecklistItem($checklist_item){
        $formatted_checklist = [
            'data' => [
                'id' => $checklist_item->id,
                'attribute' => $checklist_item
            ],
            'links' => [
                'self' => url("/checklists/{$checklist_item->checklist_id}/items/{$checklist_item->id}")
            ]
        ];
        return $formatted_checklist;
    }

    private function _updateChecklist($post, $checklist_item){
        $attributes = (empty($post['attributes']) ? $post['attribute'] : $post['attributes']);

        if (!empty($attributes['description']))
            $checklist_item->description = $attributes['description'];
        if (!empty($attributes['due']))
            $checklist_item->due = $attributes['due'];
        if (!empty($attributes['urgency']))
            $checklist_item->urgency = $attributes['urgency'];

        $checklist_item->updated_by = Auth::user()->id;

        $updated_checklist = $checklist_item->save();
        if (!$updated_checklist)
            return false;

        return $this->_formatChecklistItem(ChecklistItem::whereChecklistId($checklist_item->checklist_id)->find($checklist_item->id));
    }
}
