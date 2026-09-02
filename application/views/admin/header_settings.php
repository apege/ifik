<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Pengaturan Header</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
          colors: { brand: '#ea580c' }
        }
      }
    }
  </script>
  <style>
    body { background-color: #fbf7f1; color: #1e293b; }
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(234, 88, 12, 0.15);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border-radius: 20px;
    }
    .btn-brand {
        background: #ea580c; color: #fff;
        transition: all 0.3s;
    }
    .btn-brand:hover {
        background: #c2410c; transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
    }
    .btn-secondary {
        background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;
        transition: all 0.3s;
    }
    .btn-secondary:hover { background: #e2e8f0; color: #1e293b; }
    .form-input {
        width: 100%; padding: 12px 16px; border-radius: 10px;
        border: 1px solid #cbd5e1; background: #fff;
        transition: all 0.3s; outline: none;
    }
    .form-input:focus {
        border-color: #ea580c; box-shadow: 0 0 0 3px rgba(234,88,12,0.1);
    }
    .dropzone {
        border: 2px dashed #cbd5e1; border-radius: 12px; padding: 30px 20px;
        text-align: center; background: #f8fafc; transition: all 0.3s;
        position: relative; cursor: pointer;
    }
    .dropzone.dragover { border-color: #ea580c; background: #fff7ed; }
    .dropzone input[type="file"] {
        position: absolute; width: 100%; height: 100%;
        top: 0; left: 0; opacity: 0; cursor: pointer;
    }
    .preview-container img, .preview-container video {
        max-height: 150px; border-radius: 8px; margin: 10px auto; object-fit: cover;
    }
    /* Staging Table */
    .staging-table th, .staging-table td { padding: 8px 12px; text-align: left; }
    .staging-table th { background: #f8fafc; border-bottom: 2px solid #e2e8f0; font-size: 0.85rem; }
    .staging-table td { border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
  </style>
</head>
<body class="p-6 md:p-12">

  <div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900">Pengaturan Header</h1>
            <p class="text-gray-500 mt-1">Kelola slide carousel dan pengaturan halaman depan.</p>
        </div>
        <a href="<?= base_url('dashboard') ?>" class="text-brand font-semibold hover:underline">&larr; Kembali ke Dashboard</a>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
        <?= $this->session->flashdata('success') ?>
    </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
        <?= $this->session->flashdata('error') ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- KOLOM 1: TAMBAH SLIDE BARU -->
        <div class="glass-card p-6 md:p-8 lg:col-span-8">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                Tambah Slide Baru
            </h2>

            <form action="<?= base_url('adminheader/add_slide') ?>" method="POST" enctype="multipart/form-data" id="formAddSlide">
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Label Indikator (Judul Tombol Bawah)</label>
                    <input type="text" name="label" id="slideLabel" class="form-input" placeholder="Contoh: Prestasi" required>
                    <p class="text-xs text-gray-500 mt-1">Kolom ini akan terkunci setelah Anda menambahkan file ke tabel di bawah.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-orange-800 mb-2">Judul Utama Slide</label>
                    <input type="text" name="overlay_title" class="form-input text-sm" placeholder="Contoh: Inovasi &amp; Kreativitas FIK" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-orange-800 mb-2">Deskripsi Slide</label>
                    <textarea id="overlayDescription" name="overlay_description" class="form-input text-sm" rows="5" placeholder="Tulis deskripsi untuk slide ini..."></textarea>
                    <p class="text-xs text-orange-600 mt-1">&#9998; Editor TinyMCE aktif.</p>
                </div>

                <!-- STAGING AREA -->
                <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <h3 class="font-bold text-sm mb-3">Upload File Media (Bisa Lebih Dari Satu)</h3>
                    
                    <div class="dropzone mb-4" id="dropzoneStaging">
                        <input type="file" id="stagedFile" accept="image/*,video/*" multiple>
                        <div class="text-gray-400 pointer-events-none">
                            <svg class="w-10 h-10 mx-auto mb-2 text-brand opacity-50" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"></path></svg>
                            <p class="font-semibold text-gray-700">Tarik &amp; Lepas File Media di Sini</p>
                            <p class="text-xs mt-1">atau klik untuk memilih file (Bisa Pilih Banyak)</p>
                        </div>
                    </div>

                    <!-- Tabel Staging -->
                    <div class="overflow-x-auto border border-gray-200 rounded-lg bg-white">
                        <table class="w-full staging-table" id="stagingTable">
                            <thead>
                                <tr>
                                    <th>Nama File</th>
                                    <th>Tipe</th>
                                    <th>Durasi (s)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="stagingTableBody">
                                <tr><td colspan="4" class="text-center text-gray-400 py-4">Belum ada file yang ditambahkan.</td></tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Hidden inputs for final submission -->
                    <input type="file" name="media_files[]" id="finalFiles" multiple style="display: none;">
                    <div id="hiddenDurationsContainer"></div>
                </div>

                <button type="submit" class="btn-brand w-full py-3 rounded-xl font-bold text-lg" id="btnSubmitSlide" disabled>Tambah Slide (Pilih File Dulu)</button>
            </form>
        </div>

        <!-- KOLOM 2: Manajemen Slide & Dekanat -->
        <div class="flex flex-col gap-8 lg:col-span-4">
            
            <!-- DAFTAR SLIDE -->
            <div class="glass-card p-6 md:p-8">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Daftar Slide Background
                </h2>

                <div class="flex flex-col gap-4">
                    <?php if(!empty($slides)): foreach($slides as $slide): ?>
                    <div class="flex flex-col p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <?php 
                                    $is_multi = false;
                                    $first_img = '';
                                    $media_type = $slide->media_type;
                                    
                                    if (!empty($slide->media_path) && strpos($slide->media_path, '[') === 0) {
                                        $decoded = json_decode($slide->media_path, true);
                                        if (is_array($decoded) && count($decoded) > 0) {
                                            $is_multi = true;
                                            $first_img = $decoded[0]['file'] ?? '';
                                        }
                                    } else {
                                        $first_img = $slide->media_path;
                                    }
                                ?>
                                <?php if($media_type == 'video'): ?>
                                    <div class="w-16 h-12 bg-gray-900 rounded flex items-center justify-center text-white text-xs font-bold overflow-hidden">VIDEO</div>
                                <?php else: ?>
                                    <img src="<?= base_url('assets/images/' . $first_img) ?>" class="w-16 h-12 object-cover rounded border border-gray-100">
                                <?php endif; ?>
                                <div>
                                    <h4 class="font-bold text-gray-800"><?= htmlspecialchars($slide->label) ?></h4>
                                    <p class="text-xs text-gray-400 uppercase tracking-wide">
                                        <?= $is_multi ? 'Multi-Image' : $media_type ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="openEditSlideModal(<?= $slide->id ?>, '<?= htmlspecialchars($slide->label, ENT_QUOTES) ?>', <?= $slide->duration ?>)" class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center hover:bg-blue-500 hover:text-white transition-colors" title="Edit Slide">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <a href="<?= base_url('adminheader/delete_slide/'.$slide->id) ?>" onclick="return confirm('Yakin ingin menghapus slide ini?')" class="w-8 h-8 rounded-full bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors" title="Hapus Slide">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                    <p class="text-gray-500 text-sm">Belum ada slide. Silakan tambahkan.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- PENGATURAN TEKS UTAMA & DEKANAT -->
            <div class="glass-card p-6 md:p-8">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Pengaturan Teks Utama & Dekanat
                </h2>

                <form action="<?= base_url('adminheader/update_settings') ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2">Gambar Dekanat Saat Ini</label>
                        <?php if(!empty($settings->dekanat_image)): ?>
                            <div class="mb-3 bg-gray-50 rounded-lg p-4 border border-gray-200 inline-block">
                                <img src="<?= base_url('assets/images/' . $settings->dekanat_image) ?>" class="h-32 object-contain" alt="Dekanat">
                            </div>
                        <?php endif; ?>
                        <label class="block text-sm font-semibold mb-2 mt-3">Upload Gambar Dekanat Baru (Opsional)</label>
                        <div class="dropzone" id="dropzoneDekanat">
                            <input type="file" name="dekanat_image" id="dekanatImageInput" accept="image/*">
                            <div class="preview-container text-gray-400" id="previewDekanat">
                                <p class="font-semibold text-gray-700">Tarik &amp; Lepas Gambar di Sini</p>
                                <p class="text-xs mt-1">atau klik untuk mencari file (JPG, PNG, WEBP)</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-brand w-full py-3 rounded-xl font-bold text-lg">Simpan Gambar</button>
                </form>
            </div>
        </div>

    </div>
  </div>

  <!-- MODAL EDIT SLIDE -->
  <div id="editSlideModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center opacity-0 transition-opacity duration-300">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 transform scale-95 transition-transform duration-300">
          <h3 class="text-xl font-bold mb-4">Edit Slide</h3>
          <form id="editSlideForm" action="" method="POST" enctype="multipart/form-data">
              <div class="mb-4">
                  <label class="block text-sm font-bold text-gray-700 mb-2">Label Indikator</label>
                  <input type="text" id="editLabel" name="label" class="form-input" required>
              </div>
              <div class="mb-4">
                  <label class="block text-sm font-bold text-gray-700 mb-2">Durasi (s) - Jika 1 Gambar</label>
                  <input type="number" id="editDuration" name="duration" class="form-input" min="1">
              </div>
              <div class="mb-6">
                  <label class="block text-sm font-bold text-gray-700 mb-2">Upload File Baru (Opsional)</label>
                  <input type="file" name="media_file" class="form-input text-sm" accept="image/*,video/*">
              </div>
              <div class="flex justify-end gap-3">
                  <button type="button" onclick="closeEditModal()" class="btn-secondary px-5 py-2 rounded-xl font-bold">Batal</button>
                  <button type="submit" class="btn-brand px-5 py-2 rounded-xl font-bold">Simpan</button>
              </div>
          </form>
      </div>
  </div>

  <script>
      function openEditSlideModal(id, label, duration) {
          document.getElementById('editSlideForm').action = '<?= base_url("adminheader/edit_slide/") ?>' + id;
          document.getElementById('editLabel').value = label;
          document.getElementById('editDuration').value = duration;
          
          const modal = document.getElementById('editSlideModal');
          modal.classList.remove('hidden');
          // setTimeout for transition
          setTimeout(() => {
              modal.classList.remove('opacity-0');
              modal.querySelector('div').classList.remove('scale-95');
          }, 10);
      }
      function closeEditModal() {
          const modal = document.getElementById('editSlideModal');
          modal.classList.add('opacity-0');
          modal.querySelector('div').classList.add('scale-95');
          setTimeout(() => {
              modal.classList.add('hidden');
          }, 300);
      }
  </script>

  <script>
      tinymce.init({
          selector: '#overlayDescription',
          plugins: 'lists link autolink',
          toolbar: 'bold italic underline | bullist numlist | link | removeformat',
          menubar: false,
          height: 180,
          skin: 'oxide',
          branding: false,
          setup: function(editor) {
              editor.on('change', function() { editor.save(); });
          }
      });

      document.getElementById('formAddSlide').addEventListener('submit', function() {
          tinymce.triggerSave();
      });

      // --- STAGING TABLE LOGIC ---
      const dataTransfer = new DataTransfer();
      const stagingFiles = []; // To hold duration meta
      const fileInput = document.getElementById('stagedFile');
      const finalFilesInput = document.getElementById('finalFiles');
      const hiddenDurationsContainer = document.getElementById('hiddenDurationsContainer');
      const tableBody = document.getElementById('stagingTableBody');
      const labelInput = document.getElementById('slideLabel');
      const btnSubmit = document.getElementById('btnSubmitSlide');
      const dropzoneStaging = document.getElementById('dropzoneStaging');

      function renderTable() {
          if (stagingFiles.length === 0) {
              tableBody.innerHTML = '<tr><td colspan="4" class="text-center text-gray-400 py-4">Belum ada file yang ditambahkan.</td></tr>';
              labelInput.disabled = false;
              btnSubmit.disabled = true;
              btnSubmit.innerText = "Tambah Slide (Pilih File Dulu)";
              return;
          }

          labelInput.disabled = true;
          btnSubmit.disabled = false;
          btnSubmit.innerText = "Tambah Slide";

          tableBody.innerHTML = '';
          hiddenDurationsContainer.innerHTML = '';

          stagingFiles.forEach((item, index) => {
              const tr = document.createElement('tr');
              tr.innerHTML = `
                  <td class="font-semibold text-gray-700">${item.file.name}</td>
                  <td>${item.file.type.startsWith('video/') ? 'Video' : 'Gambar'}</td>
                  <td><input type="number" class="form-input py-1 px-2 text-sm w-20" min="1" value="${item.duration}" onchange="updateDuration(${index}, this.value)"></td>
                  <td><button type="button" onclick="removeStagedFile(${index})" class="text-red-500 font-bold hover:underline text-sm">Hapus</button></td>
              `;
              tableBody.appendChild(tr);

              const hiddenDuration = document.createElement('input');
              hiddenDuration.type = 'hidden';
              hiddenDuration.name = 'durations[]';
              hiddenDuration.value = item.duration;
              hiddenDurationsContainer.appendChild(hiddenDuration);
          });
      }

      window.updateDuration = function(index, val) {
          stagingFiles[index].duration = val;
          renderTable();
      };

      function handleStagingFiles(files) {
          Array.from(files).forEach(file => {
              dataTransfer.items.add(file);
              stagingFiles.push({ file: file, duration: 3 });
          });
          finalFilesInput.files = dataTransfer.files;
          fileInput.value = '';
          renderTable();
      }

      fileInput.addEventListener('change', () => {
          if (fileInput.files.length) handleStagingFiles(fileInput.files);
      });
      
      dropzoneStaging.addEventListener('dragover', e => { e.preventDefault(); dropzoneStaging.classList.add('dragover'); });
      dropzoneStaging.addEventListener('dragleave', e => { dropzoneStaging.classList.remove('dragover'); });
      dropzoneStaging.addEventListener('drop', e => {
          e.preventDefault(); dropzoneStaging.classList.remove('dragover');
          if (e.dataTransfer.files.length) {
              handleStagingFiles(e.dataTransfer.files);
          }
      });

      window.removeStagedFile = function(index) {
          stagingFiles.splice(index, 1);
          dataTransfer.items.remove(index);
          finalFilesInput.files = dataTransfer.files;
          renderTable();
      }

      // Ensure disabled input is submitted
      document.getElementById('formAddSlide').addEventListener('submit', function(e) {
          labelInput.disabled = false; // re-enable before submit so the value gets POSTed
      });

      // --- DROPZONE DEKANAT ---
      function setupDropzoneDekanat() {
          const dropzone = document.getElementById('dropzoneDekanat');
          if (!dropzone) return;
          const input = document.getElementById('dekanatImageInput');
          const preview = document.getElementById('previewDekanat');

          dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('dragover'); });
          dropzone.addEventListener('dragleave', e => { dropzone.classList.remove('dragover'); });
          dropzone.addEventListener('drop', e => {
              e.preventDefault(); dropzone.classList.remove('dragover');
              if (e.dataTransfer.files.length) {
                  input.files = e.dataTransfer.files;
                  handleDekanatPreview(input.files[0], preview);
              }
          });
          input.addEventListener('change', () => {
              if (input.files.length) handleDekanatPreview(input.files[0], preview);
          });
      }

      function handleDekanatPreview(file, container) {
          container.innerHTML = '';
          const img = document.createElement('img');
          img.src = URL.createObjectURL(file);
          container.appendChild(img);
          const txt = document.createElement('p');
          txt.className = 'text-xs mt-2 font-bold text-gray-700';
          txt.innerText = file.name;
          container.appendChild(txt);
      }
      
      setupDropzoneDekanat();
  </script>
</body>
</html>
