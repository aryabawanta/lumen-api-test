<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Checklist;

class ChecklistController extends Controller
{
    private $type = 'checklists';

    public function __construct(){
        $this->middleware('auth');
    }

    public function create(Request $request){
        $post = $request->input('data');

        $checklist = $this->_createChecklist($post);

        if (empty($checklist))
            return response('Failed to Create Checklist', 500);

        return response($checklist, 201);
    }

    public function delete($checklistId){
        $deleted = Checklist::find($checklistId)->delete();

        if (!$deleted)
            return response("Delete Failed!", 400);

        return response("Success to Delete this Checklist!", 204);
    }

    public function index(){
        $checklists = Checklist::all();

        if (empty($checklists))
            return response('Data not Found!', 404);

        return response($checklists, 200);
    }

    public function update(Request $request, $checklistId){
        $post = $request->input('data');

        $checklist = Checklist::find($checklistId);

        $updated_checklist = $this->_updateChecklist($post, $checklist);
        if (!$updated_checklist)
            return response('Failed to Update!', 400);

        return response($updated_checklist, 201);
    }

    public function show($checklistId){
        $checklist = Checklist::find($checklistId);

        if (empty($checklist))
            return response('Data not Found!', 404);

        return response($checklist, 200);
    }

    private function _createChecklist($post){
        $record = $post['attributes'];
        $record['created_by'] = Auth::user()->id;

        $inserted_checklist =  Checklist::create($record);
        if (empty($inserted_checklist))
            return false;

        return $this->_formatChecklist($inserted_checklist);
    }

    private function _formatChecklist($checklist){
        $formatted_checklist = [
            'data' => [
                'type' => $this->type,
                'id' => $checklist->id,
                'attributes' => $checklist
            ],
            'links' => [
                'self' => url("/checklists/{$checklist->id}")
            ]
        ];
        return $formatted_checklist;
    }

    private function _updateChecklist($post, $checklist){
        $attributes = $post['attributes'];

        if (!empty($attributes['object_domain']))
            $checklist->object_domain = $attributes['object_domain'];
        if (!empty($attributes['object_id']))
            $checklist->object_id = $attributes['object_id'];
        if (!empty($attributes['description']))
            $checklist->description = $attributes['description'];
        if (!empty($attributes['is_completed']))
            $checklist->is_completed = $attributes['is_completed'];
        if (!empty($attributes['completed_at']))
            $checklist->completed_at = $attributes['completed_at'];
        if (!empty($attributes['items']))
            $checklist->items = $attributes['items'];

        $checklist->updated_by = Auth::user()->id;

        $updated_checklist = $checklist->save();
        if (!$updated_checklist)
            return false;

        return $this->_formatChecklist(Checklist::find($checklist->id));
    }
}
