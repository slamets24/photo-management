<x-app-layout>
    <x-slot:title>Data event | </x-slot:title>

    {{-- Breadcrumb --}}
    <nav class="mb-5">
        <ol class="flex items-center gap-2">
            <li>
                <a class="font-medium text-gray-600" href="{{ route('dashboard') }}">Dashboard /</a>
            </li>
            <li class="font-medium text-primary">
                <a class="font-medium text-gray-600" href="{{ route('events.index') }}">Event /</a>
            </li>
            <li class="font-medium text-primary">Tambah Event</li>
        </ol>
    </nav>

    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-1">
            <div class="">
                <h1 class="mb-6 text-xl font-bold text-black-dashboard dark:text-white-dahsboard"> Tambah Event
                </h1>
            </div>
            <div class="px-6 py-6 mb-6 bg-white rounded-lg shadow-lg dark:bg-black">
                <div class="px-6 py-6 mb-6 bg-white rounded-lg dark:bg-black">
                    <label for="thumbnail" class="block mb-2 text-sm font-medium text-black dark:text-white">
                        Masukan Poster Event <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs font-medium text-gray-400">* Pastikan file bertipe jpeg, jpg, png</p>
                    <p class="text-xs font-medium text-gray-400">* Maksimal file 1MB</p>
                    <div id="imagePreviewContainer" class="flex flex-wrap gap-5 mt-3"></div>
                    <input type="file" accept="image/*" name="thumbnail" id="thumbnail" class="mt-3">
                    <x-partials.input-error :messages="$errors->get('thumbnail')" />
                </div>
                <div class="mb-5">
                    <label for="name"
                        class="mb-3 block text-sm font-medium text-black-dashboard dark:text-white-dahsboard">
                        Nama Event<span class="text-red-500">*</span>
                    </label>
                    <input type="text" required name="name" autocomplete="name" maxlength="100"
                        placeholder="Masukan Nama" value="{{ old('name') }}"
                        class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal text-black-dashboard outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white-dahsboard dark:focus:border-primary" />
                    <x-partials.input-error :messages="$errors->get('name')" />
                </div>

                <div class="mb-5">
                    <label for="pass_event"
                        class="mb-3 block text-sm font-medium text-black-dashboard dark:text-white-dahsboard">
                        Password Event <span class="text-red-500">*</span>
                    </label>
                    <input type="text" required name="pass_event" placeholder="Masukan Password Event"
                        autocomplete="new-password"
                        class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal text-black-dashboard outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white-dahsboard dark:focus:border-primary" />
                    <x-partials.input-error :messages="$errors->get('pass_event')" />
                </div>

                <div class="px-6 py-6 mb-6 bg-white rounded-lg shadow-lg dark:bg-black">
                    <label for="image" class="block mb-2 text-sm font-medium text-black dark:text-white">
                        Masukan Foto <span class="text-red-500">*</span>
                    </label>
                    <div id="imagePreviewContainer" class="flex flex-wrap gap-5 mt-3"></div>
                    <input type="file" name="images[]" id="images" accept="image/*" multiple
                        onchange="handlePhotoUpload(event)" class="mt-3">
                    <x-partials.input-error :messages="$errors->get('images.')" />
                </div>
            </div>
        </div>
        <button type="submit"
            class="flex justify-center w-full p-3 font-medium text-white rounded bg-gray-800 hover:bg-opacity-90">
            Kirim
        </button>
    </form>

    <script>
        async function loadModels() {
            await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
        }

        async function handlePhotoUpload(event) {
            const images = event.target.files;
            const faceDescriptors = [];

            for (let i = 0; i < images.length; i++) {
                const img = await faceapi.bufferToImage(images[i]);
                const detection = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();

                if (detection) {
                    faceDescriptors.push({
                        descriptor: detection.descriptor,
                        file: images[i]
                    });
                } else {
                    alert(`No face detected in image ${images[i].name}`);
                }
            }

            // Hanya panggil upload jika ada deskriptor yang terdeteksi
            if (faceDescriptors.length > 0) {
                uploadImagesWithDescriptors(faceDescriptors);
            } else {
                alert('Tidak ada wajah terdeteksi di gambar yang diunggah.');
            }
        }


        async function uploadImagesWithDescriptors(faceDescriptors) {
            const formData = new FormData();
            formData.append('event_id', ''); // Kosongkan atau atur ke ID event yang sesuai jika ada.

            faceDescriptors.forEach((item, index) => {
                formData.append(`images[${index}]`, item.file);
                formData.append(`descriptors[${index}]`, JSON.stringify(item
                    .descriptor)); // Pastikan ini ditambahkan
            });

            // Debug: Cetak semua field dalam formData
            for (const [key, value] of formData.entries()) {
                console.log(key, value);
            }

            fetch('{{ route('events.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Images uploaded successfully');
                    } else {
                        alert('Upload failed');
                    }
                });
        }

        loadModels();
    </script>


</x-app-layout>
