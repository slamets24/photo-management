<x-app-layout>
    <div class="py-12">
        <h2>Filter Photos by Selfie</h2>
        <form action="{{ route('match-face') }}" method="POST" enctype="multipart/form-data">
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
    </div>
</x-app-layout>
