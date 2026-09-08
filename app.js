// app.js - Frontend interactions and workflow
document.addEventListener('DOMContentLoaded', () => {
    const imageInput = document.getElementById('imageInput');
    const chooseBtn = document.getElementById('chooseBtn');
    const dropArea = document.getElementById('dropArea');
    const fileInfo = document.getElementById('fileInfo');
    const imagePreview = document.getElementById('imagePreview');
    const startOcrBtn = document.getElementById('startOcrBtn');
    const clearBtn = document.getElementById('clearBtn');
    const statusIndicator = document.getElementById('statusIndicator');
    const resultTextarea = document.getElementById('resultTextarea');
    const copyBtn = document.getElementById('copyBtn');
    const printBtn = document.getElementById('printBtn');
    const exportBtn = document.getElementById('exportBtn');

    let selectedFile = null;

    // Trigger file chooser
    chooseBtn.addEventListener('click', () => imageInput.click());
    dropArea.addEventListener('click', (e) => {
        if (e.target === dropArea || e.target === fileInfo) {
            imageInput.click();
        }
    });

    // File Selection handling
    imageInput.addEventListener('change', (e) => {
        handleFile(e.target.files[0]);
    });

    // Drag and Drop
    dropArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropArea.classList.add('dragover');
    });

    dropArea.addEventListener('dragleave', () => {
        dropArea.classList.remove('dragover');
    });

    dropArea.addEventListener('drop', (e) => {
        e.preventDefault();
        dropArea.classList.remove('dragover');
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            handleFile(e.dataTransfer.files[0]);
        }
    });

    function handleFile(file) {
        if (!file) return;

        // Frontend validation
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            alert(langData.errNoImage || "Invalid image format.");
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            alert(langData.errTooLarge || "File is larger than 10 MB.");
            return;
        }

        selectedFile = file;
        fileInfo.textContent = `${file.name} (${(file.size / (1024 * 1024)).toFixed(2)} MB)`;
        startOcrBtn.disabled = false;

        // Preview image
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    // Start OCR workflow
    startOcrBtn.addEventListener('click', async () => {
        if (!selectedFile) {
            alert(langData.errNoImage);
            return;
        }

        const formData = new FormData();
        formData.append('image', selectedFile);

        try {
            statusIndicator.textContent = langData.statusStarting;
            await sleep(200);

            statusIndicator.textContent = langData.statusUploading;
            await sleep(300);

            statusIndicator.textContent = langData.statusRecognizing;

            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                statusIndicator.textContent = langData.statusComplete;
                resultTextarea.value = data.text;
            } else {
                statusIndicator.textContent = langData.statusReady;
                alert(data.error || "OCR Processing failed.");
            }
        } catch (error) {
            statusIndicator.textContent = langData.statusReady;
            alert("Network or Server error occurred.");
        }
    });

    // Clear Button
    clearBtn.addEventListener('click', () => {
        selectedFile = null;
        imageInput.value = '';
        imagePreview.src = '';
        imagePreview.style.display = 'none';
        fileInfo.textContent = langData.statusReady === 'Ready' ? 'No file selected' : '未选择任何文件';
        startOcrBtn.disabled = true;
        statusIndicator.textContent = langData.statusReady;
        resultTextarea.value = '';
    });

    // Copy Text
    copyBtn.addEventListener('click', () => {
        if (!resultTextarea.value.trim()) return;
        navigator.clipboard.writeText(resultTextarea.value).then(() => {
            alert("Text copied to clipboard!");
        });
    });

    // Print
    printBtn.addEventListener('click', () => {
        if (!resultTextarea.value.trim()) return;
        const printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Print OCR Text</title></head><body>');
        printWindow.document.write('<pre style="font-family:sans-serif; white-space:pre-wrap;">' + escapeHtml(resultTextarea.value) + '</pre>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });

    // Export to Word (.doc)
    exportBtn.addEventListener('click', () => {
        const text = resultTextarea.value;
        if (!text.trim()) return;

        const htmlContent = `<html><head><meta charset='utf-8'></head><body><p style='white-space: pre-wrap; font-family: Arial, sans-serif;'>${escapeHtml(text).replace(/\n/g, '<br>')}</p></body></html>`;
        const blob = new Blob(['\ufeff' + htmlContent], { type: 'application/msword' });
        const url = URL.createObjectURL(blob);
        
        const a = document.createElement('a');
        a.href = url;
        a.download = 'ocr-document-' + Date.now() + '.doc';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});