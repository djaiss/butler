<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\Vault\VaultController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Settings\Users\UserController;
use App\Http\Controllers\Auth\AcceptInvitationController;
use App\Http\Controllers\Settings\Personalize\PersonalizeController;
use App\Http\Controllers\Settings\Preferences\PreferencesController;
use App\Http\Controllers\Settings\CancelAccount\CancelAccountController;
use App\Http\Controllers\Settings\Personalize\Labels\PersonalizeLabelController;
use App\Http\Controllers\Settings\Personalize\Genders\PersonalizeGenderController;
use App\Http\Controllers\Settings\Personalize\Pronouns\PersonalizePronounController;
use App\Http\Controllers\Settings\Personalize\Templates\PersonalizeTemplatesController;
use App\Http\Controllers\Settings\Personalize\AddressTypes\PersonalizeAddressTypeController;
use App\Http\Controllers\Settings\Personalize\Relationships\PersonalizeRelationshipController;
use App\Http\Controllers\Settings\Personalize\PetCategories\PersonalizePetCategoriesController;
use App\Http\Controllers\Settings\Personalize\Relationships\PersonalizeRelationshipTypeController;
use App\Http\Controllers\Settings\Personalize\ContactInformationTypes\PersonalizeContatInformationTypesController;
use App\Http\Controllers\Settings\Personalize\Templates\PersonalizeTemplatePagesController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

require __DIR__.'/auth.php';

Route::get('invitation/{code}', [AcceptInvitationController::class, 'show'])->name('invitation.show');
Route::post('invitation', [AcceptInvitationController::class, 'store'])->name('invitation.store');

Route::middleware(['auth', 'verified'])->group(function () {
    // vaults
    Route::prefix('vaults')->group(function () {
        Route::get('', [VaultController::class, 'index'])->name('vault.index');
        Route::get('create', [VaultController::class, 'create'])->name('vault.create');
        Route::post('', [VaultController::class, 'store'])->name('vault.store');

        Route::middleware(['vault'])->prefix('{vault}')->group(function () {
            Route::get('', [VaultController::class, 'show'])->name('vault.show');
        });
    });

    // settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('', [SettingsController::class, 'index'])->name('index');

        // only for administrators
        Route::middleware(['administrator'])->group(function () {
            // preferences
            Route::prefix('preferences')->name('preferences.')->group(function () {
                Route::get('', [PreferencesController::class, 'index'])->name('index');
                Route::post('', [PreferencesController::class, 'store'])->name('store');
            });

            // users
            Route::prefix('users')->name('user.')->group(function () {
                Route::get('', [UserController::class, 'index'])->name('index');
                Route::get('create', [UserController::class, 'create'])->name('create');
                Route::post('', [UserController::class, 'store'])->name('store');
                Route::get('{user}', [UserController::class, 'show'])->name('show');
            });

            // personalize
            Route::prefix('personalize')->name('personalize.')->group(function () {
                Route::get('', [PersonalizeController::class, 'index'])->name('index');

                // relationship group types
                Route::get('relationships', [PersonalizeRelationshipController::class, 'index'])->name('relationship.index');
                Route::post('relationships', [PersonalizeRelationshipController::class, 'store'])->name('relationship.grouptype.store');
                Route::put('relationships/{groupType}', [PersonalizeRelationshipController::class, 'update'])->name('relationship.grouptype.update');
                Route::delete('relationships/{groupType}', [PersonalizeRelationshipController::class, 'destroy'])->name('relationship.grouptype.destroy');

                // relationship group types
                Route::post('relationships/{groupType}/types', [PersonalizeRelationshipTypeController::class, 'store'])->name('relationship.type.store');
                Route::put('relationships/{groupType}/types/{type}', [PersonalizeRelationshipTypeController::class, 'update'])->name('relationship.type.update');
                Route::delete('relationships/{groupType}/types/{type}', [PersonalizeRelationshipTypeController::class, 'destroy'])->name('relationship.type.destroy');

                // labels
                Route::get('labels', [PersonalizeLabelController::class, 'index'])->name('label.index');
                Route::post('labels', [PersonalizeLabelController::class, 'store'])->name('label.store');
                Route::put('labels/{label}', [PersonalizeLabelController::class, 'update'])->name('label.update');
                Route::delete('labels/{label}', [PersonalizeLabelController::class, 'destroy'])->name('label.destroy');

                // genders
                Route::get('genders', [PersonalizeGenderController::class, 'index'])->name('gender.index');
                Route::post('genders', [PersonalizeGenderController::class, 'store'])->name('gender.store');
                Route::put('genders/{gender}', [PersonalizeGenderController::class, 'update'])->name('gender.update');
                Route::delete('genders/{gender}', [PersonalizeGenderController::class, 'destroy'])->name('gender.destroy');

                // pronouns
                Route::get('pronouns', [PersonalizePronounController::class, 'index'])->name('pronoun.index');
                Route::post('pronouns', [PersonalizePronounController::class, 'store'])->name('pronoun.store');
                Route::put('pronouns/{pronoun}', [PersonalizePronounController::class, 'update'])->name('pronoun.update');
                Route::delete('pronouns/{pronoun}', [PersonalizePronounController::class, 'destroy'])->name('pronoun.destroy');

                // address types
                Route::get('addressTypes', [PersonalizeAddressTypeController::class, 'index'])->name('address_type.index');
                Route::post('addressTypes', [PersonalizeAddressTypeController::class, 'store'])->name('address_type.store');
                Route::put('addressTypes/{addressType}', [PersonalizeAddressTypeController::class, 'update'])->name('address_type.update');
                Route::delete('addressTypes/{addressType}', [PersonalizeAddressTypeController::class, 'destroy'])->name('address_type.destroy');

                // pet categories
                Route::get('petCategories', [PersonalizePetCategoriesController::class, 'index'])->name('pet_category.index');
                Route::post('petCategories', [PersonalizePetCategoriesController::class, 'store'])->name('pet_category.store');
                Route::put('petCategories/{petCategory}', [PersonalizePetCategoriesController::class, 'update'])->name('pet_category.update');
                Route::delete('petCategories/{petCategory}', [PersonalizePetCategoriesController::class, 'destroy'])->name('pet_category.destroy');

                // contact information
                Route::get('contactInformationType', [PersonalizeContatInformationTypesController::class, 'index'])->name('contact_information_type.index');
                Route::post('contactInformationType', [PersonalizeContatInformationTypesController::class, 'store'])->name('contact_information_type.store');
                Route::put('contactInformationType/{type}', [PersonalizeContatInformationTypesController::class, 'update'])->name('contact_information_type.update');
                Route::delete('contactInformationType/{type}', [PersonalizeContatInformationTypesController::class, 'destroy'])->name('contact_information_type.destroy');

                // templates
                Route::prefix('templates')->name('template.')->group(function () {
                    Route::get('', [PersonalizeTemplatesController::class, 'index'])->name('index');
                    Route::post('', [PersonalizeTemplatesController::class, 'store'])->name('store');
                    Route::put('{template}', [PersonalizeTemplatesController::class, 'update'])->name('update');
                    Route::delete('{template}', [PersonalizeTemplatesController::class, 'destroy'])->name('destroy');
                    Route::get('{template}', [PersonalizeTemplatesController::class, 'show'])->name('show');

                    // template pages
                    Route::post('{template}', [PersonalizeTemplatePagesController::class, 'store'])->name('template_page.store');
                    Route::put('{template}/template_pages/{page}', [PersonalizeTemplatePagesController::class, 'update'])->name('template_page.update');
                    Route::delete('{template}/template_pages/{page}', [PersonalizeTemplatePagesController::class, 'destroy'])->name('template_page.destroy');
                });
            });

            // cancel
            Route::get('cancel', [CancelAccountController::class, 'index'])->name('cancel.index');
            Route::put('cancel', [CancelAccountController::class, 'destroy'])->name('cancel.destroy');
        });
    });

    Route::get('contacts', 'ContactController@index');

    Route::resource('settings/information', 'Settings\\InformationController');

    // contacts
    Route::get('vaults/{vault}/contacts/{contact}', 'HomeController@index')->name('contact.show');
});
