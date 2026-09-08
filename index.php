<?php
// index.php - Main OCR Converter Application
session_start();

$lang = isset($_GET['lang']) && $_GET['lang'] === 'zh' ? 'zh' : 'en';

$translations = [
    'en' => [
        'title' => 'OCR Converter',
        'manual' => 'User Manual',
        'choose_image' => 'Choose Image',
        'start_ocr' => 'Start OCR',
        'clear' => 'Clear',
        'copy' => 'Copy Text',
        'print' => 'Print',
        'export' => 'Export to Word',
        'placeholder' => 'Recognized text will appear here...',
        'file_info' => 'No file selected',
        'status_ready' => 'Ready',
        'status_starting' => 'Starting...',
        'status_uploading' => 'Uploading...',
        'status_recognizing' => 'Recognizing...',
        'status_complete' => 'Complete',
        'err_no_image' => 'Please select an image file first.',
        'err_invalid_type' => 'Unsupported file format.',
        'err_too_large' => 'File size exceeds the 10 MB limit.',
        'err_api_fail' => 'OCR processing failed.',
        'err_no_text' => 'No text detected in the image.'
    ],
    'zh' => [
        'title' => 'OCR 文字识别转换器',
        'manual' => '使用手册',
        'choose_image' => '选择图片',
        'start_ocr' => '开始识别',
        'clear' => '清空',
        'copy' => '复制文本',
        'print' => '打印',
        'export' => '导出为 Word',
        'placeholder' => '识别出的文本将显示在这里...',
        'file_info' => '未选择任何文件',
        'status_ready' => '就绪',
        'status_starting' => '正在启动...',
        'status_uploading' => '正在上传...',
        'status_recognizing' => '正在识别...',
        'status_complete' => '识别完成',
        'err_no_image' => '请先选择一张图片。',
        'err_invalid_type' => '不支持的文件格式。',
        'err_too_large' => '文件大小超过 10 MB 限制。',
        'err_api_fail' => 'OCR 处理失败。',
        'err_no_text' => '未在图片中检测到文字。'
    ]
];

$t = $translations[$lang];
$altLang = $lang === 'en' ? 'zh' : 'en';
$altLangQuery = '?lang=' . $altLang;

// Handle AJAX Request for OCR Processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    header('Content-Type: application/json');
    
    // Check API Key (优先从 config.php 读取，若没有则读取环境变量)
    $config = file_exists('config.php') ? include 'config.php' : [];
    $apiKey = $config['AGNES_API_KEY'] ?? getenv('AGNES_API_KEY');

    if (!$apiKey || $apiKey === 'your_agnes_api_key_here') {
        echo json_encode(['success' => false, 'error' => 'Server configuration error: API key missing or not configured.']);
        exit;
    }

    $file = $_FILES['image'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'error' => $t['err_no_image']]);
        exit;
    }

    if ($file['size'] > 10 * 1024 * 1024) {
        echo json_encode(['success' => false, 'error' => $t['err_too_large']]);
        exit;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/webp'];
    if (!in_array($mimeType, $allowedMimes)) {
        echo json_encode(['success' => false, 'error' => $t['err_invalid_type']]);
        exit;
    }

    // Call Agnes AI API (agnes-2.5-flash)
    $imageData = base64_encode(file_get_contents($file['tmp_name']));
    $dataUri = 'data:' . $mimeType . ';base64,' . $imageData;

    $payload = [
        'model' => 'agnes-2.5-flash',
        'messages' => [
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Extract all readable text from this image precisely. Preserve line breaks and reading order. Do not translate the text.'
                    ],
                    [
                        'type' => 'image_url',
                        'image_url' => ['url' => $dataUri]
                    ]
                ]
            ]
        ]
    ];

    $ch = curl_init('https://apihub.agnes-ai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$response) {
        echo json_encode(['success' => false, 'error' => $t['err_api_fail']]);
        exit;
    }

    $responseData = json_decode($response, true);
    $extractedText = $responseData['choices'][0]['message']['content'] ?? '';

    if (trim($extractedText) === '') {
        echo json_encode(['success' => false, 'error' => $t['err_no_text']]);
        exit;
    }

    echo json_encode(['success' => true, 'text' => $extractedText]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($t['title']); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="app-header">
        <h1><?php echo htmlspecialchars($t['title']); ?></h1>
        <div class="header-nav">
            <a href="manual.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo htmlspecialchars($t['manual']); ?></a>
            <a href="<?php echo $altLangQuery; ?>" class="lang-switch"><?php echo strtoupper($altLang); ?></a>
        </div>
    </header>

    <main class="app-container">
        <div class="panel upload-panel">
            <div class="file-drop-area" id="dropArea">
                <input type="file" id="imageInput" accept=".jpg,.jpeg,.png,.gif,.bmp,.tiff,.webp" hidden>
                <button type="button" id="chooseBtn" class="btn primary-btn"><?php echo htmlspecialchars($t['choose_image']); ?></button>
                <span id="fileInfo" class="file-info"><?php echo htmlspecialchars($t['file_info']); ?></span>
            </div>
            
            <div class="preview-container">
                <img id="imagePreview" src="" alt="Preview" style="display: none;">
            </div>

            <div class="action-buttons">
                <button type="button" id="startOcrBtn" class="btn success-btn" disabled><?php echo htmlspecialchars($t['start_ocr']); ?></button>
                <button type="button" id="clearBtn" class="btn danger-btn"><?php echo htmlspecialchars($t['clear']); ?></button>
            </div>

            <div id="statusIndicator" class="status-indicator"><?php echo htmlspecialchars($t['status_ready']); ?></div>
        </div>

        <div class="panel result-panel">
            <textarea id="resultTextarea" placeholder="<?php echo htmlspecialchars($t['placeholder']); ?>"></textarea>
            
            <div class="result-actions">
                <button type="button" id="copyBtn" class="btn secondary-btn"><?php echo htmlspecialchars($t['copy']); ?></button>
                <button type="button" id="printBtn" class="btn secondary-btn"><?php echo htmlspecialchars($t['print']); ?></button>
                <button type="button" id="exportBtn" class="btn secondary-btn"><?php echo htmlspecialchars($t['export']); ?></button>
            </div>
        </div>
    </main>

    <script>
        const langData = {
            statusReady: "<?php echo $t['status_ready']; ?>",
            statusStarting: "<?php echo $t['status_starting']; ?>",
            statusUploading: "<?php echo $t['status_uploading']; ?>",
            statusRecognizing: "<?php echo $t['status_recognizing']; ?>",
            statusComplete: "<?php echo $t['status_complete']; ?>",
            errNoImage: "<?php echo $t['err_no_image']; ?>",
            errTooLarge: "<?php echo $t['err_too_large']; ?>"
        };
    </script>
    <script src="app.js"></script>
</body>
</html>