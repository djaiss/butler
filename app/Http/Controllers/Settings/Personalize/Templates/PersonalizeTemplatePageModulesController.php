<?php

namespace App\Http\Controllers\Settings\Personalize\Templates;

use App\Models\Template;
use App\Models\TemplatePage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Account\ManageTemplate\CreateTemplatePage;
use App\Services\Account\ManageTemplate\UpdateTemplatePage;
use App\Services\Account\ManageTemplate\DestroyTemplatePage;
use App\Http\Controllers\Settings\Personalize\Templates\ViewHelpers\PersonalizeTemplateShowViewHelper;
use App\Http\Controllers\Settings\Personalize\Templates\ViewHelpers\PersonalizeTemplatePageShowViewHelper;
use App\Services\Account\ManageTemplate\AssociateModuleToTemplatePage;
use App\Services\Account\ManageTemplate\RemoveModuleFromTemplatePage;

class PersonalizeTemplatePageModulesController extends Controller
{
    public function store(Request $request, int $templateId, int $templatePageId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'template_id' => $templateId,
            'template_page_id' => $templatePageId,
            'module_id' => $request->input('module_id'),
        ];

        $templatePage = TemplatePage::findOrFail($templatePageId);

        $module = (new AssociateModuleToTemplatePage)->execute($data);

        return response()->json([
            'data' => PersonalizeTemplatePageShowViewHelper::dtoModule($templatePage, $module),
        ], 201);
    }

    public function destroy(Request $request, int $templateId, int $templatePageId, int $moduleId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'template_id' => $templateId,
            'template_page_id' => $templatePageId,
            'module_id' => $moduleId,
        ];

        $templatePage = TemplatePage::findOrFail($templatePageId);

        $module = (new RemoveModuleFromTemplatePage)->execute($data);

        return response()->json([
            'data' => PersonalizeTemplatePageShowViewHelper::dtoModule($templatePage, $module),
        ], 200);
    }
}
