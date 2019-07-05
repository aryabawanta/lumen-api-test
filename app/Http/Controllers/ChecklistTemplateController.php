<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ChecklistTemplate;

class ChecklistTemplateController extends Controller
{
    private $type = 'templates';

    public function __construct(){
        $this->middleware('auth');
    }

    // postponed
    public function assigns($templateId){
        return response("Success",201);
    }

    public function create(Request $request){
        $post = $request->input('data');

        $checklist_template = $this->_createChecklistTemplate($post);

        if (empty($checklist_template))
            return response('Failed to Create Checklist Template', 500);

        return response($checklist_template, 201);
    }

    public function delete($checklistId){
        $deleted = ChecklistTemplate::find($checklistId)->delete();

        if (!$deleted)
            return response("Delete Failed!", 400);

        return response("Success to Delete this Checklist Template!", 204);
    }

    public function index(){
        $checklist_templates = ChecklistTemplate::all();

        if (empty($checklist_templates))
            return response('Data not Found!', 404);

        return response($checklist_templates, 200);
    }

    public function update(Request $request, $checklistId){
        $post = $request->input('data');

        $checklist_template = ChecklistTemplate::find($checklistId);

        $updated_checklist_template = $this->_updateChecklistTemplate($post, $checklist_template);
        if (!$updated_checklist_template)
            return response('Failed to Update!', 400);

        return response($updated_checklist_template, 201);
    }

    public function show($templateId){
        $checklist_template = ChecklistTemplate::find($templateId);

        if (empty($checklist_template))
            return response('Data not Found!', 404);

        return response($this->_formatChecklistTemplate($checklist_template), 200);
    }

    private function _createChecklistTemplate($post){
        $record = $post['attributes'];

        $inserted_checklist_template =  ChecklistTemplate::create($record);
        if (empty($inserted_checklist_template))
            return false;

        return $this->_formatChecklistTemplate($inserted_checklist_template);
    }

    private function _formatChecklistTemplate($checklist_template){
        $formatted_checklist_template = [
            'data' => [
                'type' => $this->type,
                'id' => $checklist_template->id,
                'attributes' => $checklist_template
            ],
            'links' => [
                'self' => url("/checklists/template/{$checklist_template->id}")
            ]
        ];
        return $formatted_checklist_template;
    }

    private function _updateChecklistTemplate($post, $checklist_template){
        $attributes = $post;

        if (!empty($attributes['name']))
            $checklist_template->name = $attributes['name'];
        if (!empty($attributes['checklist']))
            $checklist_template->checklist = $attributes['checklist'];
        if (!empty($attributes['items']))
            $checklist_template->items = $attributes['items'];

        $updated_checklist_template = $checklist_template->save();
        if (!$updated_checklist_template)
            return false;

        return $this->_formatChecklistTemplate(ChecklistTemplate::find($checklist_template->id));
    }
}
