<?php

namespace App\Settings\ManageTemplates\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Settings\ManageTemplates\Services\UpdateTemplatePagePosition;
use App\Settings\ManageTemplates\Web\ViewHelpers\PersonalizeTemplateShowViewHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalizeTemplatePagePositionController extends Controller
{
    public function update(Request $request, int $templateId, int $templatePageId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'template_id' => $templateId,
            'template_page_id' => $templatePageId,
            'new_position' => $request->input('position'),
        ];

        $templatePage = (new UpdateTemplatePagePosition())->execute($data);

        return response()->json([
            'data' => PersonalizeTemplateShowViewHelper::dtoTemplatePage($templatePage->template, $templatePage),
        ], 200);
    }
}
