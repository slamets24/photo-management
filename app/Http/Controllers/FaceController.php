<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Photo;

class FaceController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function detectFaceId($imagePath)
    {
        $url = env('FACE_API_ENDPOINT') . '/face/v1.0/detect';
        $headers = [
            'Ocp-Apim-Subscription-Key' => env('FACE_API_KEY'),
            'Content-Type' => 'application/octet-stream'
        ];

        // Baca foto dalam binary
        $imageData = file_get_contents(storage_path('app/public/' . $imagePath));

        try {
            // Request ke Face API
            $response = $this->client->post($url, [
                'headers' => $headers,
                'body' => $imageData,
                'query' => [
                    'returnFaceId' => 'true'
                ]
            ]);
            \Log::info('Face API response: ' . $response->getBody());

            $result = json_decode($response->getBody(), true);

            // Jika tidak ada wajah yang terdeteksi
            if (empty($result)) {
                \Log::error('No faces detected in the image: ' . $imagePath);
                return null; // Kembalikan null jika tidak ada wajah yang terdeteksi
            }

            // Mengembalikan faceId (jika ada wajah terdeteksi)
            return $result[0]['faceId'] ?? null;
        } catch (\Exception $e) {
            // Tangkap dan log kesalahan
            \Log::error('Face detection error: ' . $e->getMessage());
            return null;
        }
    }


    public function matchFace(Request $request)
    {
        // Validasi input
        $request->validate([
            'selfie' => 'required|image|mimes:jpeg,png,jpg|max:5048',
        ]);

        // Simpan foto selfie
        $selfiePath = $request->file('selfie')->store('selfies', 'public');

        // Dapatkan faceId dari foto selfie
        $selfieFaceId = $this->detectFaceId($selfiePath);

        if (!$selfieFaceId) {
            return response()->json(['error' => 'Wajah tidak terdeteksi pada foto selfie'], 400);
        }

        // Cek setiap foto di galeri untuk dicocokkan
        $matchedPhotos = [];
        $photos = Photo::all(); // Mengambil semua foto dari database

        foreach ($photos as $photo) {
            // Mengambil file path foto
            $galleryImagePath = $photo->file_path;

            // Dapatkan faceId dari foto galeri
            $galleryFaceId = $this->detectFaceId($galleryImagePath);

            if (!$galleryFaceId) {
                continue; // Skip jika tidak ada wajah terdeteksi
            }

            // Lakukan permintaan pencocokan
            try {
                $urlVerify = env('FACE_API_ENDPOINT') . '/face/v1.0/verify';
                $response = $this->client->post($urlVerify, [
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => env('FACE_API_KEY')
                    ],
                    'json' => [
                        'faceId1' => $selfieFaceId,
                        'faceId2' => $galleryFaceId
                    ]
                ]);

                $result = json_decode($response->getBody(), true);

                // Jika cocok, tambahkan ke daftar hasil
                if ($result['isIdentical']) {
                    $matchedPhotos[] = $photo; // Menambahkan foto yang cocok
                }
            } catch (\Exception $e) {
                \Log::error('Face verification error: ' . $e->getMessage());
            }
        }

        return view('pages.dashboard.index', compact('matchedPhotos'));
    }
}
