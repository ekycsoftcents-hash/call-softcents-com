<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\TTS;

use App\Http\Controllers\Controller;
use App\Http\Resources\TTSLanguageResource;
use App\Models\TTSLanguage;
use Illuminate\Http\Request;

/**
 * @group Text to Speech
 *
 * APIs for managing text-to-speech languages.
 */
final class LanguageController extends Controller
{
    /**
     * List Languages
     *
     * Get a paginated list of available TTS languages.
     *
     * @queryParam search string Filter languages by name or code. No-example
     * @queryParam engine string Filter languages by engine (e.g., 'azure'). No-example
     * @queryParam page integer The page number. Example: 1
     * @queryParam per_page integer The number of items per page. Example: 15
     *
     * @response {
     *  "data": [
     *      {
     *          "id": 1,
     *          "name": "English (United States)",
     *          "code": "en-US",
     *          "engine": "azure",
     *          "enabled": true
     *      }
     *  ],
     *  "links": {...},
     *  "meta": {...}
     * }
     */
    public function index(Request $request)
    {
        $query = TTSLanguage::query()
            ->where('enabled', true);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('engine')) {
            $query->where('engine', $request->input('engine'));
        }

        return TTSLanguageResource::collection(
            $query->paginate($request->input('per_page', 15))
        );
    }

    /**
     * Get Language
     *
     * Get a specific TTS language by ID.
     *
     * @urlParam language integer required The ID of the language. Example: 1
     *
     * @response {
     *  "data": {
     *      "id": 1,
     *      "name": "English (United States)",
     *      "code": "en-US",
     *      "engine": "azure",
     *      "enabled": true
     *  }
     * }
     */
    public function show(TTSLanguage $language)
    {
        return new TTSLanguageResource($language);
    }
}
