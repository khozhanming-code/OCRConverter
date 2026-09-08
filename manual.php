<?php
// manual.php - User Manual
$lang = isset($_GET['lang']) && $_GET['lang'] === 'zh' ? 'zh' : 'en';

$content = [
    'en' => [
        'title' => 'OCR Converter - User Manual',
        'back' => '← Back to Converter',
        'h1' => 'How to Use OCR Converter',
        'intro' => 'Welcome to the OCR Converter application documentation. Follow this guide to seamlessly extract text from images.',
        'sec1' => '1. Selecting an Image',
        'desc1' => 'Click the "Choose Image" button or drag and drop your file into the designated area. Supported formats include JPG, JPEG, PNG, GIF, BMP, TIFF, and WEBP. Maximum file size allowed is 10 MB.',
        'sec2' => '2. Starting OCR',
        'desc2' => 'Once an image is previewed correctly, click the "Start OCR" button. The app will trace through status updates: Starting → Uploading → Recognizing → Complete.',
        'sec3' => '3. Editing, Copying & Printing',
        'desc3' => 'The recognized text will populate the editable text box. You can modify any minor mistakes directly, copy the text to your clipboard, or print the text instantly using the browser print tool.',
        'sec4' => '4. Exporting to Word',
        'desc4' => 'Click "Export to Word" to download the recognized text formatted safely into a downloadable .doc file containing your exact formatting and line breaks.',
        'sec5' => '5. OCR Quality Tips',
        'desc5' => 'Ensure images are well-lit, high contrast, straight, and clearly focused for optimal recognition results.'
    ],
    'zh' => [
        'title' => 'OCR 转换器 - 使用手册',
        'back' => '← 返回转换器',
        'h1' => 'OCR 文字识别转换器使用指南',
        'intro' => '欢迎阅读 OCR 转换器使用文档。本指南将协助您顺利从图像中提取文本。',
        'sec1' => '1. 选择图片',
        'desc1' => '点击“选择图片”按钮或将文件拖放到指定区域。支持的格式包括 JPG、JPEG、PNG、GIF、BMP、TIFF 和 WEBP。最大文件大小限制为 10 MB。',
        'sec2' => '2. 开始识别',
        'desc2' => '预览图片后，点击“开始识别”按钮。应用将依序显示进度状态：正在启动 → 正在上传 → 正在识别 → 识别完成。',
        'sec3' => '3. 编辑、复制与打印',
        'desc3' => '识别出的文本将显示在可编辑文本框中。您可以直接进行内容修改，点击“复制文本”复制到剪贴板，或通过浏览器打印功能直接打印。',
        'sec4' => '4. 导出为 Word',
        'desc4' => '点击“导出为 Word”，将识别后的文本保存为安全的 .doc 兼容文件，完美保留段落与换行。',
        'sec5' => '5. 提高识别质量提示',
        'desc5' => '为了获得最佳识别效果，请确保所选图片光线充足、对比度高、方向正且对焦清晰。'
    ]
];

$c = $content[$lang];
$altLang = $lang === 'en' ? 'zh' : 'en';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($c['title']); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="app-header">
        <h1><?php echo htmlspecialchars($c['title']); ?></h1>
        <div class="header-nav">
            <a href="index.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo htmlspecialchars($c['back']); ?></a>
            <a href="manual.php?lang=<?php echo $altLang; ?>" class="lang-switch"><?php echo strtoupper($altLang); ?></a>
        </div>
    </header>

    <main class="manual-container">
        <h2><?php echo htmlspecialchars($c['h1']); ?></h2>
        <p><?php echo htmlspecialchars($c['intro']); ?></p>

        <section>
            <h3><?php echo htmlspecialchars($c['sec1']); ?></h3>
            <p><?php echo htmlspecialchars($c['desc1']); ?></p>
        </section>

        <section>
            <h3><?php echo htmlspecialchars($c['sec2']); ?></h3>
            <p><?php echo htmlspecialchars($c['desc2']); ?></p>
        </section>

        <section>
            <h3><?php echo htmlspecialchars($c['sec3']); ?></h3>
            <p><?php echo htmlspecialchars($c['desc3']); ?></p>
        </section>

        <section>
            <h3><?php echo htmlspecialchars($c['sec4']); ?></h3>
            <p><?php echo htmlspecialchars($c['desc4']); ?></p>
        </section>

        <section>
            <h3><?php echo htmlspecialchars($c['sec5']); ?></h3>
            <p><?php echo htmlspecialchars($c['desc5']); ?></p>
        </section>
    </main>
</body>
</html>