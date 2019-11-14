<?php

namespace EricLagarda\SettingsCard\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsCardController extends Controller
{
    /**
     * @var mixed
     */
    protected $disks;

    /**
     * @param Request $request
     */
    public function saveSettings(Request $request)
    {
        $fields = collect($request->except('disks'))->reject(function ($value, $key) {
            return Str::contains($key, 'DraftId');
        });

        $this->disks = collect(json_decode($request->get('disks')));

        $fields->each(function ($value, $key) use ($request) {
            $value = $this->getRequestValue($request, $key, $value);

            if (!$value) {
                setting()->forget($key);
            }
            if ($value) {
                setting([$key => $value]);
            }
        });

        setting()->save();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * @param $request
     * @param $key
     * @param $setting
     * @return mixed
     */
    private function getRequestValue($request, $key, $value)
    {
        if ($value instanceof UploadedFile) {
            $disk = 'public';

            if ($this->disks->has($key)) {
                $disk = $this->disks->get($key);
            }

            $this->deletePreviusImage($key, $disk);

            $path = $request->{$key}->store('', $disk);

            return json_encode([
                'path' => $path,
                'url'  => Storage::disk($disk)->url($path),
                'size' => $request->{$key}->getSize(),
                'name' => $request->{$key}->getClientOriginalName(),
            ]);
        }

        return $value;
    }

    /**
     * @param $key
     */
    private function deletePreviusImage($key, $disk)
    {
        $data = setting($key, false);
        if ($data !== false) {
            $data = json_decode($data);
            Storage::disk($disk)->delete($data->path);
        }
    }
}
