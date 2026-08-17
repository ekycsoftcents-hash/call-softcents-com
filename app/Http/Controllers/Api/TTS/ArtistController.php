<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\TTS;

use App\Http\Controllers\Controller;
use App\Http\Resources\TTSArtistResource;
use App\Models\TTSArtist;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Text to Speech
 *
 * APIs for managing text-to-speech artists.
 */
final class ArtistController extends Controller
{
    /**
     * List Artists
     *
     * Get a paginated list of available TTS artists.
     *
     * @queryParam search string Filter artists by name or code. No-example
     * @queryParam language string Filter artists by language code (e.g., 'en-US') or ID. No-example
     * @queryParam gender string Filter artists by gender (male, female, neutral). No-example
     * @queryParam page integer The page number. Example: 1
     * @queryParam per_page integer The number of items per page. Example: 15
     *
     * @response {
     *  "data": [
     *      {
     *          "id": 1,
     *          "name": "Ava",
     *          "code": "en-US-AvaNeural",
     *          "gender": "female",
     *          "language": {
     *              "id": 1,
     *              "name": "English (United States)",
     *              "code": "en-US",
     *              "engine": "azure",
     *              "enabled": true
     *          },
     *          "enabled": true
     *      }
     *  ],
     *  "links": {...},
     *  "meta": {...}
     * }
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = TTSArtist::query()
            ->with('ttsLanguage')
            ->where('enabled', true);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('language')) {
            $language = $request->input('language');
            if (is_numeric($language)) {
                $query->where('tts_language_id', $language);
            } else {
                $query->whereHas('ttsLanguage', function ($q) use ($language) {
                    $q->where('code', $language);
                });
            }
        }

        if ($request->has('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        return TTSArtistResource::collection(
            $query->paginate($request->input('per_page', 15))
        );
    }

    /**
     * Get Artist
     *
     * Get a specific TTS artist by ID.
     *
     * @urlParam artist integer required The ID of the artist. Example: 1
     *
     * @response {
     *  "data": {
     *      "id": 1,
     *      "name": "Ava",
     *      "code": "en-US-AvaNeural",
     *      "gender": "female",
     *      "language": {
     *          "id": 1,
     *          "name": "English (United States)",
     *          "code": "en-US",
     *          "engine": "azure",
     *          "enabled": true
     *      },
     *      "enabled": true
     *  }
     * }
     */
    public function show(TTSArtist $artist)
    {
        return new TTSArtistResource($artist);
    }
}
