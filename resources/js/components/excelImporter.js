/**
 * Component Alpine.js untuk drag-and-drop dan pratinjau berkas spreadsheet Excel/CSV.
 */
export default (config = {}) => ({
    previewUrl: config.previewUrl || '',
    csrfToken: config.csrfToken || '',
    isDragging: false,
    file: null,
    fileInfo: {
        name: '',
        size: '',
        type: '',
    },
    loading: false,
    errorMessage: '',
    previewData: null,
    isSubmitting: false,

    init() {
        // Cleanup if needed
    },

    formatBytes(bytes, decimals = 1) {
        if (!+bytes) return '0 B';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
    },

    getFileExtension(filename) {
        if (!filename) return '';
        const parts = filename.split('.');
        return parts.length > 1 ? parts.pop().toLowerCase() : '';
    },

    handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        this.isDragging = true;
    },

    handleDragEnter(e) {
        e.preventDefault();
        e.stopPropagation();
        this.isDragging = true;
    },

    handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        // Prevent flashing when dragging over child elements
        if (e.currentTarget.contains(e.relatedTarget)) {
            return;
        }
        this.isDragging = false;
    },

    handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        this.isDragging = false;

        const files = e.dataTransfer?.files;
        if (files && files.length > 0) {
            this.setFile(files[0]);
        }
    },

    handleFileSelect(e) {
        const files = e.target?.files;
        if (files && files.length > 0) {
            this.setFile(files[0]);
        }
    },

    triggerFileInput() {
        if (this.$refs.fileInput) {
            this.$refs.fileInput.click();
        }
    },

    setFile(file) {
        this.errorMessage = '';
        this.previewData = null;

        const ext = this.getFileExtension(file.name);
        const allowedExts = ['xlsx', 'xls', 'csv'];

        if (!allowedExts.includes(ext)) {
            this.errorMessage = `Format berkas .${ext || 'tidak diketahui'} tidak didukung. Harap unggah berkas .xlsx, .xls, atau .csv.`;
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            this.errorMessage = 'Ukuran berkas melebihi batas maksimum 10 MB.';
            return;
        }

        this.file = file;
        this.fileInfo = {
            name: file.name,
            size: this.formatBytes(file.size),
            type: ext.toUpperCase(),
        };

        // Sinkronisasi dengan input form native agar POST standar tetap mengirim file
        if (this.$refs.fileInput) {
            const dt = new DataTransfer();
            dt.items.add(file);
            this.$refs.fileInput.files = dt.files;
        }

        // Jalankan pratinjau spreadsheet ke server
        this.fetchPreview(file);
    },

    removeFile() {
        this.file = null;
        this.fileInfo = { name: '', size: '', type: '' };
        this.previewData = null;
        this.errorMessage = '';
        this.loading = false;
        if (this.$refs.fileInput) {
            this.$refs.fileInput.value = '';
        }
    },

    async fetchPreview(file) {
        if (!this.previewUrl) return;

        this.loading = true;
        this.errorMessage = '';

        const formData = new FormData();
        formData.append('file', file);
        if (this.csrfToken) {
            formData.append('_token', this.csrfToken);
        }

        try {
            const response = await fetch(this.previewUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: formData,
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                this.errorMessage = result.message || 'Gagal memproses pratinjau dokumen spreadsheet.';
                this.previewData = null;
            } else {
                this.previewData = result.data;
            }
        } catch (err) {
            console.error('Preview error:', err);
            this.errorMessage = 'Terjadi kesalahan saat memuat pratinjau dokumen: ' + (err.message || 'Gagal terhubung ke server');
        } finally {
            this.loading = false;
        }
    },

    handleSubmit(e) {
        if (!this.file) {
            e.preventDefault();
            this.errorMessage = 'Silakan pilih berkas Excel/CSV terlebih dahulu sebelum memulai impor.';
            return;
        }
        this.isSubmitting = true;
    },
});
