<x-app-layout>
    <div class="py-12">
        <h2>Filter Photos by Selfie</h2>
        {{-- <form action="{{ route('match-face') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div>
                <label for="selfie">Upload a Selfie:</label>
                <input type="file" name="selfie" id="selfie" accept="image/*" required>
            </div>
            <button type="submit">Filter Photos</button>
        </form>
        @if (isset($matchedPhotos) && count($matchedPhotos) > 0)
            <h3>Matching Photos:</h3>
            @foreach ($matchedPhotos as $photo)
                <div>
                    <img src="{{ asset('storage/' . $photo->file_path) }}" width="200">
                </div>
            @endforeach
        @else
            <p>No matching photos found.</p>
        @endif
    </div> --}}

        <h1>Event Photos</h1>

        <input type="file" id="uploadImage" />
        <div id="photo-list"></div>


        @foreach ($photos as $photo)
            <img src="{{ Storage::url($photo->file_path) }}" class="event-photo"
                style="width: 150px; height: auto; margin: 5px;">
        @endforeach

        <script>
            async function detectFaces() {
                await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
                await faceapi.nets.faceRecognitionNet.loadFromUri('/models');

                const image = document.getElementById('uploadImage').files[0];
                const detections = await faceapi.detectAllFaces(image).withFaceLandmarks().withFaceDescriptors();

                // Bandingkan deteksi wajah dengan foto di event
                // Lakukan matching dengan server untuk mencari foto-foto yang ada muka pengguna
            }

            document.getElementById('uploadImage').addEventListener('change', detectFaces);
        </script>

</x-app-layout>
