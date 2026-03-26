<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Buyer;

class FileController extends Controller
{   
    static $default = 'default.jpg';
    static $diskName = 'profile'; // declared in config/filesystems.php

    static $systemTypes = [
        'profile' => ['png', 'jpg', 'jpeg', 'gif'],
    ];

    private static function getDefaultExtension(String $type) {
        return reset(self::$systemTypes[$type]);
    }

    private static function isValidExtension(String $type, String $extension) {
        $allowedExtensions = self::$systemTypes[$type];

        // Note the toLowerCase() method, it is important to allow .JPG and .jpg extensions as well
        return in_array(strtolower($extension), $allowedExtensions);
    }

    private static function isValidType(String $type) {
        return array_key_exists($type, self::$systemTypes);
    }

    private static function defaultAsset(String $type) {
        return asset('images/' . $type . '/' . self::$default);
    }

    private static function getFileName(String $type, int $id, String $extension = null) {

        $fileName = null;
        switch($type) {
            case 'profile':
                $buyer = Buyer::find($id);
                $fileName = $buyer ? $buyer->profile_image : null; // can be null as well
                break;
            default:
                return null;
        }

        return $fileName;
    }

    private static function delete(String $type, int $id) {
        $existingFileName = self::getFileName($type, $id);
        if ($existingFileName) {
            Storage::disk(self::$diskName)->delete($type . '/' . $existingFileName);

            switch($type) {
                case 'profile':
                    $buyer = Buyer::find($id);
                    if ($buyer) {
                        $buyer->profile_image = null;
                        $buyer->save();
                    }
                    break;
            }
        }
    }

    function upload(Request $request) {

        // Validation: has file
        if (!$request->hasFile('file')) {
            return redirect()->back()->with('error', 'Error: File not found');
        }

        // Validation: upload type
        if (!$this->isValidType($request->type)) {
            return redirect()->back()->with('error', 'Error: Unsupported upload type');
        }

        // Validation: upload extension
        $file = $request->file('file');
        $type = $request->type;
        $extension = $file->extension();
        if (!$this->isValidExtension($type, $extension)) {
            return redirect()->back()->with('error', 'Error: Unsupported upload extension');
        }

        // Prevent existing old files
        $this->delete($type, $request->id);

        // Generate unique filename
        $fileName = $file->hashName();

        // Validation: model
        $error = null;
        switch($request->type) {
            case 'profile':
                $buyer = Buyer::find($request->id);
                if ($buyer) {
                    $buyer->profile_image = $fileName;
                    $buyer->save();
                } else {
                    $error = "unknown buyer";
                }
                break;

            default:
                return redirect()->back()->with('error', 'Error: Unsupported upload object');
        }

        if ($error) {
            return redirect()->back()->with('error', "Error: {$error}");
        }

        $file->storeAs($type, $fileName, self::$diskName);
        return redirect()->back()->with('success', 'Success: upload completed!');
    }

    static function get(String $type, int $id) {

        // Validation: upload type
        if (!self::isValidType($type)) {
            return self::defaultAsset($type);
        }

        // Validation: file exists
        $fileName = self::getFileName($type, $id);
        if ($fileName) {
            return asset('images/' . $type . '/' . $fileName);
        }

        // Not found: returns default asset
        return self::defaultAsset($type);
    }
}
